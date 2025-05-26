<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditNotes;
use Illuminate\Http\JsonResponse;
use App\Models\CreditNotesAttachment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CreditNotesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);

        $query = CreditNotes::query();

        if ($id) {
            $query->where('id', $id);
        }

        $creditNotes = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => 'Credit notes list retrieved successfully',
            'result' => $creditNotes
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sales_invoice_no' => 'nullable|string|max:100',
            'sales_invoice_date' => 'nullable|date',
            'shiping_ticket_no' => 'nullable|string|max:100',
            'date_shipped' => 'nullable|date',
            'sales_invoice_status' => 'nullable|string|max:100',
            'sales_rep' => 'nullable|string|max:100',
            'credit_note_date' => 'nullable|date',
            'customer' => 'nullable|string|max:150',
            'customer_billing_location' => 'nullable|string|max:255',
            'customer_shop_no' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'customer_contact' => 'nullable|string|max:100',
            'grn_no' => 'nullable|string|max:100',
            'grn_date' => 'nullable|date',
            'date_created' => 'nullable|date',
            'date_approved' => 'nullable|date',
            'date_applied' => 'nullable|date',
            'applied_sales_invoice' => 'nullable|string|max:100',
            'applied_date' => 'nullable|date',
            'refund_note_no' => 'nullable|string|max:100',
            'refund_date' => 'nullable|date',
            'last_updated' => 'nullable|date',
            'last_updated_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        // Determine approval status
        $action = $request->input('action');
        $validated['is_approved'] = ($action === 'approve') ? 2 : 1;

        // Generate Credit Note Number
        $todayDate = now()->format('ymd');
        $lastNote = CreditNotes::where('credit_note_no', 'LIKE', "CRD{$todayDate}-%")
                        ->orderBy('credit_note_no', 'DESC')
                        ->first();

        $count = 1;
        if ($lastNote) {
            $lastCount = (int) substr($lastNote->credit_note_no, -3);
            $count = $lastCount + 1;
        }

        $validated['credit_note_no'] = "CN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Store Credit Note record
        $creditNote = CreditNotes::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Credit Note created successfully',
            'result' => $creditNote
        ]);
    }

    public function show($id): JsonResponse
        {
            // Find the Credit Note with related items (if applicable)
            $creditNote = CreditNotes::find($id);

            // Check if Credit Note exists
            if (!$creditNote) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Credit Note not found'
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Credit Note details retrieved successfully',
                'result' => $creditNote
            ]);
        }
        public function update(Request $request, $id): JsonResponse
    {
        // Find the Credit Note record
        $creditNote = CreditNotes::find($id);

        if (!$creditNote) {
            return response()->json([
                'status' => 404,
                'message' => 'Credit Note not found'
            ], 404);
        }

        // Get all incoming request data
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approved'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update the Credit Note record
        $creditNote->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Credit Note updated successfully',
            'result' => $creditNote
        ]);
    }

    public function destroy($id): JsonResponse
    {
        // Find the Credit Note record
        $creditNote = CreditNotes::find($id);

        // Check if the Credit Note exists
        if (!$creditNote) {
            return response()->json(['message' => 'Credit Note not found'], 404);
        }
        $creditNote->delete();

        return response()->json(['message' => 'Credit Note deleted successfully']);
    }


    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
    
            $validated = $request->all(); // No validation as per your request
    
            // Fetch the Credit Note and update notes if type is 1 (example)
            $creditNote = CreditNotes::find($validated['credit_id']);
            if ($creditNote && $request->type == 1) {
                $creditNote->notes = $request->file_description;
                $creditNote->save();
            }
    
            // Handle file upload and attachment saving
            $creditNoteAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/credit_notes/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);
    
                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path); 
                    $creditNoteAttachment = CreditNotesAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status' => 200,
                'message' => ($creditNoteAttachment && $creditNote)
                    ? 'Credit Note Attachment and Notes updated successfully.'
                    : ($creditNoteAttachment
                        ? 'Credit Note Attachment created successfully.'
                        : ($creditNote
                            ? 'Credit Note Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $creditNoteAttachment,
                    'credit_note' => $creditNote
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_all_attachments($id): JsonResponse
    {
        // Retrieve all credit note attachments
        $attachments = CreditNotesAttachment::where('credit_id', $id)->get();

        // Retrieve credit note info (e.g., notes)
        $credit_info = new \stdClass();
        $notes = CreditNotes::where('id', $id)->value('notes');
        $credit_info->notes = $notes ?? '';

        // Format attachment URLs
        $attachments->map(function ($attachment) {
            if ($attachment->file_path) {
                $attachment->file = Storage::disk('spaces')->url($attachment->file_path);
                $attachment->file_name = basename($attachment->file_path);
            } else {
                $attachment->file = null;
            }
            return $attachment;
        });

        return response()->json([
            'status' => 200,
            'message' => 'Credit Note attachments retrieved successfully.',
            'result' => [
                'data' => $attachments,
                'credit_info' => $credit_info
            ],
        ]);
    }
    public function delete_attachment($id): JsonResponse
    {
        try {
            // Find the Credit Note attachment by ID
            $attachment = CreditNotesAttachment::findOrFail($id);

            // Check if the attachment has a file path
            if ($attachment->file_path) {
                $filePath = $attachment->file_path;

                // Check if the file exists in DigitalOcean Spaces and delete it
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            // Delete the Credit Note attachment record from the database
            $attachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Credit Note attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    



}

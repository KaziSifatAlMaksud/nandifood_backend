<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SalesInvoiceAttachment;
use App\Http\Resources\SalesInvoiceResource;
use App\Http\Resources\SalesInvoiceCollection;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
        {
            $id = $request->input('id');
            $limit = (int) $request->input('limit', 5);
            $page = (int) $request->input('page', 1);

            $query = SalesInvoice::query();

            if ($id) {
                $query->where('id', $id);
            }

            $salesInvoices = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status' => 200,
                'message' => 'Sales invoices list retrieved successfully',
                'result' => $salesInvoices
            ]);
        }

        public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:100',
            'customer' => 'nullable|string|max:150',
            'customer_billing_address' => 'nullable|string|max:255',
            'customer_shipping_address' => 'nullable|string|max:255',
            'customer_po' => 'nullable|string|max:100',
            'reference_no' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:100',
            'payment_due_date' => 'nullable|date',
            'invoice_currency' => 'nullable|string|max:10',
            'sales_rep' => 'nullable|string|max:100',
            'warehouse' => 'nullable|string|max:100',
            'planned_ship_out_date' => 'nullable|date',
            'last_updated' => 'nullable|date',
            'last_updated_by' => 'nullable|string|max:100',
            'invoice_status' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'memo_notes' => 'nullable|string|max:1000',
        ]);

        // Determine approval status
        $action = $request->input('action');
        $validated['is_approved'] = ($action === 'approve') ? 2 : 1;

        // Auto-generate invoice number if not provided
        if (empty($validated['invoice_no'])) {
            $todayDate = now()->format('ymd');
            $lastInvoice = SalesInvoice::where('invoice_no', 'LIKE', "INV{$todayDate}-%")
                                ->orderBy('invoice_no', 'DESC')
                                ->first();

            $count = 1;
            if ($lastInvoice) {
                $lastCount = (int) substr($lastInvoice->invoice_no, -3);
                $count = $lastCount + 1;
            }

            $validated['invoice_no'] = "INV{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        // Create the Sales Invoice
        $salesInvoice = SalesInvoice::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Sales Invoice created successfully',
            'result' => $salesInvoice
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Find the Sales Invoice record
        $salesInvoice = SalesInvoice::find($id);

        if (!$salesInvoice) {
            return response()->json([
                'status' => 404,
                'message' => 'Sales Invoice not found'
            ], 404);
        }

        // Get all incoming request data
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approved'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update the Sales Invoice record
        $salesInvoice->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Sales Invoice updated successfully',
            'result' => $salesInvoice
        ]);
    }

    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->all(); // Adjust if you want validation

            // Fetch the Sales Invoice and update memo_notes if type is 1 (example)
            $salesInvoice = SalesInvoice::find($validated['si_id']);
            if ($salesInvoice && $request->type == 1) {
                $salesInvoice->memo_notes = $request->file_description;
                $salesInvoice->save();
            }

            // Handle file upload and attachment saving
            $invoiceAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/sales_invoices/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path);
                    $invoiceAttachment = SalesInvoiceAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($invoiceAttachment && $salesInvoice)
                    ? 'Sales Invoice Attachment and Notes updated successfully.'
                    : ($invoiceAttachment
                        ? 'Sales Invoice Attachment created successfully.'
                        : ($salesInvoice
                            ? 'Sales Invoice Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $invoiceAttachment,
                    'salesInvoice' => $salesInvoice
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

    public function destroy($id): JsonResponse
    {
        try {
            // Find the Sales Invoice by ID
            $salesInvoice = SalesInvoice::findOrFail($id);

            // Delete associated attachments
            $attachments = SalesInvoiceAttachment::where('si_id', $id)->get();
            foreach ($attachments as $attachment) {
                if ($attachment->file_path && Storage::disk('spaces')->exists($attachment->file_path)) {
                    Storage::disk('spaces')->delete($attachment->file_path);
                }
                $attachment->delete();
            }

            // Delete the Sales Invoice record
            $salesInvoice->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Sales Invoice deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_all_attachments($id): JsonResponse
    {
        try {
            // Retrieve all sales invoice attachments
            $attachments = SalesInvoiceAttachment::where('si_id', $id)->get();

            // Retrieve sales invoice notes/info
            $invoice_info = new \stdClass();
            $notes = SalesInvoice::where('id', $id)->value('notes');
            $invoice_info->notes = $notes ?? '';

            // Format attachment URLs
            $attachments->map(function ($attachment) {
                if ($attachment->file_path) {
                    $attachment->file = Storage::disk('spaces')->url($attachment->file_path);
                    $attachment->file_name = basename($attachment->file_path);
                } else {
                    $attachment->file = null;
                    $attachment->file_name = null;
                }
                return $attachment;
            });

            return response()->json([
                'status' => 200,
                'message' => 'Sales invoice attachments retrieved successfully.',
                'result' => [
                    'data' => $attachments,
                    'salesInvoice' => $invoice_info
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function delete_attachment($id): JsonResponse
    {
        try {
            // Find the Sales Invoice attachment by ID
            $attachment = SalesInvoiceAttachment::findOrFail($id);

            // Check if the attachment has a file path
            if ($attachment->file_path) {
                $filePath = $attachment->file_path;

                // Check if the file exists in DigitalOcean Spaces and delete it
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            // Delete the Sales Invoice attachment record from the database
            $attachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Sales Invoice attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
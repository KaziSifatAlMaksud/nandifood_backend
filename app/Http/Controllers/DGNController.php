<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Warehouse;

use App\Models\GRNAttachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DGNController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $limit = (int) $request->input('limit', 5);
            $page = (int) $request->input('page', 1);

            $query = DGN::query(); // Change from GRN to DGN

            if ($id) {
                $query->where('id', $id);
            }

            $dgns = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status' => 200,
                'message' => 'DGN list retrieved successfully',
                'result' => $dgns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'defult_warehouse' => 'nullable|string|max:255',
                'damage_date' => 'nullable|string|max:255',
                'address1' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'regerence_no' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'damage_reported_by' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'last_update' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'office_phone' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Determine approval status
            $action = $request->input('action'); 
            $validated['is_approve'] = ($action == 'approve') ? '2' : '1';

            // Generate DGN Number
            $todayDate = now()->format('ymd');
            $lastDGN = DGN::where('regerence_no', 'LIKE', "DGN{$todayDate}-%")
                ->orderBy('regerence_no', 'DESC')
                ->first();

            $count = 1;
            if ($lastDGN) {
                $lastCount = (int) substr($lastDGN->regerence_no, -3);
                $count = $lastCount + 1;
            }

            $validated['regerence_no'] = "DGN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Store DGN record
            $dgn = DGN::create($validated);

            return response()->json([
                'status' => 201,
                'message' => 'DGN created successfully',
                'result' => $dgn
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id): JsonResponse
    {
        // Find the DGN entry
        $dgn = DGN::find($id);

        // Check if DGN exists
        if (!$dgn) {
            return response()->json([
                'status' => 404,
                'message' => 'DGN not found'
            ], 404);
        }

        // Fetch warehouse details
        $warehouse = Warehouse::find($dgn->defult_warehouse);

        // Add warehouse details to the response (without modifying the DGN record)
        $dgn->warehouse_name = $warehouse ? $warehouse->name : null;
        $dgn->warehouse_country = $warehouse ? $warehouse->country : null;
        $dgn->warehouse_state = $warehouse ? $warehouse->state : null;
        $dgn->warehouse_city = $warehouse ? $warehouse->city : null;

        return response()->json([
            'status' => 200,
            'message' => 'DGN details retrieved successfully',
            'result' => $dgn
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Find the DGN record
        $dgn = DGN::find($id);

        // Check if DGN exists
        if (!$dgn) {
            return response()->json([
                'status' => 404,
                'message' => 'DGN not found'
            ], 404);
        }

        // Convert request data to an array
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approve'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update DGN record with the provided data
        $dgn->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'DGN updated successfully',
            'result' => $dgn
        ]);
    }

    public function destroy($id): JsonResponse
    {
        // Find the DGN record
        $dgn = DGN::find($id);

        // Check if DGN exists
        if (!$dgn) {
            return response()->json(['message' => 'DGN not found'], 404);
        }

        // Delete associated attachments
        DGNAttachment::where('dgn_id', $id)->delete();

        // Delete the DGN record
        $dgn->delete();

        return response()->json(['message' => 'DGN deleted successfully']);
    }

    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->all(); // No validation as per your request

            $dgnInfo = DGN::find($validated['dgn_id']);
            if ($dgnInfo && $request->type == 1) {
                $dgnInfo->notes = $request->file_description;
                $dgnInfo->save();
            }

            $dgnAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/dgn_attachments/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path); 
                    $dgnAttachment = DGNAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($dgnAttachment && $dgnInfo)
                    ? 'DGN Attachment and Notes updated successfully.'
                    : ($dgnAttachment
                        ? 'DGN Attachment created successfully.'
                        : ($dgnInfo
                            ? 'DGN Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $dgnAttachment,
                    'dgnInfo' => $dgnInfo
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

    public function get_all_attachments($dgn_id): JsonResponse
    {
        // Retrieve all DGN attachments
        $dgn_attachments = DGNAttachment::where('dgn_id', $dgn_id)->get();

        // Retrieve DGN notes
        $dgn_info = new \stdClass();
        $notes = DGN::where('id', $dgn_id)->value('notes');
        $dgn_info->notes = $notes ?? '';

        // Format attachment URLs
        $dgn_attachments->map(function ($attachment) {
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
            'message' => 'DGN Attachments retrieved successfully.',
            'result' => [
                'data' => $dgn_attachments,
                'dgn_info' => $dgn_info
            ],
        ]);
    }

    public function delete_attachment($id): JsonResponse
    {
        try {
            // Find the DGN attachment by ID
            $dgnAttachment = DGNAttachment::findOrFail($id);

            // Check if the attachment has a file path
            if ($dgnAttachment->file_path) {
                $filePath = $dgnAttachment->file_path;
                
                // Check if the file exists in DigitalOcean Spaces and delete it
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            // Delete the DGN attachment record from the database
            $dgnAttachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'DGN Attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}

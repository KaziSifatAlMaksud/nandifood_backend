<?php

namespace App\Http\Controllers;

use App\Models\RGN;
use App\Models\RGNAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PRD;
use App\Models\PRDAttachment;

class RGNController extends Controller
{
     public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);

        $query = RGN::query();

        if ($id) {
            $query->where('id', $id);
        }

        $rgns = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => 'RGN list retrieved successfully',
            'result' => $rgns
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        // dd($request->all());
        // Validation
        $validated = $request->validate([
            'rgn_date' => 'nullable|string|max:200',
            'warehouse_id' => 'nullable|string|max:10',
            'supplier' => 'nullable|string|max:50',
            'bol_no' => 'nullable|string|max:100',
            'shipping_company' => 'nullable|string|max:150',
            'returned_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:100',
            'total_amount' => 'nullable|numeric|between:0,99999999.99',
            'last_updated' => 'nullable|string|max:150',
            'last_updated_by' => 'nullable|string|max:100',
        ]);

        // Determine approval status
        $action = $request->input('action');
        $validated['is_approve'] = ($action == 'approve') ? 2 : 1;

        // Generate RGN Number
        $todayDate = now()->format('ymd');
        $lastRGN = RGN::where('rgn_no', 'LIKE', "RGN{$todayDate}-%")
                    ->orderBy('rgn_no', 'DESC')
                    ->first();

        $count = 1;
        if ($lastRGN) {
            $lastCount = (int) substr($lastRGN->rgn_no, -3);
            $count = $lastCount + 1;
        }

        $validated['rgn_no'] = "RGN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Store RGN record
        $rgn = RGN::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'RGN created successfully',
            'result' => $rgn
        ]);
    }

    public function show($id): JsonResponse
    {
        // Find the GTN entry
        $rgn = RGN::find($id);

        // Check if GTN exists
        if (!$rgn) {
            return response()->json([
                'status' => 404,
                'message' => 'GTN not found'
            ], 404);
        }

        // Fetch warehouse details
        $warehouseInfo = Warehouse::find($rgn->warehouse_id);
        $supplierInfo = Supplier::find($rgn->supplier);

        // Add warehouse details to the response (without modifying the GTN record)
        $rgn->warehouse_name = $warehouseInfo ? $warehouseInfo->name : null;
        $rgn->warehouse_country = $warehouseInfo ? $warehouseInfo->country : null;
        $rgn->warehouse_state = $warehouseInfo ? $warehouseInfo->state : null;
        $rgn->warehouse_city = $warehouseInfo ? $warehouseInfo->city : null;

        // Add supplier details to the response (without modifying the GTN record)
        $rgn->supplier_legal_name = $supplierInfo ? $supplierInfo->supplier_legal_name : null;
        $rgn->supplier_country = $supplierInfo ? $supplierInfo->country : null;
        $rgn->supplier_state = $supplierInfo ? $supplierInfo->state : null;
        $rgn->supplier_city = $supplierInfo ? $supplierInfo->city : null;
        $rgn->supplier_phone = $supplierInfo ? $supplierInfo->phone : null;
        $rgn->supplier_email = $supplierInfo ? $supplierInfo->email : null;


        // Fetch attachments

        return response()->json([
            'status' => 200,
            'message' => 'RGN details retrieved successfully',
            'result' => $rgn
        ]);
    }


   public function update(Request $request, $id): JsonResponse
    {
        // Find the GTN record
        $rgn = RGN::find($id);

        // Check if GTN exists
        if (!$rgn) {
            return response()->json([
                'status' => 404,
                'message' => 'RGN not found'
            ], 404);
        }

        // Convert request data to an array
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approve'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update GTN record with the provided data
        $rgn->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'RGN updated successfully',
            'result' => $rgn
        ]);
    }

    public function destroy($id): JsonResponse
        {
            // Find the GTN record
            $rgn = RGN::find($id);

            // Check if GTN exists
            if (!$rgn) {
                return response()->json(['message' => 'GTN not found'], 404);
            }

            // Delete associated attachments
            RGNAttachment::where('rgn_id', $id)->delete();

            // Delete the GTN record
            $rgn->delete();

            return response()->json(['message' => 'RGN deleted successfully']);
        }


    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->all(); // No validation as per your request

            $rgnInfo = RGN::find($validated['rgn_id']);
            if ($rgnInfo && $request->type == 1) {
                $rgnInfo->notes = $request->file_description;
                $rgnInfo->save();
            }

            $rgnAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/rgn_attachments/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path); 
                    $rgnAttachment = RGNAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($rgnAttachment && $rgnInfo)
                    ? 'RGN Attachment and Notes updated successfully.'
                    : ($rgnAttachment
                        ? 'RGN Attachment created successfully.'
                        : ($rgnInfo
                            ? 'RGN Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $rgnAttachment,
                    'rgnInfo' => $rgnInfo
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


     public function get_all_attachments($rgn_id): JsonResponse
    {
        // Retrieve all GTN attachments
        $rgn_attachments = RGNAttachment::where('rgn_id', $rgn_id)->get();

        // Retrieve GTN notes
        $rgn_info = new \stdClass();
        $notes = RGN::where('id', $rgn_id)->value('notes');
        $rgn_info->notes = $notes ?? '';

        // Format attachment URLs
        $rgn_attachments->map(function ($attachment) {
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
            'message' => 'RGN Attachments retrieved successfully.',
            'result' => [
                'data' => $rgn_attachments,
                'rgn_info' => $rgn_info
            ],
        ]);
    }

    public function delete_attachment($id): JsonResponse
    {
        try {
            // Find the RGN attachment by ID
            $rgnAttachment = RGNAttachment::findOrFail($id);

            // Check if the attachment has a file path
            if ($rgnAttachment->file_path) {
                $filePath = $rgnAttachment->file_path;
                
                // Check if the file exists in DigitalOcean Spaces and delete it
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            // Delete the RGN attachment record from the database
            $rgnAttachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'RGN attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}

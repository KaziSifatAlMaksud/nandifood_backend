<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Warehouse;

use App\Models\GRNAttachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GRNController extends Controller
{
    /**
     * Get all GRNs
     */
    public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);

        $query = GRN::query();
        
        if ($id) {
            $query->where('id', $id);
        }
        

        $grns = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => 'GRN list retrieved successfully',
            'result' => $grns
        ]);
    }

    public function getWarehouse(){
        // System will auto populate the default warehouse of the user.
        $warehouses = Warehouse::select('id', 'warehouse_name as name','city','state','country')->get();

        return response()->json([
            'status' => '200',
            'message' => 'Ok',
            'result' => [
                'data' => $warehouses
            ]
        ]);
    }


    /**
     * Store a new GRN
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiving_warehouse_id' => 'nullable|string|max:255',
            'date_received' => 'nullable|string|max:255',
            'our_po' => 'nullable|string|max:255',
            'shipping_carrier' => 'nullable|string|max:255',
            'supplier_shipping_address' => 'nullable|string',
            'bol_date' => 'nullable|string|max:255', 
            'delivery_driver' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'last_updated_by' => 'nullable|string|max:100',
            'status' => 'nullable|string',
            'grn_notes' => 'nullable|string',
            'received_details' => 'nullable|string', 
            'bol_number' => 'nullable|string|max:100',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'other_reference' => 'nullable|string|max:100',
        ]);

        // Determine approval status
        $action = $request->input('action'); 
        $validated['is_approve'] = ($action == 'approve') ? 2 : 1;
        $todayDate = now()->format('ymd'); 
        $lastGRN = GRN::where('grn_no', 'LIKE', "GRN{$todayDate}-%")
                    ->orderBy('grn_no', 'DESC')
                    ->first();

        $count = 1;
        if ($lastGRN) {
            $lastCount = (int) substr($lastGRN->grn_no, -3); 
            $count = $lastCount + 1;
        }

        $validated['grn_no'] = "GRN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        $grn = GRN::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'GRN created successfully',
            'result' => $grn
        ]);
    }

    /**
     * Get a single GRN by ID
     */
    public function show($id): JsonResponse
    {
        // Find the GRN entry
        $grn = GRN::find($id);

        // Check if GRN exists
        if (!$grn) {
            return response()->json([
                'status' => 404,
                'message' => 'GRN not found'
            ], 404);
        }

        // Fetch warehouse details
        $warehouse = Warehouse::find($grn->receiving_warehouse_id);

        // Add warehouse details to the response (without modifying the GRN record)
        $grn->warehouse_name = $warehouse ? $warehouse->name : null;
        $grn->warehouse_country = $warehouse ? $warehouse->country : null;
        $grn->warehouse_state = $warehouse ? $warehouse->state : null;
        $grn->warehouse_city = $warehouse ? $warehouse->city : null;

        return response()->json([
            'status' => 200,
            'message' => 'GRN details retrieved successfully',
            'result' => $grn
        ]);
    }


    /**
     * Update a GRN
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Find the GRN record
        $grn = GRN::find($id);

        // Check if GRN exists
        if (!$grn) {
            return response()->json([
                'status' => 404,
                'message' => 'GRN not found'
            ], 404);
        }

        // Convert request data to an array
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approved'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update GRN record with the provided data
        $grn->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'GRN updated successfully',
            'result' => $grn
        ]);
    }



    /**
     * Delete a GRN
     */
    public function destroy($id): JsonResponse
    {
        $grn = GRN::findOrFail($id);
        $grn_linked = GRNAttachment::where('grn_id', $id)->delete();
        if (!$grn) {
            return response()->json(['message' => 'GRN not found'], 404);
        }
        $grn->delete();
        return response()->json(['message' => 'GRN deleted successfully']);
    }



  public function get_all_attachments($grn_id): JsonResponse
    {
        $grn_attachments = GRNAttachment::where([
            ['grn_id', $grn_id]
        ])->get();

        $grn_info = new \stdClass();
        $notes = GRN::where('id', $grn_id)->value('grn_notes');

        $grn_info->notes = $notes ?? '';

        $grn_attachments->map(function ($attachment) {
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
            'message' => 'GRN Attachments retrieved successfully.',
            'result' => [
                'data' => $grn_attachments,
                'grn_info' => $grn_info
            ],
        ]);
    }

    /**
     * Store a new GRN attachment.
     */
    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->all(); // No validation as per your request

            $grnInfo = GRN::find($validated['grn_id']);
            if ($grnInfo && $request->type == 1) {
                $grnInfo->grn_notes = $request->file_description;
                $grnInfo->save();
            }

            $grnAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/grn_attachments/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path); 
                    $grnAttachment = GRNAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($grnAttachment && $grnInfo)
                    ? 'GRN Attachment and Notes updated successfully.'
                    : ($grnAttachment
                        ? 'GRN Attachment created successfully.'
                        : ($grnInfo
                            ? 'GRN Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $grnAttachment,
                    'grnInfo' => $grnInfo
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

    /**
     * Delete a GRN attachment.
     */
    public function delete_attachment($id): JsonResponse
    {
        try {
            $grnAttachment = GRNAttachment::findOrFail($id);

            if ($grnAttachment->file_path) {
                $filePath = $grnAttachment->file_path;
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            $grnAttachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'GRN Attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    
}

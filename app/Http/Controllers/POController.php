<?php

namespace App\Http\Controllers;

use App\Models\DGN;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PO;
use App\Models\POAttachment;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\POItemDetail;
use App\Models\POItemDetailAttachment;

class POController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $limit = (int) $request->input('limit', 5);
            $page = (int) $request->input('page', 1);

            $query = PO::query();

            if ($id) {
                $query->where('id', $id);
            }

            $pos = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status' => 200,
                'message' => 'PO list retrieved successfully',
                'result' => $pos
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
            // Validate the incoming request
            $validated = $request->validate([
                'po_date' => 'nullable|string|max:255',
                'po_due_date' => 'nullable|string|max:255',
                'supplier' => 'nullable|string|max:255',
                'supp_country' => 'nullable|string|max:255',
                'supp_state' => 'nullable|string|max:255',
                'supp_city' => 'nullable|string|max:255',
                'warehouse' => 'nullable|string|max:255',
                'war_country' => 'nullable|string|max:255',
                'war_state' => 'nullable|string|max:255',
                'war_city' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:255',
                'amount' => 'nullable|string|max:255',
                'po_status' => 'nullable|string|max:255',
                'receiving_status' => 'nullable|string|max:255',
                'created_at' => 'nullable|string|max:255',
                'created_by' => 'nullable|string|max:255',
                'updated_by' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:255',
            ]);

            // Determine approval status based on action
            $action = $request->input('action'); 
            $validated['is_approve'] = ($action == 'approve') ? '2' : '1';

            // Generate PO Number
            $todayDate = now()->format('ymd');
            $lastPO = PO::where('po_no', 'LIKE', "PO{$todayDate}-%")
                ->orderBy('po_no', 'DESC')
                ->first();

            $count = 1;
            if ($lastPO) {
                $lastCount = (int) substr($lastPO->po_no, -3);
                $count = $lastCount + 1;
            }

            $validated['po_no'] = "PO{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Store PO record
            $po = PO::create($validated);

            return response()->json([
                'status' => 201,
                'message' => 'PO created successfully',
                'result' => $po
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
        // Find the PO record with its related item details
        $po = PO::with('poItemDetails')->find($id);

        // Check if PO exists
        if (!$po) {
            return response()->json([
                'status' => 404,
                'message' => 'PO not found'
            ], 404);
        }

        // Fetch related warehouse
        $warehouse = Warehouse::find($po->warehouse);

        // Add warehouse fields to the response object
        $po->warehouse_name    = $warehouse->name ?? null;

        $supplier = Supplier::find($po->supplier);

        $po->supplier_legal_name = $supplier->supplier_legal_name ?? null;

        return response()->json([
            'status' => 200,
            'message' => 'PO details retrieved successfully',
            'result' => $po
        ]);
    }


    public function update(Request $request, $id)
    {
        // Find the PO record
        $po = PO::find($id);

        if (!$po) {
            return response()->json([
                'status' => 404,
                'message' => 'PO not found'
            ], 404);
        }

        // Get all incoming request data
        $data = $request->all();
        if ($request->has('action')) {
            $data['is_approve'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update PO with new data
        $po->update($data);

        // Delete existing PO item details
        POItemDetail::where('po_id', $po->id)->delete();

        // Get item details from the request
        $poItems = $request->input('po_item_details');
        if (is_array($poItems)) {
            foreach ($poItems as $item) {
                POItemDetail::create([
                    'po_id'          => $po->id,
                    'supplier_sku'   => $item['supplier_sku'] ?? null,
                    'sku'            => $item['sku'] ?? null,
                    'product_name'   => $item['product_name'] ?? null,
                    'size'           => $item['size'] ?? null,
                    'uom'            => $item['uom'] ?? null,
                    'qty'            => $item['qty'] ?? null,
                    'unit_price'     => $item['unit_price'] ?? null,
                    'total_amount'   => $item['total_amount'] ?? null,
                    'comment'        => $item['comment'] ?? null,
                    'created_by'     => $item['created_by'] ?? null,
                    'created_at'     => $item['created_at'] ?? now(),
                ]);
            }
        }

        // Load fresh PO item details
        $po->load('poItemDetails');

        return response()->json([
            'status' => 200,
            'message' => 'PO updated successfully',
            'result' => $po
        ]);
    }


    public function destroy($id): JsonResponse
    {
        // Find the PO record
        $po = PO::find($id);

        // Check if PO exists
        if (!$po) {
            return response()->json(['message' => 'PO not found'], 404);
        }

        // Delete associated PO item details
        POItemDetail::where('po_id', $id)->delete();

        // Delete the PO record
        $po->delete();

        return response()->json(['message' => 'PO deleted successfully']);
    }


    public function store_attachment(Request $request): JsonResponse
{
    try {
        DB::beginTransaction();

        $validated = $request->all(); // No validation as per your request

        // Fetch the PO info and update notes if type is 1 (example)
        $poInfo = PO::find($validated['po_id']);
        if ($poInfo && $request->type == 1) {
            $poInfo->notes = $request->file_description;
            $poInfo->save();
        }

        // Handle file upload and attachment saving
        $poAttachment = null;
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $fileName = $file->getClientOriginalName();
            $path = "uploads/po_attachments/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

            if ($uploaded) {
                $validated['file_path'] = $path;
                $fileUrl = Storage::disk('spaces')->url($path); 
                $poAttachment = POAttachment::create($validated);
                $validated['file_url'] = $fileUrl;
            } else {
                throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
            }
        }

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => ($poAttachment && $poInfo)
                ? 'PO Attachment and Notes updated successfully.'
                : ($poAttachment
                    ? 'PO Attachment created successfully.'
                    : ($poInfo
                        ? 'PO Notes updated successfully.'
                        : 'No changes were made.'
                    )
                ),
            'result' => [
                'data' => $poAttachment,
                'poInfo' => $poInfo
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



    public function get_all_attachments($po_id): JsonResponse
    {
        // Retrieve all PO attachments
        $po_attachments = POAttachment::where('po_id', $po_id)->get();

        // Retrieve PO notes
        $po_info = new \stdClass();
        $notes = PO::where('id', $po_id)->value('notes');
        $po_info->notes = $notes ?? '';

        // Format attachment URLs
        $po_attachments->map(function ($attachment) {
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
            'message' => 'PO Attachments retrieved successfully.',
            'result' => [
                'data' => $po_attachments,
                'po_info' => $po_info
            ],
        ]);
    }


        public function delete_attachment($id): JsonResponse
        {
            try {
                // Find the PO attachment by ID
                $poAttachment = POAttachment::findOrFail($id);

                // Check if the attachment has a file path
                if ($poAttachment->file_path) {
                    $filePath = $poAttachment->file_path;
                    
                    // Check if the file exists in DigitalOcean Spaces and delete it
                    if (Storage::disk('spaces')->exists($filePath)) {
                        Storage::disk('spaces')->delete($filePath);
                    }
                }

                // Delete the PO attachment record from the database
                $poAttachment->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'PO Attachment deleted successfully.',
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 500,
                    'error' => $e->getMessage(),
                ], 500);
            }
        }


    

}

<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Warehouse;
use App\Models\GTNAttachment;
use App\Models\GTN;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GTNController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);

        $query = GTN::query();
        
        if ($id) {
            $query->where('id', $id);
        }

        $gtns = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => 'GTN list retrieved successfully',
            'result' => $gtns
        ]);
    }
   
    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'grn_number' => 'nullable|string|max:50',
            'transfer_out_warehouse' => 'nullable|string|max:100',
            'transfer_in_warehouse' => 'nullable|string|max:100',
            'date_tran_out' => 'nullable|string|max:150', 
            'po_id' => 'nullable|string',
            'other_reference' => 'nullable|string|max:255',
            'bol_number' => 'nullable|string|max:50',
            'bol_date' => 'nullable|string|max:150', 
            'shipping_carrier' => 'nullable|string|max:100',
            'delivery_driver' => 'nullable|string|max:100',
            'transferred_out_by' => 'nullable|string|max:100',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'last_updated_by' => 'nullable|string|max:100'
        ]);

        // Determine approval status
        $action = $request->input('action'); 
        $validated['is_approved'] = ($action == 'approve') ? 2 : 1;

        // Generate GTN Number
        $todayDate = now()->format('ymd'); 
        $lastGTN = GTN::where('grn_number', 'LIKE', "GTN{$todayDate}-%")
                    ->orderBy('grn_number', 'DESC')
                    ->first();

        $count = 1;
        if ($lastGTN) {
            $lastCount = (int) substr($lastGTN->grn_number, -3); 
            $count = $lastCount + 1;
        }

        $validated['grn_number'] = "GTN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Store GTN record
        $gtn = GTN::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'GTN created successfully',
            'result' => $gtn
        ]);
    }
    public function show($id): JsonResponse
    {
        // Find the GTN entry
        $gtn = GTN::find($id);

        // Check if GTN exists
        if (!$gtn) {
            return response()->json([
                'status' => 404,
                'message' => 'GTN not found'
            ], 404);
        }

        // Fetch warehouse details
        $outWarehouse = Warehouse::find($gtn->transfer_out_warehouse);
        $inWarehouse = Warehouse::find($gtn->transfer_in_warehouse);

        // Add warehouse details to the response (without modifying the GTN record)
        $gtn->out_warehouse_name = $outWarehouse ? $outWarehouse->name : null;
        $gtn->out_warehouse_country = $outWarehouse ? $outWarehouse->country : null;
        $gtn->out_warehouse_state = $outWarehouse ? $outWarehouse->state : null;
        $gtn->out_warehouse_city = $outWarehouse ? $outWarehouse->city : null;

        $gtn->in_warehouse_name = $inWarehouse ? $inWarehouse->name : null;
        $gtn->in_warehouse_country = $inWarehouse ? $inWarehouse->country : null;
        $gtn->in_warehouse_state = $inWarehouse ? $inWarehouse->state : null;
        $gtn->in_warehouse_city = $inWarehouse ? $inWarehouse->city : null;

        return response()->json([
            'status' => 200,
            'message' => 'GTN details retrieved successfully',
            'result' => $gtn
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Find the GTN record
        $gtn = GTN::find($id);

        // Check if GTN exists
        if (!$gtn) {
            return response()->json([
                'status' => 404,
                'message' => 'GTN not found'
            ], 404);
        }

        // Convert request data to an array
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approved'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update GTN record with the provided data
        $gtn->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'GTN updated successfully',
            'result' => $gtn
        ]);
    }

    public function destroy($id): JsonResponse
    {
        // Find the GTN record
        $gtn = GTN::find($id);

        // Check if GTN exists
        if (!$gtn) {
            return response()->json(['message' => 'GTN not found'], 404);
        }

        // Delete associated attachments
        GTNAttachment::where('gtn_id', $id)->delete();

        // Delete the GTN record
        $gtn->delete();

        return response()->json(['message' => 'GTN deleted successfully']);
    }

    public function store_attachment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->all(); // No validation as per your request

            $gtnInfo = GTN::find($validated['gtn_id']);
            if ($gtnInfo && $request->type == 1) {
                $gtnInfo->notes = $request->file_description;
                $gtnInfo->save();
            }

            $gtnAttachment = null;
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $fileName = $file->getClientOriginalName();
                $path = "uploads/gtn_attachments/{$fileName}";
                $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                if ($uploaded) {
                    $validated['file_path'] = $path;
                    $fileUrl = Storage::disk('spaces')->url($path); 
                    $gtnAttachment = GTNAttachment::create($validated);
                    $validated['file_url'] = $fileUrl;
                } else {
                    throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => ($gtnAttachment && $gtnInfo)
                    ? 'GTN Attachment and Notes updated successfully.'
                    : ($gtnAttachment
                        ? 'GTN Attachment created successfully.'
                        : ($gtnInfo
                            ? 'GTN Notes updated successfully.'
                            : 'No changes were made.'
                        )
                    ),
                'result' => [
                    'data' => $gtnAttachment,
                    'gtnInfo' => $gtnInfo
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


    public function get_all_attachments($gtn_id): JsonResponse
    {
        // Retrieve all GTN attachments
        $gtn_attachments = GTNAttachment::where('gtn_id', $gtn_id)->get();

        // Retrieve GTN notes
        $gtn_info = new \stdClass();
        $notes = GTN::where('id', $gtn_id)->value('notes');
        $gtn_info->notes = $notes ?? '';

        // Format attachment URLs
        $gtn_attachments->map(function ($attachment) {
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
            'message' => 'GTN Attachments retrieved successfully.',
            'result' => [
                'data' => $gtn_attachments,
                'gtn_info' => $gtn_info
            ],
        ]);
    }

    
    public function delete_attachment($id): JsonResponse
    {
        try {
            // Find the GTN attachment by ID
            $gtnAttachment = GTNAttachment::findOrFail($id);

            // Check if the attachment has a file path
            if ($gtnAttachment->file_path) {
                $filePath = $gtnAttachment->file_path;
                
                // Check if the file exists in DigitalOcean Spaces and delete it
                if (Storage::disk('spaces')->exists($filePath)) {
                    Storage::disk('spaces')->delete($filePath);
                }
            }

            // Delete the GTN attachment record from the database
            $gtnAttachment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'GTN Attachment deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }




}

<?php

namespace App\Http\Controllers;

use App\Models\PRD;
use App\Models\PRDAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PRDController extends Controller
{
    /**
     * Get all PRDs
     */
    public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 5);
        $page = (int) $request->input('page', 1);

        $query = PRD::query();

        if ($id) {
            $query->where('id', $id);
        }

        $prds = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

          // Get unique warehouse IDs from GRNs
        // $warehouseIds = $grns->pluck('receiving_warehouse_id')->unique()->toArray();
        
        // $warehouses = Warehouse::whereIn('id', $warehouseIds)->get()->keyBy('id');

        // foreach ($grns as $grn) {
        //     $warehouse = $warehouses[$grn->receiving_warehouse_id] ?? null;

        //     if ($warehouse) {
        //         $grn->warehouse_name = $warehouse->name;
        //         $grn->country = $warehouse->country;
        //         $grn->state = $warehouse->state;
        //         $grn->city = $warehouse->city;
        //     }
        // }

        return response()->json([
            'status' => 200,
            'message' => 'PRD list retrieved successfully',
            'result' => $prds
        ]);
    }

    /**
     * Store a new PRD
     */
   public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'warehouse' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:150', 
            'prd_date' => 'nullable|string|max: 150',
            'prd_no' => 'nullable|string|max:255',
            'pro_start_date' => 'nullable|string|max:200',
            'pro_end_date' => 'nullable|string|max:200', 
            'pro_supervisor' => 'nullable|string|max:100',
            'last_updated' => 'nullable|string|max:100',
            'last_updated_by' => 'nullable|string|max:100',
            'status' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Determine approval status
        $action = $request->input('action'); 
        $validated['is_approve'] = ($action == 'approve') ? 2 : 1;

        // Generate GTN Number
        $todayDate = now()->format('ymd'); 
        $lastGTN = PRD::where('prd_no', 'LIKE', "PRD{$todayDate}-%")
                    ->orderBy('prd_no', 'DESC')
                    ->first();

        $count = 1;
        if ($lastGTN) {
            $lastCount = (int) substr($lastGTN->prd_no, -3); 
            $count = $lastCount + 1;
        }

        $validated['prd_no'] = "PRD{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Store GTN record
        $prd = PRD::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'PRD created successfully',
            'result' => $prd
        ]);
    }

    /**
     * Get a single PRD by ID
     */
    public function show($id): JsonResponse
    {
        $prd = PRD::find($id);

        if (!$prd) {
            return response()->json([
                'status' => 404,
                'message' => 'PRD not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'PRD details retrieved successfully',
            'result' => $prd
        ]);
    }

    /**
     * Update a PRD
     */

     public function update(Request $request, $id): JsonResponse
    {
        // Find the GRN record
        $prd = PRD::find($id);

        // Check if GRN exists
        if (!$prd) {
            return response()->json([
                'status' => 404,
                'message' => 'GRN not found'
            ], 404);
        }
        $data = $request->all();

        // Determine approval status
        if ($request->has('action')) {
            $data['is_approve'] = ($request->input('action') === 'approve') ? 2 : 1;
        }

        // Update GRN record with the provided data
        $prd->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'PRD updated successfully',
            'result' => $prd
        ]);
    }

    
    /**
     * Delete a PRD
     */
    public function destroy($id): JsonResponse
    {
        $prd = PRD::find($id);

        if (!$prd) {
            return response()->json(['message' => 'PRD not found'], 404);
        }

        $prd->delete();

        return response()->json(['message' => 'PRD deleted successfully']);
    }

    /**
     * Upload attachment (Optional - if PRD has attachments)
     */
   
     public function store_attachment(Request $request): JsonResponse
        {
            try {
                DB::beginTransaction();

                $validated = $request->all(); // No validation as per your structure

                $prdInfo = PRD::find($validated['prd_id']);
                if ($prdInfo && $request->type == 1) {
                    $prdInfo->notes = $request->file_description;
                    $prdInfo->save();
                }

                $prdAttachment = null;
                if ($request->hasFile('file_path')) {
                    $file = $request->file('file_path');
                    $fileName = $file->getClientOriginalName();
                    $path = "uploads/prd_attachments/{$fileName}";
                    $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), ['visibility' => 'public']);

                    if ($uploaded) {
                        $validated['file_path'] = $path;
                        $fileUrl = Storage::disk('spaces')->url($path); 
                        $prdAttachment = PRDAttachment::create($validated);
                        $validated['file_url'] = $fileUrl;
                    } else {
                        throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => ($prdAttachment && $prdInfo)
                        ? 'PRD Attachment and Notes updated successfully.'
                        : ($prdAttachment
                            ? 'PRD Attachment created successfully.'
                            : ($prdInfo
                                ? 'PRD Notes updated successfully.'
                                : 'No changes were made.'
                            )
                        ),
                    'result' => [
                        'data' => $prdAttachment,
                        'prdInfo' => $prdInfo
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

        public function get_all_attachments($prd_id): JsonResponse
        {
            $prd_attachments = PRDAttachment::where('prd_id', $prd_id)->get();

            $prd_info = new \stdClass();
            $notes = PRD::where('id', $prd_id)->value('notes');
            $prd_info->notes = $notes ?? '';

            $prd_attachments->map(function ($attachment) {
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
                'message' => 'PRD Attachments retrieved successfully.',
                'result' => [
                    'data' => $prd_attachments,
                    'prd_info' => $prd_info
                ],
            ]);
        }

        public function delete_attachment($id): JsonResponse
        {
            try {
                $prdAttachment = PRDAttachment::findOrFail($id);

                if ($prdAttachment->file_path) {
                    $filePath = $prdAttachment->file_path;

                    if (Storage::disk('spaces')->exists($filePath)) {
                        Storage::disk('spaces')->delete($filePath);
                    }
                }

                $prdAttachment->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'PRD Attachment deleted successfully.',
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 500,
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
}

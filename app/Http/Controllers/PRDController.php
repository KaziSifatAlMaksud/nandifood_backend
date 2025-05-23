<?php

namespace App\Http\Controllers;

use App\Models\PRD;
use App\Models\PRDAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductionOrder;
use App\Models\PrdCrew;
use App\Models\Position;
use App\Models\PrdInputDetail;
use App\Models\PrdOutputDetail;


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
        $prd = PRD::with('prd_input_details', 'prd_output_details')->find($id);

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

     public function update(Request $request, $id)
     {
         // Find the PRD record
         $prd = PRD::find($id);
         // Check if PRD exists
         if (!$prd) {
             return response()->json([
                 'status' => 404,
                 'message' => 'PRD not found'
             ], 404);
         }

         $data = $request->all();
        // dd($data);
     
         if ($request->has('action')) {
             $data['is_approve'] = ($request->input('action') == 'approve') ? 2 : 1;
         }
     
         $prd->update($data);

         PrdInputDetail::where('prd_id', $prd->id)->delete();
         PrdOutputDetail::where('prd_id', $prd->id)->delete();

         $prd_inputDetails = $request->input('prd_input_details');
         if (is_array($prd_inputDetails)) {
             foreach ($prd_inputDetails as $prd_inputDetail) {
                PrdInputDetail::create([
                     'prd_id'           => $prd->id,
                     'production_order_no' => $prd_inputDetail['production_order_no'] ?? null,
                     'production_date'  => $prd_inputDetail['production_date'] ?? null,
                     'product_category' => $prd_inputDetail['product_category'] ?? null,
                     'sub_category1'    => $prd_inputDetail['sub_category1'] ?? null,
                     'sub_category2'    => $prd_inputDetail['sub_category2'] ?? null,
                     'input_item'       => $prd_inputDetail['input_item'] ?? null,
                     'sku'              => $prd_inputDetail['sku'] ?? null,
                     'size'             => $prd_inputDetail['size'] ?? null,
                     'uom'              => $prd_inputDetail['uom'] ?? null,
                     'qty'              => $prd_inputDetail['qty'] ?? null,
                     'currency'         => $prd_inputDetail['currency'] ?? null,
                     'unit_cost'        => $prd_inputDetail['unit_cost'] ?? null,
                     'amount'           => $prd_inputDetail['amount'] ?? null,
                     'created_at'       => $prd_inputDetail['created_at'] ?? now(),
                 ]);
             }
         }
         $prd_outputDetails = $request->input('prd_output_details');
        if (is_array($prd_outputDetails)) {
            foreach ($prd_outputDetails as $prd_outputDetail) {
                PrdOutputDetail::create([
                    'prd_id'              => $prd->id,
                    'production_order_no' => $prd_outputDetail['production_order_no'] ?? null,
                    'production_date'     => $prd_outputDetail['production_date'] ?? null,
                    'product_category'    => $prd_outputDetail['product_category'] ?? null,
                    'sub_category1'       => $prd_outputDetail['sub_category1'] ?? null,
                    'sub_category2'       => $prd_outputDetail['sub_category2'] ?? null,
                    'output_item'         => $prd_outputDetail['output_item'] ?? null,
                    'sku'                 => $prd_outputDetail['sku'] ?? null,
                    'size'                => $prd_outputDetail['size'] ?? null,
                    'uom'                 => $prd_outputDetail['uom'] ?? null,
                    'qty'                 => $prd_outputDetail['qty'] ?? null,
                    'currency'            => $prd_outputDetail['currency'] ?? null,
                    'unit_cost'           => $prd_outputDetail['unit_cost'] ?? null,
                    'amount'              => $prd_outputDetail['amount'] ?? null,
                    'created_at'          => $prd_outputDetail['created_at'] ?? now(),
                ]);
            }
        }

        // Load fresh relationships
        $prd->load('prd_input_details', 'prd_output_details');
     
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


        PrdInputDetail::where('prd_id', $prd->id)->delete();
        PrdOutputDetail::where('prd_id', $prd->id)->delete();

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

     
        public function crew_index($prd_id)
        {
            $crews = PrdCrew::where('prd_id', $prd_id)->get();

            $crews->map(function ($crew) {
                // Get employee full name
                $employee = DB::table('employee')
                    ->where('id', $crew->emp_id)
                    ->select(DB::raw('CONCAT(first_name, " ", last_name) AS full_name'), 'position_id')
                    ->first();

                if ($employee) {
                    $crew->emp_name = $employee->full_name;

                    // Get position name
                    $position_name = DB::table('positions')
                        ->where('id', $employee->position_id)
                        ->value('position_name');

                    $crew->position_name = $position_name;
                } else {
                    $crew->emp_name = null;
                    $crew->position_name = null;
                }

                return $crew;
            });

            return response()->json([
                'status' => 200,
                'message' => 'PRD Crew retrieved successfully.',
                'result' => $crews
            ]);
        }


           // Create a new crew
        public function crew_store(Request $request)
        {
            $validated = $request->validate([
                'prd_id' => 'required|integer',
                'emp_id' => 'required|string|max:10'
            ]);

            $crew = PrdCrew::create($validated);

            return response()->json([
                'message' => 'Crew created successfully',
                'data' => $crew
            ], 201);
        }


        public function crew_destroy($id)
        {
            $crew = PrdCrew::find($id);
    
            if (!$crew) {
                return response()->json(['message' => 'Crew not found'], 404);
            }
    
            $crew->delete();
    
            return response()->json(['message' => 'Crew deleted successfully']);
        }


}

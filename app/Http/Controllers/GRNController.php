<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Warehouse;

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
            'receiving_warehouse_id' => 'nullable|integer',
            'date_received' => 'nullable|date',
            'our_po' => 'nullable|string|max:255',
            'shipping_carrier' => 'nullable|string|max:255',
            'supplier_shipping_address' => 'nullable|string',
            'bol_date' => 'nullable|date',
            'delivery_driver' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'last_updated_by' => 'nullable|integer',
            'status' => 'nullable|in:pending,received,cancelled',
            'grn_notes' => 'nullable|string',
            'is_approve' => 'nullable|boolean',
            'received_details' => 'nullable|json',
        ]);

        // Generate GRN No.
        $todayDate = now()->format('ymd'); // YYMMDD format
        $lastGRN = GRN::where('grn_no', 'LIKE', "GRN{$todayDate}-%")
                    ->orderBy('grn_no', 'DESC')
                    ->first();

        $count = 1;
        if ($lastGRN) {
            $lastCount = (int) substr($lastGRN->grn_no, -3); // Extract last count
            $count = $lastCount + 1; // Increment count
        }

        $grn_no = "GRN{$todayDate}-" . str_pad($count, 3, '0', STR_PAD_LEFT); // Ensure 3-digit count

        // Add GRN No. to validated data
        $validated['grn_no'] = $grn_no;

        

        // Create the GRN entry
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
        $grn = GRN::find($id);

        if (!$grn) {
            return response()->json(['message' => 'GRN not found'], 404);
        }

        return response()->json($grn);
    }

    /**
     * Update a GRN
     */
    public function update(Request $request, $id): JsonResponse
    {
        $grn = GRN::find($id);

        if (!$grn) {
            return response()->json(['message' => 'GRN not found'], 404);
        }

        $validated = $request->validate([
            'receiving_warehouse_id' => 'nullable|integer',
            'date_received' => 'nullable|date',
            'grn_no' => 'nullable|string|max:255',
            'our_po' => 'nullable|string|max:255',
            'shipping_carrier' => 'nullable|string|max:255',
            'supplier_shipping_address' => 'nullable|string',
            'bol_date' => 'nullable|date',
            'delivery_driver' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'last_updated_by' => 'nullable|integer',
            'status' => 'nullable|in:pending,received,cancelled',
            'grn_notes' => 'nullable|string',
            'is_approve' => 'nullable|boolean',
            'received_details' => 'nullable|json',
        ]);

        $grn->update($validated);

        return response()->json($grn);
    }

    /**
     * Delete a GRN
     */
    public function destroy($id): JsonResponse
    {
        $grn = GRN::find($id);

        if (!$grn) {
            return response()->json(['message' => 'GRN not found'], 404);
        }

        $grn->delete();

        return response()->json(['message' => 'GRN deleted successfully']);
    }

    /**
     * Get only approved GRNs
     */
    public function getApproved(): JsonResponse
    {
        $approvedGrns = GRN::where('is_approve', true)->get();
        return response()->json($approvedGrns);
    }
}

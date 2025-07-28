<?php

namespace App\Http\Controllers;

use App\Models\CreateNewPickingTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CreateNewPickingTicketController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $limit = (int) $request->input('limit', 10);
        $page = (int) $request->input('page', 1);

        $query = CreateNewPickingTicket::query();

        if ($id) {
            $query->where('id', $id);
        }

        $tickets = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 200,
            'message' => 'Picking ticket list retrieved successfully',
            'result' => $tickets
        ]);
    }

  public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sales_order' => 'required|string|max:50',
            'warehouse' => 'required|string|max:100',
            'picking_ticket_no' => 'required|string|max:50',
            'picking_ticket_date' => 'required|string|max:150',
            'sales_rep' => 'nullable|string|max:100',
            'customer' => 'nullable|string|max:100',
            'ship_to_address' => 'nullable|string|max:255',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'order_priority' => 'nullable|string|max:50',
            'planned_shipping_out_date' => 'nullable|string|max:150',
            'scheduled_picking_start_date' => 'nullable|string|max:150',
            'scheduled_picking_end_date' => 'nullable|string|max:150',
            'actual_picking_start_date' => 'nullable|string|max:150',
            'actual_picking_end_date' => 'nullable|string|max:150',
            'assigned' => 'nullable|string|max:100',
            'partial_shipment' => 'nullable|string|max:10',
            'last_updated' => 'nullable|string|max:150',
            'last_updated_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'performance' => 'nullable|string|max:50',
            'sales_order_status' => 'nullable|string|max:50',
            'picking_method' => 'nullable|string|max:50',
            'picking_operation' => 'nullable|string|max:50',
        ]);

        $ticket = CreateNewPickingTicket::create($data);

        return response()->json([
            'status' => 201,
            'message' => 'Picking ticket created successfully',
            'result' => $ticket
        ]);
    }


    public function show($id): JsonResponse
    {
        $ticket = CreateNewPickingTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 404,
                'message' => 'Picking ticket not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Picking ticket details retrieved successfully',
            'result' => $ticket
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $ticket = CreateNewPickingTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 404,
                'message' => 'Picking ticket not found.',
                'result' => ['data' => []],
            ]);
        }

        // Fill model with request data
        $ticket->fill($request->all());

        // You can add additional logic here if needed (e.g., conditional flags or file uploads)

        $ticket->save();

        return response()->json([
            'status' => 200,
            'message' => 'Picking ticket updated successfully.',
            'result' => ['data' => $ticket],
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $ticket = CreateNewPickingTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => 404,
                'message' => 'Picking ticket not found'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Picking ticket deleted successfully'
        ]);
    }
}

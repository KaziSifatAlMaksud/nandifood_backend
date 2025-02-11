<?php

namespace App\Http\Controllers;
use App\Models\ShippingInfo;
use Illuminate\Http\Request;
use App\Models\Positions;

class ShippingInfoController extends Controller
{
    public function index($shipping_type, $cus_or_sup_id)
    {
        $shippingInfo = ShippingInfo::where('shipping_type', $shipping_type)
        ->where('cus_or_sup_id', $cus_or_sup_id)
        ->leftJoin('positions', 'shipping_info.position', '=', 'positions.id') // Join the Positions table
        ->select('shipping_info.*', 'positions.position_name') // Select all shipping info fields and position_name
        ->get();

        // Check if records were found
        if ($shippingInfo->isEmpty()) {
            return response()->json([
                'message' => 'No shipping info found for the given type and ID.'
            ], 404);
        }

        return response()->json([
            'message' => 'Shipping info retrieved successfully!',
            'data' => $shippingInfo
        ], 200);
    }

    
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'select_shipping_location' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'position' => 'nullable|string|max:255',
            'status' => 'required|string|max:20',
            'shipping_type' => 'required|string|max:20',
            'cus_or_sup_id' => 'required|string|max:20'
        ]);

        // Create a new shipping info record
        $shippingInfo = ShippingInfo::create($validatedData);

        // Return a JSON response
        return response()->json([
            'message' => 'Shipping info created successfully!',
            'result' => $shippingInfo
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $shippingInfo = ShippingInfo::find($id);

        if (!$shippingInfo) {
            return response()->json([
                'message' => 'Shipping info not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'select_shipping_location' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'position' => 'nullable|string|max:255',
            'status' => 'required|string|max:20',
            'shipping_type' => 'required|string|max:20',
            'cus_or_sup_id' => 'required|string|max:20'
        ]);

        $shippingInfo->update($validatedData);

        $shippingInfo->position_name = Positions::where('id', $shippingInfo->position)->value('position_name');

        return response()->json([
            'message' => 'Shipping info updated successfully!',
            'result' => $shippingInfo
        ], 200);
    }

    public function destroy($id)
    {
        $shippingInfo = ShippingInfo::find($id);

        if (!$shippingInfo) {
            return response()->json([
                'message' => 'Shipping info not found'
            ], 404);
        }

        $shippingInfo->delete();

        return response()->json([
            'message' => 'Shipping info deleted successfully!'
        ], 200);
    }



    public function show($id)
    {
        $shippingInfo = ShippingInfo::find($id);
        if (!$shippingInfo) {
            return response()->json([
                'message' => 'Shipping info not found'
            ], 404);
        }

        // Return the data as JSON
        return response()->json([
            'message' => 'Shipping info retrieved successfully',
            'data' => $shippingInfo
        ], 200);
    }
}

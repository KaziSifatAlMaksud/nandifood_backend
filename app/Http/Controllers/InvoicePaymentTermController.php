<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoicePaymentTerm;
use App\Models\InvoiceStatus;
use App\Models\PaymentStatus;
use Illuminate\Http\JsonResponse;

class InvoicePaymentTermController extends Controller
{
    // Get all payment terms
    public function index()
    {
        $invoicepaymentTerms = InvoicePaymentTerm::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Payment terms fetched successfully.',
            'result' => [
                'data' => $invoicepaymentTerms,
            ],
        ]);
    }


    // Create a new payment term
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|string|max:8',
            'payment_terms_name' => 'required|string|max:15',
            'number_of_days' => 'required|integer',
            'days_type' => 'required|string|max:10',
        ]);

        $term = InvoicePaymentTerm::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Payment term created successfully.',
            'result' => $term
            
        ]);
    }

    public function get_invoice_status()
    {
        $invoicePaymentTerms = InvoiceStatus::all();

        return response()->json([
            'status' => 200,
            'message' => 'Payment terms fetched successfully.',
            'result' =>  $invoicePaymentTerms
        ]);
    }

        public function get_payment_status()
    {
        $invoicePaymentTerms = PaymentStatus::all();

        return response()->json([
            'status' => 200,
            'message' => 'Payment terms fetched successfully.',
            'result' =>  $invoicePaymentTerms
        ]);
    }

    

    // Get a single payment term
    public function show($id)
    {
        $term = InvoicePaymentTerm::find($id);

        if (!$term) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment term not found.',
                'result' => null,
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Payment term fetched successfully.',
            'result' => [
                'data' => $term,
            ],
        ]);
    }

    // Update a payment term
    public function update(Request $request, $id)
    {
        $term = InvoicePaymentTerm::find($id);

        if (!$term) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment term not found.',
                'result' => null,
            ]);
        }

        $validated = $request->validate([
            'payment_id' => 'sometimes|string|max:8',
            'payment_terms_name' => 'sometimes|string|max:15',
            'number_of_days' => 'sometimes|integer',
            'days_type' => 'sometimes|string|max:10',
        ]);

        $term->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Payment term updated successfully.',
            'result' => [
                'data' => $term,
            ],
        ]);
    }

    // Delete a payment term
    public function destroy($id)
    {
        $term = InvoicePaymentTerm::find($id);

        if (!$term) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment term not found.',
                'result' => null,
            ]);
        }

        $term->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Payment term deleted successfully.',
            'result' => null,
        ]);
    }
}

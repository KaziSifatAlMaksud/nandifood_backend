<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductPriceController extends Controller
{

    public function importProductPrices(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new ProductPrice, $request->file('file'));

            return response()->json([
                'message' => 'Product prices imported successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to import product prices.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Uom;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\PriceExcelFileImport;

class ProductPriceController extends Controller
{
    /**
     * Import Product Prices from Excel
     */
    public function importProductPrices(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new PriceExcelFileImport, $request->file('file'));

            return response()->json([
                'message' => 'Product prices imported successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to import product prices.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate and Upload Excel File
     */
    public function validateAndUpload(Request $request)
    {
        $requiredColumns = [
            'price_list_id',
            'price_list_name',
            'eff_date',
            'exp_date',
            'status',
            'last_update',
            'updated_by',
            'action',
            'file',
        ];

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $import = new PriceExcelFileImport();
            $collection = Excel::toCollection($import, $file);

            if ($collection->isEmpty() || $collection->first()->isEmpty()) {
                return response()->json(['error' => 'The file is empty or invalid.'], 400);
            }

            // Check if all required columns are present
            $headers = $collection->first()->first()->keys()->toArray();
            $missingColumns = array_diff($requiredColumns, $headers);

            if (!empty($missingColumns)) {
                return response()->json([
                    'error' => 'The file is missing required columns.',
                    'missing_columns' => $missingColumns,
                ], 400);
            }

            // Upload file to DigitalOcean Spaces
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/Price/{$fileName}";
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');

            if (!$uploaded) {
                throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
            }

            return response()->json([
                'success' => 'File is valid and uploaded successfully.',
                'file_url' => Storage::disk('spaces')->url($path),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during file upload.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

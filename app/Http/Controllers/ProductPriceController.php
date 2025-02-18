<?php

namespace App\Http\Controllers;

use App\Models\PriceExcelFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PriceExcelFileImport;

class ProductPriceController extends Controller
{
    /**
     * Validate and Upload Excel File
     */
    public function validateAndUpload(Request $request)
    {
        // Define required columns
        $requiredColumns = [
            'price_list_id',
            'price_list_name',
            'eff_date',
            'exp_date',
            'status',
            'last_update',
            'updated_by',
            'action',
        ];

        // Validate file input
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');

            // Read file content
            $import = new PriceExcelFileImport();
            $collection = Excel::toCollection($import, $file);

            // Check if file is empty
            if ($collection->isEmpty() || $collection->first()->isEmpty()) {
                return response()->json(['error' => 'The uploaded file is empty or invalid.'], 400);
            }

            // Validate required columns in the file
            $headers = array_keys($collection->first()->first()->toArray());
            $missingColumns = array_diff($requiredColumns, $headers);

            if (!empty($missingColumns)) {
                return response()->json([
                    'error' => 'The file is missing required columns.',
                    'missing_columns' => $missingColumns,
                ], 400);
            }

            // Generate unique file name and path
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = "uploads/Price/{$fileName}";

            // Upload file to storage
            $uploaded = Storage::disk('spaces')->put($path, file_get_contents($file), 'public');
            if (!$uploaded) {
                throw new \Exception('Failed to upload file to DigitalOcean Spaces.');
            }

            DB::beginTransaction();

            // Save file data to the database
            $priceFile = PriceExcelFile::create([
                'price_list_id' => null,
                'price_list_name' => null,
                'eff_date' => $request->eff_date,
                'exp_date' => $request->eff_date,
                'status' => 'uploaded',
                'last_update' => now(),
                'updated_by' => $request->updated_by,
                'action' => 'upl',
                'file' => $path,
            ]);

            if (!$priceFile) {
                throw new \Exception('Failed to save file details to the database.');
            }

            DB::commit();

            return response()->json([
                'success' => 'File is valid and uploaded successfully.',
                'file_url' => Storage::disk('spaces')->url($path),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('File upload error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'error' => 'An error occurred during file upload.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function GetExcelFile()
    {
        // Fetch data from the database
        $productPrice = PriceExcelFile::get()->all();
        // Return a JSON response
        return response()->json([
            'status' => 200,
            'message' => 'Price retrieved successfully.',
            'result' => ['data' => $productPrice]
        ]);
    } 


}

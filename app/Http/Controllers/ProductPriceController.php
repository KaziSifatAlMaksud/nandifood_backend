<?php

namespace App\Http\Controllers;

use App\Models\PriceExcelFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PriceExcelFileImport;
use App\Models\Price; 


class ProductPriceController extends Controller
{


    /**
     * Validate and Upload Excel File
     */
    public function validateAndUpload(Request $request)
    {
        // Define required columns
        $requiredColumns = [
            'price',
            'no',
            'country',
            'state',
            'city',
            'warehouse',
            'sku',
            'product_name',
            'category',
            'sub_category1',
            'sub_category2',
            'inventory_uom',
            'size',
            'product_weight_in_lb',
            'product_weight_kg',
            'on_hand_qty_inventory_uom',
            'sales_uom1',
            'on_hand_qty_sales_uom1',
            'sales_uom2',
            'on_hand_qty_sales_uom2',
            'sales_uom3',
            'on_hand_qty_sales_uom3',
        ];

        // Validate file input
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
            'price_list_id' => 'required|string',
            'price_list_name' => 'required|string|max:255',
            'eff_date' => 'nullable|string',
            'exp_date' => 'nullable|string',
            'status' => 'nullable|string',
            'updated_by' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:255',
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
                'price_list_id' => $request->price_list_id,
                'price_list_name' => $request->price_list_name,
                'eff_date' => $request->eff_date,
                'exp_date' => $request->exp_date,
                'status' => $request->status,
                'last_update' => now(),
                'updated_by' => $request->updated_by,
                'action' => $request->action,
                'file' => $path,
            ]);

       

            if (!$priceFile) {
                throw new \Exception('Failed to save file details to the database.');
            }
            $priceFile->file_url = Storage::disk('spaces')->url($path);

            DB::commit();

            return response()->json([
                'success' => 'File is valid and uploaded successfully.',
                'result' => ['data' => $priceFile ]
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

    public function getPrice(){
        $price = Price::get()->all();
        return response()->json([
            'status' => 200,
            'message' => 'Price retrieved successfully.',
            'result' => ['data' => $price]
        ]);
    }


    public function importPriceData($id)
    {
        try {
            // Retrieve the price file from the database
            $priceFile = PriceExcelFile::find($id);

            if (!$priceFile || empty($priceFile->file)) {
                return response()->json(['error' => 'File not found in database.'], 404);
            }

            $filePath = $priceFile->file;

            // Check if the file exists in storage
            if (!Storage::disk('spaces')->exists($filePath)) {
                return response()->json(['error' => 'File not found in storage.'], 404);
            }

            // Save the file to a temporary location
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            file_put_contents($tempFile, Storage::disk('spaces')->get($filePath));

            // Import Excel file data into collection
            $import = new PriceExcelFileImport();
            $collection = Excel::toCollection($import, $tempFile);

            // Check if collection is empty
            if ($collection->isEmpty() || $collection->first()->isEmpty()) {
                unlink($tempFile);
                return response()->json(['error' => 'Excel file is empty.'], 400);
            }

            // Start a database transaction for data insertion
            DB::beginTransaction();

            // Gather data for batch insert
            $priceData = [];

            // Iterate over the imported rows and prepare data
            foreach ($collection->first() as $row) {
                $priceData[] = [
                    'excel_id'                  => $id,
                    'price'                     => $row['price'] ?? null,
                    'no'                        => $row['no'] ?? null,
                    'country'                   => $row['country'] ?? null,
                    'state'                     => $row['state'] ?? null,
                    'city'                      => $row['city'] ?? null,
                    'warehouse'                 => $row['warehouse'] ?? null,
                    'sku'                       => $row['sku'] ?? null,
                    'product_name'              => $row['product_name'] ?? null,
                    'category'                  => $row['category'] ?? null,
                    'sub_category1'             => $row['sub_category1'] ?? null,
                    'sub_category2'             => $row['sub_category2'] ?? null,
                    'inventory_uom'             => $row['inventory_uom'] ?? null,
                    'size'                      => $row['size'] ?? null,
                    'product_weight_in_lb'      => $row['product_weight_in_lb'] ?? null,
                    'product_weight_kg'         => $row['product_weight_kg'] ?? null,
                    'on_hand_qty_inventory_uom' => $row['on_hand_qty_inventory_uom'] ?? null,
                    'sales_uom1'                => $row['sales_uom1'] ?? null,
                    'on_hand_qty_sales_uom1'    => $row['on_hand_qty_sales_uom1'] ?? null,
                    'sales_uom2'                => $row['sales_uom2'] ?? null,
                    'on_hand_qty_sales_uom2'    => $row['on_hand_qty_sales_uom2'] ?? null,
                    'sales_uom3'                => $row['sales_uom3'] ?? null,
                    'on_hand_qty_sales_uom3'    => $row['on_hand_qty_sales_uom3'] ?? null,
                ];
            }

            // Insert data in batches for better performance
            if (!empty($priceData)) {
                Price::insert($priceData);
            }

            // Mark the file as processed
            PriceExcelFile::where('id', $id)->update(['action' => 2]);

            // Commit the database transaction
            DB::commit();

            // Clean up temporary file
            unlink($tempFile);

            // Return success response
            return response()->json(['success' => 'Price data imported successfully.'], 200);

        } catch (\Exception $e) {
            // Rollback if an error occurs
            DB::rollBack();

            // Log the error for debugging
            Log::error('Import error: ' . $e->getMessage(), ['exception' => $e]);

            // Return error response with exception message
            return response()->json([
                'error' => 'An error occurred during import.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function GetExcelFile()
    {
        // Fetch data from the database
        $productPrice = PriceExcelFile::where('action', 1)->get();

        // Loop through each product price and set the file_name property
        foreach ($productPrice as $product) {
            $product->file_name = Storage::disk('spaces')->url($product->file);
        }

        // Return a JSON response
        return response()->json([
            'status' => 200,
            'message' => 'Price retrieved successfully.',
            'result' => ['data' => $productPrice]
        ]);
    }

    public function GetExcelFile1()
    {
        // Fetch data from the database
       
        $productPrice = PriceExcelFile::where('action', 2)->get()->all();
        foreach ($productPrice as $product) {
            $product->file_name = Storage::disk('spaces')->url($product->file);
        }
        // Return a JSON response
        return response()->json([
            'status' => 200,
            'message' => 'Price retrieved successfully.',
            'result' => ['data' => $productPrice]
        ]);
    }
     

    public function destroyExcel($id)
    {
        $priceFile = PriceExcelFile::find($id);

        if (!$priceFile) {
            return response()->json([
                'message' => 'Price file not found'
            ], 404);
        }

        $priceFile->delete();

        return response()->json([
            'message' => 'Price file deleted successfully!'
        ], 200);
    }

    public function destroy($id)
    {
        $priceInfo = Price::find($id);

        if (!$priceInfo) {
            return response()->json([
                'message' => 'Price info not found'
            ], 404);
        }

        $priceInfo->delete();

        return response()->json([
            'message' => 'Price info deleted successfully!'
        ], 200);
    }


}

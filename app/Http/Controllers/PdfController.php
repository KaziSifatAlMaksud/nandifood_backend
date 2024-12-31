<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Warehouse;
use App\Models\Uom;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PdfController extends Controller
{
  
    public function warehouse_pdf(){
        $warehouses = Warehouse::all();
        $imagePath = public_path('storage/company-logo.png'); 
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageSrc = 'data:image/png;base64,' . $imageData;

        $data = [
            'title' => 'NandiFood Industries',
            'date' => date('m/d/Y'),
            'result' => $warehouses,
            'imageSrc' => $imageSrc 
        ];
    
        $slugDate = Str::slug(date('Y-m-d'));
        $fileName = "{$slugDate}_warehouse_List.pdf";
        $pdf = Pdf::loadView('pdf.warehouse_pdf', $data);
        return $pdf->download($fileName);
    }
    

    public function uom_list_pdf()
{
    try {
        // Fetch all UOM data
        $uom = Uom::all();

        // Path to the company logo
        $imagePath = public_path('storage/company-logo.png');

        // Prepare data for the PDF view
        $data = [
            'title' => 'Unit of Measure (UOM) List',
            'date' => date('m/d/Y'),
            'imageSrc' => $imagePath, // Pass the base64 encoded image
            'result' => $uom,
        ];

        // Generate the PDF using the view
        $pdf = Pdf::loadView('pdf.uom_list', $data);

        // Stream the PDF in the browser
        return $pdf->stream('uom_list.pdf');
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while generating the UOM list PDF.',
            'error' => $e->getMessage()
        ], 500);
    }
}



    
// public function uom_list_pdf()
// {
//     try {
//         // Fetch all UOM data
//         $uom = Uom::all();
//         $imagePath = public_path('storage/company-logo.png');

//         if (!file_exists($imagePath)) {
//             throw new \Exception('Company logo file not found at ' . $imagePath);
//         }
//         $imageData = base64_encode(file_get_contents($imagePath));
//         $imageSrc = 'data:image/png;base64,' . $imageData;
//         $data = [
//             'title' => 'Unit of Measure (UOM) List',
//             'date' => date('m/d/Y'),
//             'imageSrc' => $imageSrc,
//             'result' => $uom,
//         ];

//         $pdf = Pdf::loadView('pdf.uom_list', $data);
//         return $pdf->stream('uom_list.pdf');
//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'An error occurred while generating the UOM list PDF.',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


}


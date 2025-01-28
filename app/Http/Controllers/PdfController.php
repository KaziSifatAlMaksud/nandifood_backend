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


    public function hu_list_pdf(Request $request)
{
    try {
        // Fetch HU data
        $pu_code = 'HU';
        $pu_lists = Hupu::select([
                'id', 'hu_pu_code', 'hu_pu_type', 'hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name',
                'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
            ])
            ->where('hu_pu_code', $pu_code)
            ->get()
            ->map(function ($pu_list) {
                $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');

                // Add dimensions and weights in both units
                $is_cm = $pu_list->unit == 0; // 0 for cm, 1 for inches
                $pu_list->length_cm = $is_cm ? $pu_list->length : null;
                $pu_list->width_cm = $is_cm ? $pu_list->width : null;
                $pu_list->height_cm = $is_cm ? $pu_list->height : null;
                $pu_list->min_weight_kg = $is_cm ? $pu_list->min_weight : null;
                $pu_list->max_weight_kg = $is_cm ? $pu_list->max_weight : null;
                $pu_list->length_in = !$is_cm ? $pu_list->length : null;
                $pu_list->width_in = !$is_cm ? $pu_list->width : null;
                $pu_list->height_in = !$is_cm ? $pu_list->height : null;
                $pu_list->min_weight_lb = !$is_cm ? $pu_list->min_weight : null;
                $pu_list->max_weight_lb = !$is_cm ? $pu_list->max_weight : null;

                // Add additional details
                $result = Hupu::fullName($pu_list->id);
                $pu_list->short_name = $result['short_name'];
                $pu_list->full_name = $result['full_name'];
                $pu_list->volumem3 = $result['volumem3'];
                $pu_list->volumeft3 = $result['volumeft3'];

                return $pu_list;
            });

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $terms = explode(' ', $search);
            $pu_lists = $pu_lists->filter(function ($pu_list) use ($terms) {
                foreach ($terms as $term) {
                    if (stripos($pu_list->pu_hu_name, $term) !== false || 
                        stripos($pu_list->description, $term) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Prepare PDF data
        $data = [
            'title' => 'Unit of Measure (UOM) List',
            'date' => date('m/d/Y'),
            'result' => $pu_lists,
        ];

        // Generate and stream PDF
        $pdf = Pdf::loadView('pdf.hu_list', $data);
        return $pdf->stream('hu_list.pdf');

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


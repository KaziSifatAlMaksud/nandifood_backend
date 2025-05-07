<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Warehouse;
use App\Models\Uom;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\Positions;
use App\Models\Hupu;
use App\Models\Uom_type;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;

use App\Models\GRN;

use App\Models\GTN;
use App\Models\RGN;
use App\Models\DGN;
use App\Models\PRD;
use App\Models\PO;


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
            $hu_code = 'HU';

            $hu_lists = Hupu::select([
                    'id', 'hu_pu_code', 'hu_pu_type', 'hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name',
                    'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
                ])
                ->where('hu_pu_code', $hu_code)
                ->get()
                ->map(function ($hu_list) {
                    // Get UOM type name safely
                    $hu_list->hu_pu_type_name = Uom_type::where('id', $hu_list->hu_pu_type)->value('uom_name');

                    // Check if unit is cm/kg (0) or in/lb (1)
                    $is_cm = $hu_list->unit == 0;

                    // Metric (cm/kg) & Imperial (in/lb) conversions
                    $hu_list->length_cm = $is_cm ? $hu_list->length : round($hu_list->length * 2.54, 2);
                    $hu_list->width_cm = $is_cm ? $hu_list->width : round($hu_list->width * 2.54, 2);
                    $hu_list->height_cm = $is_cm ? $hu_list->height : round($hu_list->height * 2.54, 2);
                    $hu_list->min_weight_kg = $is_cm ? $hu_list->min_weight : round($hu_list->min_weight * 0.453592, 2);
                    $hu_list->max_weight_kg = $is_cm ? $hu_list->max_weight : round($hu_list->max_weight * 0.453592, 2);

                    $hu_list->length_in = !$is_cm ? $hu_list->length : round($hu_list->length / 2.54, 2);
                    $hu_list->width_in = !$is_cm ? $hu_list->width : round($hu_list->width / 2.54, 2);
                    $hu_list->height_in = !$is_cm ? $hu_list->height : round($hu_list->height / 2.54, 2);
                    $hu_list->min_weight_lb = !$is_cm ? $hu_list->min_weight : round($hu_list->min_weight / 0.453592, 2);
                    $hu_list->max_weight_lb = !$is_cm ? $hu_list->max_weight : round($hu_list->max_weight / 0.453592, 2);

                    // Volume calculation
                    $volume_cm3 = $hu_list->length_cm * $hu_list->width_cm * $hu_list->height_cm;
                    $hu_list->volume_ft3 = round($volume_cm3 / 28316.8466, 4);

                    // Get additional details
                    $result = Hupu::fullName($hu_list->id);
                    if ($result) {
                        $hu_list->short_name = $result['short_name'] ?? null;
                        $hu_list->full_name = $result['full_name'] ?? null;
                        $hu_list->volumem3 = $result['volumem3'] ?? null;
                        $hu_list->volumeft3 = $result['volumeft3'] ?? null;
                    }

                    return $hu_list;
                });

            // Generate and Download PDF
            $pdf = Pdf::loadView('pdf.hu_list', compact('hu_lists'));
            return $pdf->download('hu_list.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while generating the HU list PDF.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function pu_list_pdf(Request $request)
    {
        try {
            $pu_code = 'PU';

            $pu_lists = Hupu::select([
                    'id', 'hu_pu_code', 'hu_pu_type', 'hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name',
                    'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
                ])
                ->where('hu_pu_code', $pu_code)
                ->get()
                ->map(function ($pu_list) {
                    // Get UOM type name safely
                    $pu_list->hu_pu_type_name = optional(Uom_type::find($pu_list->hu_pu_type))->uom_name;

                    // Check if unit is cm/kg (0) or in/lb (1)
                    $is_cm = $pu_list->unit == 0;

                    // Metric (cm/kg) & Imperial (in/lb) conversions
                    $pu_list->length_cm = $is_cm ? $pu_list->length : round($pu_list->length * 2.54, 2);
                    $pu_list->width_cm = $is_cm ? $pu_list->width : round($pu_list->width * 2.54, 2);
                    $pu_list->height_cm = $is_cm ? $pu_list->height : round($pu_list->height * 2.54, 2);
                    $pu_list->min_weight_kg = $is_cm ? $pu_list->min_weight : round($pu_list->min_weight * 0.453592, 2);
                    $pu_list->max_weight_kg = $is_cm ? $pu_list->max_weight : round($pu_list->max_weight * 0.453592, 2);

                    $pu_list->length_in = !$is_cm ? $pu_list->length : round($pu_list->length / 2.54, 2);
                    $pu_list->width_in = !$is_cm ? $pu_list->width : round($pu_list->width / 2.54, 2);
                    $pu_list->height_in = !$is_cm ? $pu_list->height : round($pu_list->height / 2.54, 2);
                    $pu_list->min_weight_lb = !$is_cm ? $pu_list->min_weight : round($pu_list->min_weight / 0.453592, 2);
                    $pu_list->max_weight_lb = !$is_cm ? $pu_list->max_weight : round($pu_list->max_weight / 0.453592, 2);

                    // Volume calculation
                    $volume_cm3 = $pu_list->length_cm * $pu_list->width_cm * $pu_list->height_cm;
                    $pu_list->volume_ft3 = round($volume_cm3 / 28316.8466, 4);

                    // Get additional details
                    $result = Hupu::fullName($pu_list->id);
                    if ($result) {
                        $pu_list->short_name = $result['short_name'] ?? null;
                        $pu_list->full_name = $result['full_name'] ?? null;
                        $pu_list->volumem3 = $result['volumem3'] ?? null;
                        $pu_list->volumeft3 = $result['volumeft3'] ?? null;
                    }

                    return $pu_list;
                });

            // Generate and Download PDF
            $pdf = Pdf::loadView('pdf.pu_list', compact('pu_lists'));
            return $pdf->download('pu_list.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while generating the PU list PDF.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function employee_list_pdf()
    {
        // Get the employee data with necessary joins
        $employees = Employee::query()
            ->leftJoin('positions', 'employee.position_id', '=', 'positions.id')
            ->leftJoin('warehouse', 'employee.warehouse_id', '=', 'warehouse.id')
            ->select('employee.*', 'positions.position_name as position_name', 'warehouse.warehouse_name as warehouse_name')
            ->get();

        $pdf = PDF::loadView('pdf.employee_list', ['employees' => $employees]);

        // Download the generated PDF
        return $pdf->download('employee_list.pdf');
    }


    public function grns_print_pdf($id)
    {
        $grns = GRN::with('receivingDetails')->find($id);

        if (!$grns) {
            abort(404, "GRN not found");
        }
        $warehouse = Warehouse::find($grns->receiving_warehouse_id);
        $suppliers = Supplier::where('supplier_no', $grns->supplier)->first();

        // Load the Blade view and pass the data
        $pdf = Pdf::loadView('pdf.grn.grn_details', [
            'grns' => $grns,
            'warehouse' => $warehouse,
            'suppliers' => $suppliers
        ])->setPaper('a4', 'landscape');
        // Download the generated PDF
        return $pdf->download('grn_details_' . $grns->id . '.pdf');
        return $pdf->download('grn_details_' . $grns->id . '.pdf');
      

    }
    

    
    public function customer_list_pdf()
    {
        $customers = Customer::all();
        $pdf = PDF::loadView('pdf.customer_list', ['customers' => $customers]);
        return $pdf->download('customer-list.pdf');
    }
    public function product_list_pdf()
    {
        $products = Product::all();
        $pdf = PDF::loadView('pdf.product_list', ['products' => $products]);
        return $pdf->download('product-list.pdf');
    }
    public function supplier_list_pdf()
    {
        $suppliers = Supplier::all();
        $pdf = PDF::loadView('pdf.supplier_list', ['suppliers' => $suppliers]);
        return $pdf->download('supplier_list.pdf');
    }

    public function grns_list_pdf()
    {
        $grns = GRN::all();
        $pdf = PDF::loadView('pdf.grns_list', ['grns' => $grns]);
        return $pdf->download('grns-list.pdf');
    }
    public function gtns_list_pdf(){
        $gtns = GTN::all();
        $pdf = PDF::loadView('pdf.gtns_list', ['gtn_lists' => $gtns]);
        return $pdf->download('gtns-list.pdf');
    }

    public function rgns_list_pdf()
    {
        $rgns = RGN::all();
        $pdf = PDF::loadView('pdf.rgns_list', ['rgn_lists' => $rgns]);
        return $pdf->download('rgns-list.pdf');
    }

    public function dgns_list_pdf()
    {
        $dgns = DGN::all();
        $pdf = PDF::loadView('pdf.dgns_list', ['dgn_lists' => $dgns]);
        return $pdf->download('dgns-list.pdf');
    }

    public function prds_list_pdf()
    {
        $prds = PRD::all();
        $pdf = PDF::loadView('pdf.prds_list', ['prd_lists' => $prds]);
        return $pdf->download('prds-list.pdf');
    }
    public function pos_list_pdf()
    {
        $pos = PO::all();
        $pdf = Pdf::loadView('pdf.pos_list', ['po_lists' => $pos]);
        return $pdf->download('pos-list.pdf');
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


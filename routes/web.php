<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Models\Hupu;
use App\Models\Uom_type;
use App\Models\Employee;
use App\Models\Positions;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Http\Controllers\EmployeeController;
use App\Models\GRN;
use App\Models\GTN;
use App\Models\DGN;
use App\Models\PO;
use App\Models\RGN;
use App\Models\PRD;
use App\Models\Warehouse;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });


use Illuminate\Http\Request;

Route::get('/uom-list-view', function () {
    $uom = \App\Models\Hupu::all(); // Assuming you need this list

    $pu_code = 'HU';

    // Fetching the pu lists and adding necessary details
    $pu_lists = Hupu::select([
            'id', 'hu_pu_code', 'hu_pu_type', 'hu_pu_id', 'bulk_code', 'flex', 'pu_hu_name',
            'description', 'unit', 'length', 'width', 'height', 'min_weight', 'max_weight'
        ])
        ->where('hu_pu_code', $pu_code)
        ->get()
        ->map(function ($pu_list) {
            $pu_list->hu_pu_type_name = Uom_type::where('id', $pu_list->hu_pu_type)->value('uom_name');

            // Add dimensions and weights in both units (cm and inches)
          $is_cm = $pu_list->unit == 0; // 0 for cm/kg, 1 for inches/lb

        // Metric values (if unit is cm/kg)
          $pu_list->length_cm = $is_cm ? $pu_list->length : round($pu_list->length * 2.54, 2);
          $pu_list->width_cm = $is_cm ? $pu_list->width : round($pu_list->width * 2.54, 2);
          $pu_list->height_cm = $is_cm ? $pu_list->height : round($pu_list->height * 2.54, 2);
          $pu_list->min_weight_kg = $is_cm ? $pu_list->min_weight : round($pu_list->min_weight * 0.453592, 2);
          $pu_list->max_weight_kg = $is_cm ? $pu_list->max_weight : round($pu_list->max_weight * 0.453592, 2);

        // Imperial values (if unit is inches/lb)
          $pu_list->length_in = !$is_cm ? $pu_list->length : round($pu_list->length / 2.54, 2);
          $pu_list->width_in = !$is_cm ? $pu_list->width : round($pu_list->width / 2.54, 2);
          $pu_list->height_in = !$is_cm ? $pu_list->height : round($pu_list->height / 2.54, 2);
          $pu_list->min_weight_lb = !$is_cm ? $pu_list->min_weight : round($pu_list->min_weight / 0.453592, 2);
          $pu_list->max_weight_lb = !$is_cm ? $pu_list->max_weight : round($pu_list->max_weight / 0.453592, 2);

        // Volume Calculation (Cubic Feet)
        $volume_cm3 = $pu_list->length_cm * $pu_list->width_cm * $pu_list->height_cm;
        $pu_list->volume_ft3 = round($volume_cm3 / 28316.8466, 2); // Convert cm³ to ft³

            // Add additional details (assuming fullName is a method in your Hupu model)
            $result = Hupu::fullName($pu_list->id);
            $pu_list->short_name = $result['short_name'];
            $pu_list->full_name = $result['full_name'];
            $pu_list->volumem3 = $result['volumem3'];
            $pu_list->volumeft3 = $result['volumeft3'];

            return $pu_list;
        });
        return view('pdf.hu_list', ['pu_lists' => $pu_lists]);

});



Route::get('/employee-list-view', function () {
  $employees  = Employee::all();
        return view('pdf.product_list', ['employees' => $employees]);
});
Route::get('/customer-list-view', function () {
  $customers  = Customer::all();
  return view('pdf.customer_list', ['customers' => $customers]);
});
Route::get('/supplier-list-view', function () {
  $suppliers  = Supplier::all();
  return view('pdf.supplier_list', ['suppliers' => $suppliers]);
});

Route::get('/proudct-list-view', function () {
  $proudcts  = Product::all();
  return view('pdf.product_list', ['products' => $proudcts]);
});

Route::get('/grns_print_view/{id}', function ($id) {
  $grns = GRN::with('receivingDetails')->find($id);
  if (!$grns) {
      abort(404, "GRN not found");
  }

  $warehouse = Warehouse::find($grns->receiving_warehouse_id);
  $suppliers = Supplier::where('supplier_no', $grns->supplier)->first();


  return view('pdf.grn.grn_details', [
      'grns' => $grns,
      'warehouse' => $warehouse,
      'suppliers' => $suppliers
  ]);
});

Route::get('/gtns_print_view/{id}', function ($id) {
  $gtns = GTN::with('transferOutDetail')->find($id);
  if (!$gtns) {
      abort(404, "GTN not found");
  }

  $out_warhouse = Warehouse::find($gtns->transfer_out_warehouse);
  $in_warehouse = Warehouse::find($gtns->transfer_in_warehouse);

  return view('pdf.gtn.gtn_details', [
      'gtns' => $gtns,
      'out_warhouse' => $out_warhouse,
      'in_warehouse' => $in_warehouse
  ]);
});


Route::get('/rgns_print_view/{id}', function ($id) {
  $rgns = RGN::with('rgnItemDetails')->find($id);
  if (!$rgns) {
      abort(404, "RGN not found");
  }

  $warehouse = Warehouse::find($rgns->warehouse_id);
  $suppliers = Supplier::where('supplier_no', $rgns->supplier)->first();


  return view('pdf.rgn.rgn_details', [
      'rgns' => $rgns,
      'warehouse' => $warehouse,
      'suppliers' => $suppliers
  ]);
});


Route::get('/pos_print_view/{id}', function ($id) {
  $pos = PO::with('poItemDetails')->find($id);
  if (!$pos) {
      abort(404, "PO not found");
  }


  $suppliers = Supplier::where('supplier_no', $pos->supplier)->first();
  $warehouse = Warehouse::find($pos->warehouse);


  return view('pdf.po.pos_details', [
      'pos' => $pos,
      'warehouse' => $warehouse,
      'suppliers' => $suppliers
  ]);
});

Route::get('/dgns_print_view/{id}', function ($id) {
  $dgns = DGN::with('damageDetails')->find($id);
  if (!$dgns) {
      abort(404, "DGN not found");
  }
  $defult_warehouse = Warehouse::find($dgns->defult_warehouse);

  return view('pdf.dgn.dgn_details', [
      'dgns' => $dgns,
      'warehouse' => $defult_warehouse
  ]);
});

Route::get('/grns/list', function() {
  $grns = GRN::all(); 
  return view('pdf.grns_list', [
      'grns' => $grns
  ]);
});

Route::get('/gtns/list', function() {
  $grns = GTN::all(); 
  return view('pdf.gtns_list', [
      'gtn_lists' => $grns
  ]);
});

Route::get('/dgns/list', function() {
  $dgns = DGN::all(); 
  return view('pdf.dgns_list', [
      'dgn_lists' => $dgns
  ]);
});

Route::get('/pos/list', function() {
  $pos = PO::all(); 
  return view('pdf.pos_list', [
      'po_lists' => $pos
  ]);
});

Route::get('/prds/list', function() {
  $prds = PRD::all(); 
  return view('pdf.prds_list', [
      'prd_lists' => $prds
  ]);
});

Route::get('/rgns/list', function() {
  $rgns = RGN::all(); 
  return view('pdf.rgns_list', [
      'rgn_lists' => $rgns
  ]);
});



Route::get('/grns/printpdf/{id}', [PdfController::class, 'grns_print_pdf'])->name('grns.printpdf');
Route::get('/gtns/printpdf/{id}', [PdfController::class, 'gtns_print_pdf'])->name('gtns.printpdf');
Route::get('/rgns/printpdf/{id}', [PdfController::class, 'rgns_print_pdf'])->name('rgns.printpdf');
Route::get('/dgns/printpdf/{id}', [PdfController::class, 'dgns_print_pdf'])->name('dgns.printpdf');
route::get('/warehouse/downloadpdf', [PdfController::class, 'warehouse_pdf']);
route::get('/uom_list/downloadpdf', [PdfController::class, 'uom_list_pdf']);
route::get('/hu/downloadpdf', [PdfController::class, 'hu_list_pdf'] );
route::get('/pu/downloadpdf', [PdfController::class, 'pu_list_pdf'] );
route::get('/employee/downloadpdf', [PdfController::class, 'employee_list_pdf'] );
route::get('/customer/downloadpdf', [PdfController::class, 'customer_list_pdf'] );
route::get('/product/downloadpdf', [PdfController::class, 'product_list_pdf'] );
route::get('/supplier/downloadpdf', [PdfController::class, 'supplier_list_pdf'] );
route::get('/grns/downloadpdf', [PdfController::class, 'grns_list_pdf'] );



route::get('/gtns/downloadpdf', [PdfController::class, 'gtns_list_pdf'] );
route::get('/rgns/downloadpdf', [PdfController::class, 'rgns_list_pdf'] );
route::get('/dgns/downloadpdf', [PdfController::class, 'dgns_list_pdf'] );
route::get('/prds/downloadpdf', [PdfController::class, 'prds_list_pdf'] );
route::get('/pos/downloadpdf', [PdfController::class, 'pos_list_pdf'] );
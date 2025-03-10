<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Models\Hupu;
use App\Models\Uom_type;
use App\Models\Employee;
use App\Models\Positions;
use App\Http\Controllers\EmployeeController;

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
   
        return view('pdf.product_list', ['employees' => $employees]);
});


route::get('/warehouse/downloadpdf', [PdfController::class, 'warehouse_pdf']);
route::get('/uom_list/downloadpdf', [PdfController::class, 'uom_list_pdf']);
route::get('/hu/downloadpdf', [PdfController::class, 'hu_list_pdf'] );
route::get('/pu/downloadpdf', [PdfController::class, 'pu_list_pdf'] );
route::get('/employee/downloadpdf', [PdfController::class, 'employee_list_pdf'] );


route::get('/customer/downloadpdf', [PdfController::class, 'customer_list_pdf'] );
route::get('/product/downloadpdf', [PdfController::class, 'product_list_pdf'] );
route::get('/supplier/downloadpdf', [PdfController::class, 'supplier_list_pdf'] );

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BinLocationController; 
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\HupuController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/welcom', function () {
//     return 'welcome';
// });
//warehouse route 
route::resource('/warehouse', WarehouseController::class);

route::get('/war_name', [WarehouseController::class,'warehouse_name']);

Route::get('/employee/{id?}', [EmployeeController::class, 'index']);
route::post('/employee/create', [EmployeeController::class, 'store']);
route::get('/employee/{id}/edit', [EmployeeController::class, 'edit']);
route::put('/employee/{id}', [EmployeeController::class, 'update']);

route::delete('/employee/delete/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');



// route::get('/country',[WarehouseController::class, 'country']);

//warehouse attachment route
route::get('/warehouse_attachment', [WarehouseController::class, 'warehouse_compliance']);
route::post('/warehouse_attachment/create', [WarehouseController::class, 'warehouse_attachment_store']);
route::delete('/warehouse_attachment/delete/{id}', [WarehouseController::class, 'warehouse_attachment_destroy'])->name('warehouse-attachment.destroy');

//warehouse information
Route::get('/employee', [EmployeeController::class, 'index']);

// route::get('/warehouse_attachment', [WarehouseController::class, 'warehouse_compliance']);
route::post('/employee_notes/create', [EmployeeController::class, 'employee_notes_store']);

route::get('/country',[WarehouseController::class, 'country']);


route::get('/country_name',[WarehouseController::class, 'getCountries']);
Route::get('/states/{countryName}', [WarehouseController::class, 'getStates']);
Route::get('/cities/{stateName}', [WarehouseController::class, 'getCities']);

Route::get('/warhouse/employee/{warehouse_id}', [WarehouseController::class, 'getEmployee']);
Route::get('/warhouse/attachment/{warehouse_id}', [WarehouseController::class, 'getAttachment']);
Route::get('/warhouse/bin_location/{warehouse_id}', [WarehouseController::class, 'getBinLocation']);
// addtional information

Route::get('/bin_status', [WarehouseController::class, 'bin_status']);
route::get('/bin_storage_type',[WarehouseController::class, 'bin_storage_type']);
route::get('/uom_type',[WarehouseController::class, 'uom_type']);

//download PDF route
// route::get('/warehouse/download', [PdfController::class, 'warehouse_pdf']);
route::post('/warehouse/excel/create', [WarehouseController::class, 'warehouse_excel']);
Route::get('/warehouse/excel/export', [WarehouseController::class, 'export']);

//csv file export 

Route::get('/uom/excel/export', [UomController::class, 'uom_export']);
Route::get('/uom/csv/export', [WarehouseController::class, 'exportCsv']);




Route::get('/binlocation', [BinLocationController::class, 'index']);
// route::get('/binlocation/create', [BinLocationController::class, 'create']);
Route::post('/binlocation/create', [BinLocationController::class, 'store']);
Route::get('/binlocation/{id}', [BinLocationController::class, 'show']);
Route::get('/binlocation/{id}/edit', [BinLocationController::class, 'edit']);
Route::put('/binlocation/{id}', [BinLocationController::class, 'update']);
Route::delete('/binlocation/delete/{id}', [BinLocationController::class, 'destroy'])->name('bin-location.destroy');


// Unit Of Manage  List 
Route::get('/uom',[UomController::class, 'index']);
Route::get('/all_uom',[UomController::class, 'all_uom']);
Route::post('/uom/create', [UomController::class, 'store']);
Route::get('/uom/{id}', [UomController::class, 'show']);
Route::get('/uom/{id}/edit', [UomController::class, 'edit']);
Route::put('/uom/{id}', [UomController::class, 'update']);
Route::delete('/uom/delete/{id}', [UomController::class, 'destroy'])->name('uom.destroy');

Route::get('/hu_pu',[HupuController::class, 'hupu_list']);

// handaling Unit List 
Route::get('/hu',[HupuController::class, 'hu_list']);
route::get('/all_hu',[HupuController::class, 'hu_all']);
Route::POST('hu_pu/create',[HupuController::class, 'store']);
Route::Get('hu_pu/{id}',[HupuController::class, 'show']);
Route::Get('hu_pu/{id}/edit',[HupuController::class, 'edit']);
Route::put('hu_pu/{id}', [HupuController::class, 'update']);
Route::delete('/hu_pu/delete/{id}', [HupuController::class, 'destroy'])->name('hu_pu.destroy');


// Purchasing Unit List 
route::get('/pu',[HupuController::class, 'pu_list']);
route::get('/all_pu',[HupuController::class, 'pu_all']);

Route::get('/linked_hu_pu/{id}', [HupuController::class, 'linked_hu_pu']);


// Helper API Route

route::get('/get_position',[EmployeeController::class, 'get_position']);
route::get('/get_all_notes/{id}',[EmployeeController::class, 'get_all_notes']);

// route::get('/',[HupuController::class, 'pu_list']);



//Product API

Route::get('/product', [ProductController::class, 'index']);
Route::Post('/product/create', [ProductController::class, 'store']);






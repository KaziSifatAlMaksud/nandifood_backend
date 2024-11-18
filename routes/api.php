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


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::get('/welcom', function () {
//     return 'welcome';
// });
//warehouse route 
route::resource('/warehouse', WarehouseController::class);

//warehouse information
Route::get('/employee/{id?}', [EmployeeController::class, 'index']);
route::get('/country',[WarehouseController::class, 'country']);

//warehouse attachment route
route::get('/warehouse_attachment', [WarehouseController::class, 'warehouse_compliance']);
route::post('/warehouse_attachment/create', [WarehouseController::class, 'warehouse_attachment_store']);
route::delete('/warehouse_attachment/delete/{id}', [WarehouseController::class, 'warehouse_attachment_destroy'])->name('warehouse-attachment.destroy');

//download PDF route
// route::get('/warehouse/downloadpdf', [PdfController::class, 'warehouse_pdf']);
route::post('/warehouse/excel/create', [WarehouseController::class, 'warehouse_excel']);
Route::get('warehouse/excel/export', [WarehouseController::class, 'export']);


Route::post('/lavel/create', [BinLocationController::class, 'form']);


// Route::delete('/country/{id}', [BinLocationController::class, 'destroy']);



route::get('/binlocation/create', [BinLocationController::class, 'create']);
Route::post('/binlocation/create', [BinLocationController::class, 'store']);
Route::get('/binlocation/{war_id?}', [BinLocationController::class, 'index']);
Route::delete('/binlocation/delete/{id}', [BinLocationController::class, 'destroy'])->name('bin-location.destroy');








// Unit Of Manage  List 
route::get('/uom',[UomController::class, 'index']);
Route::post('/uom/create', [UomController::class, 'store']);


// handaling Unit List 
route::get('/hu',[HupuController::class, 'hu_list']);
route::post('/hu/create',[HupuController::class, 'store']);
// Purchasing Unit List 
route::get('/pu',[HupuController::class, 'pu_list']);
// route::post('/pu/create',[HupuController::class, 'store_pu']);







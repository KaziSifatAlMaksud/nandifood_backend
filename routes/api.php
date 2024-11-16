<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BinLocationController; 
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PdfController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::get('/welcom', function () {
//     return 'welcome';
// });
//warehouse route 
route::resource('/warehouse', WarehouseController::class);
//download PDF route
route::get('/warehouse/downloadpdf', [PdfController::class, 'warehouse_pdf']);
route::post('/warehouse/excel/create', [WarehouseController::class, 'warehouse_excel']);

Route::get('warehouse/excel/export', [WarehouseController::class, 'export']);






Route::post('/binlocation', [BinLocationController::class, 'store']);

Route::post('/lavel/create', [BinLocationController::class, 'form']);
Route::delete('/country/{id}', [BinLocationController::class, 'destroy']);



Route::get('/binlocation/{war_id?}', [BinLocationController::class, 'index']);
Route::post('/binlocation/create/{war_id?}', [BinLocationController::class, 'store']);



Route::get('/employee/{id?}', [EmployeeController::class, 'index']);

route::get('/binlocation/create', [BinLocationController::class, 'create']);
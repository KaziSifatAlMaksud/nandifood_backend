<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BinLocationController; 
use App\Http\Controllers\EmployeeController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::get('/welcom', function () {
//     return 'welcome';
// });

Route::post('/binlocation', [BinLocationController::class, 'store']);


route::resource('/warehouse', WarehouseController::class);




Route::post('/lavel/create', [BinLocationController::class, 'form']);
Route::delete('/country/{id}', [BinLocationController::class, 'destroy']);



Route::get('/binlocation/{war_id?}', [BinLocationController::class, 'index']);
Route::post('/binlocation/create/{war_id?}', [BinLocationController::class, 'store']);



Route::get('/employee/{id?}', [EmployeeController::class, 'index']);

route::get('/binlocation/create', [BinLocationController::class, 'create']);
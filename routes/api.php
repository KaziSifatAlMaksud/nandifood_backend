<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BinLocationController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::get('/welcom', function () {
//     return 'welcome';
// });

Route::post('/binlocation', [BinLocationController::class, 'store']);


route::resource('/warehouse', WarehouseController::class);


// route::get('/binlocation', [BinLocationController::class, 'index']);
Route::get('/binlocation/{id?}', [BinLocationController::class, 'index']);

route::get('/binlocation/create', [BinLocationController::class, 'create']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BinLocationController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/welcom', function () {
    return 'welcome';
});

Route::get('/warehouse', [WarehouseController::class, 'index']);

Route::post('/binlocation', [BinLocationController::class, 'store']);


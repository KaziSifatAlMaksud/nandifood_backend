<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

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


Route::get('/uom-list-view', function () {
    $uom = \App\Models\Uom::all();

    $data = [
        'title' => 'Unit of Measure (UOM) List',
        'date' => date('m/d/Y'),
        'result' => $uom,
    ];

    return view('pdf.uom_list', $data);
});

route::get('/warehouse/downloadpdf', [PdfController::class, 'warehouse_pdf']);
route::get('/uom_list/downloadpdf', [PdfController::class, 'uom_list_pdf']);
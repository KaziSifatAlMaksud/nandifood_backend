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
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ShippingInfoController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/welcom', function () {
//     return 'welcome';
// });
//warehouse route 
// route::resource('/warehouse', WarehouseController::class);
route::get('/warehouse', [WarehouseController::class, 'index']);
route::post('/warehouse/create', [WarehouseController::class, 'store']);
route::get('/warehouse/{id}', [WarehouseController::class, 'show']);
route::post('/warehouse/{id}', [WarehouseController::class, 'update']);
route::Delete('/warehouse/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

//Warehouse Name route
route::get('/binzones', [WarehouseController::class, 'get_binzones']);
route::get('/binbin', [WarehouseController::class, 'get_binbin']);
route::get('/binsection', [WarehouseController::class, 'get_binsection']);
route::get('/binaisle', [WarehouseController::class, 'get_binaisle']);
route::get('/binrack', [WarehouseController::class, 'get_binrack']);
route::get('/binshelf', [WarehouseController::class, 'get_binshelf']);


Route::get('/employee', [EmployeeController::class, 'index']);
route::post('/employee/create', [EmployeeController::class, 'store']);
route::get('/employee/{id}', [EmployeeController::class, 'show']);
route::post('/employee/{id}', [EmployeeController::class, 'update']);

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
route::delete('/employee/notes/delete/{id}', [EmployeeController::class, 'employee_notes_delete']);
route::get('/country',[WarehouseController::class, 'country']);


route::get('/country_name',[WarehouseController::class, 'getCountries']);
Route::get('/states/{countryName}', [WarehouseController::class, 'getStates']);
Route::get('/cities/{stateName}', [WarehouseController::class, 'getCities']);

Route::get('/warhouse/employee/{warehouse_id}', [WarehouseController::class, 'getEmployee']);
Route::get('/warhouse/attachment/{warehouse_id}', [WarehouseController::class, 'getAttachment']);
Route::get('/warhouse/bin_location/{warehouse_id}', [WarehouseController::class, 'getBinLocation']);
Route::get('/warhouse/capacity/{warehouse_id}', [WarehouseController::class, 'getCapacity']);
// addtional information

Route::get('/bin_status', [WarehouseController::class, 'bin_status']);
route::get('/bin_storage_type',[WarehouseController::class, 'bin_storage_type']);


//download PDF route
// route::get('/warehouse/download', [PdfController::class, 'warehouse_pdf']);
// route::post('/warehouse/excel/create', [WarehouseController::class, 'warehouse_excel']);




Route::get('/warehouse/excel/export', [WarehouseController::class, 'export']);
Route::get('/product/excel/export', [UomController::class, 'product_export']);
//csv file export 

Route::get('/uom/excel/export', [UomController::class, 'uom_export']);
Route::get('/hu/excel/export', [HupuController::class, 'hu_export']);
Route::get('/employee/excel/export', [EmployeeController::class, 'employeeExport']);

Route::get('/uom/csv/export', [WarehouseController::class, 'exportCsv']);


//price API Start  
Route::post('price/upload_excel', [ProductPriceController::class, 'validateAndUpload']);
Route::get('price/get_price_file', [ProductPriceController::class, 'GetExcelFile']);
Route::get('price/get_price', [ProductPriceController::class, 'getPrice']);
Route::get('price/import-price-data/{id}', [ProductPriceController::class, 'importPriceData']);

Route::post('price/import_excel_from_database', [ProductPriceController::class, 'validateAndimport']);
//price API End  


Route::get('/binlocation', [BinLocationController::class, 'index']);
// route::get('/binlocation/create', [BinLocationController::class, 'create']);
Route::post('/binlocation/create', [BinLocationController::class, 'store']);
Route::get('/binlocation/{id}', [BinLocationController::class, 'show']);
Route::get('/binlocation/{id}/edit', [BinLocationController::class, 'edit']);
Route::post('/binlocation/{id}', [BinLocationController::class, 'update']);
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

// Customer API Route
Route::get('/customer', [CustomerController::class, 'customer_list']);
Route::post('/customer/create', [CustomerController::class, 'customer_store']);
Route::get('/customer/{id}', [CustomerController::class, 'customer_show']);
Route::post('/customer/{id}', [CustomerController::class, 'customer_update']);
Route::delete('/customer/delete/{id}', [CustomerController::class, 'customer_destroy'])->name('customer.destroy');


route::get('/get_customer_all_notes/{id}',[CustomerController::class, 'get_customer_all_notes']);
route::post('/customer_notes/create', [CustomerController::class, 'customer_notes_store']);
route::delete('/customer_notes/delete/{id}', [CustomerController::class, 'customer_notes_delete']);
//Customer Helper API
route::get('/customer_category', [CustomerController::class, 'customer_category']);


// Supplier API Route
Route::get('/supplier', [SupplierController::class, 'supplier_list']);
Route::post('/supplier/create', [SupplierController::class, 'supplier_store']);
Route::get('/supplier/{id}', [SupplierController::class, 'supplier_show']);
Route::post('/supplier/{id}', [SupplierController::class, 'supplier_update']);
Route::delete('/supplier/delete/{id}', [SupplierController::class, 'supplier_destroy'])->name('supplier.destroy');


Route::get('/get_supplier_all_notes/{id}', [SupplierController::class, 'get_supplier_all_notes']);
Route::post('/supplier_notes/create', [SupplierController::class, 'supplier_notes_store']);
Route::delete('/supplier_notes/delete/{id}', [SupplierController::class, 'supplier_notes_delete']);

//Supplier Helper API
route::get('/supplier_category', [SupplierController::class, 'supplier_category']);

route::post('/credit_terms/create', [SupplierController::class, 'credit_terms_store']);
Route::get('/credit_terms/{type}/{cus_sup_id}', [SupplierController::class, 'get_credit_terms']);

// Purchasing Unit List 
route::get('/pu',[HupuController::class, 'pu_list']);
route::get('/all_pu',[HupuController::class, 'pu_all']);

Route::get('/linked_hu_pu/{id}', [HupuController::class, 'linked_hu_pu']);


// Helper API Route

route::get('/get_position',[EmployeeController::class, 'get_position']);
route::get('/get_all_notes/{id}',[EmployeeController::class, 'get_all_notes']);

// route::get('/',[HupuController::class, 'pu_list']);


Route::get('/shipping_info/{shipping_type}/{cus_or_sup_id}', [ShippingInfoController::class, 'index']);
Route::POST('/shipping_info/create', [ShippingInfoController::class, 'store']);
Route::Get('/shipping_info/{id}',[ShippingInfoController::class, 'show']);
Route::POST('/shipping_info/{id}', [ShippingInfoController::class, 'update']);
Route::delete('/shipping_info/delete/{id}', [ShippingInfoController::class, 'destroy']);



//Product API

Route::get('/product', [ProductController::class, 'index']);
Route::POST('/product/create', [ProductController::class, 'store']);
Route::Get('/product/{id}',[ProductController::class, 'show']);
Route::POST('/product/{id}', [ProductController::class, 'update2']);
Route::delete('/product/delete/{id}', [ProductController::class, 'destroy']);

route::post('/product_attachment/create', [ProductController::class, 'product_notes_store']);
route::get('/product_attachment/{id}', [ProductController::class, 'get_all_notes']);
Route::delete('/product/notes/delete/{id}', [ProductController::class, 'product_notes_delete']);


//Helper Common API
Route::get('/uom_type',[WarehouseController::class, 'uom_type']);
Route::get('/product_category', [ProductController::class, 'getproduct_cat']);
Route::get('/product_sub_category', [ProductController::class, 'getproduct_sub_cat']);
Route::get('/product_sub_category2', [ProductController::class, 'getproduct_sub_cat2']);
Route::get('/employee_name', [EmployeeController::class, 'get_employee_name']);
route::get('/war_name', [WarehouseController::class,'warehouse_name']);
route::get('/defult_warehouse_info', [WarehouseController::class,'defult_warehouse_info']);
route::get('/product_size', [ProductController::class,'size_name']);
route::get('/uom_name',[UomController::class, 'uom_name']);



Route::get('/shipping_info', [SupplierController::class, 'get_shipping_info']);


route::get('/country_name',[WarehouseController::class, 'getCountries']);
Route::get('/states/{countryName}', [WarehouseController::class, 'getStates']);
Route::get('/cities/{stateName}', [WarehouseController::class, 'getCities']);



// Route::middleware('cors')->get('/country_name',[WarehouseController::class, 'getCountries']);
// Route::middleware('cors')->get('/states/{countryName}', [WarehouseController::class, 'getStates']);
// Route::middleware('cors')->get('/cities/{stateName}', [WarehouseController::class, 'getCities']);


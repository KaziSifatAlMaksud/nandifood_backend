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
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\GTNController;
use App\Http\Controllers\DGNController;
use App\Http\Controllers\PRDController;
use App\Http\Controllers\RGNController;
use App\Http\Controllers\POController;



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





Route::get('/supplier/excel/export',[ExcelController::class, 'supplier_export']);
Route::get('/product/excel/export', [ExcelController::class, 'product_export']);

Route::get('/warehouse/excel/export', [WarehouseController::class, 'export']);

//csv file export 

Route::get('/uom/excel/export', [UomController::class, 'uom_export']);
Route::get('/hu/excel/export', [HupuController::class, 'hu_export']);
Route::get('/employee/excel/export', [EmployeeController::class, 'employeeExport']);

Route::get('/customer/excel/export',[ExcelController::class, 'customer_export']);


Route::get('/grns/excel/export', [ExcelController::class, 'grns_export']);
Route::get('/gtns/excel/export', [ExcelController::class, 'gtns_export']);
Route::get('/rgns/excel/export', [ExcelController::class, 'rgns_export']);
Route::get('/dgns/excel/export', [ExcelController::class, 'dgns_export']);
Route::get('/prds/excel/export', [ExcelController::class, 'prds_export']);
Route::get('/pos/excel/export', [ExcelController::class, 'pos_export'] );





Route::get('/uom/csv/export', [WarehouseController::class, 'exportCsv']);


//price API Start  
Route::post('/price/upload_excel', [ProductPriceController::class, 'validateAndUpload']);
Route::get('/price/get_price_file', [ProductPriceController::class, 'GetExcelFile']);
Route::get('/price/get_price_file_uploaded', [ProductPriceController::class, 'GetExcelFile1']);
Route::get('/price/get_price', [ProductPriceController::class, 'getPrice']);
Route::get('/price/import-price-data/{id}', [ProductPriceController::class, 'importPriceData']);
Route::post('/price/import_excel_from_database', [ProductPriceController::class, 'validateAndimport']);
Route::delete('/price/delete_excel/{id}', [ProductPriceController::class, 'destroyExcel'])->name('price.destroyExcel');
Route::delete('/price/delete/{id}', [ProductPriceController::class, 'destroy'])->name('price.destroy');
Route::get('/price/get_excel_price/{id}', [ProductPriceController::class, 'getExcelById']);
Route::get('/price/{excel_id}/{on_off}', [ProductPriceController::class, 'price_active_inactive']);



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


route::get('/get_customer_all_notes/{id}/{type}',[CustomerController::class, 'get_customer_all_notes']);
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


Route::get('/get_supplier_all_notes/{id}/{type}', [SupplierController::class, 'get_supplier_all_notes']);
Route::post('/supplier_notes/create', [SupplierController::class, 'supplier_notes_store']);
Route::delete('/supplier_notes/delete/{id}', [SupplierController::class, 'supplier_notes_delete']);



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

Route::get('/product/price_list/{product_id}', [ProductController::class, 'product_price_list']);
Route::get('/product/quintity_list/{product_id}', [ProductController::class, 'product_quintity_list']);
Route::get('/product/uom/{product_id}', [ProductController::class, 'product_uom']);


route::post('/product_attachment/create', [ProductController::class, 'product_notes_store']);
route::get('/product_attachment/{id}', [ProductController::class, 'get_all_notes']);
Route::delete('/product/notes/delete/{id}', [ProductController::class, 'product_notes_delete']);

// Get all route GRNs
Route::get('/grns', [GRNController::class, 'index']); 
Route::post('/grns/create', [GRNController::class, 'store']);
Route::get('/grns/{id}', [GRNController::class, 'show']); 
Route::post('/grns/{id}', [GRNController::class, 'update']);
Route::get('/grns/receivingDetails/{id}', [GRNController::class, 'getReceivingDetails']);

Route::delete('/grns/{id}/delete', [GRNController::class, 'destroy']); 


Route::post('/grn_attachment/create', [GRNController::class, 'store_attachment']);
Route::get('/grn_attachment/{id}', [GRNController::class, 'get_all_attachments']);
Route::delete('/grn_attachment/delete/{id}', [GRNController::class, 'delete_attachment']);

//Get All route for PO..
// PO Routes
Route::get('/pos', [POController::class, 'index']);
Route::post('/pos/create', [POController::class, 'store']);
Route::get('/pos/{id}', [POController::class, 'show']);
Route::post('/pos/{id}', [POController::class, 'update']);
Route::delete('/pos/{id}/delete', [POController::class, 'destroy']);

Route::post('/po_attachment/create', [POController::class, 'store_attachment']);
Route::get('/po_attachment/{id}', [POController::class, 'get_all_attachments']);
Route::delete('/po_attachment/delete/{id}', [POController::class, 'delete_attachment']);

Route::get('/pos/po_trackings/{po_id}', [POController::class, 'index_tracking']);
Route::post('/pos/po_trackings/create', [POController::class, 'sotre_tracking']);
Route::post('/pos/po_trackings/{id}', [POController::class, 'update_tracking']);
Route::get('/pos/po_tracking/show/{id}', [POController::class, 'show_tracking']);
Route::delete('/pos/po_trackings/delete_traking/{id}', [POController::class, 'destroy_tracking']);

// Helper PO Receiving Details
Route::get('/pos/po_receiving_details/{po_id}', [POController::class, 'po_receiving_details']);

// Get all route GRNs
Route::get('/rgns', [RGNController::class, 'index']); 
Route::post('/rgns/create', [RGNController::class, 'store']);
Route::get('/rgns/{id}', [RGNController::class, 'show']); 
Route::post('/rgns/{id}', [RGNController::class, 'update']);
Route::delete('/rgns/{id}/delete', [RGNController::class, 'destroy']); 


Route::post('/rgn_attachment/create', [RGNController::class, 'store_attachment']);
Route::get('/rgn_attachment/{id}', [RGNController::class, 'get_all_attachments']);
Route::delete('/rgn_attachment/delete/{id}', [RGNController::class, 'delete_attachment']);

// GRN API Helper
Route::get('/grn/warehouse', [GRNController::class, 'getWarehouse']);

// Get all route GRNs

//GTN API
Route::get('/gtns', [GTNController::class, 'index']); 
Route::post('/gtns/create', [GTNController::class, 'store']);
Route::get('/gtns/{id}', [GTNController::class, 'show']); 
Route::post('/gtns/{id}', [GTNController::class, 'update']);
Route::delete('/gtns/{id}/delete', [GTNController::class, 'destroy']);



//GTN Attachment API
Route::post('/gtn_attachment/create', [GTNController::class, 'store_attachment']);
Route::get('/gtn_attachment/{id}', [GTNController::class, 'get_all_attachments']);
Route::delete('/gtn_attachment/delete/{id}', [GTNController::class, 'delete_attachment']);


//DGN API

Route::get('/dgns', [DGNController::class, 'index']); 
Route::post('/dgns/create', [DGNController::class, 'store']);
Route::get('/dgns/{id}', [DGNController::class, 'show']); 
Route::post('/dgns/{id}', [DGNController::class, 'update']);
Route::delete('/dgns/{id}/delete', [DGNController::class, 'destroy']);



//DGN Attachment API
Route::post('/dgn_attachment/create', [DGNController::class, 'store_attachment']);
Route::get('/dgn_attachment/{id}', [DGNController::class, 'get_all_attachments']);
Route::delete('/dgn_attachment/delete/{id}', [DGNController::class, 'delete_attachment']);

// PRD API
Route::get('/prds', [PRDController::class, 'index']); 
Route::post('/prds/create', [PRDController::class, 'store']);
Route::get('/prds/{id}', [PRDController::class, 'show']); 
Route::post('/prds/{id}', [PRDController::class, 'update']);
Route::delete('/prds/{id}/delete', [PRDController::class, 'destroy']);

// PRD Attachment API
Route::post('/prd_attachment/create', [PRDController::class, 'store_attachment']);
Route::get('/prd_attachment/{id}', [PRDController::class, 'get_all_attachments']);
Route::delete('/prd_attachment/delete/{id}', [PRDController::class, 'delete_attachment']);



//Helper Common API

//Supplier Helper API
route::get('/supplier_category', [SupplierController::class, 'supplier_category']);
route::get('/supplier_list', [SupplierController::class, 'supplier_list_api']);


Route::get('/product_name_sku', [ProductController::class, 'get_product_name']);
Route::get('/product_name_sku/{prductorsku}', [ProductController::class, 'product_name_sku_prductorsku']);

Route::get('/uom_type',[WarehouseController::class, 'uom_type']);
Route::get('/product_category', [ProductController::class, 'getproduct_cat']);
Route::get('/product_category2/{category_id}', [ProductController::class, 'getproduct_sub_cat']);
Route::get('/product_category3/{category_id}', [ProductController::class, 'getproduct_sub_cat2']);

Route::get('/employee_name', [EmployeeController::class, 'get_employee_name']);
route::get('/war_name', [WarehouseController::class,'warehouse_name']);
route::get('/defult_warehouse_info', [WarehouseController::class,'defult_warehouse_info']);
route::get('/product_size', [ProductController::class,'size_name']);
route::get('/uom_name',[UomController::class, 'uom_name']);

route::get('/credit_terms_name', [SupplierController::class, 'get_credit_terms_name']);
Route::get('/shipping_info', [SupplierController::class, 'get_shipping_info']);
route::get('/country_name',[WarehouseController::class, 'getCountries']);
Route::get('/states/{countryName}', [WarehouseController::class, 'getStates']);
Route::get('/cities/{stateName}', [WarehouseController::class, 'getCities']);

Route::get('/price/get_excel_id', [ProductPriceController::class, 'getExcelId']);








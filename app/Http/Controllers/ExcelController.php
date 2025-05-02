<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\EmployeeNotes;
use App\Models\CustomerNote;
 use Illuminate\Support\Facades\DB;
use App\Models\Positions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Exports\EmployeeExport;
use App\Exports\CustomerExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Supplier;
use App\Models\SupplierNote;
use App\Models\SupplierCategories;
use App\Models\CreditTerm;
use App\Models\CreditName;
use App\Models\Customer;
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\ProductExport;
use App\Models\Product;
use App\Models\GRN;
use App\Exports\GrnExport;
use App\Exports\GtnExport;
use App\Exports\DgnExport;
use App\Exports\RgnExport;
use App\Exports\PrdExport;
use App\Exports\PoExport;


class ExcelController extends Controller
{
   
    public function customer_export()
    {
        $fileName = now()->format('Y-m-d') . '_CustomerList.xlsx';
    
        return Excel::download(new CustomerExport, $fileName);
    }
    public function supplier_export()
    {
        $fileName = now()->format('Y-m-d') . '_SupplierList.xlsx';

        return Excel::download(new SupplierExport, $fileName);
    }

    public function product_export()
    {
        $fileName = now()->format('Y-m-d') . '_ProductList.xlsx';

        return Excel::download(new ProductExport, $fileName);
    }

    public function grns_export()
    {
        $fileName = 'grn_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new GrnExport, $fileName);
    }


    public function gtns_export()
    {
        $fileName = 'gtn_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new GtnExport, $fileName);
    }



    public function rgns_export()
    {
        $fileName = 'rgn_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new RgnExport, $fileName);
    }
   
  
    
    public function dgns_export()
    {
        $fileName = 'dgn_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new DgnExport, $fileName);
    }




    public function prds_export()
    {
        $fileName = 'prd_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new PrdExport, $fileName);
    }

 

    public function pos_export()
    {
        $fileName = 'po_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new PoExport, $fileName);
    }


    

    

}

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
use Maatwebsite\Excel\Concerns\FromCollection;

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

}

<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeeExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Employee::all();
    }

    // public function headings(): array
    // {
    //     return [
    //         'ID',
    //         'Warehouse Name',
    //         'Country',
    //         'State',
    //         'City',
    //         'Zip Code',
    //         'Address1',
    //         'Address2',
    //         'Email',
    //         'Phone',
    //         'Warehouse Contact'
    //     ];
    // }
}

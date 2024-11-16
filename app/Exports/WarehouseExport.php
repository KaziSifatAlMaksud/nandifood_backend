<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarehouseExport implements FromCollection, withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      
        return Warehouse::select('id','warehouse_name','country','state','city','zip_code','address1','address2','email','phone','warehouse_contact')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Warehouse Name',
            'Country',
            'State',
            'City',
            'Zip Code',
            'Address1',
            'Address2',
            'Email',
            'Phone',
            'Warehouse Contact'
        ];
    }
}

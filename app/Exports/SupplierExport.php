<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all supplier data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Supplier::select([
            'id',
            'supplier_no',
            'supplier_legal_name',
            'supplier_trade_name',
            'first_name',
            'middle_name',
            'last_name',
            'position',
            'address1',
            'address2',
            'country',
            'state',
            'city',
            'zip_code',
            'email',
            'phone',
            'mobile',
            'supplier_category',
            'supplier_category_name',
            'account_manager',
            'category_manager',
            'eff_date',
            'credit_terms',
            'last_updated',
            'last_updated_by',
            'status',
            'is_approved',
            'notes',
            'notes2',
            'img'
        ])->get();
    }

    /**
     * Define the headers for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Supplier ID',
            'Supplier No.',
            'Supplier Legal Name',
            'Supplier Trade Name',
            'Contact Name',
            'Position',
            'Address 1',
            'Address 2',
            'Country',
            'State',
            'City',
            'Zip Code',
            'Email',
            'Phone',
            'Mobile',
            'Supplier Category ID',
            'Supplier Category Name',
            'Account Manager',
            'Category Manager',
            'Effective Date',
            'Credit Terms',
            'Last Updated',
            'Last Updated By',
            'Status',
            'Approval Status',
            'Notes',
            'Additional Notes',
            'Image URL'
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $supplier
     * @return array
     */
    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->supplier_no,
            $supplier->supplier_legal_name,
            $supplier->supplier_trade_name,
            trim(($supplier->first_name ?? '') . ' ' . ($supplier->middle_name ?? '') . ' ' . ($supplier->last_name ?? '')),
            $supplier->position ?? 'N/A',
            $supplier->address1,
            $supplier->address2 ?? 'N/A',
            $supplier->country,
            $supplier->state,
            $supplier->city,
            $supplier->zip_code,
            $supplier->email,
            $supplier->phone,
            $supplier->mobile ?? 'N/A',
            $supplier->supplier_category,
            $supplier->supplier_category_name ?? 'N/A',
            $supplier->account_manager ?? 'N/A',
            $supplier->category_manager ?? 'N/A',
            $supplier->eff_date,
            $supplier->credit_terms ?? 'N/A',
            $supplier->last_updated,
            $supplier->last_updated_by ?? 'N/A',
            $supplier->status,
            $supplier->is_approved,
            $supplier->notes ?? 'N/A',
            $supplier->notes2 ?? 'N/A',
            $supplier->img ?? 'N/A'
        ];
    }
}

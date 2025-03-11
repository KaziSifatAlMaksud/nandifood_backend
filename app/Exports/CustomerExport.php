<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all customer data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Customer::select([
            'id',
            'customer_no',
            'customer_legal_name',
            'customer_trade_name',
            'address1',
            'address2',
            'country',
            'state',
            'city',
            'zip_code',
            'email',
            'phone',
            'mobile',
            'first_name',
            'middle_name',
            'last_name',
            'customer_category',
            'account_manager',
            'category_manager',
            'eff_date',
            'credit_terms',
            'last_updated',
            'last_updated_by',
            'status',
            'notes'
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
            'Customer ID',
            'Customer No.',
            'Legal Name',
            'Trade Name',
            'Address 1',
            'Address 2',
            'Country',
            'State',
            'City',
            'Zip Code',
            'Email',
            'Phone',
            'Mobile',
            'First Name',
            'Middle Name',
            'Last Name',
            'Position',
            'Customer Category',
            'Account Manager',
            'Category Manager',
            'Effective Date',
            'Credit Terms',
            'Last Updated',
            'Last Updated By',
            'Status',
            'Notes'
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $customer
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->customer_no,
            $customer->customer_legal_name,
            $customer->customer_trade_name,
            $customer->address1,
            $customer->address2 ?? 'N/A',
            $customer->country,
            $customer->state,
            $customer->city,
            $customer->zip_code,
            $customer->email,
            $customer->phone,
            $customer->mobile,
            $customer->first_name,
            $customer->middle_name ?? 'N/A',
            $customer->last_name,
            $customer->customer_category,
            $customer->account_manager ?? 'N/A',
            $customer->category_manager ?? 'N/A',
            $customer->eff_date,
            $customer->credit_terms ?? 'N/A',
            $customer->last_updated,
            $customer->last_updated_by ?? 'N/A',
            $customer->status,
            $customer->notes ?? 'N/A',
        ];
    }
}

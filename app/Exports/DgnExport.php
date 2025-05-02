<?php

namespace App\Exports;

use App\Models\DGN;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export DGN (Damage Goods Note) data to Excel.
 * 
 * Fetches DGN records and formats them for Excel download.
 */
class DgnExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all DGN data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DGN::all();
    }

    /**
     * Define the headers for Excel columns.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Default Warehouse',
            'Damage Date',
            'Address 1',
            'Address 2',
            'Reference No',
            'Country',
            'State',
            'Damage Reported By',
            'City',
            'Zip Code',
            'Last Update',
            'Email',
            'Phone',
            'Status',
            'Office Phone',
            'Notes',
            'Is Approved',
            'DGN Number',
            'Supplier',
            'BOL Number',
            'Disposal Date',
            'Disposal By',
            'Last Updated By',
        ];
    }

    /**
     * Map data for each Excel row.
     *
     * @param mixed $dgn
     * @return array
     */
    public function map($dgn): array
    {
        return [
            $dgn->defult_warehouse,
            $dgn->damage_date,
            $dgn->address1,
            $dgn->address2,
            $dgn->regerence_no,
            $dgn->country,
            $dgn->state,
            $dgn->damage_reported_by,
            $dgn->city,
            $dgn->zip_code,
            $dgn->last_update,
            $dgn->email,
            $dgn->phone,
            $dgn->status,
            $dgn->office_phone,
            $dgn->notes,
            $dgn->is_approved,
            $dgn->dgn_number,
            $dgn->Supplier,
            $dgn->bol_number,
            $dgn->disposal_date,
            $dgn->disposal_by,
            $dgn->last_updated_by,
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\RGN;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export RGN (Returned Goods Note) data to Excel file.
 * 
 * This class fetches RGN records from the database,
 * maps the necessary fields, and formats them for Excel export.
 */
class RgnExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all RGN data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return RGN::all();
    }

    /**
     * Define the headers for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'RGN No',
            'Date',
            'Warehouse ID',
            'Supplier',
            'BOL No',
            'Shipping Company',
            'Returned By',
            'Status',
            'Total Amount',
            'Last Updated By',
            'Last Updated',
            'Is Approved',
            'Notes',
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $rgn
     * @return array
     */
    public function map($rgn): array
    {
        return [
            $rgn->rgn_no,
            $rgn->date,
            $rgn->warehouse_id,
            $rgn->supplier,
            $rgn->bol_no,
            $rgn->shipping_company,
            $rgn->returned_by,
            $rgn->status,
            $rgn->total_amount,
            $rgn->last_updated_by,
            $rgn->last_updated,
            $rgn->is_approve,
            $rgn->notes,
        ];
    }
}

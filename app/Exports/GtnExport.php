<?php

namespace App\Exports;

use App\Models\GTN;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export GTN (Goods Transfer Note) data to Excel file.
 * 
 * This class fetches GTN records from the database,
 * maps the necessary fields, and formats them for Excel export.
 */
class GtnExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all GTN data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return GTN::all();
    }

    /**
     * Define the headers for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'GRN Number',
            'Transfer Out Warehouse',
            'Transfer In Warehouse',
            'Date Tran Out',
            'PO ID',
            'Other Reference',
            'BOL Number',
            'BOL Date',
            'Shipping Carrier',
            'Delivery Driver',
            'Transferred Out By',
            'Status',
            'Notes',
            'Last Updated By',
            'Is Approved',
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $gtn
     * @return array
     */
    public function map($gtn): array
    {
        return [
            $gtn->grn_number,
            $gtn->transfer_out_warehouse,
            $gtn->transfer_in_warehouse,
            $gtn->date_tran_out,
            $gtn->po_id,
            $gtn->other_reference,
            $gtn->bol_number,
            $gtn->bol_date,
            $gtn->shipping_carrier,
            $gtn->delivery_driver,
            $gtn->transferred_out_by,
            $gtn->status,
            $gtn->notes,
            $gtn->last_updated_by,
            $gtn->is_approved,
        ];
    }
}

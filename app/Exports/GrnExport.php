<?php

namespace App\Exports;

use App\Models\GRN;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GrnExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Fetch all GRN records with the specified fields
        return Grn::select([
            'id',
            'receiving_warehouse_id',
            'date_received',
            'grn_no',
            'our_po',
            'shipping_carrier',
            'supplier_shipping_address',
            'bol_date',
            'delivery_driver',
            'received_by',
            'other_reference',
            'last_updated',
            'last_updated_by',
            'status',
            'grn_notes',
            'is_approve',
            'received_details',
            'bol_number',
            'supplier_invoice_no',
            'supplier',
            'other_reference'
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
            'ID',
            'Receiving Warehouse ID',
            'Date Received',
            'GRN No',
            'Our PO',
            'Shipping Carrier',
            'Supplier Shipping Address',
            'BOL Date',
            'Delivery Driver',
            'Received By',
            'Other Reference',
            'Last Updated',
            'Last Updated By',
            'Status',
            'GRN Notes',
            'Is Approved',
            'Received Details',
            'BOL Number',
            'Supplier Invoice No',
            'Supplier',
            'Other Reference'
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $grn
     * @return array
     */
    public function map($grn): array
    {
        return [
            $grn->id,
            $grn->receiving_warehouse_id,
            $grn->date_received,
            $grn->grn_no,
            $grn->our_po,
            $grn->shipping_carrier,
            $grn->supplier_shipping_address,
            $grn->bol_date,
            $grn->delivery_driver,
            $grn->received_by,
            $grn->other_reference,
            $grn->last_updated,
            $grn->last_updated_by,
            $grn->status,
            $grn->grn_notes,
            $grn->is_approve ? 'Yes' : 'No', // convert to readable
            $grn->received_details,
            $grn->bol_number,
            $grn->supplier_invoice_no,
            $grn->supplier,
            $grn->other_reference
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\PO;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export Purchase Order (PO) data to Excel.
 */
class PoExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all PO data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PO::all();
    }

    /**
     * Define the headers for Excel columns.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'PO No',
            'PO Date',
            'PO Due Date',
            'Supplier',
            'Supplier Country',
            'Supplier State',
            'Supplier City',
            'Warehouse',
            'Warehouse Country',
            'Warehouse State',
            'Warehouse City',
            'Priority',
            'Currency',
            'Amount',
            'PO Status',
            'Receiving Status',
            'Is Approved',
            'Created At',
            'Created By',
            'Updated At',
            'Updated By',
            'Notes',
        ];
    }

    /**
     * Map data for each Excel row.
     *
     * @param mixed $po
     * @return array
     */
    public function map($po): array
    {
        return [
            $po->po_no,
            $po->po_date,
            $po->po_due_date,
            $po->supplier,
            $po->supp_country,
            $po->supp_state,
            $po->supp_city,
            $po->warehouse,
            $po->war_country,
            $po->war_state,
            $po->war_city,
            $po->priority,
            $po->currency,
            $po->amount,
            $po->po_status,
            $po->receiving_status,
            $po->is_approve,
            $po->created_at,
            $po->created_by,
            $po->updated_at,
            $po->updated_by,
            $po->notes,
        ];
    }
}

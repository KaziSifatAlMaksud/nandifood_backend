<?php

namespace App\Exports;

use App\Models\PRD;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export PRD (Production Report Document) data to Excel.
 */
class PrdExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all PRD data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PRD::all();
    }

    /**
     * Define the headers for Excel columns.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Warehouse',
            'Country',
            'City',
            'State',
            'PRD Date',
            'PRD No',
            'Production Start Date',
            'Production End Date',
            'Production Supervisor',
            'Last Updated',
            'Last Updated By',
            'Status',
            'Is Approved',
            'Notes',
        ];
    }

    /**
     * Map data for each Excel row.
     *
     * @param mixed $prd
     * @return array
     */
    public function map($prd): array
    {
        return [
            $prd->warehouse,
            $prd->country,
            $prd->city,
            $prd->state,
            $prd->prd_date,
            $prd->prd_no,
            $prd->pro_start_date,
            $prd->pro_end_date,
            $prd->pro_supervisor,
            $prd->last_updated,
            $prd->last_updated_by,
            $prd->status,
            $prd->is_approve,
            $prd->notes,
        ];
    }
}

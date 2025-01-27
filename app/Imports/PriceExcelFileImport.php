<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PriceExcelFileImport implements ToCollection, WithValidation, WithHeadingRow
{
    public $data = [];

    /**
     * Process each row from the Excel file
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->data[] = [
                'price_list_id' => $row['price_list_id'] ?? null,
                'price_list_name' => $row['price_list_name'] ?? null,
                'eff_date' => $row['eff_date'] ?? null,
                'exp_date' => $row['exp_date'] ?? null,
                'status' => $row['status'] ?? null,
                'last_update' => $row['last_update'] ?? null,
                'updated_by' => $row['updated_by'] ?? null,
                'action' => $row['action'] ?? null,
                'file' => $row['file'] ?? null,
            ];
        }
    }

    /**
     * Validation rules for rows
     */
    public function rules(): array
    {
        return [
            'price_list_id' => 'nullable|string|max:50',
            'price_list_name' => 'nullable|string|max:100',
            'eff_date' => 'required|date',
            'exp_date' => 'required|date|after_or_equal:eff_date',
            'status' => 'required|string|max:10',
            'last_update' => 'required|date',
            'updated_by' => 'required|string|max:100',
            'action' => 'required|string|max:5',
            'file' => 'nullable|string|max:255',
        ];
    }

    /**
     * Retrieve imported data
     */
    public function getData(): array
    {
        return $this->data;
    }
}

<?php

namespace App\Imports;

use App\Models\PriceExcelFile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PriceExcelFileImport implements ToModel, WithValidation, WithHeadingRow
{
    use Importable;

    public $data = [];

    /**
     * Map the rows from Excel to database columns
     */
    public function model(array $row)
    {
        // Map Excel columns to database fields
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

        return null;
    }

    /**
     * Define validation rules for the rows
     */
    public function rules(): array
    {
        return [
            'price_list_id' => 'nullable|string|max:50',
            'price_list_name' => 'nullable|string|max:100',
            'eff_date' => 'required|string|max:100',
            'exp_date' => 'required|string|max:100',
            'status' => 'required|string|max:10',
            'last_update' => 'required|string|max:100',
            'updated_by' => 'required|string|max:100',
            'action' => 'required|string|max:5',
            'file' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the validation failures (if any)
     */
    public function failures()
    {
        return collect($this->getValidationFailures());
    }

    /**
     * Get the imported data (if needed for saving later)
     */
    public function getData()
    {
        return $this->data;
    }
}

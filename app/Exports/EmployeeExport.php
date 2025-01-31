<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all employee data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Employee::select([
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'position_id',
            'country',
            'status',
            'city',
            'warehouse_id',
            'email',
            'off_phone',
            'phone'
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
            'Employee No.',
            'First Name',
            'Middle Name',
            'Last Name',
            'Position',
            'Country',
            'Status',
            'City',
            'Warehouse Name',
            'Email',
            'Office Phone',
            'Mobile Phone'
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $employee
     * @return array
     */
    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->first_name,
            $employee->middle_name,
            $employee->last_name,
            $employee->position->position_id ?? 'N/A',
            $employee->country,
            $employee->status,
            $employee->city,
            $employee->warehouse->name ?? 'N/A',
            $employee->email,
            $employee->off_phone,
            $employee->phone,
        ];
    }
}

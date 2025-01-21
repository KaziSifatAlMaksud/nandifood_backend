<?php

namespace App\Exports;

use App\Models\Uom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request; // Add this import for request

class UomExport implements FromCollection, WithHeadings, WithMapping
{
    
/**
 * Fetch all data to export.
 *
 * @return \Illuminate\Support\Collection
 */
public function collection()
{
    // Initialize the query for selecting required fields
    $query = Uom::select([
        'uom_id',
        'description',
        'bulk_code',
        'unit',
        'inventory_uom',
        'production_uom',
        'purchase_uom',
        'sales_uom',
        'uom_length',
        'uom_width',
        'uom_height'
    ]);


    // Fetch the data
    $uoms = $query->get();

    // Mapping data for UOM objects
    return $uoms->map(function ($uom) {
        if ($uom->unit == 0) { // Metric
            $length_cm = $uom->uom_length;
            $width_cm = $uom->uom_width;
            $height_cm = $uom->uom_height;
            $length_in = $length_cm / 2.54;  // Convert to inches
            $width_in = $width_cm / 2.54;    // Convert to inches
            $height_in = $height_cm / 2.54;  // Convert to inches
        } else { // Imperial
            $length_in = $uom->uom_length;
            $width_in = $uom->uom_width;
            $height_in = $uom->uom_height;
            $length_cm = $length_in * 2.54;  // Convert to cm
            $width_cm = $width_in * 2.54;    // Convert to cm
            $height_cm = $height_in * 2.54;  // Convert to cm
        }

        // Fetch additional data
        $result = Uom::fullName($uom->uom_id);
        $uom->uom_type_name = $result['uom_type_name'];
        $uom->short_name = $result['short_name'];
        $uom->full_name = $result['full_name'];
        $uom->volumem3 = $result['volumem3'];
        $uom->volumeft3 = $result['volumeft3'];
        $uom->length_in = $length_in;
        $uom->width_in = $width_in;
        $uom->height_in = $height_in;
        $uom->length_cm = $length_cm;
        $uom->width_cm = $width_cm;
        $uom->height_cm = $height_cm;

        return $uom;
    });
}

    /**
     * Define the headers for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'UOM ID',
            'Description',
            'Bulk Code',
            'Inventory UOM',
            'Production UOM',
            'Purchase UOM',
            'Sales UOM',
            'UOM Type Name',
            'Short Name',
            'Full Name',
            'Unit',
            'Volume (m³)',
            'Volume (ft³)',
            'Length (cm/in)',
            'Width (cm/in)',
            'Height (cm/in)',
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $uom
     * @return array
     */
    public function map($uom): array
    {
        // Determine the unit type
        $isMetric = $uom->unit == 0;

        return [
            $uom->uom_id,
            $uom->description,
            $uom->bulk_code,
            $uom->inventory_uom,
            $uom->production_uom,
            $uom->purchase_uom,
            $uom->sales_uom,
            $uom->uom_type_name,
            $uom->short_name,
            $uom->full_name,
            $isMetric ? 'Metric' : 'Imperial',
            $uom->volumem3,
            $uom->volumeft3,
            $isMetric ? $uom->length_cm : $uom->length_in,
            $isMetric ? $uom->width_cm : $uom->width_in,
            $isMetric ? $uom->height_cm : $uom->height_in, // Corrected here
        ];
    }
}

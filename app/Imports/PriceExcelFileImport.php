<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Price;

class PriceExcelFileImport implements ToCollection, WithValidation, WithHeadingRow
{
    /**
     * Process each row from the Excel file and insert into the database
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Price::create([
                'price'                     => $row['price'] ?? null,
                'no'                        => $row['no'] ?? null,
                'country'                   => $row['country'] ?? null,
                'state'                     => $row['state'] ?? null,
                'city'                      => $row['city'] ?? null,
                'warehouse'                 => $row['warehouse'] ?? null,
                'sku'                       => $row['sku'] ?? null,
                'product_name'              => $row['product_name'] ?? null,
                'category'                  => $row['category'] ?? null,
                'sub_category1'             => $row['sub_category1'] ?? null,
                'sub_category2'             => $row['sub_category2'] ?? null,
                'inventory_uom'             => $row['inventory_uom'] ?? null,
                'size'                      => $row['size'] ?? null,
                'product_weight_in_lb'      => $row['product_weight_in_lb'] ?? null,
                'product_weight_kg'         => $row['product_weight_kg'] ?? null,
                'on_hand_qty_inventory_uom' => $row['on_hand_qty_inventory_uom'] ?? null,
                'sales_uom1'                => $row['sales_uom1'] ?? null,
                'on_hand_qty_sales_uom1'    => $row['on_hand_qty_sales_uom1'] ?? null,
                'sales_uom2'                => $row['sales_uom2'] ?? null,
                'on_hand_qty_sales_uom2'    => $row['on_hand_qty_sales_uom2'] ?? null,
                'sales_uom3'                => $row['sales_uom3'] ?? null,
                'on_hand_qty_sales_uom3'    => $row['on_hand_qty_sales_uom3'] ?? null,
            ]);
        }
    }

    /**
     * Validation rules for rows
     */
    public function rules(): array
    {
        return [
            'price'                     => 'nullable|numeric',
            'no'                        => 'nullable|string|max:50',
            'country'                   => 'nullable|string|max:100',
            'state'                     => 'nullable|string|max:100',
            'city'                      => 'nullable|string|max:100',
            'warehouse'                 => 'nullable|string|max:100',
            'sku'                       => 'required|string|max:50',
            'product_name'              => 'required|string|max:255',
            'category'                  => 'nullable|string|max:100',
            'sub_category1'             => 'nullable|string|max:100',
            'sub_category2'             => 'nullable|string|max:100',
            'inventory_uom'             => 'nullable|string|max:20',
            'size'                      => 'nullable|string|max:20',
            'product_weight_in_lb'      => 'nullable|string',
            'product_weight_kg'         => 'nullable|string',
            'on_hand_qty_inventory_uom' => 'nullable|string',
            'sales_uom1'                => 'nullable|string|max:20',
            'on_hand_qty_sales_uom1'    => 'nullable|string',
            'sales_uom2'                => 'nullable|string|max:20',
            'on_hand_qty_sales_uom2'    => 'nullable|string',
            'sales_uom3'                => 'nullable|string|max:20',
            'on_hand_qty_sales_uom3'    => 'nullable|string',
        ];
    }
}

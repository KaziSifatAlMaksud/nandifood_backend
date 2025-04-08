<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all product data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::select([
            'id',
            'p_sku_no',
            'p_long_name',
            'product_short_name',
            'product_category',
            'sub_category1',
            'sub_category2',
            'size',
            'default_sales_uom',
            'inventory_uom',
            'purchase_uom',
            'production_uom',
            'product_cert1',
            'product_cert2',
            'product_cert3',
            'product_upc',
            'default_warehouse',
            'country',
            'state',
            'city',
            'product_manager',
            'eff_date',
            'end_date',
            'last_updated_by',
            'notes'
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
            'SKU No',
            'Long Name',
            'Short Name',
            'Category',
            'Sub Category 1',
            'Sub Category 2',
            'Size',
            'Sales UOM',
            'Inventory UOM',
            'Purchase UOM',
            'Production UOM',
            'Cert 1',
            'Cert 2',
            'Cert 3',
            'UPC',
            'Warehouse',
            'Country',
            'State',
            'City',
            'Product Manager',
            'Effective Date',
            'End Date',
            'Last Updated By',
            'Notes'
        ];
    }

    /**
     * Map the data for each row.
     *
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->p_sku_no,
            $product->p_long_name,
            $product->product_short_name,
            $product->product_category,
            $product->sub_category1,
            $product->sub_category2,
            $product->size,
            $product->default_sales_uom,
            $product->inventory_uom,
            $product->purchase_uom,
            $product->production_uom,
            $product->product_cert1,
            $product->product_cert2,
            $product->product_cert3,
            $product->product_upc,
            $product->default_warehouse,
            $product->country,
            $product->state,
            $product->city,
            $product->product_manager,
            $product->eff_date,
            $product->end_date,
            $product->last_updated_by,
            $product->notes,
        ];
    }
}

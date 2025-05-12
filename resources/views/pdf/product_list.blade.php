<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - All Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            color: #000;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        table, th, td {
            border: none;
        }
        
        .header-table {
            margin-bottom: 20px;
        }
        
        .logo {
            width: 100px;
            vertical-align: top;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
        }
        
        .address {
            font-size: 12px;
            text-align: center;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 12px;
            line-height: 0.7;
        }

        .info-value {
            font-size: 12px;
            line-height: 0.7;
           
        }
        .info-label1 {
            font-weight: bold;
            font-size: 12px;
            text-align: left;
            line-height: 0.7;
        }

        .info-value1 {
            font-size: 10px;
            text-align: right; 
            line-height: 0.5;
        }
                
        .supplier-receiver {
            margin: 20px 0;
        }
        
        .supplier-receiver td {
            padding: 5px;
            vertical-align: top;
            width: 50%;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .products-table {
            margin: 20px 0;
        }
        
        .products-table th {
            background-color: #8bc34a;
            color: black;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        
        .products-table td {
            padding: 8px;
            font-size: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row td {
            padding: 8px;
            font-weight: bold;
            text-align: right;
        }
        
        .notes-table {
            margin-top: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            font-size: 14px;
        }
        
        .notes-content {
            font-size: 12px;
            padding: 5px 0;
        }
        
      
    </style>
</head>
<body>
    <div class="border-container">
        <table class="header-table">
            <tr>
                <td style="border: none; text-align:center;">
                    <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                </td>
                <td width="60%">
                    <div class="company-name">Nandi Foods</div>
                    <div class="address">7931 Coronet Road, Edmonton, Alberta T5E 4N7 CANADA  <br/> Email: info@nandifoods.com <br/>  Phone: +1 780 328 0957 <br/> Visit Us: www.nandifoods.com</div>
                </td>
                <td width="20%"></td>
            </tr>
        </table>
        
        <table>
            <tr>
                <td class="title">All Product List</td>
            </tr>
        </table>
       
        

     <br>

        
     
        <table class="products-table">
            <thead>
                <tr>
                    <th class="uom-id"  width="2%">SI</th>
                    <th class="uom-name"  width="5%">Country</th>
                    <th class="description"  width="5%">State</th>
                    <th class="hu_long_name" width="5%">City</th>
                    <th class="production-uom"  width="5%">Warehouse</th>
                    <th class="sales-uom" width="5%">SKU</th>
                    {{-- <th class="sales-uom" width="5%">UPC</th> --}}
                    <th class="sales-uom"  width="5%">Product Name</th>
                    <th class="sales-uom" width="15%">Category</th>
                    <th class="sales-uom"  width="12%">Sub-Category</th>
                    <th class="purchase-uom"  width="10%">Sub-Category 1</th>
                    <th class="purchase-uom"  width="10%">Sub-Category 2</th>
                    <th class="sales-uom" width="10%" >Inventory UOM</th>
                    <th class="sales-uom"  width="10%">Sales UOM 1</th>
                    <th class="sales-uom"  width="10%">On Hand Qty</th>
                    <th class="sales-uom"  width="10%">Sales UOM 2</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach ($products as $product)
                    <tr>
                        <td class="uom-id">{{ $i }}</td>
                        <td class="left-align">{{ $product->country ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->state ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->city ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->default_warehouse ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->p_sku_no ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->product_upc ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->p_long_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->product_category_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->sub_category1_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->sub_category2_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->inventory_uom_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->default_sales_uom_name ?? 'N/A' }}</td>
                        <td class="left-align">{{ $product->size_kg ?? 'N/A' }} kg</td>
                        <td class="left-align">{{ $product->size_lb ?? 'N/A' }} lb</td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
            </tbody>
        </table>
        

        <p class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

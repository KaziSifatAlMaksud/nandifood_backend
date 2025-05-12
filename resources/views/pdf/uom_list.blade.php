

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - All UOM List</title>
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
                <td class="title">All UOM List</td>
            </tr>
        </table>
       
        

     <br>
     @php
        // Mapping of id to uom_name
        $uomNames = [
            1 => "EACH",
            2 => "BOX",
            3 => "CARTON",
            4 => "CASE",
            5 => "CRATE",
            6 => "T-TOTE",
            7 => "PALLET",
            8 => "TEU"
        ];
    @endphp
        
     
        <table class="products-table">
            <thead>
                <tr>
                    <th class="uom-id">UOM ID</th>
                    <th class="uom-name">UOM Name</th>
                    <th class="description">Description</th>
                    <th class="inventory-uom">Inventory UOM</th>
                    <th class="production-uom">Production UOM</th>
                    <th class="purchase-uom">Purchase UOM</th>
                    <th class="sales-uom">Sales UOM</th>
                    <th class="unit">Unit</th>
                    <th class="right-align">Length</th>
                    <th class="right-align">Width</th>
                    <th class="right-align">Height</th>
                    <th class="right-align">Weight</th>
                    <th class="right-align">Bulk Code</th>
                </tr>
                
            </thead>
            <tbody>
                @foreach ($result as $uom)
                
                <tr>
                    <td class="uom-id">{{ $uom->uom_id }}</td>
                    <td class="uom-name">{{ $uomNames[$uom->uom_type_id] ?? 'Unknown' }}</td>
                    <td class="description">{{ $uom->description }}</td>
                    <td class="inventory-uom">{{ $uom->inventory_uom }}</td>
                    <td class="production-uom">{{ $uom->production_uom }}</td>
                    <td class="purchase-uom">{{ $uom->purchase_uom }}</td>
                    <td class="sales-uom">{{ $uom->sales_uom }}</td>
                    <td class="unit">
                        @if ($uom->unit == 0)
                            Matrix
                        @elseif ($uom->unit == 1)
                            Imperial
                        @else
                            Unknown
                        @endif
                    </td>
                    <td class="right-align">{{ $uom->uom_length }}</td>
                    <td class="right-align">{{ $uom->uom_width }}</td>
                    <td class="right-align">{{ $uom->uom_height }}</td>
                    <td class="right-align">{{ $uom->weight }}</td>
                    <td class="right-align">{{ $uom->bulk_code }}</td>
                </tr>
                @endforeach            
            </tbody>
        </table>
        

        <p class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

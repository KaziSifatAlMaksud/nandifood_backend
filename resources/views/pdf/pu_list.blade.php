
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - PU List</title>
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
                <td class="title">Nandi Foods - PU List</td>
            </tr>
        </table>
       
        

     <br>

        
     
        <table class="products-table">
            <thead>
            <tr>
                    <th class="uom-id" style="width: 3%;">SI</th>
                    {{-- <th class="uom-name" style="width: 10%;">HU Name</th> --}}
                    {{-- <th class="description" style="width: 15%;">Description</th> --}}
                    <th class="hu_long_name" style="width: 20%;">HU Long Name</th>
                    {{-- <th class="production-uom" style="width: 10%;">HU Short Name</th> --}}
                    <th class="purchase-uom" style="width: 7%;">Length (CM)</th>     
                    <th class="sales-uom" style="width: 7%;">Width (CM)</th>
                    <th class="sales-uom" style="width: 7%;">Height (CM)</th>
                    <th class="sales-uom" style="width: 7%;">Volume (M<sub>3</sub>)</th>
                    <th class="sales-uom" style="width: 7%;">Min Weight (Kg)</th>
                    <th class="sales-uom" style="width: 7%;">Max Weight (Kg)</th>
                    <th class="purchase-uom" style="width: 7%;">Length (IN)</th>     
                    <th class="sales-uom" style="width: 7%;">Width (IN)</th>
                    <th class="sales-uom" style="width: 7%;">Height (IN)</th>
                    <th class="sales-uom" style="width: 7%;">Volume (FT<sub>3</sub>)</th>
                    <th class="center-align" style="width: 7%;">Min Weight (Lb)</th>
                    <th class="center-align" style="width: 7%;">Max Weight (Lb)</th>
                    {{-- <th class="center-align" style="width: 5%;">SLP</th> --}}
                    <th class="center-align" style="width: 5%;">Bulk Code</th>
                </tr>

                
            </thead>
            <tbody>
                @php
                        $i = 0;
                @endphp
                @foreach ($pu_lists as $pu_list)
            
                <tr>
                    <td>{{ ++$i }}</td>
                    {{-- <td class="uom-id">{{ $pu_list->hu_pu_id }}</td> --}}
                    {{-- <td class="uom-name">{{ $pu_list->hu_name }}</td> --}}
                    {{-- <td class="description">{{ $pu_list->description }}</td> --}}
                    <td class="left-align">{{ $pu_list->full_name }}</td>
                    {{-- <td class="inventory-uom">{{ $pu_list->short_name }}</td> --}}
                    <td class="length">{{ $pu_list->length_cm }}</td>
                    <td class="width">{{ $pu_list->width_cm }}</td>
                    <td class="height">{{ $pu_list->height_cm }}</td>
                    <td class="right-align">{{ number_format($pu_list->volumem3,4) }}</td>
                    <td class="min_weight_kg">{{ $pu_list->min_weight_kg }}</td>
                    <td class="min_weight_kg">{{ $pu_list->max_weight_kg }}</td>
                    <td class="right-align">{{ $pu_list->length_in }}</td>
                    <td class="right-align">{{ $pu_list->width_in }}</td>
                    <td class="right-align">{{ $pu_list->height_in }}</td>
                    <td class="right-align">{{ $pu_list->min_weight_lb }}</td>
                    <td class="right-align">{{ $pu_list->max_weight_lb }}</td>
                <td class="right-align">{{ number_format($pu_list->volumeft3, 4) }}</td>

                    {{-- <td class="right-align"> </td> --}}
                    <td class="center-align">{{ $pu_list->bulk_code }}</td>
                </tr>
                @endforeach            
            </tbody>
        </table>
        

        <p class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

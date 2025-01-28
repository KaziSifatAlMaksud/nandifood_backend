<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nandi Foods - All Product List</title>
    <style>
        body {
            font-family: "Times New Roman", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center; 
            justify-content: flex-start; 
            text-align: center;
        }

        .container {
            width: 100%; 
            max-width: 900px;
            padding: 10px;
            box-sizing: border-box;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        header img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        h1 {   
            font-size: 14px;
            color: #333;
        }

        h4 {
            text-align: left;
            font-size: 12px;
            color: #333;
        }
        .header-info {
            display: flex;
            justify-content: space-between; 
            margin-bottom: 20px;
            width: 100%;
        }

        .left-section {
            width: 50%; 
            text-align: left;
            font-size: 12px;
        }

        .right-section {
            width: 50%;
            text-align: right;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #777;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #000;
            table-layout: fixed; 
            margin: 0 auto; 
        }

        th, td {
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
            border: 1px solid #000;
        }
        
        th.right-align, td.right-align {
            text-align: right; 
        }
        th.left-align, td.left-align {
            text-align: left; 
        }

        th {
            background-color: #f2f2f2;
        }

        @page {
            size: A4; 
            margin: 20mm;
        }
        table, th, td {
            border: 1px solid #000; 
        }

    </style>
</head>
<body>

<div class="container">

    <header>
        <table style="border: none; width: 100%; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="border: none; text-align:start;">
                   <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo" class="logo">
                </td>
                <td style="border: none; " colspan="5">
                    <h1 style="font-size: 24px; line-height: 0.2; padding-top:20px;">Nandi Foods</h1>
                    <p style="line-height: 0.5;">A Passion for Good Food</p>
                    <h1 style="font-size: 18px; line-height: 2;">All Employee List</h1>
                </td>                
            </tr>
        </table>
    </header>

    <table>
        <thead>
            <tr>
                <th class="uom-id" colspan="2" width="2%">SI</th>
                <th class="uom-name"  colspan="2" width="5%">Country</th>
                <th class="description" colspan="2" width="5%">State</th>
                <th class="hu_long_name" colspan="2" width="5%">City</th>
                <th class="production-uom" colspan="2" width="5%">Warehouse</th>
                <th class="purchase-uom"  colspan="2" width="5%">Defult Warehouse</th>     
                <th class="sales-uom" colspan="2" width="5%">SKU</th>
                <th class="sales-uom" colspan="2" width="5%">UPC</th>
                <th class="sales-uom" colspan="2" width="5%">Product Name</th>
                <th class="sales-uom" colspan="2" width="15%">Category</th>
                <th class="sales-uom" colspan="2" width="12%">Sub-Category</th>
                <th class="purchase-uom" colspan="2" width="10%">Sub-Category 1</th>    
                <th class="purchase-uom" colspan="2" width="10%">Sub-Category 2</th>      
                <th class="sales-uom" width="10%" rowspan="5">Inventory UOM</th>
                <th class="sales-uom" colspan="2" width="10%">sales UOM 1 </th>
                <th class="sales-uom" colspan="2" width="10%">On Hand Qty</th>
                 <th class="sales-uom" colspan="2" width="10%">Sales UOM 2 </th>
            </tr>
           
            
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach ($employees as $employee)
                <tr>
                    <td class="uom-id">{{ $i }}</td>
                    <td class="center-align">{{ $employee->id ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->first_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->middle_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->last_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->position_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->country ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->state ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->city ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->warehouse_id ? $employee->warehouse_id . ' - ' . $employee->warehouse_name : 'N/A' }}</td>
                    <td class="left-align">{{ $employee->email ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->off_phone ?? 'N/A' }}</td>
                    <td class="left-align">{{ $employee->phone ?? 'N/A' }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
       
        </tbody>
    </table>
    <h4 class="center-align">Printed By:  Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>

</body>
</html>

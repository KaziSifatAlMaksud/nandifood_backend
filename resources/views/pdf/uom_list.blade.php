<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
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

        /* Layout for form number, serial number, and company info */
        .header-info {
            display: flex;
            justify-content: space-between; /* Distribute content across the page */
            margin-bottom: 20px;
            width: 100%;
        }

        .left-section {
            width: 50%; /* Left side takes up 50% of the width */
            text-align: left;
            font-size: 12px;
        }

        .right-section {
            width: 50%; /* Right side takes up 50% of the width */
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
            border: 1px solid #000; /* Black border */
            table-layout: fixed; /* Fixed width for cells */
            margin: 0 auto; /* Center table horizontally */
        }

        th, td {
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
            border: 1px solid #000; /* Black border for table cells */
        }
        
        th.right-align, td.right-align {
            text-align: right; /* Right align for specific columns */
        }

        th {
            background-color: #f2f2f2;
        }

        @page {
            size: A4; /* Use A4 page size */
            margin: 20mm; /* Set page margins */
        }

        /* Ensure the table adapts to page size for smaller content */
        table, th, td {
            border: 1px solid #000; /* Set black borders for all elements */
        }

    </style>
</head>
<body>

<div class="container">

    <header>
        <!-- Company Logo -->
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo" class="logo">
        <h1>{{ $title }}</h1>
    </header>

    <h4>Date: {{ $date }}</h4>

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

    <table>
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
</div>

</body>
</html>

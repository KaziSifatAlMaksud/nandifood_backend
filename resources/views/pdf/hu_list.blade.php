<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <title>{{ $title }}</title> --}}
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
        <table style="border: none; width: 100%; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="border: none; text-align:start;">
                   <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo" class="logo">
                </td>
                <td style="border: none; " colspan="5">
                    <h1 style="font-size: 24px; line-height: 0.2; padding-top:20px;">Nandi Foods</h1>
                    <p style="line-height: 0.5;">A Passion for Good Food</p>
                    {{-- <h1 style="font-size: 18px; line-height: 2;">{{ $title }}</h1> --}}
                </td>                
            </tr>
        </table>
    </header>
    

    {{-- <h4>Date: {{ $date }}</h4> --}}

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
    {{ $pu_lists }}
    <table>
        <thead>
            <tr>
                <th class="uom-id">HU ID</th>
                <th class="uom-name">HU Name</th>
                <th class="description">Description</th>
                <th class="hu_long_name">HU Long Name</th>
                <th class="production-uom">HU Short Name</th>
                <th class="purchase-uom">Leanth (CM)</th>     
                <th class="sales-uom">Weith (CM)</th>
                <th class="sales-uom">Hight (CM)</th>
                <th class="sales-uom">Volume (M <sub>3</sub>)</th>
                <th class="sales-uom">Minimum Weight</th>
                <th class="sales-uom">Maximum Weight</th>
                <th class="purchase-uom">Leanth (IN)</th>     
                <th class="sales-uom">Weith (IN)</th>
                <th class="sales-uom">Hight (IN)</th>
                <th class="sales-uom">Volume (FT <sub>3</sub>)</th>
                <th class="center-align">Maximum Weight</th>
                <th class="center-align">Maximum Weight</th>
                <th class="center-align">SLP</th>
                <th class="center-align">Bulk Code</th>
            </tr>
            
        </thead>
        <tbody>
            @foreach ($pu_lists as $pu_list)
            
            <tr>
                <td class="uom-id">{{ $pu_list->hu_pu_id }}</td>
                <td class="uom-name">{{ $uomNames[$pu_list->uom_type_id] ?? 'Unknown' }}</td>
                <td class="description">{{ $pu_list->description }}</td>
                <td class="inventory-uom">{{ $pu_list->inventory_uom }}</td>
                <td class="production-uom">{{ $pu_list->production_uom }}</td>
                <td class="purchase-uom">{{ $pu_list->purchase_uom }}</td>
                <td class="sales-uom">{{ $pu_list->sales_uom }}</td>
                <td class="unit">
                    @if ($pu_list->unit == 0)
                        Matrix
                    @elseif ($pu_list->unit == 1)
                        Imperial
                    @else
                        Unknown
                    @endif
                </td>
                <td class="right-align">{{ $pu_list->uom_length }}</td>
                <td class="right-align">{{ $pu_list->uom_width }}</td>
                <td class="right-align">{{ $pu_list->uom_height }}</td>
                <td class="right-align">{{ $pu_list->weight }}</td>
                <td class="right-align">{{ $pu_list->bulk_code }}</td>
            </tr>
            @endforeach            
        </tbody>
    </table>
    {{-- <h4 class="center-align">Printed By: {{ $date }}</h4> --}}
</div>

</body>
</html>

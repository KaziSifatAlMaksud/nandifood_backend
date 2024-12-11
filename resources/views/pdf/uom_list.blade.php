<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            width: 100%;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1, h3 {
            font-size: 14px;
            color: #333;
        }
        h6 {
            font-size: 10px;
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            table-layout: auto; /* Ensure the table adjusts to the content */
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #777;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
        }

        /* Handling large tables */
        @media (max-width: 576px) {
            table {
                font-size: 10px;
                overflow-x: auto;
                display: block;
                white-space: nowrap; /* Prevent wrapping of table content */
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <!-- Company Logo -->
        <img src="{{ asset('storage/company-logo.png') }}" alt="Company Logo" class="logo">
        <h3>{{ $title }}</h3>
    </header>

    <h6>Date: {{ $date }}</h6>

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
                <th>UOM ID</th>
                <th>UOM Name</th> 
                <th>Description</th>
                <th>Inventory UOM</th>
                <th>Production UOM</th>
                <th>Purchase UOM</th>
                <th>Sales UOM</th>
                <th>Unit</th>
                <th>Length</th>
                <th>Width</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Bulk Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $uom)
                <tr>
                    <td>{{ $uom->uom_id }}</td>
                    <td>{{ $uomNames[$uom->uom_type_id] ?? 'Unknown' }}</td>
                    <td>{{ $uom->description }}</td>
                    <td>{{ $uom->inventory_uom }}</td>
                    <td>{{ $uom->production_uom }}</td>
                    <td>{{ $uom->purchase_uom }}</td>
                    <td>{{ $uom->sales_uom }}</td>
                    <td>
                        @if ($uom->unit == 0)
                            Matrix
                        @elseif ($uom->unit == 1)
                            Imparial
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>{{ $uom->uom_length }}</td>
                    <td>{{ $uom->uom_width }}</td>
                    <td>{{ $uom->uom_height }}</td>
                    <td>{{ $uom->weight }}</td>
                    <td>{{ $uom->bulk_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>

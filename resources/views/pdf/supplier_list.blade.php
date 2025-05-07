<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $page_title ?? 'Document Title' }}</title> <!-- Dynamic title -->
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
            max-width: 1200px;
            padding: 10px;
            box-sizing: border-box;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%; 
            border: none;
            table-layout: fixed; 
            margin-bottom: 20px;
        }

        .header-table td {
            padding: 5px;
        }

        .header-table .company-name {
            font-size: 22px;
            font-weight: bold;
        }

        .header-table .address {
            font-size: 12px;
            line-height: 1.5;
        }

        .title-table {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .title-table td.title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
        }

        /* Print A4 Landscape */
        @media print {
            @page {
                size: A4 landscape; 
                margin: 10mm;
            }
            body {
                width: 100%;
                height: auto;
            }
            .container {
                max-width: 100%;
                padding: 0;
            }
            table th, table td {
                font-size: 9px;
            }
            .header-table .company-name {
                font-size: 20px;
            }
            .header-table .address {
                font-size: 10px;
            }
        }

        table, th, td {
            border: 1px solid #000;
            border-collapse: collapse;
        }

        th, td {
            text-align: center;
            padding: 5px;
            font-size: 10px;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <header>
        <table class="header-table">
            <tr>
                <td style="border: none; text-align:start;">
                    <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                </td>
                <td width="60%">
                    <div class="company-name">Nandi Foods</div>
                    <div class="address">
                        7931 Coronet Road, Edmonton, Alberta T5E 4N7 CANADA <br/>
                        Email: info@nandifoods.com <br/>
                        Phone: +1 780 328 0957 <br/>
                        Visit Us: www.nandifoods.com
                    </div>
                </td>
                <td width="20%"></td>
            </tr>
        </table>
    </header>

    <!-- Dynamic Title Section -->
    <table class="title-table">
        <tr>
            <td class="title">{{ $page_title ?? 'Good Received Note' }}</td> <!-- Dynamic Title -->
        </tr>
    </table>

    <!-- Content Table (Suppliers Data) -->
    <table>
        <thead>
            <tr>
                <th>SI</th>
                <th>Supplier ID</th>
                <th>Supplier No</th>
                <th>Legal Name</th>
                <th>Trade Name</th>
                <th>Address</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Zip Code</th>
                <th>Email</th>
                <th>Phone</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Category</th>
                <th>Date</th>
                <th>Credit Terms</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($suppliers as $supplier)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $supplier->id ?? 'N/A' }}</td>
                    <td>{{ $supplier->supplier_no ?? 'N/A' }}</td>
                    <td>{{ $supplier->supplier_legal_name ?? 'N/A' }}</td>
                    <td>{{ $supplier->supplier_trade_name ?? 'N/A' }}</td>
                    <td>{{ $supplier->address1 ?? 'N/A' }} {{ $supplier->address2 ?? '' }}</td>
                    <td>{{ $supplier->country ?? 'N/A' }}</td>
                    <td>{{ $supplier->state ?? 'N/A' }}</td>
                    <td>{{ $supplier->city ?? 'N/A' }}</td>
                    <td>{{ $supplier->zip_code ?? 'N/A' }}</td>
                    <td>{{ $supplier->email ?? 'N/A' }}</td>
                    <td>{{ $supplier->phone ?? 'N/A' }}</td>
                    <td>{{ $supplier->first_name ?? 'N/A' }}</td>
                    <td>{{ $supplier->last_name ?? 'N/A' }}</td>
                    <td>{{ $supplier->supplier_category_name ?? 'N/A' }}</td>
                    <td>{{ $supplier->eff_date ?? 'N/A' }}</td>
                    <td>{{ $supplier->credit_terms ?? 'N/A' }}</td>
                </tr>
                @php $i++; @endphp
            @endforeach
        </tbody>
    </table>

    <h4 class="center-align">Printed By:  Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>

</body>
</html>

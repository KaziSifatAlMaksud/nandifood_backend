<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nandi Foods - All Supplier List</title>
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
                    <h1 style="font-size: 18px; line-height: 2;">All Supplier List</h1>
                </td>                
            </tr>
        </table>
    </header>

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

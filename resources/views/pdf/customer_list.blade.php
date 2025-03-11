<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nandi Foods - All Customer List</title>
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
                    <h1 style="font-size: 18px; line-height: 2;">All Customer List</h1>
                </td>                
            </tr>
        </table>
    </header>

    <table>
        <thead>
            <tr>
                <th class="uom-id" width="2%">SI</th>
                <th class="uom-name" width="5%">Customer ID</th>
                <th class="description" width="5%">Customer No</th>
                <th class="hu_long_name" width="10%">Legal Name</th>
                <th class="hu_long_name" width="10%">Trade Name</th>
                <th class="production-uom" width="10%">Address</th>
                <th class="sales-uom" width="5%">Country</th>     
                <th class="sales-uom" width="5%">State</th>
                <th class="sales-uom" width="5%">City</th>
                <th class="sales-uom" width="5%">Zip Code</th>
                <th class="sales-uom" width="15%">Email</th>
                <th class="sales-uom" width="10%">Phone</th>
                <th class="purchase-uom" width="5%">First Name</th>     
                <th class="sales-uom" width="5%">Last Name</th>
                <th class="sales-uom" width="5%">Category</th>
                <th class="sales-uom" width="5%">Date</th>
                <th class="sales-uom" width="5%">Credit Terms</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($customers as $customer)
                <tr>
                    <td class="uom-id">{{ $i }}</td>
                    <td class="center-align">{{ $customer->id ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->customer_no ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->customer_legal_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->customer_trade_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->address1 ?? 'N/A' }} {{ $customer->address2 ?? '' }}</td>
                    <td class="left-align">{{ $customer->country ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->state ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->city ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->zip_code ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->email ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->phone ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->first_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->last_name ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->customer_category ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->eff_date ?? 'N/A' }}</td>
                    <td class="left-align">{{ $customer->credit_terms ?? 'N/A' }}</td>
                </tr>
                @php $i++; @endphp
            @endforeach
        </tbody>
        
        
    </table>
    <h4 class="center-align">Printed By:  Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>

</body>
</html>

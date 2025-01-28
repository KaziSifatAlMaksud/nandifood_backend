<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nandi Foods - All Employee List</title>
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
        th.left-align, td.left-align {
            text-align: left; /* Right align for specific columns */
        }

        th {
            background-color: #f2f2f2;
        }

        @page {
            size: A4; /* Use A4 page size */
            margin: 20mm; /* Set page margins */
        }
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
                    <h1 style="font-size: 18px; line-height: 2;">All Employee List</h1>
                </td>                
            </tr>
        </table>
    </header>

    <table>
        <thead>
            <tr>
                <th class="uom-id" width="2%">SI</th>
                <th class="uom-name" width="5%">Employee ID</th>
                <th class="description" width="5%">First Name</th>
                <th class="hu_long_name" width="5%">Middle Name</th>
                <th class="production-uom" width="5%">Last Name</th>
                <th class="purchase-uom" width="5%">Position</th>     
                <th class="sales-uom" width="5%">Country</th>
                <th class="sales-uom" width="5%">State</th>
                <th class="sales-uom" width="5%">City</th>
                <th class="sales-uom" width="15%">Warehouse</th>
                <th class="sales-uom" width="12%">Email</th>
                <th class="purchase-uom" width="10%">Office Phone</th>     
                <th class="sales-uom" width="10%">Mobile</th>
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

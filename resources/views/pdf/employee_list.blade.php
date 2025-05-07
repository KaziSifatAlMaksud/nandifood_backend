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
            max-width: 1000px;
            padding: 10px;
            box-sizing: border-box;
        }

        header {
            margin-bottom: 20px;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 20%;
            text-align: left;
        }

        .header-center {
            width: 60%;
            text-align: center;
        }

        .header-right {
            width: 20%;
            text-align: right;
            font-size: 12px;
            color: #555;
        }

        header img {
            max-width: 120px;
        }

        h1 {   
            font-size: 20px;
            color: #333;
            margin: 5px 0;
        }

        h2 {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
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
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo">
                </td>
                <td class="header-center">
                    <h1>Nandi Foods</h1>
                    <p style="margin:0;">A Passion for Good Food</p>
                    <h2>All Employee List</h2>
                </td>
                <td class="header-right">
                    Printed By: <br>
                    Printed Time: <br>
                    {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}
                </td>
            </tr>
        </table>
    </header>

    <table>
        <thead>
            <tr>
                <th width="2%">SI</th>
                <th width="5%">Employee ID</th>
                <th width="5%">First Name</th>
                <th width="5%">Middle Name</th>
                <th width="5%">Last Name</th>
                <th width="5%">Position</th>     
                <th width="5%">Country</th>
                <th width="5%">State</th>
                <th width="5%">City</th>
                <th width="15%">Warehouse</th>
                <th width="12%">Email</th>
                <th width="10%">Office Phone</th>     
                <th width="10%">Mobile</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $employee->id ?? 'N/A' }}</td>
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

</div>

</body>
</html>

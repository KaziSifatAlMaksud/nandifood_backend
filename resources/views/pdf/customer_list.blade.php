

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
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center; 
            justify-content: flex-start; 
            text-align: center;
        }

        .container {
            width: 100%; 
            max-width: 100%; /* full width for print */
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
            font-size: 16px;
            color: #333;
            margin: 10px 0;
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
            table-layout: fixed; 
            margin: 0 auto; 
        }

        th, td {
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
            border: 1px solid #000;
            padding: 4px;
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

        /* Make header-table completely borderless */
        .header-table, 
        .header-table tr, 
        .header-table td, 
        .header-table th {
            border: none !important;
        }

        /* Print styles */
        @page {
            size: A4 landscape; 
            margin: 15mm;
        }

        @media print {
            body {
                width: 100%;
                height: auto;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }

    </style>
</head>
<body>

<div class="container">

    <header>
        <table class="header-table" style="width: 100%;">
            <tr>
                <td style="text-align:start;">
                    <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                </td>
               <td width="60%">
                    <div class="company-name" style="font-weight:bold; font-size:24px;">Nandi Foods</div>
                    <div class="address" style="font-size:14px; line-height: 1.6;">
                        7931 Coronet Road, Edmonton, Alberta T5E 4N7 CANADA  <br/> 
                        Email: info@nandifoods.com <br/>  
                        Phone: +1 780 328 0957 <br/> 
                        Visit Us: www.nandifoods.com
                    </div>
                </td>

                <td width="20%"></td>
            </tr>
        </table>

        <h1>All Customer List</h1>
    </header>

    <table>
        <thead>
            <tr>
                <th width="2%">SI</th>
                <th width="5%">Customer ID</th>
                <th width="5%">Customer No</th>
                <th width="10%">Legal Name</th>
                <th width="10%">Trade Name</th>
                <th width="10%">Address</th>
                <th width="5%">Country</th>     
                <th width="5%">State</th>
                <th width="5%">City</th>
                <th width="5%">Zip Code</th>
                <th width="15%">Email</th>
                <th width="10%">Phone</th>
                <th width="5%">First Name</th>     
                <th width="5%">Last Name</th>
                <th width="5%">Category</th>
                <th width="5%">Date</th>
                <th width="5%">Credit Terms</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $customer->id ?? 'N/A' }}</td>
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

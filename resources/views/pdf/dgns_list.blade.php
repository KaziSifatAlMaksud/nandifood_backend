<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - Damaged Goods Note (DGN)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            color: #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: none;
        }

        .header-table {
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            vertical-align: top;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
        }

        .address {
            font-size: 12px;
            text-align: center;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        .products-table th {
            background-color: #8bc34a;
            color: black;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .products-table td {
            padding: 8px;
            font-size: 12px;
            border-bottom: 1px solid #ddd;
        }

        .center-align {
            text-align: center;
        }

        .left-align {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="border-container">
        <table class="header-table">
            <tr>
                <td style="text-align: center;">
                    <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                </td>
                <td width="60%">
                    <div class="company-name">Nandi Foods</div>
                    <div class="address">
                        7931 Coronet Road, Edmonton, Alberta T5E 4N7 CANADA<br/>
                        Email: info@nandifoods.com<br/>
                        Phone: +1 780 328 0957<br/>
                        Visit Us: www.nandifoods.com
                    </div>
                </td>
                <td width="20%"></td>
            </tr>
        </table>

        <div class="title">Damaged Goods Note (DGN)</div>

        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 3%;">SI</th>
                    <th style="width: 10%;">DGN No</th>
                    <th style="width: 10%;">Damage Date</th>
                    <th style="width: 12%;">Warehouse</th>
                    <th style="width: 12%;">Supplier</th>
                    <th style="width: 10%;">BOL No</th>
                    <th style="width: 12%;">Reported By</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 10%;">Disposal Date</th>
                    <th style="width: 10%;">Disposal By</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
                @foreach ($dgn_lists as $dgn)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $dgn->dgn_number }}</td>
                        <td>{{ $dgn->damage_date }}</td>
                        <td class="left-align">{{ $dgn->defult_warehouse }}</td>
                        <td class="left-align">{{ $dgn->supplier ?? 'N/A' }}</td>
                        <td>{{ $dgn->bol_number }}</td>
                        <td>{{ $dgn->damage_reported_by }}</td>
                        <td>{{ ucfirst($dgn->status) }}</td>
                        <td>{{ $dgn->disposal_date }}</td>
                        <td>{{ $dgn->disposal_by }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>
            Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}
        </p>
    </div>
</body>
</html>

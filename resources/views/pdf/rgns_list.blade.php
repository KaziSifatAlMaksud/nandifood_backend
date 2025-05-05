<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - RGN List</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 20px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            word-wrap: break-word;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .left-align {
            text-align: left;
        }

        .right-align {
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #777;
        }

        @page {
            size: A4;
            margin: 20mm;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; text-align: left;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo">
                </td>
                <td style="border: none;" colspan="5">
                    <h1 style="font-size: 24px; line-height: 0.2; padding-top:20px;">Nandi Foods</h1>
                    <p style="line-height: 0.5;">A Passion for Good Food</p>
                    <h1 style="font-size: 18px; line-height: 2;">Returned Goods Note (RGN) List</h1>
                </td>
            </tr>
        </table>
    </header>

    <table>
        <thead>
        <tr>
            <th style="width: 3%;">SI</th>
            <th style="width: 10%;">RGN No</th>
            <th style="width: 10%;">Date</th>
            <th style="width: 12%;">Warehouse</th>
            <th style="width: 12%;">Supplier</th>
            <th style="width: 10%;">BOL No</th>
            <th style="width: 12%;">Shipping Company</th>
            <th style="width: 10%;">Returned By</th>
            <th style="width: 8%;">Status</th>
            <th style="width: 10%;">Total Amount</th>
        </tr>
        </thead>
        <tbody>
        @php $i = 0; @endphp
        @foreach ($rgn_lists as $rgn)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $rgn->rgn_no }}</td>
                <td>{{ $rgn->date }}</td>
                <td class="left-align">{{ $rgn->warehouse_id }}</td>
                <td class="left-align">{{ $rgn->supplier }}</td>
                <td>{{ $rgn->bol_no }}</td>
                <td>{{ $rgn->shipping_company }}</td>
                <td>{{ $rgn->returned_by }}</td>
                <td>{{ ucfirst($rgn->status) }}</td>
                <td class="right-align">{{ number_format($rgn->total_amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h4 class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>
</body>
</html>

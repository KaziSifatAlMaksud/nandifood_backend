<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - PRD List</title>
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
                    <h1 style="font-size: 18px; line-height: 2;">Production (PRD) List</h1>
                </td>
            </tr>
        </table>
    </header>

    <table>
        <thead>
        <tr>
            <th>SI</th>
            <th>PRD No</th>
            <th>Date</th>
            <th>Warehouse</th>
            <th>Country</th>
            <th>State</th>
            <th>City</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Supervisor</th>
            <th>Status</th>
            <th>Is Approved</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        @php $i = 0; @endphp
        @foreach ($prd_lists as $prd)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $prd->prd_no }}</td>
                <td>{{ $prd->prd_date }}</td>
                <td class="left-align">{{ $prd->warehouse }}</td>
                <td>{{ $prd->country }}</td>
                <td>{{ $prd->state }}</td>
                <td>{{ $prd->city }}</td>
                <td>{{ $prd->pro_start_date }}</td>
                <td>{{ $prd->pro_end_date }}</td>
                <td class="left-align">{{ $prd->pro_supervisor }}</td>
                <td>{{ ucfirst($prd->status) }}</td>
                <td>{{ $prd->is_approve ? 'Yes' : 'No' }}</td>
                <td class="left-align">{{ $prd->notes }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h4 class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>
</body>
</html>

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
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            font-size: 12px;
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
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
    </style>
</head>
<body>

<div class="container">
   <header>
        <!-- Company Logo -->
        <img src="{{ asset('storage/company-logo.png') }}" alt="Company Logo" class="logo">
        <h3>{{ $title }}</h3>
        {{-- <p>{{ $company_name }} | {{ $company_contact }}</p>  <!-- Uncomment this to show company details --> --}}
    </header>


    <h6>Date: {{ $date }}</h6>

    <table>
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Warehouse Name</th>
                <th>Warehouse Address</th>
                <th>Warehouse Capacity (kg)</th>
                <th>Warehouse Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $warehouse)
                <tr>
                    <td>{{ $warehouse->id }}</td>
                    <td>{{ $warehouse->warehouse_name }}</td>
                    <td>{{ $warehouse->address1 }}</td>
                    <td>{{ $warehouse->warehouse_capacity_in_kg }}</td>
                    <td>{{ $warehouse->volume }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <div class="footer">
        <p>&copy; {{ date('Y') }} {{ $company_name }}. All rights reserved.</p>
        <p>{{ $company_address }} | Phone: {{ $company_phone }} | Email: {{ $company_email }}</p>
    </div> --}}
</div>

</body>
</html>

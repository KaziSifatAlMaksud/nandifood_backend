
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - All Warehouse List</title>
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
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 12px;
            line-height: 0.7;
        }

        .info-value {
            font-size: 12px;
            line-height: 0.7;
           
        }
        .info-label1 {
            font-weight: bold;
            font-size: 12px;
            text-align: left;
            line-height: 0.7;
        }

        .info-value1 {
            font-size: 10px;
            text-align: right; 
            line-height: 0.5;
        }
                
        .supplier-receiver {
            margin: 20px 0;
        }
        
        .supplier-receiver td {
            padding: 5px;
            vertical-align: top;
            width: 50%;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .products-table {
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
        
        .total-row td {
            padding: 8px;
            font-weight: bold;
            text-align: right;
        }
        
        .notes-table {
            margin-top: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            font-size: 14px;
        }
        
        .notes-content {
            font-size: 12px;
            padding: 5px 0;
        }
        
      
    </style>
</head>
<body>
    <div class="border-container">
        <table class="header-table">
            <tr>
                <td style="border: none; text-align:center;">
                    <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                </td>
                <td width="60%">
                    <div class="company-name">Nandi Foods</div>
                    <div class="address">7931 Coronet Road, Edmonton, Alberta T5E 4N7 CANADA  <br/> Email: info@nandifoods.com <br/>  Phone: +1 780 328 0957 <br/> Visit Us: www.nandifoods.com</div>
                </td>
                <td width="20%"></td>
            </tr>
        </table>
        
        <table>
            <tr>
                <td class="title">All Warehouse List</td>
            </tr>
        </table>
       
        

     <br>
        <table class="products-table">
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
        

        <p class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>






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
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png')) }}" alt="Nandi Foods Logo" class="logo">
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

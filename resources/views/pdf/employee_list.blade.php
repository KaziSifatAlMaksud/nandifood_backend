
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - All Employee List</title>
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
                <td class="title">All Employee List</td>
            </tr>
        </table>
     <br>
     <table class="products-table">
     <thead>
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
        <p class="center-align">Printed By: __________ | Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>



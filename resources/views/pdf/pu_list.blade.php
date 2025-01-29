<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nandi Foods - PU List</title>
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
        th.left-align, td.left-align {
            text-align: left; 
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
                        <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Nandi Foods Logo" class="logo">
                    </td>

                <td style="border: none; " colspan="5">
                    <h1 style="font-size: 24px; line-height: 0.2; padding-top:20px;">Nandi Foods</h1>
                    <p style="line-height: 0.5;">A Passion for Good Food</p>
                    <h1 style="font-size: 18px; line-height: 2;"> Purchase Unit (PU) List </h1>
                </td>                
            </tr>
        </table>
    </header>
    

    {{-- <h4>Date: {{ $date }}</h4> --}}
    <table>
        <thead>
           <tr>
                <th class="uom-id" style="width: 3%;">SI</th>
                {{-- <th class="uom-name" style="width: 10%;">HU Name</th> --}}
                {{-- <th class="description" style="width: 15%;">Description</th> --}}
                <th class="hu_long_name" style="width: 20%;">HU Long Name</th>
                {{-- <th class="production-uom" style="width: 10%;">HU Short Name</th> --}}
                <th class="purchase-uom" style="width: 7%;">Length (CM)</th>     
                <th class="sales-uom" style="width: 7%;">Width (CM)</th>
                <th class="sales-uom" style="width: 7%;">Height (CM)</th>
                <th class="sales-uom" style="width: 7%;">Volume (M<sub>3</sub>)</th>
                <th class="sales-uom" style="width: 7%;">Min Weight (Kg)</th>
                <th class="sales-uom" style="width: 7%;">Max Weight (Kg)</th>
                <th class="purchase-uom" style="width: 7%;">Length (IN)</th>     
                <th class="sales-uom" style="width: 7%;">Width (IN)</th>
                <th class="sales-uom" style="width: 7%;">Height (IN)</th>
                <th class="sales-uom" style="width: 7%;">Volume (FT<sub>3</sub>)</th>
                <th class="center-align" style="width: 7%;">Min Weight (Lb)</th>
                <th class="center-align" style="width: 7%;">Max Weight (Lb)</th>
                {{-- <th class="center-align" style="width: 5%;">SLP</th> --}}
                <th class="center-align" style="width: 5%;">Bulk Code</th>
            </tr>

            
        </thead>
        <tbody>
             @php
                    $i = 0;
            @endphp
            @foreach ($pu_lists as $pu_list)
           
            <tr>
                <td>{{ ++$i }}</td>
                {{-- <td class="uom-id">{{ $pu_list->hu_pu_id }}</td> --}}
                {{-- <td class="uom-name">{{ $pu_list->hu_name }}</td> --}}
                {{-- <td class="description">{{ $pu_list->description }}</td> --}}
                 <td class="left-align">{{ $pu_list->full_name }}</td>
                {{-- <td class="inventory-uom">{{ $pu_list->short_name }}</td> --}}
                <td class="length">{{ $pu_list->length_cm }}</td>
                 <td class="width">{{ $pu_list->width_cm }}</td>
                <td class="height">{{ $pu_list->height_cm }}</td>
                <td class="right-align">{{ number_format($pu_list->volumem3,4) }}</td>
                <td class="min_weight_kg">{{ $pu_list->min_weight_kg }}</td>
                <td class="min_weight_kg">{{ $pu_list->max_weight_kg }}</td>
                <td class="right-align">{{ $pu_list->length_in }}</td>
                <td class="right-align">{{ $pu_list->width_in }}</td>
                <td class="right-align">{{ $pu_list->height_in }}</td>
                <td class="right-align">{{ $pu_list->min_weight_lb }}</td>
                <td class="right-align">{{ $pu_list->max_weight_lb }}</td>
               <td class="right-align">{{ number_format($pu_list->volumeft3, 4) }}</td>

                 {{-- <td class="right-align"> </td> --}}
                  <td class="center-align">{{ $pu_list->bulk_code }}</td>
            </tr>
            @endforeach            
        </tbody>
    </table>
    <h4 class="center-align">Printed By:  Printed Time: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</h4>
</div>

</body>
</html>

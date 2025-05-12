<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - Good Received Note</title>
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
            line-height: 1;
        }

        .info-value1 {
            font-size: 10px;
            text-align: right; 
            line-height: 0.9;
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
                <td style="border: none; text-align:start;">
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
                <td class="title">Good Transfer Note (GTN)</td>
            </tr>
        </table>
        <table class="info-table">
            <tr>
                <td width="70%"></td>
                <td width="30%">
                    <table>
                        <tr>
                            <td class="info-label1">Date Transferred Out:</td>
                            <td class="info-value1">
                                {{ 
                                    !empty($dgns->date_tran_out) 
                                        ? \Carbon\Carbon::parse(preg_replace('/\s*\(.*\)$/', '', $dgns->date_tran_out))->format('d-m-Y') 
                                        : 'N/A' 
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label1">Goods Transfer Notes No:</td>
                            <td class="info-value1">{{ $gtns->grn_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">PO No:</td>
                            <td class="info-value1">{{ $gtns->po_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">BOL No:</td>
                            <td class="info-value1">{{ $gtns->bol_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">Reference Number:</td>
                            <td class="info-value1">{{ $gtns->other_reference ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">Transfered Out by:</td>
                            @php
                                $employee = $gtns->transferred_out_by ? \App\Models\Employee::find($gtns->transferred_out_by) : null;
                            @endphp
                            <td class="info-value1">
                                {{ $employee ? ($employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name .' ('.$gtns->transferred_out_by .')'  ?? 'N/A') : 'N/A' }}
                            </td>

                        </tr>
                    </table>
                    
                </td>
            </tr>
        </table>

     <br>
        <table class="supplier-receiver">
            <tr>
              
                <td width="60%" valign="top">
                    <div class="section-title">Transfer Out Warehouse:</div>
                    <table>
                        @if($out_warhouse)
                            <tr>
                                <td class="info-value">{{ $out_warhouse->warehouse_name ?? 'N/A' }} ({{ $out_warhouse->id ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b> Contact: </b> Dinal Mahlangui</td> {{-- This is hardcoded --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Address:</b> {{ $out_warhouse->address1 ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>City: </b> {{ $out_warhouse->city ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>State:</b> {{ $out_warhouse->state ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>ZipCode:</b> {{ $out_warhouse->zip_code ?? 'N/A' }} </td> {{-- Fixed this --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Country: </b> {{ $out_warhouse->country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>E-mail:</b> {{ $out_warhouse->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>Phone: </b> {{ $out_warhouse->warehouse_contact ?? 'N/A' }}</td>
                            </tr>
                        @endif

                    </table>
                </td>
                <td width="40%" valign="top">
                    <div class="section-title">Transfer In Warehouse:</div>
                    <table>
                        @if($in_warehouse)
                            <tr>
                                <td class="info-value">{{ $in_warehouse->warehouse_name ?? 'N/A' }} ({{ $in_warehouse->id ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b> Contact: </b> Dinal Mahlangui</td> {{-- This is hardcoded --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Address:</b> {{ $in_warehouse->address1 ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>City: </b> {{ $in_warehouse->city ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>State:</b> {{ $in_warehouse->state ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>ZipCode:</b> {{ $in_warehouse->zip_code ?? 'N/A' }} </td> {{-- Fixed this --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Country: </b> {{ $in_warehouse->country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>E-mail:</b> {{ $in_warehouse->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>Phone: </b> {{ $in_warehouse->warehouse_contact ?? 'N/A' }}</td>
                            </tr>
                        @endif

                    </table>
                </td>
            </tr>
        </table>
     
        <table class="products-table">
            <tr>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>UOM</th>
                <th>Batch No.</th>
                <th>Expiration Date</th>
                <th>Qty Transferred Out</th>
                <th>Unit Cost</th>
                <th>Total Amount</th>
                <th>Transferred Out</th>
            </tr>
            @php
                $totalAmount = 0; 
            @endphp
            @foreach ($gtns->transferOutDetail as $receiving_detail)
            <tr>
                <td>{{ $receiving_detail->sku }}</td>
                <td>{{ $receiving_detail->product_name }}</td>
                <td>{{ $receiving_detail->size }}</td>
                <td>{{ $receiving_detail->uom }}</td>
                <td>{{ $receiving_detail->batch_no ?? 'N/A' }}</td>
                <td>{{ $receiving_detail->expiration_date ?? 'N/A' }}</td>
                <td>{{ $receiving_detail->qty_transferred_out ?? 0 }}</td>
                <td>${{ number_format($receiving_detail->unit_cost ?? 0, 2) }}</td>
                <td>${{ number_format($receiving_detail->total_amount ?? 0, 2) }}</td>
                <td>{{ $receiving_detail->transfer ? '✓' : '✗' }}</td>
            </tr>   
            @php
                $totalAmount += $receiving_detail->total_amount ?? 0;
            @endphp     
            @endforeach
        
            <tr>
                <td colspan="8" class="total-row">Total:</td>
                <td>${{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </table>
        
        <table class="notes-table">
            <tr>
                <td class="notes-title">Receiving Notes</td>
            </tr>
            <tr>
                <td class="notes-content">{{ $rgns->grn_notes ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
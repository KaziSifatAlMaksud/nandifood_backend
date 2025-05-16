<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nandi Foods - Purchase Order (PO)</title>
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
                <td class="title">Purchase Order (PO)</td>
            </tr>
        </table>
        
        <table class="info-table">
            <tr>
                <td width="70%"></td>
                <td width="30%">
                    <table>
                        <tr>
                            <td class="info-label1">Date Received:</td>
                            <td class="info-value1">
                                {{ 
                                    !empty($dgns->rgn_date) 
                                        ? \Carbon\Carbon::parse(preg_replace('/\s*\(.*\)$/', '', $dgns->rgn_date))->format('d-m-Y') 
                                        : 'N/A' 
                                }}
                              
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label1">Goods Receive Note No:</td>
                            <td class="info-value1">{{ $pos->rgn_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">Bol No:</td>
                            <td class="info-value1">{{ $pos->bol_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label1">Shipping Company:</td>
                            <td class="info-value1">{{ $pos->shipping_company ?? 'N/A' }}</td>
                        </tr>
                      
                        <tr>
                            <td class="info-label1">Returned by:</td>
                            @php
                                $employee = $pos->returned_by ? \App\Models\Employee::find($pos->returned_by) : null;
                            @endphp
                            <td class="info-value1">
                                {{ $employee ? ($employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name .' ('.$pos->returned_by .')'  ?? 'N/A') : 'N/A' }}
                            </td>

                        </tr>
                    </table>
                    
                </td>
            </tr>
        </table>
        

     <br>

        <table class="supplier-receiver">
            <tr>
                @if($suppliers)
                    <td width="60%" valign="top">
                        <div class="section-title">Supplied by:</div>
                        <table>
                            <tr>
                                <td class="info-value">{{ $suppliers->supplier_legal_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value">
                                    <b> Contact: </b> 
                                    {{ $suppliers->first_name ?? '' }}  
                                    {{ $suppliers->middle_name ?? '' }} 
                                    {{ $suppliers->last_name ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>Address:</b> {{ $suppliers->address1 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b> City: </b> {{ $suppliers->city ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>State: </b> {{ $suppliers->state ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b> ZipCode:  </b> {{ $suppliers->zip_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"> <b> Country:</b> {{ $suppliers->country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>E-mail: </b>  {{ $suppliers->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"> <b> Phone: </b>  {{ $suppliers->phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </td>
                @endif
                <td width="40%" valign="top">
                    <div class="section-title">Receiving Warehouse:</div>
                    <table>
                        @if($warehouse)
                            <tr>
                                <td class="info-value">{{ $warehouse->warehouse_name ?? 'N/A' }} ({{ $warehouse->id ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b> Contact: </b> Dinal Mahlangui</td> {{-- This is hardcoded --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Address:</b> {{ $warehouse->address1 ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>City: </b> {{ $warehouse->city ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>State:</b> {{ $warehouse->state ?? 'N/A' }} </td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>ZipCode:</b> {{ $warehouse->zip_code ?? 'N/A' }} </td> {{-- Fixed this --}}
                            </tr>
                            <tr>
                                <td class="info-value"><b>Country: </b> {{ $warehouse->country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>E-mail:</b> {{ $warehouse->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="info-value"><b>Phone: </b> {{ $warehouse->warehouse_contact ?? 'N/A' }}</td>
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
                <th>Qty Received</th>
                <th>QTY Variance</th>
                <th>Unit Cost</th>
                <th>Total Amount</th>
                <th>Received</th>
            </tr>
            @php
                $totalAmount = 0; 
            @endphp
            @foreach ($pos->poItemDetails as $receiving_detail)
            <tr>
                <td>{{ $receiving_detail->sku }}</td>
                <td>{{ $receiving_detail->product_name }}</td>
                <td>{{ $receiving_detail->size }}</td>
                <td>{{ $receiving_detail->uom }}</td>
                <td>{{ $receiving_detail->batch_no ?? 'N/A' }}</td>
                <td>{{ $receiving_detail->expiration_date ?? 'N/A' }}</td>
                <td>{{ $receiving_detail->qty_received ?? 0 }}</td>
                <td>{{ $receiving_detail->qty_variance ?? 0 }}</td>
                <td>${{ number_format($receiving_detail->unit_cost ?? 0, 2) }}</td>
                <td>${{ number_format($receiving_detail->total_amount ?? 0, 2) }}</td>
                <td>{{ $receiving_detail->receive_reject_action ? '✓' : '✗' }}</td>
            </tr>   
            @php
                $totalAmount += $receiving_detail->total_amount ?? 0;
            @endphp     
            @endforeach
        
            <tr>
                <td colspan="9" class="total-row">Total:</td>
                <td>${{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </table>
        
        <table class="notes-table">
            <tr>
                <td class="notes-title">Receiving Notes</td>
            </tr>
            <tr>
                <td class="notes-content">{{ $pos->notes ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
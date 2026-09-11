<!DOCTYPE html>
<html>
<head>
    <title>Chalan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
            background-color: transparent; /* prevent black/white bug */
        }

        th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>
<body>
    {{-- Header Info --}}
    <table style="border: 1.5px black dotted;">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">
                Chalan No   : <b>{{$chalan_no}}</b><br>
                @if($client)
                    Client Name : <b>{{$client['client_name']}}</b> <br>
                    Mobile No.  : <b>{{$client['mobile_no']}}</b><br>
                    Address     : <b>{{$client['address']}}</b>
                @endif
                @if($vendor)
                    Vendor Name : <b>{{$vendor['vendor_name']}}</b> <br>
                    Vendor GST No. : <b>{{$vendor['gst_no']}}</b><br>
                    Mobile No.  : <b>{{$vendor['mobile_no']}}</b><br>
                    Address     : <b>{{$vendor['address']}}</b>
                @endif
                
            </td>
            <td colspan="2"></td>
            <td colspan="2">
                Date            : <b> {{$chalan_date}}</b><br>
                @if($auto)
                    Auto Name       : <b>{{$auto['auto_name']??"N/A"}}</b><br>
                    Auto No         : <b>{{$auto['auto_no']??"N/A"}}</b><br>
                    Auto Mobile No  : <b>{{$auto['mobile_no']??"----------"}}</b> <br>
                @endif
                @if($transposer)
                    Transporter Name  : <b>{{$transposer['transporter_name']??"N/A"}}</b><br>
                    GST No            : <b>{{$transposer['gst_no']??"N/A"}}</b><br>
                @endif
            </td>
        </tr>
    </table>
    
    @foreach($table as $unit=> $unitTable)
        {{-- Bag Details --}}
        <table style="margin-top:20px">
            <thead>
                <tr>
                    <th>Role Type</th>
                    <th>Role No</th>
                    <th>Quality</th>
                    <th>Color</th>
                    <th>GSM</th>
                    <th>Length</th>
                    <th>Size</th>
                    <th>Net Weight</th>
                    <th>Gross Weight</th>
                    <th>Hardness</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unitTable["row"] as $item)
                <tr>
                    <td>{{ $item['roll_type'] }}</td>
                    <td>{{ $item['roll_no'] }}</td>
                    <td>{{ $item['quality'] }}</td>
                    <td>{{ $item['roll_color'] }}</td>
                    <td>{{ $item['gsm']}} {{$item['gsm_json'] ? "(".implode(",",json_decode($item['gsm_json'])).")" :""}}</td>
                    <td>{{ $item['length']}}</td>
                    <td>{{ $item['size']}}</td>
                    <td>{{ $item['net_weight']}}</td>
                    <td>{{ $item['gross_weight']}}</td>
                    <td>{{ $item['hardness']}}</td>

                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Grand Total</th>
                    <th>{{ $unitTable["grand_total"]["total"] }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $unitTable["grand_total"]["total_net_weight"] }}</th>
                    <th>{{ $unitTable["grand_total"]["total_gross_weight"] }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    @endforeach
</body>
</html>

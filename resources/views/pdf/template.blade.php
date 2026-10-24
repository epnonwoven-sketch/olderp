<!DOCTYPE html>
<html>

<head>
    <title>Chalan</title>
    <style>
        html,
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            background-color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            word-wrap: break-word;
            background-color: transparent;
        }

        th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        .header {
            border: 1.5px dotted #000;
            margin-bottom: 14px;
        }

        .header td {
            border: none;
            vertical-align: top;
            width: 50%;
            padding: 8px 10px;
            line-height: 1.45;
        }

        .header td.right {
            border-left: 1.5px dotted #000;
        }

        .summary {
            margin-top: 8px;
        }

        .details {
            margin-top: 16px;
        }

        .qty {
            text-align: right;
            white-space: nowrap;
        }

        .subtotal td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .grand td {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        .company {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px 0;
            letter-spacing: 0.4px;
        }

        .sign {
            margin-top: 36px;
            page-break-inside: avoid;
        }

        .sign td {
            border: none;
            width: 50%;
            vertical-align: bottom;
            padding: 0;
            line-height: 1.5;
        }

        .sign td.right {
            text-align: right;
        }
    </style>
</head>

<body>
    @php
        $trimNum = function ($n) {
            $formatted = number_format((float) $n, 2, '.', '');
            return rtrim(rtrim($formatted, '0'), '.');
        };
        $lineQty = function ($n) {
            return number_format((float) $n, 2, '.', '');
        };
        $displayDate = $chalan_date;
        if (is_string($chalan_date) && preg_match('/^\d{4}-\d{2}-\d{2}/', $chalan_date)) {
            try {
                $displayDate = \Carbon\Carbon::createFromFormat('Y-m-d', substr($chalan_date, 0, 10))->format('d/m/Y');
            } catch (\Exception $e) {
            }
        } elseif (is_string($chalan_date) && preg_match('/^\d{2}[-\/]\d{2}[-\/]\d{4}/', $chalan_date)) {
            try {
                $displayDate = \Carbon\Carbon::createFromFormat(
                    'd-m-Y',
                    str_replace('/', '-', substr($chalan_date, 0, 10)),
                )->format('d/m/Y');
            } catch (\Exception $e) {
            }
        }
    @endphp

    {{-- <div class="company">Evergreen Industries</div> --}}

    <table class="header">
        <tr>
            <td>
                Chalan No : <b>{{ $chalan_no }}</b><br>
                Client Name : <b>{{ data_get($client, 'client_name') }}</b><br>
                Mobile NO. : <b>{{ data_get($client, 'mobile_no') }}</b><br>
                Address : <b>{{ data_get($client, 'address') }}</b>
                @if (!empty($payment_terms ?? null) && ($payment_terms ?? null) !== '-')
                    <br>Payment Terms : <b>{{ $payment_terms }}</b>
                @endif
            </td>
            <td class="right">
                Date : <b>{{ $displayDate }}</b><br>
                Auto Name : <b>{{ data_get($auto, 'auto_name', 'N/A') }}</b><br>
                Auto No : <b>{{ data_get($auto, 'auto_no', 'N/A') }}</b><br>
                Auto Mobile No : <b>{{ data_get($auto, 'mobile_no', '----------') }}</b>
                @if (empty($is_local))
                    @if (!empty($bus_no))
                        <br>Bus Name : <b>{{ $transposer['transporter_name'] ?? 'N/A' }}</b>
                        <br>Bus No : <b>{{ $bus_no }}</b>
                    @elseif(!empty($transposer))
                        <br>Transporter Name : <b>{{ $transposer['transporter_name'] ?? 'N/A' }}</b>
                        <br>GST No : <b>{{ $transposer['gst_no'] ?? 'N/A' }}</b>
                    @endif
                @endif
            </td>
        </tr>
    </table>

    @foreach ($table as $unit => $unitTable)
        <table class="summary">
            <thead>
                <tr>
                    <th>Bag size</th>
                    <th>Bag type</th>
                    <th>colour</th>
                    <th>rate</th>
                    <th>fare</th>
                    <th>stereo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unitTable['row'] as $item)
                    <tr>
                        <td>{{ $item['size'] }}</td>
                        <td>{{ $item['bag_type'] }}</td>
                        <td>{{ $item['color'] }}</td>
                        <td>{{ $item['rate'] !== '' && $item['rate'] !== null ? $trimNum($item['rate']) : '' }}
                            ({{ $unit }})
                        </td>
                        <td>{{ $item['fare'] ?? '' }}</td>
                        <td>{{ $item['stereo'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>Bag Type</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th class="qty">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unitTable['row'] as $item)
                    @foreach ($item['bags'] as $bags)
                        <tr>
                            <td>{{ $item['bag_type'] }}</td>
                            <td>{{ $bags['bag_size'] }}</td>
                            <td>{{ $bags['bag_color'] }}</td>
                            <td class="qty">
                                {{ $lineQty($unit == 'Kg' ? $bags['packing_weight'] : $bags['packing_bag_pieces']) }}
                                {{ $unit }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td colspan="3"></td>
                        <td class="qty"><b>{{ $trimNum($item['total_unit']) }} {{ $unit }}</b></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="grand">
                    <td colspan="2">Grand Total</td>
                    <td class="qty">{{ $unitTable['grand_total']['total'] }}</td>
                    <td class="qty">{{ $trimNum($unitTable['grand_total']['total_unit']) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    <table class="sign">
        <tr>
            <td>
                <br><br><br>
                ________________________<br>
                Receiver's Signature
            </td>
            <td class="right">
                {{-- For <b>Evergreen Industries</b><br><br><br> --}}
                ________________________<br>
                Authorised Signatory
            </td>
        </tr>
    </table>
</body>

</html>

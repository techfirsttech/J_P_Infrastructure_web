<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Ledger</title>
    <link href="{{ public_path('assets/css/common-pdf.css') }}" rel="stylesheet">

    <style>
        @page {
            margin: 0cm 0.5cm;
        }

        .page-break {
            page-break-after: always;
        }

        body {
            /* margin-top: 4cm; */
            margin-top: 1cm;
            margin-left: 0cm;
            margin-right: 0cm;
            margin-bottom: 3cm;
            background: var(--white);
            color: var(--black);
            font-family: Roboto, 'helvetica', 'Segoe UI', Tahoma, sans-serif;
            font-size: 72%;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        footer {
            position: fixed;
            bottom: 0.5cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        header img,
        footer img {
            width: 100%;
            height: auto;
        }

        .border-tbl th,
        .border-tbl td {
            border: 1px solid var(--black);
        }

        .borderless-tbl th,
        .borderless-tbl td {
            border: 0;
        }

        .margin-top-25 {
            margin-top: 25px;
        }

        .text-success {
            color: #28c76f;
        }

        .text-danger {
            color: #ff4c51;
        }
    </style>
</head>

<body>


    <main>
        <h2 class="text-center pb-10">Payment Ledger</h2>
        {{-- <table class="border-tbl ">
            <tr>
                <th width="50%">
                    <h3>Site : </h3>
                </th>
                <th width="50%">
                    <h3>Supervisor : </h3>
                </th>
            </tr>

        </table> --}}

        <table class="border-tbl margin-top-25">
            <thead>
                <tr>
                    <th class="text-center" width="5%">No.</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Site</th>
                    <th class="text-center">Supervisor</th>
                    <th class="text-center">Remark</th>
                    <th class="text-center">Credit</th>
                    <th class="text-center">Debit</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($payments as $payKey => $payment)
                    <tr class="vertical-top">
                        <td class="text-center">{{ $payKey + 1 }}</td>
                        <td>{{ $payment->date ?? '' }}</td>
                        <td>{{ $payment->site_name ?? '' }}</td>
                        <td>{{ $payment->supervisor_name ?? '' }}</td>
                        <td>{{ $payment->remark ?? '' }}</td>
                        <td class="text-success text-center">
                            @if (strtolower($payment->status) == 'credit')
                                <b>{{ $payment->amount }}</b>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-danger text-center ">
                            @if (strtolower($payment->status) == 'debit')
                                <b>{{ $payment->amount }}</b>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end"> Closing Balance : {{ $closing_balance }}</th>
                    <th class="text-end">Total Income : {{ $total_income }}</th>
                    <th class="text-end">Total Expense : {{ $total_expense }}</th>
                </tr>
            </tfoot>


        </table>

    </main>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('report::message.ledger_report') }}</title>
    <link href="{{ public_path('assets/css/common-pdf.css') }}" rel="stylesheet">

    <style>
        @page {
            margin: 0cm 0.5cm;
        }

        .page-break {
            page-break-after: always;
        }

        body {
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
            margin-top: 10px;
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
        <h2 class="text-center pb-10">{{ __('report::message.ledger_report') }}</h2>
        <table class="border-tbl margin-top-25">
            <thead>
                <tr>
                    <th class="text-center" width="5%">#</th>
                    <th class="text-center">{{ __('message.common.date') }}</th>
                    <th class="text-center">{{ __('report::message.site') }}</th>
                    <th class="text-center">{{ __('report::message.supervisor') }}</th>
                    <th class="text-center">{{ __('report::message.remark') }}</th>
                    <th class="text-center">{{ __('report::message.credit') }}</th>
                    <th class="text-center">{{ __('report::message.debit') }}</th>
                </tr>
            </thead>

            <tbody>
                @php $totalExpense = $totalIncome = 0; @endphp
                @foreach ($query as $payKey => $payment)
                    <tr class="vertical-top">
                        <td class="text-center">{{ $payKey + 1 }}</td>
                        <td>{{ $payment->date ?? '' }}</td>
                        <td>{{ $payment->site_name ?? '' }}</td>
                        <td>{{ $payment->supervisor_name ?? '' }}</td>
                        <td>{{ $payment->remark ?? '' }}</td>
                        <td class="text-success text-center">
                            @if (strtolower($payment->status) == 'credit')
                                <b>{{ number_format($payment->amount, 2) }}</b>
                                @php $totalIncome += $payment->amount; @endphp
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-danger text-center ">
                            @if (strtolower($payment->status) == 'debit')
                                <b>{{ number_format($payment->amount, 2) }}</b>
                                @php $totalExpense += $payment->amount; @endphp
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                        <th colspan="5" class="text-nowarp text-end"> {{ __('report::message.closing_balance') }} : {{ number_format($totalIncome - $totalExpense, 2) }}</th>
                    <th class="text-nowarp text-end"> {{ __('report::message.total') }} Inc. : {{ number_format($totalIncome, 2) }}</th>
                    <th class="text-nowarp text-end"> {{ __('report::message.total') }} Exp. : {{ number_format($totalExpense, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </main>
</body>
</html>

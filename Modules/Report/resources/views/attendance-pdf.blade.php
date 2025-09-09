<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('report::message.attendance_report') }}</title>
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
        <h2 class="text-center pb-10">{{ __('report::message.attendance_report') }}</h2>
        <table class="border-tbl margin-top-25">
            <thead>
                <tr>
                    <th class="text-center" width="5%">#</th>
                    <th class="text-center">{{ __('attendance::message.labour') }}</th>
                    <th class="text-center">{{ __('attendance::message.contractor') }}</th>
                    <th class="text-center">{{ __('attendance::message.site') }}</th>
                    <th class="text-center">{{ __('report::message.full') }}</th>
                    <th class="text-center">{{ __('report::message.half') }}</th>
                    <th class="text-center">{{ __('report::message.absent') }}</th>
                    <th class="text-center">{{ __('report::message.salary') }}</th>
                </tr>
            </thead>

            <tbody>
                @php $totalSalary = $full_count = $half_count = $absent_count = 0; @endphp
                @foreach ($query as $atKey => $attendance)
                @php
                $totalSalary += $attendance->salary ?? 0;
                $full_count += $attendance->full_count ?? 0;
                $half_count += $attendance->half_count ?? 0;
                $absent_count += $attendance->absent_count ?? 0;
                @endphp
                <tr class="vertical-top">
                    <td class="text-center">{{ $atKey + 1 }}</td>
                    <td>{{ $attendance->labour->labour_name ?? '' }}</td>
                    <td>{{ $attendance->contractor->contractor_name ?? '' }}</td>
                    <td>{{ $attendance->site->site_name ?? '' }}</td>
                    <td class=" text-center">{{ $attendance->full_count ?? 0 }}</td>
                    <td class=" text-center">{{ $attendance->half_count ?? 0 }}</td>
                    <td class=" text-center">{{ $attendance->absent_count ?? 0 }}</td>
                    <td class=" text-end">{{ number_format($attendance->salary ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">{{ __('report::message.total') }} : </th>
                    <th colspan="" class="text-center">{{ $full_count }} </th>
                    <th colspan="" class="text-center">{{ $half_count }} </th>
                    <th colspan="" class="text-center">{{ $absent_count }} </th>
                    <th class="text-end">{{ number_format($totalSalary ?? 0, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>
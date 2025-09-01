<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report</title>
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
        <h2 class="text-center pb-10">Salary Report</h2>
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
                    <th class="text-center">Labour</th>
                    <th class="text-center">Contractor</th>
                    <th class="text-center">Site</th>
                    <th class="text-center">Full</th>
                    <th class="text-center">Half</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Salary (Per Day)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $totalSalary = 0;
                    $full_count = 0;
                    $half_count = 0;
                    $absent_count = 0;
                @endphp
                @foreach ($attendanceData as $atKey => $attendance)
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
                        <td class=" text-center">
                            {{ $attendance->full_count ?? 0 }}
                        </td>
                        <td class=" text-center ">
                            {{ $attendance->half_count ?? 0 }}

                        </td>
                        <td class=" text-center ">
                            {{ $attendance->absent_count ?? 0 }}
                        </td>
                        <td class=" text-end ">
                             {{ number_format($attendance->salary ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end"> Total : </th>
                    <th colspan="" class="text-center"> {{ $full_count }} </th>
                    <th colspan="" class="text-center">{{ $half_count }} </th>
                    <th colspan="" class="text-center">{{ $absent_count }} </th>
                    <th class="text-end">   {{ number_format($totalSalary ?? 0, 2) }}</th>
                </tr>
            </tfoot>


        </table>

    </main>
</body>

</html>

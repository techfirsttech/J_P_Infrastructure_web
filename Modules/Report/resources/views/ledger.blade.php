@extends('layouts.app')
@section('title', __('report::message.ledger_report'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('report::message.ledger_report') }}</h5>
    </div>

    <div class="col-12 mb-2">
        <div class="card">
            <div class="card-body">
                <form id="filter_form" action="javascript:void(0)" method="POST">
                    @csrf
                    <div class="row g-2 pt-25 align-items-end">
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label" for="s_date">{{ __('message.common.start_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="s_date" id="s_date" value="" autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label" for="e_date">{{ __('message.common.end_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="e_date" id="e_date" value="" autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label for="filter_site_id" class="form-label">{{ __('report::message.site') }}</label>
                            <select id="filter_site_id" name="filter_site_id" class="select2 form-select site-change">
                                <option value="All">{{ __('message.common.all') }}</option>
                                @foreach ($siteMaster as $site)
                                <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label for="filter_supervisor_id" class="form-label">Supervisor</label>
                            <select id="filter_supervisor_id" name="filter_supervisor_id" class="select2 form-select">
                                <option value="All">{{ __('message.common.all') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            @php $search = true; $reset = true; $export = $urlPdf; @endphp
                            {{ view('layouts.filter-button', compact('search', 'reset', 'export')) }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('message.common.date') }}</th>
                            <th>{{ __('report::message.site') }}</th>
                            <th>{{ __('report::message.supervisor') }}</th>
                            <th>{{ __('report::message.remark') }}</th>
                            <th class="text-dark">{{ __('report::message.credit') }}</th>
                            <th class="text-dark">{{ __('report::message.debit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="3" class="text-start ms-2 ps-3"><b>{{ __('report::message.closing_balance') }} : </b><b class=" total-balance"> 0.00</b></th>
                            <th class="text-end pe-5"><b>{{ __('report::message.total') }}</b></th>
                            <th class=""><b></b></th>
                            <th class=" text-end pe-5"><b class=" total_credit">0.00</b></th>
                            <th class=" text-end pe-5"><b class=" total_debit">0.000</b></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{ $url }}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: URL,
                data: function(d) {
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.s_date = $('#s_date').val();
                    d.e_date = $('#e_date').val();
                }
            },
            processing: true,
            serverSide: true,
            fixedHeader: true,
            scrollX: true,
            bScrollInfinite: true,
            bScrollCollapse: true,
            sScrollY: "465px",
            aLengthMenu: [
                [15, 30, 50, 100, -1],
                [15, 30, 50, 100, "All"]
            ],
            order: [
                [1, 'asc']
            ],
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        var rowNumber = meta.row + meta.settings._iDisplayStart + 1;
                        var isResponsive = meta.settings.responsive && meta.settings.responsive.details;
                        if (type === 'display' && isResponsive && meta.settings.responsive.details.type === 'column') {
                            return '';
                        } else {
                            return rowNumber;
                        }
                    },
                    orderable: false,
                    createdCell: function(td, cellData, rowData, row, col) {
                        var isResponsive = table.responsive.hasHidden();
                        if (isResponsive) {
                            $(td).addClass('dtr-control');
                        } else {
                            $(td).removeClass('dtr-control');
                        }
                    }
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'site_name',
                    name: 'site_masters.site_name'
                },
                {
                    data: 'supervisor_name',
                    name: 'supervisor.name'
                },
                {
                    data: 'remark',
                    name: 'remark',
                },
                {
                    data: 'credit',
                    name: 'credit',
                    className: 'text-success text-end pe-5'
                },
                {
                    data: 'debit',
                    name: 'debit',
                    className: 'text-danger text-end pe-5'
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                // Helper to safely parse values
                const safeParse = (val) => {
                    if (val === null || val === undefined || val === '-' || val === '') return 0;
                    if (typeof val === 'string') val = val.replace(/,/g, '').trim();
                    let num = parseFloat(val);
                    return isNaN(num) ? 0 : num;
                };

                // Credit column (index 5)
                let totalCredit = api.column(5, {
                        page: 'current'
                    }).data()
                    .reduce((a, b) => safeParse(a) + safeParse(b), 0);

                // Debit column (index 6)
                let totalDebit = api.column(6, {
                        page: 'current'
                    }).data()
                    .reduce((a, b) => safeParse(a) + safeParse(b), 0);

                // Update tfoot or any other selector
                $('.total_credit').html(totalCredit.toFixed(2));
                $('.total_debit').html(totalDebit.toFixed(2));

                $('.total-balance').html(totalCredit - totalDebit);
            },
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function(row) {
                            var rowNumber = row.index() + 1;
                            return 'Record No. : ' + rowNumber;
                        }
                    }),
                    type: "column",
                    renderer: function(t, a, e) {
                        e = $.map(e, function(t, a) {
                            return "" !== t.title ? '<tr data-dt-row="' + t.rowIndex + '" data-dt-column="' + t.columnIndex + '"><td>' + t.title + " :</td> <td>" + t.data + "</td></tr>" : ""
                        }).join("");
                        return !!e && $('<table class="table table-sm"/><tbody />').append(e)
                    }
                }
            }
        });
    });

    $(document).on('change', '.site-change', function(e) {
        e.preventDefault();
        var id = $(this).val();
        if (id != 'All') {
            $("#filter_supervisor_id").append(`<option value="" selected><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait</option>`);
            var route = "{{ route('get-site-supervisor') }}";
            $.ajax({
                type: "get",
                url: route,
                dataType: 'json',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status_code == 200) {
                        $("#filter_supervisor_id").empty();
                        $("#filter_supervisor_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
                        if (response.result.length > 0) {
                            $.each(response.result, function(index, row) {
                                $("#filter_supervisor_id").append($("<option value='" + row.id + "'>" + row.name + "</option>"));
                            });
                        } else {
                            toastr.warning('Supervisor not found.', "Warning");
                        }
                    } else if (response.status_code == 201 || response.status_code == 404) {
                        toastr.warning(response.message, "Warning");
                    } else {
                        toastr.error(response.message, "Opps!");
                    }
                }
            });
        } else {
            $("#filter_supervisor_id").empty();
            $("#filter_supervisor_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
            $('#filter_supervisor_id').val('All').trigger('change');
        }
    });
</script>
<script src="{{ asset('assets/custom/filter.js') }}"></script>
@endsection
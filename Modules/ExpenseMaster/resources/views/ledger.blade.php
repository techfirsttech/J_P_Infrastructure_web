@extends('layouts.app')
@section('title', __('expensemaster::message.ledger'))
@section('content')
    <div class="row mb-3">

        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_site_id" class="form-label">Site <span class="text-danger">*</span></label>
            <select id="filter_site_id" name="filter_site_id" class="select2 form-select"
                data-placeholder="{{ __('message.common.select') }}">
                <option value="">All Sites</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback d-block" id="error_filter_site_id" role="alert"></span>
        </div>
        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_supervisor_id" class="form-label">Supervisor<span class="text-danger">*</span></label>
            <select id="filter_supervisor_id" name="filter_supervisor_id" class="select2 form-select"
                data-placeholder="{{ __('message.common.select') }}">
                <option value="">All Supervisors</option>
                @foreach ($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback d-block" id="error_filter_supervisor_id" role="alert"></span>
        </div>

        <div class="col-md-2 form-group custom-input-group">
            <label class="form-label" for="filter_start_date">Start Date</label>
            <input type="text" class="form-control flatpickr-date" name="filter_start_date" id="filter_start_date"
                placeholder="End Date" value="">
        </div>
        <div class="col-md-2 form-group custom-input-group">
            <label class="form-label" for="filter_end_date">End Date</label>
            <input type="text" class="form-control flatpickr-date" name="filter_end_date" id="filter_end_date"
                placeholder="End Date" value="">
        </div>
        <div class="col-md-2 text-end pt-5">
            <a class="btn btn-outline-warning px-3 ledger-pdf" href="javascript:void(0);">
                <i class="fa fa-file-pdf"></i>
            </a>
            {{-- <button class="btn btn-warning px-3 pdf-view" id="pdf_button"><i class="fa fa-file-pdf"></i></button> --}}
            <button class="btn btn-outline-primary px-3" id="filter_button"><i class="fa fa-search"></i></button>
            <button class="btn btn-outline-secondary px-3" id="reset_button"><i class="fa fa-refresh"></i></button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('expensemaster::message.ledger') }}</h5>
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>{{ __('expensemaster::message.site') }}</th>
                                <th>{{ __('expensemaster::message.supervisor') }}</th>
                                <th>{{ __('expensemaster::message.remark') }}</th>
                                <th class="text-dark">{{ __('expensemaster::message.credit') }}</th>
                                <th class="text-dark">{{ __('expensemaster::message.debit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="2" class="text-end"><b>Closing Balance :</b></th>
                                <th class="text-start pe-5 "><b class=" total-balance"> 0.00</b></th>
                                <th class="text-end pe-5"><b>Total</b></th>
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
    const URL = "{{route('payment-ledger')}}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: "{{ route('payment-ledger') }}",
                data: function(d) {
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
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
                    name: 'users.name'
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

    $('#filter_button').click(function() {
        table.ajax.reload();
    });

    $(document).ready(function() {
        flatpickr('.flatpickr-date', {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: '',
            maxDate: new Date(),
        });
    });


    $('#reset_button').click(function() {
        $('#filter_site_id').val('');
        $('#filter_supervisor_id').val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        table.ajax.reload();
    });

    $(document).on('click', '.status', function() {
        let status = $(this).data('value');
        let id = $(this).data('id');
        var url = "{{route('expense-status-change')}}";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id,
                "status": status,
            },
            success: function(response) {
                if (response.status == true) {
                    table.ajax.reload(null, false);
                    toastr.success("Status Updated Successfully", "success'");
                } else {
                    toastr.error("Something Went Wrong. Please Try Again.'", "error");
                }
            },
            error: function(error) {
                toastr.error("Something Went Wrong. Please Try Again.'", "error");
                $(document.body).css('pointer-events', '');
            }
        });
    });

    $(document).on('click', '.ledger-pdf', function() {
        let pdfId = $(this).attr('data-id');
         let filter_start_date = $('#filter_start_date').val();
        let filter_end_date = $('#filter_end_date').val();
        let filter_site_id = $('#filter_site_id').val();
        let filter_supervisor_id = $('#filter_supervisor_id').val();
        let obj = $(this);
        if (pdfId != '') {
            $.ajax({
                type: "POST",
                url: "{{route('ledger-pdf')}}",
                // data: {
                //     "id": pdfId,
                //     "_token": "{{ csrf_token() }}"
                // },
                data: {
                    filter_start_date: filter_start_date,
                    filter_end_date: filter_end_date,
                    filter_site_id: filter_site_id,
                    filter_supervisor_id: filter_supervisor_id,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: 'json',
                cache: false,
                beforeSend: function() {
                    $("#error_name").html('');
                    obj.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait`);
                    obj.attr('disabled', true);
                },
                success: function(response) {
                    obj.html('<i class="fa-file-pdf"></i> <span> PDF </span>');
                    obj.attr('disabled', false);
                    if (response.status_code == 200) {
                        const link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = response.file_name;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        toastr.success(response.message, "Success");
                    } else {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(error) {
                    obj.html('<i class="fa-file-pdf"></i> <span> PDF </span>');
                    obj.attr('disabled', false);
                    toastr.error("An error occurred while generating the PDF", "Error");
                }
            });
        } else {
            toastr.error("Pdf not generated", "Error");
        }
    });
</script>
    <script src="{{ asset('assets/custom/save.js') }}"></script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection

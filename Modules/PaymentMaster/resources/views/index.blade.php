@extends('layouts.app')
@section('title', __('paymentmaster::message.paymenttransfer'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('paymentmaster::message.list') }}</h5>
        @can('income-master-create')
        <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal" class="btn btn-sm btn-primary new-create float-end"><i class="fa fa-plus me-25"></i>{{ __('message.common.addNew') }}</button>
        @endcan
    </div>
    <div class="col-12 mb-2">
        <div class="card">
            <div class="card-body">
                <form id="filter_form" action="javascript:void(0)" method="POST">
                    @csrf
                    <div class="row g-2 pt-25 align-items-end">
                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label class="form-label" for="s_date">{{ __('message.common.start_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="s_date" id="s_date" value="" autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label class="form-label" for="e_date">{{ __('message.common.end_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="e_date" id="e_date" value="" autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label for="filter_supervisor_id" class="form-label">From Supervisor</label>
                            <select id="filter_supervisor_id" name="filter_supervisor_id" class="select2 form-select">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                @foreach ($supervisor as $supervisors)
                                <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label for="filter_to_supervisor_id" class="form-label">To Supervisor</label>
                            <select id="filter_to_supervisor_id" name="filter_to_supervisor_id" class="select2 form-select">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                @foreach ($supervisor as $supervisors)
                                <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            @php $search = true; $reset = true; $export = false; @endphp
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
                            <th>From {{ __('paymentmaster::message.supervisor') }}</th>
                            <th>To {{ __('paymentmaster::message.supervisor') }}</th>
                            <th>{{ __('paymentmaster::message.amount') }}</th>
                            <th>{{ __('paymentmaster::message.remark') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inlineModal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('paymentmaster::message.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label for="to_supervisor_id" class="form-label">{{ __('paymentmaster::message.fromSupervisor') }} <span class="text-danger">*</span></label>
                            <select id="supervisor_id" name="supervisor_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($supervisor->count() > 0)
                                @foreach ($supervisor as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_supervisor_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label for="site_id" class="form-label">{{ __('paymentmaster::message.site') }} <span class="text-danger">*</span></label>
                            <select id="site_id" name="site_id" class="select2 form-select site-change">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($site->count() > 0)
                                @foreach ($site as $value)
                                <option value="{{ $value->id }}">{{ $value->site_name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_site_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label for="to_supervisor_id" class="form-label">{{ __('paymentmaster::message.supervisor') }}</label>
                            <select id="to_supervisor_id" name="to_supervisor_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                            </select>
                            <span class="invalid-feedback d-block" id="error_to_supervisor_id" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="amount">{{ __('paymentmaster::message.amount') }} <span class="text-danger">*</span></label>
                            <input type="text" min="0.1" class="form-control number" name="amount" id="amount" placeholder="{{ __('paymentmaster::message.amount') }}">
                            <span class="invalid-feedback d-block" id="error_amount" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="remark">{{ __('paymentmaster::message.remark') }}</label>
                            <textarea class="form-control" name="remark" id="remark"> </textarea>
                            <span class="invalid-feedback d-block" id="error_remark" role="alert">{{ $errors->first('remark') }}</span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save" data-route="{{ route('paymentmaster.store') }}">{{ __('message.common.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route('paymentmaster.index')}}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: "{{ route('paymentmaster.index') }}",
                data: function(d) {
                    d.filter_supervisor_id = $('#filter_supervisor_id').val();
                    d.filter_to_supervisor_id = $('#filter_to_supervisor_id').val();
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
                [0, 'desc']
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
                    name: 'payment_transfers.created_at'
                },
                {
                    data: 'supervisor_name',
                    name: 'supervisor.name'
                },
                {
                    data: 'to_supervisor_name',
                    name: 'to_supervisor.name'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'remark',
                    name: 'remark'
                },
            ],
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

    $(document).on('input', '.number', function(e) {
        const value = e.target.value;
        const validValue = value
            .replace(/[^0-9.]/g, '')
            .replace(/(\..*?)\..*/g, '$1');
        e.target.value = validValue;
    });

    $("#inlineModal").on("hidden.bs.modal", function(e) {
        $(this).find('form').trigger('reset');
        $("#id").val("");
        $('#form .select2').val('').trigger('change');
        $(".invalid-feedback,.custom-error").html("");
        $(".save").html("Submit");
        $(".save").attr('disabled', false);
        $("#exampleModalTitle").html("{{ __('paymentmaster::message.add') }}");
    });

    $(document).on('click', '.edit', function() {
        $('.modal').modal('hide');
        var id = $(this).data('id');
        var url = "{{route('paymentmaster.edit','id')}}".replace('id', id);
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {},
            success: function(response) {
                if (response.status_code == 200) {
                    $("#exampleModalTitle").html("{{ __('paymentmaster::message.edit') }}");
                    $("#site_id").val(response.result.site_id).trigger('change');
                    $("#supervisor_id").val(response.result.supervisor_id).trigger('change');
                    $("#expense_category_id").val(response.result.expense_category_id).trigger('change');
                    $("#amount").val(response.result.amount);
                    $("#date").val(response.result.date);
                    $("#remark").val(response.result.remark);
                    $("#id").val(id);
                    $("#inlineModal").modal('show');
                } else if (response.status_code == 201) {
                    toastr.warning(response.message, "Warning");
                } else if (response.status_code == 404) {
                    toastr.warning(response.message, "Warning");
                } else {
                    toastr.error(response.message, "Error");
                }
            }
        });
    });

    $("#form").validate({
        rules: {
            site_id: {
                required: true,
            },
            supervisor_id: {
                required: true,
            },
            to_supervisor_id: {
                required: true,
            },
            amount: {
                required: true,
            },

        },
        messages: {
            site_id: {
                required: "{{ __('paymentmaster::message.select_site') }}",
            },
            supervisor_id: {
                required: "{{ __('paymentmaster::message.select_supervisor') }}",
            },
            to_supervisor_id: {
                required: "{{ __('paymentmaster::message.select_supervisor') }}",
            },
            amount: {
                required: "{{ __('paymentmaster::message.enter_amount') }}",
            }
        },
        errorElement: "p",
        errorClass: "text-danger mb-0 custom-error",

        highlight: function(element) {
            $(element).addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).removeClass('has-error');
        },
        errorPlacement: function(error, element) {
            $(element).closest('.custom-input-group').append(error);
        }
    });


    $(document).on('change', '.site-change', function(e) {
        e.preventDefault();
        var id = $(this).val();
        if (id != '') {
            $("#to_supervisor_id").append(`<option value="" selected><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait</option>`);
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
                        $("#to_supervisor_id").empty();
                        $("#to_supervisor_id").append(`<option value="" selected>{{ __('message.common.select') }}</option>`);
                        if (response.result.length > 0) {
                            $.each(response.result, function(index, row) {
                                if (row.id != $('#supervisor_id').val()) {
                                    $("#to_supervisor_id").append($("<option value='" + row.id + "'>" + row.name + "</option>"));
                                }
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
            $('#to_supervisor_id').val('').trigger('change');
        }
    });
</script>
<script src="{{asset('assets/custom/filter.js')}}"></script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
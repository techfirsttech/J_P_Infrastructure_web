@extends('layouts.app')
@section('title', __('expensemaster::message.expensemaster'))
@section('content')
<div class="row mb-3">

    <div class="col-md-2 form-group custom-input-group">
        <label for="filter_site_id" class="form-label">Site <span class="text-danger">*</span></label>
        <select id="filter_site_id" name="filter_site_id" class="select2 form-select">
            <option value="">-- All --</option>
            @foreach ($siteMaster as $site)
            <option value="{{ $site->id }}">{{ $site->site_name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block" id="error_filter_site_id" role="alert"></span>
    </div>
    <div class="col-md-2 form-group custom-input-group">
        <label for="filter_supervisor_id" class="form-label">Supervisor<span class="text-danger">*</span></label>
        <select id="filter_supervisor_id" name="filter_supervisor_id" class="select2 form-select">
            <option value="">-- All --</option>
            @foreach ($supervisor as $supervisors)
            <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block" id="error_filter_supervisor_id" role="alert"></span>
    </div>
    <div class="col-md-2 form-group custom-input-group">
        <label for="filter_expense_category_id" class="form-label">Category<span class="text-danger">*</span></label>
        <select id="filter_expense_category_id" name="filter_expense_category_id" class="select2 form-select">
            <option value="">-- All --</option>
            @foreach ($expenseCategory as $category)
            <option value="{{ $category->id }}">{{ $category->expense_category_name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block" id="error_filter_expense_category_id role=" alert"></span>
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
        <button class="btn btn-primary px-3" id="filter_button"><i class="fa fa-search"></i></button>
        <button class="btn btn-secondary px-3" id="reset_button"><i class="fa fa-refresh"></i></button>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('expensemaster::message.list') }}</h5>
        @can('income-master-create')
        <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal"
            class="btn btn-sm btn-primary new-create float-end"><i
                class="fa fa-plus me-25"></i>{{ __('message.common.addNew') }}</button>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('message.common.date') }}</th>
                            <th>{{ __('expensemaster::message.expenseCategoryName') }}</th>
                            <th>{{ __('expensemaster::message.site') }}</th>
                            <th>{{ __('expensemaster::message.supervisor') }}</th>
                            <th>{{ __('expensemaster::message.amount') }}</th>
                            <th>{{ __('expensemaster::message.remark') }}</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>{{ __('message.common.action') }}</th>
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
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('expensemaster::message.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">

                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="site_id" class="form-label">{{ __('expensemaster::message.site') }}</label>
                            <select id="site_id" name="site_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($siteMaster->count() > 0)
                                @foreach ($siteMaster as $value)
                                <option value="{{ $value->id }}">{{ $value->site_name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_site_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                            <label for="supervisor_id"
                                class="form-label">{{ __('expensemaster::message.supervisor') }}</label>
                            <select id="supervisor_id" name="supervisor_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($supervisor->count() > 0)
                                @foreach ($supervisor as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_supervisor_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                            <label for="expense_category_id"
                                class="form-label">{{ __('expensemaster::message.expenseCategory') }}</label>
                            <select id="expense_category_id" name="expense_category_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($expenseCategory->count() > 0)
                                @foreach ($expenseCategory as $value)
                                <option value="{{ $value->id }}">{{ $value->expense_category_name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_expense_category_id"
                                role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                            <label class="form-label" for="amount">{{ __('expensemaster::message.amount') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amount" id="amount"
                                placeholder="{{ __('expensemaster::message.amount') }}">
                            <span class="invalid-feedback d-block" id="error_amount" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                            <label class="form-label" for="date"> Date</label>
                            <input type="text" class="form-control flatpickr-date" name="date" id="date"
                                placeholder="Date" value="">
                            <span class="invalid-feedback d-block" id="error_date"
                                role="alert">{{ $errors->first('date') }}</span>
                        </div>

                        <div class="col-12 col-sm-8 col-md-6 col-lg-6 form-group custom-input-group">
                            <label class="form-label"
                                for="document">{{ __('expensemaster::message.document') }}</label>
                            <input type="file" class="form-control" name="document" id="document"
                                placeholder="Document" value="{{ old('document') }}">
                            <span class="invalid-feedback d-block" id="error_document" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="remark">{{ __('expensemaster::message.remark') }}
                            </label>
                            <textarea class="form-control" name="remark" id="remark"> </textarea>
                            <span class="invalid-feedback d-block" id="error_remark"
                                role="alert">{{ $errors->first('remark') }}</span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close"
                                class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save"
                                data-route="{{ route('expensemaster.store') }}">{{ __('message.common.submit') }}</button>
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
    const URL = "{{route('expensemaster.index')}}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: "{{ route('expensemaster.index') }}",
                data: function(d) {
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.supervisor_id = $('#filter_expense_category_id').val();
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
                [1, 'desc']
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
                    data: 'expense_category_name',
                    name: 'expense_categories.expense_category_name'
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
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'remark',
                    name: 'remark'
                },
                {
                    data: 'document',
                    name: 'document'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
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
    $("#inlineModal").on("hidden.bs.modal", function(e) {
        $(this).find('form').trigger('reset');
        $("#id").val("");
        $('.select2').val('').trigger('change');
        $(".invalid-feedback,.custom-error").html("");
        $(".save").html("Submit");
        $(".save").attr('disabled', false);
        $("#exampleModalTitle").html("{{ __('expensemaster::message.add') }}");
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

    $(document).on('click', '.edit', function() {
        $('.modal').modal('hide');
        var id = $(this).data('id');
        var url = "{{route('expensemaster.edit','id')}}".replace('id', id);
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
                    $("#exampleModalTitle").html("{{ __('expensemaster::message.edit') }}");
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
            amount: {
                required: true,
            },

        },
        messages: {
            site_id: {
                required: "{{ __('expensemaster::message.select_site') }}",
            },
            supervisor_id: {
                required: "{{ __('expensemaster::message.select_supervisor') }}",
            },
            amount: {
                required: "{{ __('expensemaster::message.enter_amount') }}",
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

    //  $(document).ready(function() {
    //     flatpickr('.flatpickr-date', {
    //         enableTime: false,
    //         dateFormat: 'd-m-Y',
    //         defaultDate: today,
    //         maxDate: new Date(),
    //         appendTo: document.getElementById('inlineModal')
    //     });
    // });

    $('#filter_button').click(function() {
        table.ajax.reload();
    });

    $(document).ready(function() {
        flatpickr('.flatpickr-date', {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: '',
            maxDate: new Date(),
            appendTo: document.getElementById('inlineModal')

        });
    });


    $('#reset_button').click(function() {
        $('#filter_site_id').val('');
        $('#filter_supervisor_id').val('');
        $('#filter_expense_category_id').val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        table.ajax.reload();
    });
</script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
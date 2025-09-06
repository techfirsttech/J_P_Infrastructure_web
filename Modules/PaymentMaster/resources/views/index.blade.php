@extends('layouts.app')
@section('title', __('paymentmaster::message.paymenttransfer'))
@section('content')
<div class="row mb-3">


    <div class="col-md-2 form-group custom-input-group">
        <label for="supervisor_id" class="form-label">From Supervisor<span class="text-danger">*</span></label>
        <select id="supervisor_id" name="supervisor_id" class="select2 form-select">
            <option value="">-- All --</option>
            @foreach ($supervisor as $supervisors)
            <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block" id="error_supervisor_id" role="alert"></span>
    </div>
    <div class="col-md-2 form-group custom-input-group">
        <label for="to_supervisor_id" class="form-label">To Supervisor<span class="text-danger">*</span></label>
        <select id="to_supervisor_id" name="to_supervisor_id" class="select2 form-select">
            <option value="">-- All --</option>
            @foreach ($supervisor as $supervisors)
            <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block" id="error_to_supervisor_id" role="alert"></span>
    </div>

    <div class="col-md-2 form-group custom-input-group">
        <label class="form-label" for="start_date">Start Date</label>
        <input type="text" class="form-control flatpickr-date" name="start_date" id="start_date"
            placeholder="End Date" value="">
    </div>
    <div class="col-md-2 form-group custom-input-group">
        <label class="form-label" for="end_date">End Date</label>
        <input type="text" class="form-control flatpickr-date" name="end_date" id="end_date"
            placeholder="End Date" value="">
    </div>
    <div class="col-md-2 text-end pt-5">
        <button class="btn btn-primary px-3" id="filter_button"><i class="fa fa-search"></i></button>
        <button class="btn btn-secondary px-3" id="reset_button"><i class="fa fa-refresh"></i></button>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('paymentmaster::message.list') }}</h5>
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
                            <th>From {{ __('paymentmaster::message.supervisor') }}</th>

                            <!-- <th>To {{ __('paymentmaster::message.site') }}</th> -->
                            <th>To {{ __('paymentmaster::message.supervisor') }}</th>
                            <th>{{ __('paymentmaster::message.amount') }}</th>
                            <th>{{ __('paymentmaster::message.remark') }}</th>
                            {{-- <th>{{ __('message.common.action') }}</th> --}}
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
                            <label for="to_supervisor_id"
                                class="form-label">{{ __('paymentmaster::message.fromSupervisor') }}</label>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label for="to_supervisor_id"
                                class="form-label">{{ __('paymentmaster::message.site') }}</label>
                            <select id="site_id" name="site_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($site->count() > 0)
                                @foreach ($site as $value)
                                <option value="{{ $value->id }}">{{ $value->site_name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_site_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label for="to_supervisor_id"
                                class="form-label">{{ __('paymentmaster::message.supervisor') }}</label>
                            <select id="to_supervisor_id" name="to_supervisor_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($to_supervisors->count() > 0)
                                @foreach ($to_supervisors as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_to_supervisor_id" role="alert"></span>
                        </div>


                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="amount">{{ __('paymentmaster::message.amount') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amount" id="amount"
                                placeholder="{{ __('paymentmaster::message.amount') }}">
                            <span class="invalid-feedback d-block" id="error_amount" role="alert"></span>
                        </div>


                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="remark">{{ __('paymentmaster::message.remark') }}
                            </label>
                            <textarea class="form-control" name="remark" id="remark"> </textarea>
                            <span class="invalid-feedback d-block" id="error_remark"
                                role="alert">{{ $errors->first('remark') }}</span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close"
                                class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save"
                                data-route="{{ route('paymentmaster.store') }}">{{ __('message.common.submit') }}</button>
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
                    d.supervisor_id = $('#supervisor_id').val();
                    d.to_supervisor_id = $('#to_supervisor_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
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
                    name: 'date'
                },
                {
                    data: 'from_user_name',
                    name: 'user.name'
                },
                //  {
                //     data: 'site_name',
                //     name: 'site.site_name'
                // },
                {
                    data: 'to_user_name',
                    name: 'to_user.name'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'remark',
                    name: 'remark'
                },
                // {
                //      data: 'action',
                //      name: 'action',
                //      orderable: false,
                //      sortable: false
                // },

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
        });
    });


    $('#reset_button').click(function() {
        $('#supervisor_id').val('');
        $('#to_supervisor_id').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        table.ajax.reload();
    });
</script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
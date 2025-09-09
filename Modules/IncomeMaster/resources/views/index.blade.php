@extends('layouts.app')
@section('title', __('incomemaster::message.incomeMaster'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('incomemaster::message.list') }}</h5>
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
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label" for="s_date">{{ __('message.common.start_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="s_date" id="s_date" value="" autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label" for="e_date">{{ __('message.common.end_date') }}</label>
                            <input type="text" class="form-control flatpickr" name="e_date" id="e_date" value="" autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label for="filter_site_id" class="form-label">Site</label>
                            <select id="filter_site_id" name="filter_site_id" class="select2 form-select">
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
                                @foreach ($supervisor as $supervisors)
                                <option value="{{ $supervisors->id }}">{{ $supervisors->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label for="filter_party_id" class="form-label">{{ __('incomemaster::message.party') }}</label>
                            <select id="filter_party_id" name="filter_party_id" class="select2 form-select">
                                <option value="All">{{ __('message.common.all') }}</option>
                                @foreach ($party as $value)
                                <option value="{{ $value->id }}">{{ $value->party_name }}</option>
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
                            <th>{{ __('incomemaster::message.site') }}</th>
                            <th>{{ __('incomemaster::message.supervisor') }}</th>
                            <th>{{ __('incomemaster::message.party') }}</th>
                            <th>{{ __('incomemaster::message.amount') }}</th>
                            <th>{{ __('incomemaster::message.remark') }}</th>
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
                <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('incomemaster::message.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="date"> Date<span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-date" name="date" id="date" placeholder="Date" value="">
                            <span class="invalid-feedback d-block" id="error_date" role="alert">{{ $errors->first('date') }}</span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 form-group custom-input-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="site_id" class="form-label">{{ __('incomemaster::message.site') }}<span class="text-danger">*</span></label>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 form-group custom-input-group">
                            <label for="supervisor_id" class="form-label">{{ __('incomemaster::message.supervisor') }}<span class="text-danger">*</span></label>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 form-group custom-input-group">
                            <label for="party_id" class="form-label">{{ __('incomemaster::message.party') }}</label>
                            <select id="party_id" name="party_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @if ($party->count() > 0)
                                @foreach ($party as $value)
                                <option value="{{ $value->id }}">{{ $value->party_name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback d-block" id="error_party_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 form-group custom-input-group">
                            <label class="form-label" for="amount">{{ __('incomemaster::message.amount') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amount" id="amount" placeholder="{{ __('incomemaster::message.amount') }}">
                            <span class="invalid-feedback d-block" id="error_amount" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="remark">{{ __('incomemaster::message.remark') }}</label>
                            <textarea class="form-control" name="remark" id="remark"> </textarea>
                            <span class="invalid-feedback d-block" id="error_remark" role="alert">{{ $errors->first('remark') }}</span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save" data-route="{{ route('incomemaster.store') }}">{{ __('message.common.submit') }}</button>
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
    const URL = "{{route('incomemaster.index')}}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: "{{ route('incomemaster.index') }}",
                data: function(d) {
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.party_id = $('#filter_party_id').val();
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
                    data: 'party_name',
                    name: 'parties.party_name'
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-end pe-5'
                },
                {
                    data: 'remark',
                    name: 'remark'
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

    $(document).ready(function() {
        flatpickr('.flatpickr-date', {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: '',
            maxDate: new Date(),
            appendTo: document.getElementById('inlineModal')
        });
    });

    $("#inlineModal").on("hidden.bs.modal", function(e) {
        $(this).find('form').trigger('reset');
        $("#id").val("");
        $('#party_id, #site_id, #supervisor_id').val('').trigger('change');
        $(".invalid-feedback,.custom-error").html("");
        $(".save").html("Submit");
        $(".save").attr('disabled', false);
        $("#exampleModalTitle").html("{{ __('incomemaster::message.add') }}");
    });

    $(document).on('click', '.edit', function() {
        $('.modal').modal('hide');
        var id = $(this).data('id');
        var url = "{{route('incomemaster.edit','id')}}".replace('id', id);
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
                    $("#exampleModalTitle").html("{{ __('incomemaster::message.edit') }}");
                    $("#site_id").val(response.result.site_id).trigger('change');
                    $("#supervisor_id").val(response.result.supervisor_id).trigger('change');
                    $("#party_id").val(response.result.party_id).trigger('change');
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
            date: {
                required: true,
            },
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
            date: {
                required: "{{ __('incomemaster::message.select_date') }}",
            },
            site_id: {
                required: "{{ __('incomemaster::message.select_site') }}",
            },
            supervisor_id: {
                required: "{{ __('incomemaster::message.select_supervisor') }}",
            },
            amount: {
                required: "{{ __('incomemaster::message.enter_amount') }}",
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
    
</script>
<script src="{{asset('assets/custom/filter.js')}}"></script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
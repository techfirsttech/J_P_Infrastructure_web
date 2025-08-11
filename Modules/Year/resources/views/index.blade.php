@extends('layouts.app')
@section('title', __('year::message.list') )
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('year::message.list') }}</h5>
        @can('year-create')
        <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal" class="btn btn-sm btn-primary new-create float-end"><i class="fa fa-plus me-25"></i> {{ __('message.common.addNew') }}</button>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('year::message.name') }}</th>
                            <th>{{ __('year::message.default') }}</th>
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

<div class="modal fade" id="inlineModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-transparent ">
                <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('year::message.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="name">{{ __('year::message.name') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('year::message.name') }}" value="{{ old('name') }}">
                            <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-check-label"> {{ __('year::message.default') }} </label>
                            <label class="switch">
                                <input type="checkbox" name="set_default" id="set_default" class="switch-input" value="1" checked>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">
                                        <i class="icon-base fa fa-check"></i>
                                    </span>
                                    <span class="switch-off">
                                        <i class="icon-base fa fa-x"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div class="col-md-12 col-12">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save" id="save" data-route="{{route('year.store')}}">{{ __('message.common.submit') }}</button>
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
    const URL = "{{route('year.index')}}";
    var table = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: URL,
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'set_default',
                    name: 'set_default'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
                }
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

    $("#form").validate({
        rules: {
            name: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "{{ __('year::message.enter_year') }}"
            },
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

    $("#inlineModal").on("hidden.bs.modal", function() {
        $('#form')[0].reset();
        $(".invalid-feedback,.custom-error").html("");
        $('#set_default').attr('checked', true);
        $("#save").attr('data-route', '').removeClass('update');
        $("#save")
            .html("{{ __('message.common.submit') }}")
            .attr('disabled', false)
            .attr('data-route', "{{route('year.store')}}")
            .addClass('save');
        $("#exampleModalTitle").html("{{ __('year::message.add') }}");
    });

    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        $("#save").attr('data-route', '').removeClass('save').addClass('update');
        $('.modal').modal('hide');
        var id = $(this).attr('data-id');
        var url = "{{route('year.edit', ':id')}}".replace(':id', id);
        $("#save").attr('data-route', "{{route('year.update', ':id')}}".replace(':id', id));
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            success: function(response) {
                if (response.status_code == 200) {
                    $("#exampleModalTitle").html("{{ __('year::message.edit') }}");
                    $("#name").val(response.result.name);
                    if (response.result.set_default == 1) {
                        $('#set_default').attr('checked', true);
                    } else {
                        $('#set_default').attr('checked', false);
                    }
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
</script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/update.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
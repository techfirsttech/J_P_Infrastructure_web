@extends('layouts.app')
@section('title', __('country::message.country'))

@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('country::message.list') }}</h5>
        @can('country-create')
        <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal" class="btn btn-sm btn-primary new-create float-end"><i class="fa fa-plus me-25"></i> {{ __('message.common.addNew') }}</button>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead class="fix-col">
                        <tr>
                            <th class="fix-col" width="3%">#</th>
                            <th>{{ __('country::message.name') }}</th>
                            <th>{{ __('country::message.code') }}</th>
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

<div class="modal fade" id="inlineModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-transparent ">
                <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('country::message.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body " id="body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="name">{{ __('country::message.name') }} <span class="text-danger">*</span></label>
                            <input type="hidden" name="id" id="id" value="">
                            <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('country::message.name') }}">
                            <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="code">{{ __('country::message.code') }}</label>
                            <input type="text" class="form-control" name="code" id="code" placeholder="{{ __('country::message.code') }}">
                            <span class="invalid-feedback d-block" id="error_code" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                            <button type="button" class="btn btn-sm btn-primary float-end save" data-route="{{route('country.store')}}">{{ __('message.common.submit') }}</button>
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
    const URL = "{{route('country.index')}}";

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
            columns: [
                {
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    className: 'fix-col',
                },

                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false,
                    className: 'fix-col',
                },
            ],
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 0
            },
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },
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
                required: "{{ __('country::message.enter_name') }}"
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
        $(this).find('form').trigger('reset');
        $("#id").val("");
        $(".invalid-feedback,.custom-error").html("");
        $(".save").html("Submit");
        $(".save").attr('disabled', false);
        $("#exampleModalTitle").html("{{ __('country::message.add') }}");
    });

    $(document).on('click', '.edit', function() {
        $('.modal').modal('hide');
        var id = $(this).data('id');
        var url = "{{route('country.edit','id')}}".replace('id', id);
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {},
            success: function(data) {
                if (data.status_code == 200) {
                    $("#exampleModalTitle").html("{{ __('country::message.edit') }}");
                    $("#name").val(data.result.name);
                    $("#code").val(data.result.code);
                    $("#id").val(id);
                    $("#inlineModal").modal('show');
                } else if (data.status_code == 201) {
                    toastr.warning(data.message, "Warning");
                } else {
                    toastr.error(data.message, "Error");
                }
            }
        });
    });
</script>
<script src="{{asset('assets/js/location-save.js')}}"></script>
<script src="{{asset('assets/custom/delete.js')}}"></script>
@endsection
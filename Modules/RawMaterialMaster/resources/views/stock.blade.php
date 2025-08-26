@extends('layouts.app')
@section('title', __('rawmaterialmaster::message.list'))
@section('content')
    <style>
        .select2-search__field {
            width: auto !important;
            display: inline-block !important;
        }
    </style>
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0"> {{ __('rawmaterialmaster::message.list') }}</h5>
            @can('site-master-create')
                <a href="{{ route('rawmaterialmaster.create') }}" class="btn btn-sm btn-primary float-end new-create"><i
                        class="fa fa-plus me-50"></i> {{ __('message.common.addNew') }}</a>
            @endcan
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('rawmaterialmaster::message.material_category') }}</th>
                                <th>{{ __('rawmaterialmaster::message.material_name') }}</th>
                                <th>{{ __('rawmaterialmaster::message.material_code') }}</th>
                                <th>{{ __('rawmaterialmaster::message.unit') }}</th>
                                <th>{{ __('rawmaterialmaster::message.alert_quantity') }}</th>
                                <th>{{ __('rawmaterialmaster::message.tax') }}</th>
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
@endsection

@section('pagescript')
    <script type="application/javascript">
    @if($message = Session::get('error'))
    toastr.error("{{ addslashes($message) }}", "Error");
    @endif

        'use strict';
    const URL = "{{route('rawmaterialmaster.index')}}";

    var table = '';
    var assignId = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: URL,
            processing: true,
            serverSide: true,
            fixedHeader: false,
            scrollX: true,
            bScrollInfinite: true,
            bScrollCollapse: true,
            sScrollY: "465px",
            aLengthMenu: [
                [15, 30, 50, 100, -1],
                [15, 30, 50, 100, "All"]
            ],
            order: [
                [0, 'asc']
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
                    data: 'material_category_name',
                    name: 'material_category_name'
                },
                {
                    data: 'material_name',
                    name: 'material_name'
                },
                {
                    data: 'material_code',
                    name: 'material_code'
                },
                {
                    data: 'unit_name',
                    name: 'unit_name'
                },
                {
                    data: 'alert_quantity',
                    name: 'alert_quantity'
                },
                {
                    data: 'tax',
                    name: 'tax'
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

        });
    });



</script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
    <script src="{{ asset('assets/custom/status.js') }}"></script>
@endsection

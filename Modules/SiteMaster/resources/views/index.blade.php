@extends('layouts.app')
@section('title', __('sitemaster::message.list'))
@section('content')
    <style>
        .select2-search__field {
            width: auto !important;
            display: inline-block !important;
        }
    </style>
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0"> {{ __('sitemaster::message.list') }}</h5>
            @can('site-master-create')
                <a href="{{ route('sitemaster.create') }}" class="btn btn-sm btn-primary float-end new-create"><i
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
                                <th>{{ __('sitemaster::message.site_name') }}</th>
                                <th>{{ __('sitemaster::message.address') }}</th>
                                <th>{{ __('sitemaster::message.state') }}</th>
                                <th>{{ __('sitemaster::message.city') }}</th>
                                <th>{{ __('sitemaster::message.pincode') }}</th>
                                {{-- <th>{{ __('sitemaster::message.status') }}</th> --}}
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
    const URL = "{{route('sitemaster.index')}}";

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
                    data: 'site_name',
                    name: 'site_name'
                },
                {
                    data: 'state_name',
                    name: 'state_name'
                },
                {
                    data: 'city_name',
                    name: 'city_name'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'pincode',
                    name: 'pincode'
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

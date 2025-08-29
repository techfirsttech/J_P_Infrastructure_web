@extends('layouts.app')
@section('title', __('stocktransfer::message.list'))
@section('content')
    <style>
        .select2-search__field {
            width: auto !important;
            display: inline-block !important;
        }
    </style>

    <div class="row mb-3">
        <div class="col-md-3 form-group custom-input-group">
            <label for="site_id" class="form-label">Site</label>
            <select id="site_id" class="select2 form-select">
                <option value="">All Sites</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 form-group custom-input-group">
            <label for="supervisor_id" class="form-label">Supervisor</label>
            <select id="supervisor_id" class="select2 form-select">
                <option value="">All Sites</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 text-start pt-4 mt-2">
            <button class="btn btn-primary" id="filter_button"><i class="fa fa-search"></i></button>
            <button class="btn btn-secondary" id="reset_button"><i class="fa fa-refresh"></i></button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">Material Stock </h5>
            {{-- @can('site-master-create')
                <a href="{{ route('stocktransfer.create') }}" class="btn btn-sm btn-primary float-end new-create"><i
                        class="fa fa-plus me-50"></i> {{ __('message.common.addNew') }}</a>
            @endcan --}}
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                {{-- <th>Site</th>
                                <th>Supervisor</th> --}}
                                <th>Material</th>
                                <th>Quantity</th>
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
    const URL = "{{route('stock')}}";

    var table = '';
    var assignId = '';
    $(function() {
        table = $('#table').DataTable({
           ajax: {
                url: URL,
                data: function(d) {
                    d.site_id = $('#site_id').val();
                    d.supervisor_id = $('#supervisor_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
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
                    data: 'supervisor_name',
                    name: 'supervisor_name'
                },
                {
                    data: 'material_name',
                    name: 'material_name'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                }
            ],
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },

        });
    });

      // Filter Button
    $('#filter_button').click(function () {
        table.ajax.reload();
    });

    // Reset Button
    $('#reset_button').click(function () {
        $('#site_id').val('').trigger('change');
        $('#supervisor_id').val('').trigger('change');
        // $('#filter_supervisor_id').val('').trigger('change');
        // $('#filter_type').val('').trigger('change');
        // $('#filter_start_date').val('');
        // $('#filter_end_date').val('');
        table.ajax.reload();
    });



</script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
    <script src="{{ asset('assets/custom/status.js') }}"></script>
@endsection

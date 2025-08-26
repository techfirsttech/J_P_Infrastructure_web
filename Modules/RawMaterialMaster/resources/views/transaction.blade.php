@extends('layouts.app')
@section('title', __('rawmaterialmaster::message.list'))
@section('content')
    <style>
        .select2-search__field {
            width: auto !important;
            display: inline-block !important;
        }
    </style>

    {{-- <div class="row mb-2">
        <div class="col-md-3">
            <select id="filterMaterial" class="form-select">
                <option value="">Select Material</option>
                @foreach ($materials as $material)
                    <option value="{{ $material->id }}">{{ $material->material_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterSite" class="form-select">
                <option value="">Select Site</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterSupervisor" class="form-select">
                <option value="">Select Supervisor</option>
                @foreach ($supervisors as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="In">In</option>
                <option value="Out">Out</option>
            </select>
        </div>
        <div class="col-md-3 mt-2">
            <input type="date" id="startDate" class="form-control" placeholder="Start Date">
        </div>
        <div class="col-md-3 mt-2">
            <input type="date" id="endDate" class="form-control" placeholder="End Date">
        </div>
    </div> --}}
    <div class="row mb-3">
        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_material_id" class="form-label">Material</label>
            <select id="filter_material_id" class="select2 form-select">
                <option value="">All Materials</option>
                @foreach ($materials as $material)
                    <option value="{{ $material->id }}">{{ $material->material_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_site_id" class="form-label">Site</label>
            <select id="filter_site_id" class="select2 form-select">
                <option value="">All Sites</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_supervisor_id" class="form-label">Supervisor</label>
            <select id="filter_supervisor_id" class="select2 form-select">
                <option value="">All Supervisors</option>
                @foreach ($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 form-group custom-input-group">
            <label for="filter_type" class="form-label">Type</label>
            <select id="filter_type" class="select2 form-select">
                <option value="">All Types</option>
                <option value="In">In</option>
                <option value="Out">Out</option>
            </select>
        </div>

        <div class="col-md-3 form-group custom-input-group mt-2">
            <label for="filter_start_date" class="form-label">Start Date</label>
            <input type="text" class="form-control flatpickr-date" id="filter_start_date" placeholder="Start Date">
        </div>

        <div class="col-md-3 form-group custom-input-group mt-2">
            <label for="filter_end_date" class="form-label">End Date</label>
            <input type="text" class="form-control flatpickr-date" id="filter_end_date" placeholder="End Date">
        </div>

        <div class="col-md-3 text-start pt-4 mt-4">
            <button class="btn btn-primary" id="filter_button"><i class="fa fa-search"></i></button>
            <button class="btn btn-secondary" id="reset_button"><i class="fa fa-refresh"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0"> Item In/Out Transaction</h5>

        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Material Name</th>
                                <th>Site</th>
                                <th>Supervisor</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Type</th>
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
    const URL = "{{route('transaction')}}";

    var table = '';
    var assignId = '';
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: URL,
                data: function (d) {
                    d.material_id = $('#filter_material_id').val();
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.type = $('#filter_type').val();
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
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
                    data: 'material_name',
                    name: 'raw_material_masters.material_name'
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
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'type',
                    name: 'type'
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

     // Filter Button
    $('#filter_button').click(function () {
        table.ajax.reload();
    });

    // Reset Button
    $('#reset_button').click(function () {
        $('#filter_material_id').val('').trigger('change');
        $('#filter_site_id').val('').trigger('change');
        $('#filter_supervisor_id').val('').trigger('change');
        $('#filter_type').val('').trigger('change');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        table.ajax.reload();
    });

    // Date Picker
    flatpickr('.flatpickr-date', {
        enableTime: false,
        dateFormat: 'd-m-Y',
        maxDate: new Date(),
    });



</script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
    <script src="{{ asset('assets/custom/status.js') }}"></script>
@endsection

@extends('layouts.app')
@section('title', __('rawmaterialmaster::message.list'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0"> Item In/Out Transaction</h5>
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
                            <label for="filter_material_id" class="form-label">Material</label>
                            <select id="filter_material_id" name="filter_material_id" class="select2 form-select">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                @foreach ($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->material_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label for="filter_site_id" class="form-label">Site</label>
                            <select id="filter_site_id" name="filter_site_id" class="select2 form-select site-change">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                @foreach ($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2">
                            <label for="filter_supervisor_id" class="form-label">Supervisor</label>
                            <select id="filter_supervisor_id" name="filter_supervisor_id" class="select2 form-select">
                                <option value="All">{{ __('message.common.all') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-2 m-0">
                            <label for="filter_type" class="form-label">Type</label>
                            <select id="filter_type" name="filter_type" class="select2 form-select">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                <option value="In">In</option>
                                <option value="Out">Use</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-12 text-end">
                            @php $search = true; $reset = true; $export = false; @endphp
                            {{ view('layouts.filter-button', compact('search', 'reset', 'export')) }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Material Name</th>
                            <th>Site</th>
                            <th>Supervisor</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Remark</th>
                            <th>Action</th>
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
    $(function() {
        table = $('#table').DataTable({
            ajax: {
                url: URL,
                data: function(d) {
                    d.material_id = $('#filter_material_id').val();
                    d.site_id = $('#filter_site_id').val();
                    d.supervisor_id = $('#filter_supervisor_id').val();
                    d.type = $('#filter_type').val();
                    d.s_date = $('#s_date').val();
                    d.e_date = $('#e_date').val();
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
                    data: 'date',
                    name: 'raw_material_masters.created_at'
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

        });
    });


    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        var me = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            confirmButtonText: "Yes, delete it!",
            showCancelButton: true,
        }).then(function(result) {
            if (result.value) {

                axios.delete(Route + '/' + id)
                    .then(function(response) {
                        if (response.data.status_code == 200) {
                            toastr.success(response.data.message, "Success");
                            if (me.attr('data-del-class') !== undefined) {
                                $('.' + me.attr('data-del-class')).hide();
                            } else {
                                me.parent().parent().hide();
                            }
                        } else if (response.data.status_code == 201) {
                            toastr.warning(response.data.message, "Warning");
                        } else {
                            toastr.error(response.data.message, "Error");
                        }
                    })
                    .catch(function() {
                        toastr.error("Something went wrong. Please try again.", "Error");
                    });
            }
        });
    });

    $(document).on('change', '.site-change', function(e) {
        e.preventDefault();
        var id = $(this).val();
        if (id != 'All') {
            $("#filter_supervisor_id").append(`<option value="" selected><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait</option>`);
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
                        $("#filter_supervisor_id").empty();
                        $("#filter_supervisor_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
                        if (response.result.length > 0) {
                            $.each(response.result, function(index, row) {
                                $("#filter_supervisor_id").append($("<option value='" + row.id + "'>" + row.name + "</option>"));
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
            $("#filter_supervisor_id").empty();
            $("#filter_supervisor_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
            $('#filter_supervisor_id').val('All').trigger('change');
        }
    });
</script>
<script src="{{asset('assets/custom/filter.js')}}"></script>
<script src="{{ asset('assets/custom/status.js') }}"></script>
@endsection
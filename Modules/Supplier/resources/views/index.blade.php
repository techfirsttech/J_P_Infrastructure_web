@extends('layouts.app')
@section('title', __('supplier::message.list'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('supplier::message.list') }}</h5>
        @can('supplier-create')
        <a href="{{route('supplier.create')}}" class="btn btn-sm me-1 btn-primary new-create float-end"><i class="fa fa-plus me-25"></i> {{ __('message.common.addNew') }}</a>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <table id="table" class="datatables-basic table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('supplier::message.supplier_name') }}</th>
                            <th>{{ __('supplier::message.mobile') }}</th>
                            <th>{{ __('supplier::message.contact_number') }}</th>
                            <th>{{ __('supplier::message.gst_number') }}</th>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="detailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered align-items-start modal-xl">
        <div class="modal-content" id="modal_content">
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route('supplier.index')}}";
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
                    orderable: true,
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
                    data: 'supplier_name',
                    name: 'supplier_name'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    data: 'contact_number',
                    name: 'contact_number'
                },
                {
                    data: 'gst_number',
                    name: 'gst_number'
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

    $(document).on('click', '.view', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        if (id != '') {
            var route = "{{route('supplier.show', ':id')}}".replace(':id', id);
            $.ajax({
                type: "get",
                url: route,
                dataType: 'json',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $("#exampleModal").modal("show");
                    $("#modal_content").html('');
                    $("#modal_content").html(response.html);
                }
            });
        }
    });
</script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection

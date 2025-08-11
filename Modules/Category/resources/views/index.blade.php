@extends('layouts.app')
@section('title', 'Product '.__('category::message.list'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ 'Product '.__('category::message.list') }}</h5>
        @can('category-create')
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
                            <th>{{ __('category::message.name') }}</th>
                            <th>{{ __('category::message.parent') }}</th>
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
            {{ product_category_view() }}
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route($type.'category.index')}}";

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
                        var isResponsive = meta.settings.responsive && meta.settings.responsive
                            .details;
                        if (type === 'display' && isResponsive && meta.settings.responsive.details
                            .type === 'column') {
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
                    data: 'parent_name',
                    name: 'parent_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
                },
            ],
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'));
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
                            return "" !== t.title ? '<tr data-dt-row="' + t.rowIndex +
                                '" data-dt-column="' + t.columnIndex + '"><td>' + t.title +
                                " :</td> <td>" + t.data + "</td></tr>" : ""
                        }).join("");
                        return !!e && $('<table class="table table-sm"/><tbody />').append(e)
                    }
                }
            }
        });
    });

    $(document).on('click', '.edit', function() {
        $('.modal').modal('hide');
        var id = $(this).data('id');
        var url = "{{route($type.'category.edit','id')}}".replace('id', id);
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
                    $("#exampleModalTitle").html("{{ __('category::message.edit') }}");
                    $("#category_name").val(response.result.name);
                    $("#parent_id").val(response.result.parent_id).trigger('change');
                    $("#is_parent").prop('checked', false);
                    $(".is-parent").removeClass('d-none');
                    if (response.result.is_parent == '1') {
                        $("#is_parent").prop('checked', true);
                        $(".is-parent").addClass('d-none');
                    }
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
</script>
<script src="{{ asset('assets/custom/category.js') }}"></script>
<script src="{{ asset('assets/custom/save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
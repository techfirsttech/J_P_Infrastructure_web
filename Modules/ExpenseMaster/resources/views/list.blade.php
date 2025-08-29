@extends('layouts.app')
@section('title', __('expensemaster::message.expensemaster'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('expensemaster::message.list') }}</h5>
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('expensemaster::message.expenseCategoryName') }}</th>
                                <th>{{ __('expensemaster::message.site') }}</th>
                                <th>{{ __('expensemaster::message.supervisor') }}</th>
                                <th>{{ __('expensemaster::message.amount') }}</th>
                                <th>{{ __('expensemaster::message.remark') }}</th>
                                <th>Image</th>
                                <th>Status</th>
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
     'use strict';
     const URL = "{{route('expensemaster.index')}}";
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
                         data: 'expense_category_name',
                         name: 'expense_categories.expense_category_name'
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
                         data: 'amount',
                         name: 'amount'
                    },
                    {
                         data: 'remark',
                         name: 'remark'
                    },
                    {
                         data: 'document',
                         name: 'document'
                    },
                      {
                         data: 'status',
                         name: 'status'
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

    $(document).on('click', '.status', function() {
          let status = $(this).data('value');
          let id = $(this).data('id');
          var url = "{{route('expense-status-change')}}";
          $.ajax({
               type: "POST",
               url: url,
               dataType: 'json',
               data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "status": status,
               },
               success: function(response) {
                    if (response.status == true) {
                         table.ajax.reload(null, false);
                         toastr.success("Status Updated Successfully", "success'");
                    } else {
                         toastr.error("Something Went Wrong. Please Try Again.'", "error");
                    }
               },
               error: function(error) {
                    toastr.error("Something Went Wrong. Please Try Again.'", "error");
                    $(document.body).css('pointer-events', '');
               }
          });
    });

</script>
    <script src="{{ asset('assets/custom/save.js') }}"></script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection

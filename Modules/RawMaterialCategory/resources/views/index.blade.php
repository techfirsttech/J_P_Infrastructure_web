@extends('layouts.app')
@section('title', __('rawmaterialcategory::message.raw_material_category'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('rawmaterialcategory::message.list') }}</h5>
            @can('expense-category-create')
                <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal"
                    class="btn btn-sm btn-primary new-create float-end"><i
                        class="fa fa-plus me-25"></i>{{ __('message.common.addNew') }}</button>
            @endcan
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('rawmaterialcategory::message.name') }}</th>
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
                <div class="modal-header bg-transparent">
                    <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('rawmaterialcategory::message.add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="body">
                    <form id="form" action="javascript:void(0);" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                <label class="form-label"
                                    for="material_category_name">{{ __('rawmaterialcategory::message.material_category_name') }}
                                    <span class="text-danger">*</span></label>
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" name="material_category_name"
                                    id="material_category_name"
                                    placeholder="{{ __('rawmaterialcategory::message.material_category_name') }}">
                                <span class="invalid-feedback d-block" id="error_material_category_name"
                                    role="alert"></span>
                            </div>




                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                                    class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                                <button type="submit" class="btn btn-sm btn-primary float-end save"
                                    data-route="{{ route('rawmaterialcategory.store') }}">{{ __('message.common.submit') }}</button>
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
     const URL = "{{route('rawmaterialcategory.index')}}";
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
                         data: 'material_category_name',
                         name: 'material_category_name'
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

     $("#inlineModal").on("hidden.bs.modal", function(e) {
          $(this).find('form').trigger('reset');
          $("#id").val("");
          $(".invalid-feedback,.custom-error").html("");
          $(".save").html("Submit");
          $(".save").attr('disabled', false);
          $("#exampleModalTitle").html("{{ __('rawmaterialcategory::message.add') }}");
     });

     $(document).on('click', '.edit', function() {
          $('.modal').modal('hide');
          var id = $(this).data('id');
          var url = "{{route('rawmaterialcategory.edit','id')}}".replace('id', id);
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
                         $("#exampleModalTitle").html("{{ __('rawmaterialcategory::message.edit') }}");
                         $("#material_category_name").val(response.result.material_category_name);
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

     $("#form").validate({
          rules: {
               material_category_name: {
                    required: true,
               },

          },
          messages: {
               material_category_name: {
                    required: "{{ __('rawmaterialcategory::message.enter_material_category_name') }}",
               }
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


</script>
    <script src="{{ asset('assets/custom/save.js') }}"></script>
    <script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection

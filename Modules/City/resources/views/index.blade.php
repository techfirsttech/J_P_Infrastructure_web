@extends('layouts.app')
@section('title', __('city::message.city'))
@section('content')
<div class="row">
     <div class="col-12 mb-2">
          <h5 class="content-header-title float-start mb-0">{{ __('city::message.list') }}</h5>
          @can('city-create')
          <button type="button" data-bs-toggle="modal" data-bs-target="#inlineModal" class="btn btn-sm btn-primary new-create float-end"><i class="fa fa-plus me-25"></i>{{ __('message.common.addNew') }}</button>
          @endcan
     </div>
     <div class="col-12">
          <div class="card p-1">
               <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                         <thead>
                              <tr>
                                   <th>#</th>
                                   <th>{{ __('city::message.city') }}</th>
                                   <th>{{ __('city::message.state') }}</th>
                                   <th>{{ __('city::message.country') }}</th>
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
                    <h5 class="text-center mb-0" id="exampleModalTitle">{{ __('city::message.add') }} </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body " id="body">
                    <form id="form" action="javascript:void(0);" method="POST">
                         @csrf
                         <div class="row">
                              <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                   <label for="country_id" class="form-label">{{ __('city::message.country') }} <span class="text-danger">*</span></label>
                                   <select id="country_id" name="country_id" class="select2 form-select"
                                        data-placeholder="{{ __('message.common.select') }}">
                                        <option value=""></option>
                                        @if ($country->count() > 0)
                                        @foreach ($country as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }} - {{ $value->code }}
                                        </option>
                                        @endforeach
                                        @endif
                                   </select>
                                   <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                              </div>
                              <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                   <label for="state_id" class="form-label">{{ __('city::message.state') }} <span class="text-danger">*</span></label>
                                   <select id="state_id" name="state_id" class="select2 form-select" data-placeholder="{{ __('message.common.select') }}">
                                        <option value=""></option>
                                   </select>
                                   <span class="invalid-feedback d-block" id="error_state_id" role="alert"></span>
                              </div>

                              <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                   <label class="form-label" for="name">{{ __('city::message.name') }} <span class="text-danger">*</span></label>
                                   <input type="hidden" name="id" id="id" value="">
                                   <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('city::message.name') }}">
                                   <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                              </div>

                              <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                                   <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                                   <button type="submit" class="btn btn-sm btn-primary float-end save" id="save" data-route="{{ route('city.store') }}" data-token="{{ csrf_token() }}">{{ __('message.common.submit') }}</button>
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
     const URL = "{{route('city.index')}}";
     let STATE_ID = 0;
     let CITY_ID = 0;

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
                              return meta.row + meta.settings._iDisplayStart + 1;
                         },
                    },
                    {
                         data: 'name',
                         name: 'name'
                    },
                    {
                         data: function(row) {
                              return row.state ? row.state.name + ' - ' + row.state.code : '';
                         },
                         name: 'state.name',
                         className: 'text-nowrap'
                    },
                    {
                         data: function(row) {
                              return row.country ? row.country.name + ' - ' + row.country.code : '';
                         },
                         name: 'country.name',
                         className: 'text-nowrap'
                    },
                    {
                         data: 'action',
                         name: 'action',
                         orderable: false,
                         sortable: false,
                    },
               ],
               initComplete: function(settings, json) {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                         return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
               }
          });
     });

     $(document).on('change', '#country_id', function() {
          const countryId = $(this).val();
          if (countryId != null && countryId != 0) {
               getState(countryId);
          }
     });

     function getState(country_id) {
          if (country_id != '') {
               var url = "{{route('change-state')}}";
               $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: {
                         "_token": "{{ csrf_token() }}",
                         "id": country_id,
                    },
                    success: function(data) {
                         if (data.status_code == 200) {
                              $("#state_id").empty();
                              $("#state_id").append(`<option value="" selected disabled>{{ __('message.common.select') }}</option>`);
                              $.each(data.result, function(index, row) {
                                   if (STATE_ID == row.id) { //state_id === row.id ||
                                        $("#state_id").append($("<option selected value='" + row.id + "'>" + row.name + ' | ' + row.code + "</option>"));
                                   } else {
                                        $("#state_id").append($("<option value='" + row.id + "'>" + row.name + ' | ' + row.code + "</option>"));
                                   }
                              });
                              if (STATE_ID != 0) {
                                   $("#state_id").trigger('change');
                              }
                         } else if (data.status_code == 201) {
                              toastr.warning(data.message, "Warning");
                         } else {
                              toastr.error(data.message, "Error");
                         }
                    },
                    error: function(error) {
                         $(document.body).css('pointer-events', '');
                    }
               });
          }
     }

     $("#inlineModal").on("hidden.bs.modal", function(e) {
          $(this).find('form').trigger('reset');
          $("#id").val("");
          $("#state_id").val(0).trigger('change');
          $("#country_id").val(0).trigger('change');
          STATE_ID = 0;
          CITY_ID = 0;
          $(".invalid-feedback").html("");
          $(".custom-error").html("");
          $(".save").html("Submit");
          $(".save").attr('disabled', false);
          $("#exampleModalTitle").html("{{ __('city::message.add') }}");
     });

     $(document).on('click', '.edit', function() {
          $('.modal').modal('hide');
          var id = $(this).data('id');
          var url = "{{route('city.edit','id')}}".replace('id', id);
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
                         $("#exampleModalTitle").html("{{ __('city::message.edit') }}");
                         $("#name").val(data.result.name);
                         $("#state_id").val(data.result.state_id).trigger('change');
                         $("#country_id").val(data.result.country_id).trigger('change');
                         $("#id").val(id);
                         STATE_ID = data.result.state_id;
                         $("#inlineModal").modal('show');
                    } else if (data.status_code == 201) {
                         toastr.warning(data.message, "Warning");
                    } else {
                         toastr.error(data.message, "Error");
                    }
               }
          });
     });

     $("#form").validate({
          rules: {
               country_id: {
                    required: true,
               },
               state_id: {
                    required: true,
               },
               name: {
                    required: true,
               },
          },
          messages: {
               country_id: {
                    required: "{{ __('city::message.select_country') }}",
               },               
               state_id: {
                    required: "{{ __('city::message.select_state') }}",
               },               
               name: {
                    required: "{{ __('city::message.enter_name') }}",
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
</script>
<script src="{{ asset('assets/js/location-save.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
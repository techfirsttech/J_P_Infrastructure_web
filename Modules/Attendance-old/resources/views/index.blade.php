@extends('layouts.app')
@section('title', __('attendance::message.attendance'))
@section('content')
<div class="row">
     <div class="col-12 mb-2">
          <h5 class="content-header-title float-start mb-0">{{ __('attendance::message.list') }}</h5>
          @can('attendance-list')
          <a href="{{ route('attendance.show',1) }}" role="button" class="btn btn-sm btn-primary float-end"><i class="fa fa-eye me-1"></i> Report</a>
          @endcan
     </div>
     <div class="col-12 mb-2">
          <div class="card">
               <div class="card-body">
                    <form id="filter_form" action="javascript:void(0)" method="POST">
                         @csrf
                         <div class="row g-2 pt-25 align-items-end">
                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="s_date">{{ __('message.common.start_date') }}</label>
                                   <input type="text" class="form-control flatpickr" name="s_date" id="s_date" value="{{ date('Y-m-d') }}" autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="e_date">{{ __('message.common.end_date') }}</label>
                                   <input type="text" class="form-control flatpickr" name="e_date" id="e_date" value="{{ date('Y-m-d') }}" autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="leave_type">{{ __('attendance::message.type') }}</label>
                                   <select id="leave_type" name="leave_type" class="form-select select2">
                                        <option value="All">{{ __('message.common.all') }}</option>
                                        <option value="Half">{{ __('attendance::message.half') }}</option>
                                        <option value="Full">{{ __('attendance::message.full') }}</option>
                                        <option value="Absent">{{ __('attendance::message.absent') }}</option>
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="site_id">{{ __('attendance::message.site') }}</label>
                                   <select id="site_id" name="site_id" class="form-select select2">
                                        <option selected value="All">{{ __('message.common.all') }}</option>
                                        @foreach ($site as $st)
                                        <option value="{{$st->id}}">{{ $st->site_name }}</option>
                                        @endforeach
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="contractor_id">{{ __('attendance::message.contractor') }}</label>
                                   <select id="contractor_id" name="contractor_id" class="form-select select2">
                                        <option selected value="All">{{ __('message.common.all') }}</option>
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2">
                                   @php $search = true; $reset = true; $export = false; @endphp
                                   {{ view('layouts.filter-button', compact('search', 'reset', 'export')) }}
                              </div>
                         </div>
                    </form>
               </div>
          </div>
     </div>
     <div class="col-12">
          <div class="card p-1">
               <div class="card-body">
                    <table id="table" class="datatables-basic table table-hover">
                         <thead>
                              <tr>
                                   <th>#</th>
                                   <th>{{ __('attendance::message.date') }}</th>
                                   <th>{{ __('attendance::message.type') }}</th>
                                   <th>{{ __('attendance::message.labour') }}</th>
                                   <th>{{ __('attendance::message.contractor') }}</th>
                                   <th>{{ __('attendance::message.site') }}</th>
                                   <th>{{ __('attendance::message.supervisor') }}</th>
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
     'use strict';
     const URL = "{{route('attendance.index')}}";
     var table = '';
     $(function() {
          table = $('#table').DataTable({
               ajax: {
                    url: URL,
                    data: function(d) {
                         d.s_date = $('#s_date').val();
                         d.e_date = $('#e_date').val();
                         d.leave_type = $('#leave_type').val();
                         d.site_id = $('#site_id').val();
                         d.contractor_id = $('#contractor_id').val();
                    }
               },
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
                         data: 'date',
                         name: 'date'
                    },
                    {
                         data: 'type',
                         name: 'type'
                    },
                    {
                         data: 'labour_name',
                         name: 'labour.labour_name'
                    },
                    {
                         data: 'contractor_name',
                         name: 'contractor.contractor_name'
                    },
                    {
                         data: 'site_name',
                         name: 'site.site_name'
                    },
                    {
                         data: 'user_name',
                         name: 'user.name'
                    },
                    {
                         data: 'action',
                         name: 'action',
                         visible: false
                    }
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

     $(document).on('change', '#site_id', function(e) {
          e.preventDefault();
          var id = $(this).val();
          if (id != 'All') {
               $("#contractor_id").append(`<option value="" selected><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait</option>`);
               var route = "{{ route('get-contractor') }}";
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
                              $("#contractor_id").empty();
                              $("#contractor_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
                              if (response.result.length > 0) {
                                   $.each(response.result, function(index, row) {
                                        $("#contractor_id").append($("<option value='" + row.id + "'>" + row.contractor_name + "</option>"));
                                   });
                              } else {
                                   toastr.warning('Contractor not found.', "Warning");
                              }
                         } else if (response.status_code == 201 || response.status_code == 404) {
                              toastr.warning(response.message, "Warning");
                         } else {
                              toastr.error(response.message, "Opps!");
                         }
                    }
               });
          } else {
               $('#contractor_id').val('All').trigger('change');
          }
     });
</script>
<script src="{{asset('assets/custom/filter.js')}}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
@extends('layouts.app')
@section('title', __('report::message.attendance_report'))
@section('content')
<div class="row">
     <div class="col-12 mb-2">
          <h5 class="content-header-title float-start mb-0">{{ __('report::message.attendance_report') }}</h5>
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
                                   <label class="form-label" for="site_id">{{ __('attendance::message.site') }}</label>
                                   <select id="site_id" name="site_id" class="form-select select2">
                                        <option selected value="All">{{ __('message.common.all') }}</option>
                                        @foreach ($site as $st)
                                        <option value="{{ $st->id }}">{{ $st->site_name }}</option>
                                        @endforeach
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="contractor_id">{{ __('attendance::message.contractor') }}</label>
                                   <select id="contractor_id" name="contractor_id" class="form-select select2">
                                        <option selected value="All">{{ __('message.common.all') }}</option>
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2 m-0">
                                   <label class="form-label" for="labour_id">{{ __('attendance::message.labour') }}</label>
                                   <select id="labour_id" name="labour_id" class="form-select select2">
                                        <option selected value="All">{{ __('message.common.all') }}</option>
                                   </select>
                              </div>

                              <div class="col-12 col-md-4 col-lg-2">
                                   @php $search = true; $reset = true; $export = $urlPdf; @endphp
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
                                   <th>{{ __('attendance::message.labour') }}</th>
                                   <th>{{ __('attendance::message.contractor') }}</th>
                                   <th>{{ __('attendance::message.site') }}</th>
                                   <th>{{ __('report::message.full') }}</th>
                                   <th>{{ __('report::message.half') }}</th>
                                   <th>{{ __('report::message.absent') }}</th>
                                   <th>{{ __('report::message.salary') }}</th>
                              </tr>
                         </thead>
                         <tbody>
                         </tbody>
                         <tfoot>
                              <td colspan="7" class="text-end pe-5">{{ __('report::message.total') }}</td>
                              <td class="total text-end pe-5">0.00</td>
                         </tfoot>
                    </table>
               </div>
          </div>
     </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
     'use strict';
     const URL = "{{ $url }}";
     var table = '';
     $(function() {
          table = $('#table').DataTable({
               ajax: {
                    url: URL,
                    data: function(d) {
                         d.s_date = $('#s_date').val();
                         d.e_date = $('#e_date').val();
                         d.site_id = $('#site_id').val();
                         d.contractor_id = $('#contractor_id').val();
                         d.labour_id = $("#labour_id").val();
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
                         data: 'DT_RowIndex',
                         name: 'DT_RowIndex',
                         title: '#',
                         orderable: false,
                         searchable: false
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
                         data: 'full_count',
                         name: 'full_count'
                    },
                    {
                         data: 'half_count',
                         name: 'half_count'
                    },
                    {
                         data: 'absent_count',
                         name: 'absent_count'
                    },
                    {
                         data: 'salary',
                         name: 'salary',
                         className: 'text-end pe-5'
                    },
               ],
               footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    const safeParse = (val) => {
                         if (val === null || val === undefined || val === '-' || val === '') return 0;
                         if (typeof val === 'string') val = val.replace(/,/g, '').trim();
                         let num = parseFloat(val);
                         return isNaN(num) ? 0 : num;
                    };

                    let totalSalary = api.column(7, {
                              page: 'current'
                         }).data()
                         .reduce((a, b) => safeParse(a) + safeParse(b), 0);

                    $('.total').html(totalSalary.toFixed(2));

               },
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

     $(document).on('change', '#contractor_id', function(e) {
          e.preventDefault();
          var id = $(this).val();
          if (id != 'All') {
               $("#labour_id").append(`<option value="" selected><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait</option>`);
               var route = "{{ route('get-contractor-labour') }}";
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
                              $("#labour_id").empty();
                              $("#labour_id").append(`<option value="All" selected>{{ __('message.common.all') }}</option>`);
                              if (response.result.length > 0) {
                                   $.each(response.result, function(index, row) {
                                        $("#labour_id").append($("<option value='" + row.id + "'>" + row.labour_name + "</option>"));
                                   });
                              } else {
                                   toastr.warning('Labour not found.', "Warning");
                              }
                         } else if (response.status_code == 201 || response.status_code == 404) {
                              toastr.warning(response.message, "Warning");
                         } else {
                              toastr.error(response.message, "Opps!");
                         }
                    }
               });
          } else {
               $('#labour_id').val('All').trigger('change');
          }
     });
</script>
<script src="{{ asset('assets/custom/filter.js') }}"></script>
<script src="{{ asset('assets/custom/delete.js') }}"></script>
@endsection
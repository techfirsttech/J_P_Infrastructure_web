@extends('layouts.app')
@section('title', __('attendance::message.add'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('attendance::message.add') }}</h5>
        @can('attendance-list')
        <a href="{{ route('attendance.index') }}" role="button" class="btn btn-sm btn-primary float-end"><i
                class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <form id="form" action="{{ route('attendance.store') }}" method="POST" autocomplete="nope">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="date"> Date</label>
                            <input type="text" class="form-control flatpickr-date" name="date" id="date"
                                placeholder="Date" value="">
                            <span class="invalid-feedback d-block" id="error_date"
                                role="alert">{{ $errors->first('date') }}</span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="supervisor_id">{{ __('attendance::message.supervisor') }}
                                <span class="text-danger">*</span></label>

                            <select id="supervisor_id" name="supervisor_id" class="select2 form-select"
                                data-placeholder="{{ __('message.common.select') }}">
                                <option value=""></option>
                                @if ($supervisor->count() > 0)
                                @foreach ($supervisor as $value)
                                <option value="{{ $value->id }}">{{ $value->name }} </option>
                                @endforeach
                                @endif
                            </select>

                        </div>



                        <div class="col-12 col-md-4 col-lg-3 m-0">
                            <label class="form-label" for="site_id">{{ __('attendance::message.site') }}</label>
                            <select id="site_id" name="site_id" class="form-select select2">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                                @foreach ($site as $st)
                                <option value="{{ $st->id }}">{{ $st->site_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 col-lg-3 m-0">
                            <label class="form-label"
                                for="contractor_id">{{ __('attendance::message.contractor') }}</label>
                            <select id="contractor_id" name="contractor_id" class="form-select select2">
                                <option selected value="All">{{ __('message.common.all') }}</option>
                            </select>
                        </div>

                        <div class="col-8">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th>Labour Name</th>
                                        <th class="text-center" width="10%">Full</th>
                                        <th class="text-center" width="10%">Half</th>
                                        <th class="text-center" width="10%">Absent</th>
                                    </tr>
                                </thead>
                                <tbody id="labour_table_body">
                                    <tr>
                                        <td colspan="5" class="text-center">Select Contractor First</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>



                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="reset"
                                class="btn btn-sm btn-label-secondary float-start reset">{{ __('message.common.cancel') }}</button>
                            <button type="submit"
                                class="btn btn-primary float-end">{{ __('message.common.submit') }}</button>
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
    @if($message = Session::get('error'))
    toastr.error("{{ addslashes($message) }}", "Error");
    @endif

    $(document).ready(function() {
        flatpickr('.flatpickr-date', {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: '',
            maxDate: new Date(),
        });
    });


    'use strict';
    const URL = "{{route('attendance.index')}}";

    $('.number').on('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
        this.value = this.value.slice(0, 10);
    });

    $(document).on('click', '.reset', function() {
        $('#form')[0].reset();
        $('.select2').val('').trigger('change');
        $('.custom-error').html('');
    });

    $("#form").validate({
        rules: {
            supervisor_id: {
                required: true,
            },
            site_id: {
                required: true,
            },
            contractor_id: {
                required: true,
            }
        },
        messages: {
            supervisor_id: {
                required: "{{ __('attendance::message.select_supervisor') }}"
            },
            site_id: {
                required: "{{ __('attendance::message.select_site') }}"
            },
            contractor_id: {
                required: "{{ __('attendance::message.select_contractor') }}"
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

    $(document).ready(function() {

        // On Supervisor change → load Sites
        // $('#supervisor_id').on('change', function () {
        //     var supervisorId = $(this).val();
        //     $('#site_id').html('<option value="">Select Site</option>');
        //     $('#contractor_id').html('<option value="">Select Contractor</option>');
        //     $('#labour_id').html('<option value="">Select Labour</option>');

        //     if (supervisorId) {
        //         $.get("{{ url('/get-sites') }}/" + supervisorId, function (data) {
        //             $.each(data, function (key, site) {
        //                 $('#site_id').append('<option value="'+ site.id +'">'+ site.site_name +'</option>');
        //             });
        //         });
        //     }
        // });

        $(document).on('change', '#supervisor_id', function(e) {
            e.preventDefault();
            var supervisorId = $(this).val();

            // Reset dependent dropdowns + table
            $('#site_id').html('<option value="">Select Site</option>');
            $('#contractor_id').html('<option value="">Select Contractor</option>');
            $("#labour_table_body").html(`<tr><td colspan="6" class="text-center">Select Contractor First</td></tr>`);

            if (supervisorId) {
                $.ajax({
                    type: "get",
                    url: "{{ route('get-site') }}",
                    data: {
                        "id": supervisorId,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status_code == 200 && response.result.length > 0) {
                            $.each(response.result, function(index, site) {
                                $('#site_id').append(
                                    `<option value="${site.id}">${site.site_name}</option>`
                                );
                            });
                        } else {
                            toastr.warning('Sites not found for this Supervisor', "Warning");
                        }
                    }
                });
            }
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


    });

    $(document).on('change', '#contractor_id', function(e) {
        e.preventDefault();
        var id = $(this).val();

        if (id != 'All') {
            getData();
        } else {
            $("#labour_table_body").html(`<tr><td colspan="5" class="text-center">Select Contractor First</td></tr>`);
        }
    });

    $(document).on('change', '#date', function() {
        if ($(this).val() != '') {
            var contractor_id = $('#contractor_id').val();
            if (contractor_id != 'All') {
                getData();
            }
        }
    });

    function getData() {
        var contractor_id = $('#contractor_id').val();
        if (contractor_id != 'All') {
            $("#labour_table_body").html(
                `<tr><td colspan="5" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status"></div> Loading...
                </td></tr>`
            );

            var route = "{{ route('get-contractor-labour') }}";
            $.ajax({
                type: "get",
                url: route,
                dataType: 'json',
                data: {
                    "id": contractor_id,
                    "date": $("#date").val(), // form ma select karel date levani
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $("#labour_table_body").empty();
                    if (response.status_code == 200) {
                        if (response.result.length > 0) {
                            $.each(response.result, function(index, row) {
                                $("#labour_table_body").append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>
                                            <input type="hidden" name="labours[${index}][id]" value="${row.id}">
                                            ${row.labour_name}
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="labours[${index}][type]" value="Full" class="form-check-input" ${row.attendance_type == 'Full' ? 'checked' : ''}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="labours[${index}][type]" value="Half" class="form-check-input" ${row.attendance_type == 'Half' ? 'checked' : ''}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="labours[${index}][type]" value="Absent" class="form-check-input" ${row.attendance_type == 'Absent' ? 'checked' : ''}>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            $("#labour_table_body").html(`<tr><td colspan="5" class="text-center">No Labour Found</td></tr>`);
                        }
                    }
                }
            });
        }
    }
</script>
@endsection
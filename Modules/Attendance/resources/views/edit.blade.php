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
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="site_id">{{ __('attendance::message.site') }}
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="site_id" id="site_id"
                                    placeholder="{{ __('attendance::message.site_id') }}"
                                    value="{{ old('site_id', $attendance->site_name) }}" readonly>
                                <span class="invalid-feedback d-block" id="error_site_id"
                                    role="alert">{{ $errors->first('site_id') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="contractor_id">{{ __('attendance::message.contractor') }}
                                </label>
                                <input type="text" class="form-control " id="contractor_id" name="contractor_id"
                                    placeholder="{{ __('attendance::message.contractor') }}"
                                    value="{{ old('contractor_id', $attendance->contractor_name) }}" readonly>
                                <span class="invalid-feedback d-block" id="error_contractor_id"
                                    role="alert">{{ $errors->first('contractor_id') }}</span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="date"> Date</label>
                                <input type="text" class="form-control flatpickr-date" name="date" id="date"
                                    placeholder="Date" value="">
                                <span class="invalid-feedback d-block" id="error_date"
                                    role="alert">{{ $errors->first('date') }}</span>
                            </div>
                            <div id="labourTableContainer"></div>
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
             defaultDate: '', // default = today - 15 days
        minDate: new Date(new Date().setDate(new Date().getDate() - 15)), // minimum = today - 15 days
        maxDate: new Date(), // maximum = today
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
            date: {
                required: true,
            }

        },
        messages: {
            date: {
                required: "{{ __('attendance::message.date') }}"
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

    $(document).on('change', '#state_id', function () {
    let stateID = $(this).val();

    // ----------

    $(document).on('change', '#date, #contractor_id', function() {
    let date = $('#date').val();
    let contractor_id = $('#contractor_id').val();

    if(date && contractor_id){
        $.ajax({
            url: "{{ route('get-labours') }}",
            data: { date: date, contractor_id: contractor_id },
            type: 'GET',
            success: function(res){
                if(res.status){
                    let tbody = '';
                    res.labours.forEach(function(labour, index){
                        let checked = res.attendedLabourIds.includes(labour.id) ? 'checked disabled' : '';
                        tbody += '<tr>'+
                            '<td>'+ (index+1) +'</td>'+
                            '<td>'+ labour.labour_name +'</td>'+
                            '<td class="text-center">'+
                                '<input type="checkbox" name="labour_ids[]" value="'+labour.id+'" '+checked+'>'+
                            '</td>'+
                        '</tr>';
                    });
                    $('#labourTable tbody').html(tbody);
                }
            }
        });
    }
});


});

</script>
@endsection

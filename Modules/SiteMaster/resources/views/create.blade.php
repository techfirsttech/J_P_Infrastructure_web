@extends('layouts.app')
@section('title', __('sitemaster::message.add'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('sitemaster::message.add') }}</h5>
            @can('sitemaster-list')
                <a href="{{ route('sitemaster.index') }}" role="button" class="btn btn-sm btn-primary float-end"><i
                        class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
            @endcan
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <form id="form" action="{{ route('sitemaster.store') }}" method="POST" autocomplete="nope">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 form-group custom-input-group">
                                {{-- <input type="hidden" name="user_profile_id" id="user_profile_id"
                                    value="{{ old('user_profile_id') }}"> --}}
                                <label class="form-label" for="site_name">{{ __('sitemaster::message.site_name') }}
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="site_name" id="site_name"
                                    placeholder="{{ __('sitemaster::message.site_name') }}" value="{{ old('site_name') }}"
                                    required>
                                <span class="invalid-feedback d-block" id="error_site_name"
                                    role="alert">{{ $errors->first('site_name') }}</span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="user_id">{{ __('sitemaster::message.user') }} <span
                                        class="text-danger">*</span></label>

                                <select id="user_id" name="user_id[]" class="select2 form-select"
                                    data-placeholder="{{ __('message.common.select') }}" multiple>
                                    <option value=""></option>
                                    @if ($supervisor->count() > 0)
                                        @foreach ($supervisor as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }} </option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>

                            {{-- <div class="col-12 col-sm-12 col-md-4 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="country_id">{{ __('sitemaster::message.country') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="country_id" id="country_id"
                                    placeholder="Choose Country" value="{{ old('country_id') }}" required>
                                <span class="invalid-feedback d-block" id="error_country_id"
                                    role="alert">{{ $errors->first('country_id') }}</span>
                            </div> --}}

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="state_id">{{ __('sitemaster::message.state') }}</label>

                                <select id="state_id" name="state_id" class="select2 form-select">
                                    <option value="">{{ __('message.common.select') }}</option>
                                    @if ($state->count() > 0)
                                        @foreach ($state as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="invalid-feedback d-block" id="error_state_id"
                                    role="alert">{{ $errors->first('state_id') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="city_id">{{ __('sitemaster::message.city') }}<span
                                        class="text-danger">*</span></label>

                                <select id="city_id" name="city_id" class="select2 form-select">
                                    <option value="">{{ __('message.common.select') }}</option>

                                </select>
                                <span class="invalid-feedback d-block" id="error_city_id"
                                    role="alert">{{ $errors->first('city_id') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="pincode">{{ __('sitemaster::message.pincode') }} </label>
                                <input type="text" class="form-control text-lowercase" id="pincode" name="pincode"
                                    placeholder="{{ __('sitemaster::message.pincode') }}" value="{{ old('pincode') }}">
                                <span class="invalid-feedback d-block" id="error_pincode"
                                    role="alert">{{ $errors->first('pincode') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                <label class="form-label" for="address">{{ __('sitemaster::message.address') }} </label>
                                <textarea class="form-control" name="address" id="address"> </textarea>
                                <span class="invalid-feedback d-block" id="error_address"
                                    role="alert">{{ $errors->first('address') }}</span>
                            </div>



                            {{-- <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="status">{{ __('sitemaster::message.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="select2 form-select select2-hidden-accessible" name="status"
                                    id="status">
                                    <option value="Active">{{ __('message.common.active') }}</option>
                                    <option value="InActive">{{ __('message.common.inactive') }}</option>
                                </select>
                                <span class="invalid-feedback d-block" id="error_role_id"
                                    role="alert">{{ $errors->first('status') }}</span>
                            </div> --}}

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
    const URL = "{{route('sitemaster.index')}}";

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
            site_name: {
                required: true,
            },


            "roles[]": {
                required: true,
            },
        },
        messages: {
            site_name: {
                required: "{{ __('sitemaster::message.enter_site_name') }}"
            },


            "roles[]": {
                required: "{{ __('sitemaster::message.select_role') }}"
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

    $(document).on('change', '#state_id', function () {
    let stateID = $(this).val();

    // city dropdown clear + placeholder
    $('#city_id').html('<option value=""></option>').trigger('change');

    if (stateID) {
        $.ajax({
            url: '/get-cities/' + stateID,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $.each(data, function (key, city) {
                    $('#city_id').append('<option value="' + city.id + '">' + city.name + '</option>');
                });

                // Select2 re-init
                $('#city_id').trigger('change');
            }
        });
    }
});

</script>
@endsection

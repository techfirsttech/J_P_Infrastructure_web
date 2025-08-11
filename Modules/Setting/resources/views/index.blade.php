@extends('layouts.app')
@section('title', __('setting::message.setting'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('setting::message.setting') }}</h5>
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <form action="javascript:void(0);" id="form" method="POST" autocomplete="none"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label class="form-label" for="company_name">{{ __('setting::message.company_name') }}</label>
                            <input type="text" class="form-control" name="company_name" id="company_name" placeholder="{{  __('setting::message.company_name') }}" value="{{ isset($setting) && isset($setting->company_name) ? $setting->company_name : '' }}">
                            <span class="invalid-feedback d-block" id="error_company_name" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="tag_line">{{ __('setting::message.tag_line') }}</label>
                            <input type="text" class="form-control" name="tag_line" id="tag_line"
                                placeholder="{{  __('setting::message.tag_line') }}"
                                value="{{ isset($setting) && isset($setting->tag_line) ? $setting->tag_line : '' }}">
                            <span class="invalid-feedback d-block" id="error_tag_line" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="gst_number">{{ __('setting::message.gst_number') }}</label>
                            <input type="text" class="form-control" name="gst_number" id="gst_number"
                                placeholder="{{  __('setting::message.gst_number') }}"
                                value="{{ isset($setting) && isset($setting->gst_number) ? $setting->gst_number : '' }}">
                            <span class="invalid-feedback d-block" id="error_gst_number" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="pancard_number">{{ __('setting::message.pancard_number') }}</label>
                            <input type="text" class="form-control" name="pancard_number" id="pancard_number"
                                placeholder="{{  __('setting::message.pancard_number') }}"
                                value="{{ isset($setting) && isset($setting->pancard_number) ? $setting->pancard_number : '' }}">
                            <span class="invalid-feedback d-block" id="error_pancard_number" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="tan_number">{{ __('setting::message.tan_number') }}</label>
                            <input type="text" class="form-control" name="tan_number" id="tan_number"
                                placeholder="{{  __('setting::message.tan_number') }}"
                                value="{{ isset($setting) && isset($setting->tan_number) ? $setting->tan_number : '' }}">
                            <span class="invalid-feedback d-block" id="error_tan_number" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="email">{{ __('setting::message.email') }}</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="{{  __('setting::message.email') }}"
                                value="{{ isset($setting) && isset($setting->email) ? $setting->email : '' }}">
                            <span class="invalid-feedback d-block" id="error_email" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                            <label class="form-label" for="mobile">{{ __('setting::message.mobile') }}</label>
                            <input type="text" class="form-control" name="mobile" id="mobile"
                                placeholder="Mobile"
                                value="{{ isset($setting) && isset($setting->mobile) ? $setting->mobile : '' }}">
                            <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="address">{{ __('setting::message.address') }} <span class="text-danger"></span></label>
                            <textarea name="address" id="address" class="form-control">{{ old('address', $setting->address ?? '') }}</textarea>
                        </div>

                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="country_id">{{ __('message.common.country') }}</label>
                            <select class="select2 form-select select2-hidden-accessible" name="country_id" id="country_id" data-placeholder="{{ __('message.common.select') }}">
                                <option value=""></option>
                                @foreach ($country as $value)
                                <option value="{{ $value->id }}" {{ $setting->country_id == $value->id ? 'selected' : ''}}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="state_id">{{ __('message.common.state') }}</label>
                            <select class="select2 form-select select2-hidden-accessible" name="state_id" id="state_id">
                                <option value=""></option>
                            </select>
                            <span class="invalid-feedback d-block" id="error_state" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="city_id">{{ __('message.common.city') }}</label>
                            <select class="select2 form-select select2-hidden-accessible" name="city_id" id="city_id" data-placeholder="{{ __('message.common.select') }}">
                                <option value=""></option>
                            </select>
                            <span class="invalid-feedback d-block" id="error_city" role="alert"></span>
                        </div>

                        <div class="col-12 col-sm-8 col-md-4 col-lg-2 form-group custom-input-group">
                            <label class="form-label" for="logo">{{ __('setting::message.logo') }}</label>
                            <input type="file" class="form-control" name="logo" id="logo"
                                placeholder="Logo" value="{{ old('logo') }}">
                            <span class="invalid-feedback d-block" id="error_logo" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-4 col-md-2 col-lg-2 form-group custom-input-group bg-white rounded text-center mt-4 p-1">
                            <img id="logo_preview" class="img_thum rounded" height="auto" width="150" src="@if (isset($setting) && isset($setting->logo) && !empty($setting->logo)) {{ asset('setting/logo/' . $setting->logo) }} @else {{ asset('assets/img/avatars/1.png') }} @endif">
                        </div>
                        <div class="col-12 col-sm-8 col-md-4 col-lg-2 form-group custom-input-group">
                            <label class="form-label" for="logo_dark">{{ __('setting::message.logo_dark') }}</label>
                            <input type="file" class="form-control" name="logo_dark" id="logo_dark"
                                placeholder="Logo" value="{{ old('logo_dark') }}">
                            <span class="invalid-feedback d-block" id="error_logo_dark" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-4 col-md-2 col-lg-2 form-group custom-input-group bg-dark rounded text-center  mt-4 p-1">
                            <img id="logo_dark_preview" class="img_thum rounded" height="auto" width="150" src="@if (isset($setting) && isset($setting->logo_dark) && !empty($setting->logo_dark)) {{ asset('setting/logo_dark/' . $setting->logo_dark) }} @else {{ asset('assets/img/avatars/1.png') }} @endif">
                        </div>

                        <div class="col-12 col-sm-8 col-md-4 col-lg-2 form-group custom-input-group">
                            <label class="form-label" for="favicon">{{ __('setting::message.favicon') }}</label>
                            <input type="file" class="form-control" name="favicon" id="favicon"
                                placeholder="Favicon" value="{{ old('favicon') }}">
                            <span class="invalid-feedback d-block" id="error_favicon" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-4 col-md-2 col-lg-2 form-group custom-input-group mt-4 p-1">
                            <img id="favicon_preview" class="img_thum rounded" height="45" width="45" src="@if (isset($setting) && isset($setting->favicon)) {{ asset('setting/favicon/' . $setting->favicon) }} @else {{ asset('assets/img/avatars/1.png') }} @endif">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <a href="{{ route('setting.index') }}"
                                class="btn btn-label-secondary float-start">{{ __('message.common.cancel') }}</a>
                            <button type="submit" id="save"
                                class="btn btn-primary float-end save">{{ __('message.common.submit') }}</button>
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
    document.querySelectorAll('.phone-input').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    function previewImage(event) {
        const input = event.target;
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgElement = document.getElementById('logo');
            imgElement.src = e.target.result;
        };
        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        }
    }
    let STATE_ID = "{{ !is_null($setting->state_id) ? $setting->state_id : '0' }}";
    let CITY_ID = "{{ !is_null($setting->city_id) ? $setting->city_id : '0' }}";

    $(document).ready(function() {
        $('#country_id').trigger('change');
    });

    $(document).on('change', '#country_id', function() {
        const countryId = $(this).val();
        if (countryId != null && countryId != 0) {
            getState(countryId);
        }
    });

    $('#state_id').on('change', function() {
        const stateId = $(this).val();
        if (stateId != null && stateId != 0) {
            getCity(stateId);
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

    function getCity(state_id) {
        if (state_id != '' && state_id != 0) {
            var url = "{{route('change-city')}}";
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": state_id,
                },
                success: function(data) {
                    if (data.status_code == 200) {
                        $("#city_id").empty();
                        $("#city_id").append(`<option value="" selected disabled>{{ __('message.common.select') }}</option>`);
                        $.each(data.result, function(index, row) {
                            if (CITY_ID == row.id) {
                                $("#city_id").append($("<option value='" + row.id + "' selected>" + row.name + "</option>"));
                            } else {
                                $("#city_id").append($("<option value='" + row.id + "'>" + row.name + "</option>"));
                            }
                        });
                    } else if (data.status_code == 201) {
                        toastr.warning(data.message, "Warning");
                    } else {
                        toastr.error(data.message, "Error");
                    }
                },
                error: function(error) {
                    toastr.error(error, "Error");
                    $(document.body).css('pointer-events', '');
                }
            });
        }
    }


    $(document).on('click', '.save', function(e) {
        e.preventDefault();
        var formData = new FormData($("#form")[0]);
        $.ajax({
            type: 'POST',
            url: "{{ route('setting.store') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(".invalid-feedback").html('');
                $("#save").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait`);
                $("#save").attr('disabled', true);
            },
            success: function(response) {
                $("#save").html("Submit");
                $("#save").attr('disabled', false);

                if (response.status_code == 500) {
                    toastr.error("Something went wrong. Please try again.", "Error");

                } else if (response.status_code == 403) {
                    toastr.warning("Please input proper data.", "Warning");

                } else {
                    $('#form')[0].reset();
                    setTimeout(function() {
                        location.href = response.data;
                    }, 500);
                    toastr.success("Saved successfully.", "Success");
                }

            }
        });
    });
</script>
@endsection
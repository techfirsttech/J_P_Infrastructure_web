@extends('layouts.app')
@section('title', __('supplier::message.add'))
@section('content')
@section('pagecss')
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/css/typography.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/css/katex.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/css/editor.css')}}">
@endsection
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('supplier::message.add') }}</h5>
        @can('supplier-list')
        <a href="{{route('supplier.index')}}" role="button" class="btn btn-sm btn-primary float-end"><i class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="supplier_name">{{ __('supplier::message.supplier_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="supplier_name" id="supplier_name" value="" class="form-control" placeholder="{{ __('supplier::message.supplier_name') }}">
                            <span class="invalid-feedback d-block" id="error_supplier_name" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="supplier_code">{{ __('supplier::message.code') }} <span class="text-danger"></span></label>
                            <input type="text" name="supplier_code" id="supplier_code" value="" class="form-control" placeholder="{{ __('supplier::message.code') }}">
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="mobile">{{ __('supplier::message.mobile') }}</label>
                            <input type="text" maxlength="10" name="mobile" id="mobile" value="" class="form-control number" placeholder="{{ __('supplier::message.mobile') }}">
                            <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="contact_number">{{ __('supplier::message.contact_number') }}</label>
                            <input type="text" name="contact_number" id="contact_number" value="" class="form-control number" placeholder="{{ __('supplier::message.contact_number') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="email">{{ __('supplier::message.email') }} </label>
                            <input type="text" name="email" id="email" value="" class="form-control text-lowercase" placeholder="{{ __('supplier::message.email') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="gst_number">{{ __('supplier::message.gst_number') }}</label>
                            <input type="text" name="gst_number" id="gst_number" value="" class="form-control" placeholder="{{ __('supplier::message.gst_number') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="contact_person_name">{{ __('supplier::message.contact_person_name') }} </label>
                            <input type="text" name="contact_person_name" id="contact_person_name" value="" class="form-control" placeholder="{{ __('supplier::message.contact_person_name') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="contact_person_number">{{ __('supplier::message.contact_person_number') }} </label>
                            <input type="text" name="contact_person_number" id="contact_person_number" value="" class="form-control number" placeholder="{{ __('supplier::message.contact_person_number') }}">
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label mb-25">{{ __('supplier::message.gst') }} : <b id="claims">{{ __('supplier::message.included') }}</b></label><br>
                            <label class="switch">
                                <input type="checkbox" name="gst_apply" id="gst_apply" class="switch-input" value="1" checked>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">
                                        <i class="icon-base fa fa-check pt-1"></i>
                                    </span>
                                    <span class="switch-off">
                                        <i class="icon-base fa fa-x pt-1"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="address_line_1">{{ __('supplier::message.address1') }}</label>
                            <input class="form-control" name="address_line_1" id="address_line_1" placeholder="{{ __('supplier::message.address1') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="address_line_2">{{ __('supplier::message.address2') }}</label>
                            <input class="form-control" name="address_line_2" id="address_line_2" placeholder="{{ __('supplier::message.address2') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                            <label class="form-label" for="address_line_3">{{ __('supplier::message.address3') }}</label>
                            <input class="form-control" name="address_line_3" id="address_line_3" placeholder="{{ __('supplier::message.address3') }}">
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="country_id">{{ __('message.common.country') }} <span class="text-danger"></span></label>
                            <select class="select2 form-select" name="country_id" id="country_id">
                                <option value="" selected>{{ __('message.common.select') }}</option>
                                @foreach ($country as $value)
                                <option value="{{ $value->id }}">{{ $value->code }} - {{ $value->name }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="state_id">{{ __('message.common.state') }} <span class="text-danger"></span></label>
                            <select class="select2 form-select" name="state_id" id="state_id">
                                <option value="" selected>{{ __('message.common.select') }}</option>
                            </select>
                            <span class="invalid-feedback d-block" id="error_state_id" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="city_id">{{ __('message.common.city') }} <span class="text-danger"></span></label>
                            <select class="select2 form-select" name="city_id" id="city_id">
                                <option value="" selected>{{ __('message.common.select') }}</option>
                            </select>
                            <span class="invalid-feedback d-block" id="error_city_id" role="alert"></span>
                        </div>

                        <div class="col-lg-12 light-style form-group custom-input-group">
                            <label class="form-label">{{ __('supplier::message.term_condition') }}</label>
                            <div id="full-editor1"></div>
                            <input type="hidden" name="term_condition" id="editor1">
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <button type="reset" class="btn btn-sm btn-label-secondary float-start reset">{{ __('message.common.cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-primary float-end save" data-route="{{ route('supplier.store') }}">{{ __('message.common.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{asset('assets/vendor/js/katex.js')}}"></script>
<script src="{{asset('assets/vendor/js/quill.js')}}"></script>
<script type="application/javascript">
    var quill1 = new Quill("#full-editor1", {
        bounds: "#full-editor1",
        placeholder: "Type Something...",
        modules: {
            formula: !0,
            toolbar: [
                [{
                    font: []
                }, {
                    size: []
                }],
                ["bold", "italic", "underline", "strike"],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    script: "super"
                }, {
                    script: "sub"
                }],
                [{
                    header: "1"
                }, {
                    header: "2"
                }, "blockquote", "code-block"],
                [{
                    list: "ordered"
                }, {
                    list: "bullet"
                }, {
                    indent: "-1"
                }, {
                    indent: "+1"
                }],
                [{
                    direction: "rtl"
                }],
            ]
        },
        theme: "snow"
    });

    $(document).on('change', '#gst_apply', function() {
        if ($(this).is(':checked')) {
            $('#claims').text("{{ __('supplier::message.included') }}");
            $(this).val('1');
        } else {
            $('#claims').text("{{ __('supplier::message.excluded') }}");
            $(this).val('0');
        }
    });

    let STATE_ID = 0;
    let CITY_ID = 0;

    $(document).on('change', '#country_id', function() {
        const countryId = $(this).val();
        if (countryId != null && countryId != 0) {
            getState(countryId);
        } else {
            $('#state_id,#city_id').empty();
            $("#state_id,#city_id").append(`<option value="" selected>{{ __('message.common.select') }}</option>`);
            $('#state_id,#city_id').val('').trigger('change');
        }
    });

    $(document).on('change', '#state_id', function() {
        const stateId = $(this).val();
        if (stateId != null && stateId != 0) {
            getCity(stateId);
        } else {
            $('#city_id').empty();
            $("#city_id").append(`<option value="" selected>{{ __('message.common.select') }}</option>`);
            $('#city_id').val('').trigger('change');
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
                        $("#state_id").append(`<option value="" selected>{{ __('message.common.select') }}</option>`);
                        $.each(data.result, function(index, row) {
                            if (STATE_ID == row.id) {
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
                        $("#city_id").append(`<option value="" selected>{{ __('message.common.select') }}</option>`);
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

    $("#form").validate({
        rules: {

            supplier_name: {
                required: true,
            },

        },
        messages: {

            supplier_name: {
                required: "{{ __('supplier::message.enter_supplier_name') }}",
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

    $(document).on('click', '.save', function() {
        var content1 = quill1.root.innerHTML;
        if (content1 != '<p><br></p>') {
            $("#editor1").val(content1);
        }
    });

    $('.number').on('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
        this.value = this.value.slice(0, 10);
    });
</script>
<script src="{{asset('assets/custom/save.js')}}"></script>
@endsection

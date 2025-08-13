@extends('layouts.app')
@section('title', __('rawmaterialmaster::message.edit'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('rawmaterialmaster::message.edit') }}</h5>
            @can('material-master-list')
                <a href="{{ route('rawmaterialmaster.index') }}" role="button" class="btn btn-sm btn-primary float-end"><i
                        class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
            @endcan
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <form id="form" action="javascript:void(0);"
                        method="POST" autocomplete="nope">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <input type="hidden" name="id" id="id"
                                    value="{{ old('id', $rawMaterialMaster->id) }}">
                                <label class="form-label"
                                    for="material_category_id">{{ __('rawmaterialmaster::message.material_category') }}</label>
                                <select id="material_category_id" name="material_category_id" class="select2 form-select">
                                    <option value="">{{ __('message.common.select') }}</option>
                                    @if ($rawMaterialCategory->count() > 0)
                                        @foreach ($rawMaterialCategory as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $rawMaterialMaster->material_category_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->material_category_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="invalid-feedback d-block" id="error_material_category_id"
                                    role="alert">{{ $errors->first('material_category_id') }}</span>
                            </div>



                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 form-group custom-input-group">
                                <label class="form-label"
                                    for="material_name">{{ __('rawmaterialmaster::message.materialName') }}
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="material_name" id="material_name"
                                    placeholder="{{ __('rawmaterialmaster::message.material_name') }}"
                                    value="{{ old('material_name', $rawMaterialMaster->material_name) }}" required>
                                <span class="invalid-feedback d-block" id="error_material_name"
                                    role="alert">{{ $errors->first('material_name') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label"
                                    for="material_code">{{ __('rawmaterialmaster::message.material_code') }} </label>
                                <input type="material_code" class="form-control " id="material_code" name="material_code"
                                    placeholder="{{ __('rawmaterialmaster::message.material_code') }}"
                                    value="{{ old('material_code', $rawMaterialMaster->material_code) }}">
                                <span class="invalid-feedback d-block" id="error_material_code"
                                    role="alert">{{ $errors->first('material_code') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="unit_id">{{ __('rawmaterialmaster::message.unit') }}<span
                                        class="text-danger">*</span></label>

                                <select id="unit_id" name="unit_id" class="select2 form-select">
                                    <option value="">{{ __('message.common.select') }}</option>
                                    @if ($unit->count() > 0)
                                        @foreach ($unit as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $rawMaterialMaster->unit_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="invalid-feedback d-block" id="error_unit_id"
                                    role="alert">{{ $errors->first('unit_id') }}</span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                <label class="form-label"
                                    for="alert_quantity">{{ __('rawmaterialmaster::message.alert_quantity') }}
                                </label>
                                <input type="alert_quantity" class="form-control " id="alert_quantity" name="alert_quantity"
                                    placeholder="{{ __('rawmaterialmaster::message.alert_quantity') }}"
                                    value="{{ old('alert_quantity', $rawMaterialMaster->alert_quantity) }}">
                                <span class="invalid-feedback d-block" id="error_alert_quantity"
                                    role="alert">{{ $errors->first('alert_quantity') }}</span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                                <label class="form-label" for="tax">{{ __('rawmaterialmaster::message.tax') }}
                                </label>
                                <input type="tax" class="form-control " id="tax" name="tax"
                                    placeholder="{{ __('rawmaterialmaster::message.tax') }}"
                                    value="{{ old('tax', $rawMaterialMaster->tax) }}">
                                <span class="invalid-feedback d-block" id="error_tax"
                                    role="alert">{{ $errors->first('tax') }}</span>
                            </div>


                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                                <button type="reset"
                                    class="btn btn-sm btn-label-secondary float-start reset">{{ __('message.common.cancel') }}</button>

                                <button type="submit" class="btn btn-sm btn-primary float-end update"
                                    data-route="{{ route('rawmaterialmaster.update', $rawMaterialMaster->id) }}">{{ __('message.common.submit') }}</button>
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

        'use strict';
    const URL = "{{route('rawmaterialmaster.index')}}";

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
            firstname: {
                required: true,
            },
            lastname: {
                required: true,
            },
            mobile: {
                regex: /^[0-9]{10}$/,
                required: true,
                minlength: 10,
            },
            name: {
                required: true,
            },
             designation: {
                required: true,
            },
            "roles[]": {
                required: true,
            },
        },
        messages: {
            firstname: {
                required: "{{ __('rawmaterialmaster::message.enter_first_name') }}"
            },
            lastname: {
                required: "{{ __('rawmaterialmaster::message.enter_last_name') }}"
            },
            mobile: {
                regex: "{{ __('rawmaterialmaster::message.enter_valid_number') }}",
                required: "{{ __('rawmaterialmaster::message.enter_mobile') }}",
                minlength: "{{ __('rawmaterialmaster::message.enter_digits') }}",
            },
            name: {
                required: "{{ __('rawmaterialmaster::message.name') }}"
            },
             designation: {
                required: "{{ __('rawmaterialmaster::message.select_designation') }}"
            },
            "roles[]": {
                required: "{{ __('rawmaterialmaster::message.select_role') }}"
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
<script src="{{ asset('assets/custom/update.js') }}"></script>
@endsection

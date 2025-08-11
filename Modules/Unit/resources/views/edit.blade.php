@extends('layouts.app')
@section('title', __('unit::message.edit'))
@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('unit::message.edit') }}</h5>
        @can('unit-list')
        <a href="{{ route('unit.index') }}" role="button" class="btn btn-sm btn-primary float-end"><i class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
        @endcan
    </div>
    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <form id="form" action="javascript:void(0);" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-8 form-group custom-input-group">
                            <label class="form-label" for="name">{{ __('unit::message.name') }} <span class="text-danger">*</span></label>
                            <input type="hidden" name="id" id="id" value="{{ $unit->id }}">
                            <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('unit::message.name') }}" value="{{ $unit->name }}">
                            <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 form-group custom-input-group">
                            <label class="form-label" for="unit_value">{{ __('unit::message.unit_value') }}</label>
                            <input type="text" class="form-control" name="unit_value" id="unit_value" value="1" readonly>
                        </div>
                        <div class="col-12 table-responsives">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="3%" class="text-center">#</th>
                                        <th class="text-center">{{ __('unit::message.sub_unit') }}</th>
                                        <th class="text-center">{{ __('unit::message.sub_unit_value') }}</th>
                                        <th class="text-center">{{ __('message.common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="sub_data inner-clone">
                                    @foreach ($unit->unitGravity as $key => $value)
                                    <tr class="design_tr">
                                        <td class="d_sr_no text-center">{{ $key + 1 }}</td>
                                        <td class="custom-input-group">
                                            <select name="child_id[]" class="select2 form-select child-unit-select">
                                                <option value="0" selected>{{ __('message.common.select') }}</option>
                                                @foreach ($unit_name as $item)
                                                <option value="{{ $item->id }}" {{ $value->child_id == $item->id ? 'selected' : '' }} data-unit="">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="custom-input-group">
                                            <div class="input-group">
                                                <input type="text" min="0" class="form-control number quantity" name="segment_value[]" placeholder="" value="{{ $value->unit_value }}">
                                            </div>
                                        </td>
                                        <td class="custom-input-group text-center">
                                            <button type="button" class="btn btn-sm text-danger border-0 item-delete" data-id="{{ $value->child_id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if($unit->unitGravity->count() == 0)
                                    <tr class="design_tr">
                                        <td class="d_sr_no text-center">1</td>
                                        <td class="custom-input-group">
                                            <select name="child_id[]" class="select2 form-select child-unit-select">
                                                <option value="0" selected> {{ __('message.common.select') }}</option>
                                                @foreach ($unit_name as $item)
                                                <option value="{{ $item->id }}" data-unit="">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="custom-input-group">
                                            <input type="text" min="0" class="form-control number quantity" name="segment_value[]" id="segment_value" placeholder="" value="">
                                            <span class="invalid-feedback d-block error-segment_value" id="error_segment_value" role="alert"></span>
                                        </td>
                                        <td class="custom-input-group text-center">
                                            <button type="button"
                                                class="btn btn-sm text-danger border-0 remove-design">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class=""></td>
                                        <td class="text-center bg-white">
                                            <button class="btn btn-sm btn-label-primary p-1 add-design" type="button">
                                                <i class="fa fa-plus"></i>
                                                <span>{{ __('message.common.add_more') }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                            <a href="{{ route('unit.index') }}" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</a>
                            <button type="submit" class="btn btn-sm btn-primary float-end update" data-route="{{ route('unit.update', $unit->id) }}">{{ __('message.common.submit') }}</button>
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
    $(document).on("click", ".add-design", function() {
        var innerMe = $(this);
        var lastElement = $(this).closest('table').find('tbody');
        var clonedElement = $(this).closest('table').find('.design_tr').last().clone();
        var sr = lastElement.find('.d_sr_no').length;
        clonedElement.find('.d_sr_no').text(sr + 1);
        clonedElement.find('.quantity').val('');

        lastElement.append(clonedElement);

        clonedElement.find('.child-unit-select').on('change', function() {
            var element = $(this).attr('name');
            $('#form').validate().showErrors({
                [element]: ''
            });
        });
        lastElement.find('.custom-error').remove();
        clonedElement.find('.child-unit-select').next('.select2-container').remove();

        clonedElement.find('.child-unit-select').val('').trigger('change');

        clonedElement.find('.item-delete').removeAttr('data-id');
        $('.child-unit-select').select2({
            placeholder: "--Select--",
            allowClear: false,
            width: "100%",
            selectOnClose: true,
            //dropdownParent: $('#inlineModal')
        });
        clonedElement.find('.child-unit-select').focus();

    });

    function updateSerialNumbers() {
        $('.d_sr_no').each(function(index) {
            $(this).text(index + 1);
        });
    }

    $(document).on('input', '.number', function(e) {
        const value = e.target.value;
        const validValue = value
            .replace(/[^0-9.]/g, '')
            .replace(/(\..*?)\..*/g, '$1');
        e.target.value = validValue;
    });

    $(document).on("click", ".remove-design", function() {
        var designL = $('.design_tr').length;
        if (designL > 1) {
            $(this).closest('.design_tr').remove();
            updateSerialNumbers();
        } else {
            Swal.fire({
                text: "Can`t delete first item",
                icon: 'warning',
                confirmButtonText: 'OK',
            });
        }
    });

    $(document).on('change', '.child-unit-select', function() {
        var selectBox = $(this);
        let arr = [];
        let me = selectBox.val();

        if (me != '') {
            $('.design_tr').find('.child-unit-select').each(function() {
                if ($(this).val() != null && $(this).val() != 0 && me == $(this).val()) {
                    arr.push($(this).val());
                }
            });
            if (arr.length > 1) {
                toastr.clear();
                toastr.warning("Already Selected", "Opps!");
                selectBox.val(null).trigger('change');
            }
        }

    });

    $.validator.addMethod("childUnitRequired", function(value, element) {
        const $row = $(element).closest('.design_tr');
        const childUnit = $row.find('.child-unit-select').val();
        const subUnitValue = $row.find('.quantity').val();
        if (childUnit != null && childUnit != 0 && !subUnitValue) {
            return false;
        }
        return true;
    }, "Please provide value.");

    $("#form").validate({
        rules: {
            name: {
                required: true,
            },
            'segment_value[]': {
                childUnitRequired: true
            }
        },
        messages: {
            name: {
                required: "{{ __('unit::message.enter_name') }}"
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
            if (element.attr("name") == "child_id[]") {
                error.insertAfter(element.closest("td").find(".error-child_id"));
            } else if (element.attr("name") == "segment_value[]") {
                error.insertAfter(element.closest("td").find(".error-segment_value"));
            } else {
                $(element).closest('.custom-input-group').append(error);
            }
        }
    });

    $(document).on('click', '.item-delete', function() {
        toastr.clear();
        if (($('.item-delete').length) > 1) {
            var btn = $(this);
            var id = btn.data('id');
            var row = btn.closest('tr');
            if (id != undefined) {
                Swal.fire({
                        title: "Are you sure?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "Yes, Delete",
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-outline-danger ms-1'
                        },
                        buttonsStyling: false
                    })
                    .then(function(result) {
                        if (result.value) {
                            $.ajax({
                                type: "POST",
                                url: "{{route('unit-item-delete')}}",
                                data: {
                                    'id': id,
                                    'unit_id': $('#id').val(),
                                    "_token": "{{ csrf_token() }}",
                                },
                                dataType: 'json',
                                cache: false,
                                success: function(response) {
                                    if (response.status_code == 200) {
                                        setTimeout(function() {
                                            row.remove();
                                            updateSerialNumbers();
                                        }, 100);
                                        toastr.success(response.message, "Success");
                                    } else if (response.status_code == 403) {
                                        toastr.warning(response.message, "Warning");
                                    } else {
                                        toastr.error(response.message, "Error");
                                    }
                                }
                            });
                        }
                    });
            } else {
                row.remove();
            }
            setTimeout(function() {
                updateSerialNumbers();
            }, 100);
        } else {
            Swal.fire({
                text: "Can`t delete first item",
                icon: 'warning',
                confirmButtonText: 'OK',
            });
        }
    });
</script>
<script src="{{ asset('assets/custom/update.js') }}"></script>
@endsection
@extends('layouts.app')
@section('title', __('user::message.add'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">{{ __('user::message.add') }}</h5>
            @can('users-list')
                <a href="{{ route('users.index') }}" role="button" class="btn btn-sm btn-primary float-end"><i
                        class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}</a>
            @endcan
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <form id="form" action="{{ route('users.store') }}" method="POST" autocomplete="nope">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <input type="hidden" name="user_profile_id" id="user_profile_id"
                                    value="{{ old('user_profile_id') }}">
                                <label class="form-label" for="firstname">{{ __('user::message.first_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firstname" id="firstname"
                                    placeholder="{{ __('user::message.first_name') }}" value="{{ old('firstname') }}"
                                    required>
                                <span class="invalid-feedback d-block" id="error_firstname"
                                    role="alert">{{ $errors->first('firstname') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="lastname">{{ __('user::message.last_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="lastname" id="lastname"
                                    placeholder="Last Name" value="{{ old('lastname') }}" required>
                                <span class="invalid-feedback d-block" id="error_lastname"
                                    role="alert">{{ $errors->first('lastname') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="dateofbirth">{{ __('user::message.dob') }}</label>
                                <input type="text" class="form-control flatpickr-date" name="dateofbirth"
                                    id="dateofbirth" placeholder="{{ __('user::message.dob') }}"
                                    value="{{ old('dateofbirth') }}">
                                <span class="invalid-feedback d-block" id="error_dateofbirth"
                                    role="alert">{{ $errors->first('dateofbirth') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="mobile">{{ __('user::message.mobile_number') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" maxlength="10" class="form-control number" id="mobile"
                                    name="mobile" placeholder="{{ __('user::message.mobile_number') }}"
                                    value="{{ old('mobile') }}" required>
                                <span class="invalid-feedback d-block" id="error_mobile"
                                    role="alert">{{ $errors->first('mobile') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="email">{{ __('user::message.email') }} </label>
                                <input type="email" class="form-control text-lowercase" id="email" name="email"
                                    placeholder="{{ __('user::message.email') }}" value="{{ old('email') }}"
                                    autocomplete="new-email">
                                <span class="invalid-feedback d-block" id="error_email"
                                    role="alert">{{ $errors->first('email') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group"></div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="username">{{ __('user::message.user_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control text-lowercase" id="username" name="username"
                                    placeholder="{{ __('user::message.user_name') }}" value="{{ old('username') }}"
                                    required autocomplete="new-username">
                                <span class="invalid-feedback d-block" id="error_username"
                                    role="alert">{{ $errors->first('username') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="password">{{ __('user::message.password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" minlength="8" class="form-control" id="password"
                                        name="password" placeholder="{{ __('user::message.password') }}"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                        required autocomplete="new-password">
                                    <span class="input-group-text cursor-pointer toggle-password" style="z-index:1000">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <span class="invalid-feedback d-block" id="error_password"
                                    role="alert">{{ $errors->first('password') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label"
                                    for="confirm_password">{{ __('user::message.confirm_password') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" minlength="8" class="form-control" id="confirm_password"
                                        name="confirm_password" placeholder="{{ __('user::message.confirm_password') }}"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                        required autocomplete="new-password">
                                    <span class="input-group-text cursor-pointer toggle-password" style="z-index:1000">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <span class="invalid-feedback d-block" id="error_confirm_password"
                                    role="alert">{{ $errors->first('confirm_password') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group select2-primary">
                                <label class="form-label" for="designation">{{ __('user::message.designation') }} <span
                                        class="text-danger">*</span></label>
                                <input type="designation" class="form-control" id="designation"
                                    name="designation" placeholder="{{ __('user::message.designation') }}"
                                    value="{{ old('designation') }}" autocomplete="new-designation">
                                <span class="invalid-feedback d-block" id="error_designation"
                                    role="alert">{{ $errors->first('designation') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group select2-primary">
                                <label class="form-label" for="roles">{{ __('user::message.role') }} <span
                                        class="text-danger">*</span></label>
                                <select class="select2 form-select " name="roles[]" id="roles">
                                    <option value="">{{ __('message.common.select') }}</option>
                                    @foreach ($roleMaster as $role)
                                        <option value="{{ $role }}"
                                            {{ old('roles') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block" id="error_role_id"
                                    role="alert">{{ $errors->first('roles') }}</span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 form-group custom-input-group">
                                <label class="form-label" for="status">{{ __('user::message.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="select2 form-select select2-hidden-accessible" name="status"
                                    id="status">
                                    <option value="Active">{{ __('message.common.active') }}</option>
                                    <option value="InActive">{{ __('message.common.inactive') }}</option>
                                </select>
                                <span class="invalid-feedback d-block" id="error_role_id"
                                    role="alert">{{ $errors->first('status') }}</span>
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
    const URL = "{{route('users.index')}}";

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
            username: {
                required: true,
            },
            password: {
                required: true,
            },
            confirm_password: {
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
                required: "{{ __('user::message.enter_first_name') }}"
            },
            lastname: {
                required: "{{ __('user::message.enter_last_name') }}"
            },
            mobile: {
                regex: "{{ __('user::message.enter_valid_number') }}",
                required: "{{ __('user::message.enter_mobile') }}",
                minlength: "{{ __('user::message.enter_digits') }}",
            },
            username: {
                required: "{{ __('user::message.enter_username') }}"
            },
            password: {
                required: "{{ __('user::message.enter_password') }}"
            },
            confirm_password: {
                required: "{{ __('user::message.enter_confirm_password') }}"
            },
            designation: {
                required: "{{ __('user::message.select_designation') }}"
            },
            "roles[]": {
                required: "{{ __('user::message.select_role') }}"
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
@endsection

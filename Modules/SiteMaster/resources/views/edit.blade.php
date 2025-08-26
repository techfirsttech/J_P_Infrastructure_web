@extends('layouts.app')
@section('title', __('sitemaster::message.edit'))

@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <h5 class="content-header-title float-start mb-0">{{ __('sitemaster::message.edit') }}</h5>
        @can('sitemaster-list')
            <a href="{{ route('sitemaster.index') }}" role="button" class="btn btn-sm btn-primary float-end">
                <i class="fa fa-arrow-left me-1"></i> {{ __('message.common.back') }}
            </a>
        @endcan
    </div>

    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <form id="form" action="{{ route('sitemaster.update', $site->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Site Name --}}
                        <div class="col-md-8 form-group custom-input-group">
                            <label for="site_name" class="form-label">
                                {{ __('sitemaster::message.site_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="site_name" id="site_name"
                                   value="{{ old('site_name', $site->site_name) }}"
                                   placeholder="{{ __('sitemaster::message.site_name') }}" required>
                            <span class="invalid-feedback d-block" id="error_site_name">
                                {{ $errors->first('site_name') }}
                            </span>
                        </div>

                        {{-- Supervisors --}}
                        <div class="col-md-4 form-group custom-input-group">
                            <label for="user_id" class="form-label">
                                {{ __('sitemaster::message.user') }} <span class="text-danger">*</span>
                            </label>
                            <select id="user_id" name="user_id[]" class="select2 form-select" multiple>
                                @foreach($supervisor as $value)
                                    <option value="{{ $value->id }}"
                                        {{ in_array($value->id, $site->user_id ?? []) ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- State --}}
                        <div class="col-md-4 form-group custom-input-group">
                            <label for="state_id" class="form-label">{{ __('sitemaster::message.state') }}</label>
                            <select id="state_id" name="state_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @foreach ($state as $value)
                                    <option value="{{ $value->id }}"
                                        {{ $site->state_id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- City --}}
                        <div class="col-md-4 form-group custom-input-group">
                            <label for="city_id" class="form-label">
                                {{ __('sitemaster::message.city') }} <span class="text-danger">*</span>
                            </label>
                            <select id="city_id" name="city_id" class="select2 form-select">
                                <option value="">{{ __('message.common.select') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ $site->city_id == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pincode --}}
                        <div class="col-md-4 form-group custom-input-group">
                            <label for="pincode" class="form-label">{{ __('sitemaster::message.pincode') }}</label>
                            <input type="text" class="form-control" id="pincode" name="pincode"
                                   value="{{ old('pincode', $site->pincode) }}"
                                   placeholder="{{ __('sitemaster::message.pincode') }}">
                        </div>

                        {{-- Address --}}
                        <div class="col-12 form-group custom-input-group">
                            <label for="address" class="form-label">{{ __('sitemaster::message.address') }}</label>
                            <textarea class="form-control" name="address" id="address" rows="3">{{ old('address', $site->address) }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 mt-1">
                            <button type="reset" class="btn btn-sm btn-label-secondary float-start reset">
                                {{ __('message.common.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary float-end">
                                {{ __('message.common.update') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

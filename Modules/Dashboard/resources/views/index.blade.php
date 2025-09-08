@extends('layouts.app')
@section('title', __('message.dashboard'))
@section('content')
<div class="row g-6">
    @foreach($siteMaster as $site)
  
    <div class="col-lg-4 col-sm-6">
        <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-primary"><i class="fa fa-"></i></span>
                    </div>
                    <h4 class="mb-0">{{$site->site_name}}</h4>
                </div>

                <p class="text-heading fw-medium me-2 mb-1">Total Balance : {{$site->closing_balance}}</p>
                <!-- <p class="mb-0"> -->
                <div class="d-flex justify-content-between">
                    <p class="text-muted  mb-50 me-2">Income : <span class="text-success"> {{$site->total_credit}}</span></p>
                    <p class="text-muted  mb-50 me-2">Expense : <span class="text-danger"> {{$site->total_debit}}</span></p>
                </div>

                <!-- <span class="text-heading fw-medium me-2">+18.2%</span>
                    <small class="text-muted">than last week</small> -->
                <!-- </p> -->
            </div>
        </div>
    </div>
    @endforeach

</div>
 <button class="btn btn-primary dashboard-filter-btn" type="button" data-bs-toggle="offcanvas"
    data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd">
    <i class="fas fa-filter"></i>
</button> 
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEndLabel" class="offcanvas-title">{{ __('message.dashboard.filter') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-0 pt-0">
        <div class="m-0 px-0 pb-6 template-customizer-style w-100">
            <label for="customizerStyle" class="form-label d-block template-customizer-t-style_label mb-2">{{ __('message.dashboard.date_filter') }}</label>
            <div class="row px-1 template-customizer-styles-options">
                <div class="col-4 px-2 mb-4">
                    <div class="form-check custom-option custom-option-icon checked">
                        <label class="form-check-label custom-option-content p-0" for="filter_type_today">
                            <span class="custom-option-body mb-0">
                                <img src="{{ asset('assets/svg/today.svg') }}" alt="Light" class="img-fluid scaleX-n1-rtl">
                            </span>
                            <input name="filter_type" class="form-check-input d-none" type="radio" value="today" id="filter_type_today">
                        </label>
                    </div>
                    <label class="form-check-label small text-nowrap text-body mt-1 w-100 text-center" for="filter_type_today">{{ __('message.dashboard.today') }}</label>
                </div>
                <div class="col-4 px-2 mb-4">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content p-0" for="filter_type_week">
                            <span class="custom-option-body mb-0">
                                <img src="{{ asset('assets/svg/week.svg') }}" alt="Dark" class="img-fluid scaleX-n1-rtl">
                            </span>
                            <input name="filter_type" class="form-check-input d-none" type="radio" value="week" id="filter_type_week" checked="checked">
                        </label>
                    </div>
                    <label class="form-check-label small text-nowrap text-body mt-1 w-100 text-center" for="filter_type_week">{{ __('message.dashboard.week') }}</label>
                </div>
                <div class="col-4 px-2 mb-4">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content p-0" for="filter_type_month">
                            <span class="custom-option-body mb-0">
                                <img src="{{ asset('assets/svg/month.svg') }}" alt="System" class="img-fluid scaleX-n1-rtl">
                            </span>
                            <input name="filter_type" class="form-check-input d-none" type="radio" value="month" id="filter_type_month">
                        </label>
                    </div>
                    <label class="form-check-label small text-nowrap text-body mt-1 w-100 text-center" for="filter_type_month">{{ __('message.dashboard.month') }}</label>
                </div>
                <div class="col-4 px-2 mb-4">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content p-0" for="filter_type_year">
                            <span class="custom-option-body mb-0">
                                <img src="{{ asset('assets/svg/year.svg') }}" alt="System" class="img-fluid scaleX-n1-rtl">
                            </span>
                            <input name="filter_type" class="form-check-input d-none" type="radio" value="year" id="filter_type_year">
                        </label>
                    </div>
                    <label class="form-check-label small text-nowrap text-body mt-1 w-100 text-center" for="filter_type_year">{{ __('message.dashboard.year') }}</label>
                </div>
                <div class="col-4 px-2 mb-4">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content p-0" for="filter_type_custom">
                            <span class="custom-option-body mb-0">
                                <img src="{{ asset('assets/svg/custom.svg') }}" alt="System" class="img-fluid scaleX-n1-rtl">
                            </span>
                            <input name="filter_type" class="form-check-input d-none" type="radio" value="custom" id="filter_type_custom">
                        </label>
                    </div>
                    <label class="form-check-label small text-nowrap text-body mt-1 w-100 text-center" for="filter_type_custom">{{ __('message.dashboard.custom') }}</label>
                </div>

                <div class="col-12 px-2 mb-4 form-group custom-input-group custom-date d-none">
                    <label class="form-label" for="s_date">{{ __('message.common.start_date') }}</label>
                    <input type="text" class="form-control flatpickr" name="s_date" id="s_date"
                        autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                </div>
                <div class="col-12 px-2 mb-4 form-group custom-input-group custom-date d-none">
                    <label class="form-label" for="e_date">{{ __('message.common.end_date') }}</label>
                    <input type="text" class="form-control flatpickr" name="e_date" id="e_date"
                        autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                </div>
            </div>
            <button type="button" class="btn btn-primary mb-2 d-grid w-100 custom-date d-none">{{ __('message.dashboard.filter') }}</button>
        </div>
    </div>
</div>
@endsection
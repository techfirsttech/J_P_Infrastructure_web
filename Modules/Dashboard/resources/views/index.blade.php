@extends('layouts.app')
@section('title', __('message.dashboard'))
@section('content')
<style>
    .flatpickr-calendar {
        z-index: 1100 !important;
        /* higher than Bootstrap's .offcanvas (1050) */
    }

    .dashboard-filter-btn {
        padding: 0;
        position: fixed;
        top: 20%;
        right: 0;
        z-index: 1;
        display: block;
        width: 38px;
        height: 38px;
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
        border-top-right-radius: 0rem;
        border-bottom-right-radius: 0rem;
        background: var(--bs-primary);
        box-shadow: 0px 2px 6px 0px rgba(115, 103, 240, 0.3);
        color: #fff !important;
        text-align: center;
        font-size: 18px !important;
        line-height: 38px;
        opacity: 1;
        -webkit-transition: all 0.1s linear 0.2s;
        -o-transition: all 0.1s linear 0.2s;
        transition: all 0.1s linear 0.2s;
        -webkit-transform: translateX(-58px);
        -ms-transform: translateX(-58px);
        transform: translateX(-58px);
    }
</style>

<div class="dashboard_render"></div>

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
                    <label class="form-label">{{ __('message.common.start_date') }}</label>
                    <input type="text" class="form-control flatpickr" name="ds_date" id="ds_date" autocomplete="off" placeholder="{{ __('message.common.start_date') }}" readonly>
                </div>
                <div class="col-12 px-2 mb-4 form-group custom-input-group custom-date d-none">
                    <label class="form-label">{{ __('message.common.end_date') }}</label>
                    <input type="text" class="form-control flatpickr" name="de_date" id="de_date" autocomplete="off" placeholder="{{ __('message.common.end_date') }}" readonly>
                </div>
            </div>
            <button type="button" class="btn btn-primary filter-now mb-2 d-grid w-100 custom-date d-none">{{ __('message.dashboard.filter') }}</button>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="detailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered align-items-start modal-lg">
        <div class="modal-content" id="modal_content">
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    $(document).ready(function() {
        flatpickr('.flatpickr', {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: '',
            maxDate: new Date(),
        });

        submitFilterForm('week');
    });

    $(document).on('change', 'input[name="filter_type"]', function() {
        const filterValue = $(this).val();

        if (filterValue == 'custom') {
            $('.custom-date').removeClass('d-none');
        } else {
            $('.custom-date').addClass('d-none');
            $('#ds_date').val('');
            $('#de_date').val('');
            submitFilterForm(filterValue);
        }
    });

    $('.filter-now').on('click', function() {
        const selectedFilter = $('input[name="filter_type"]:checked').val();
        if (selectedFilter === 'custom') {
            const startDate = $('#ds_date').val();
            const endDate = $('#de_date').val();
            // if (!startDate || !endDate) {
            //     alert('Please select both start and end dates.');
            //     return;
            // }
            submitFilterForm(selectedFilter, startDate, endDate);
        }
    });

    function submitFilterForm(filterType, startDate = null, endDate = null) {
        var loaderimg = "{{ asset('assets/img/loader.gif') }}";
        $('.dashboard_render').html('<div class="row mt-5 pt-5"><div class="col-12 text-center mt-5 pt-5"><img src="' + loaderimg + '" width="100px" /></div></div>');

        $.ajax({
            url: "{{ route('dashboard-filter') }}",
            method: 'POST',
            data: {
                filter_type: filterType,
                s_date: startDate,
                e_date: endDate,
                _token: "{{ @csrf_token() }}",
            },
            success: function(response) {
                if (response.status_code == 200) {
                    $('.dashboard_render').html(response.html);
                } else {
                    $('.dashboard_render').html('Data not found');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Something went wrong while loading the dashboard data.');
            }
        });
    }

    $(document).on('click', '.view', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        if (id != '') {
            const selectedFilter = $('input[name="filter_type"]:checked').val();
            var route = "{{route('dashboard-model')}}";
            $.ajax({
                type: "POST",
                url: route,
                dataType: 'json',
                data: {
                    "id": id,
                    filter_type: selectedFilter,
                    s_date: $('#ds_date').val(),
                    e_date: $('#de_date').val(),
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $("#exampleModal").modal("show");
                    $("#modal_content").html('');
                    $("#modal_content").html(response.html);
                    $("#detailModalTitle").html("{{ __('sitemaster::message.siteName') }} : " + name);
                }
            });
        }
    });
</script>
@endsection
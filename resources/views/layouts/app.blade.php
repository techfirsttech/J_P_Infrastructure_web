<!DOCTYPE html>
@if (auth()->user()->menu_style == 'horizontal')
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
    data-bs-theme="{{  auth()->user()->theme }}">
@else
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" data-bs-theme="{{  auth()->user()->theme }}">
@endif

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ (setting()->favicon != '') ? asset('setting/favicon/' . setting()->favicon) : asset('assets/img/fav.png') }}" />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
    class="template-customizer-theme-css" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}" />

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-ui/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />


    @yield('pagecss')
</head>

<body>
    <!-- Layout wrapper -->
    @if (auth()->user()->menu_style == 'horizontal')
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        @else
        <div class="layout-wrapper layout-content-navbar">
            @endif
            <div class="layout-container">
                @include('layouts.navbar-' . auth()->user()->menu_style)
                <!-- Layout container -->
                <div class="layout-page">
                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        @include('layouts.' . auth()->user()->menu_style)
                        <!-- Content -->
                        <div class="container-xxl flex-grow-1 container-p-y">
                            @yield('content')
                        </div>
                        <!--/ Content -->

                        <!-- start change password model -->
                        <div class="modal fade" id="changeModal" tabindex="-1" aria-labelledby="exampleModalLabel1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header bg-transparent border-bottom p-2">
                                        <h4 class="card-title mb-0">{{ __('user::message.change_password') }} </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form id="password_form" action="javascript:void(0)" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 mb-1 custom-input-group form-password-toggle">
                                                    <label class="form-label"
                                                        for="current_password">{{ __('user::message.current_password') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group ">
                                                        <input type="password" class="form-control" name="current_password"
                                                            id="current_password"
                                                            placeholder="{{ __('user::message.current_password') }}"
                                                            aria-describedby="basic-default-password2">
                                                        <span id="basic-default-password2"
                                                            class="input-group-text cursor-pointer toggle-password">
                                                            <i class="fa fa-eye"></i>
                                                        </span>
                                                    </div>
                                                    <span class="invalid-feedback d-block" id="error_current_password"
                                                        role="alert"></span>
                                                </div>
                                                <div class="col-12 mb-1 custom-input-group form-password-toggle">
                                                    <label class="form-label"
                                                        for="password">{{ __('user::message.new_password') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group ">
                                                        <input type="password" class="form-control" name="password"
                                                            id="password" placeholder="{{ __('user::message.new_password') }}"
                                                            aria-describedby="basic-default-password3">
                                                        <span id="basic-default-password3"
                                                            class="input-group-text cursor-pointer toggle-password">
                                                            <i class="fa fa-eye"></i>
                                                        </span>
                                                    </div>
                                                    <span class="invalid-feedback d-block" id="error_password"
                                                        role="alert"></span>

                                                </div>
                                                <div class="col-12 mb-1 custom-input-group form-password-toggle">
                                                    <label class="form-label"
                                                        for="confirm_password">{{ __('user::message.confirm_password') }}
                                                        <span class="text-danger">*</span></label>
                                                    <div class="input-group ">
                                                        <input type="password" class="form-control" name="confirm_password"
                                                            id="confirm_password"
                                                            placeholder="{{ __('user::message.confirm_password') }}"
                                                            aria-describedby="basic-default-password4">
                                                        <span id="basic-default-password4"
                                                            class="input-group-text cursor-pointer toggle-password">
                                                            <i class="fa fa-eye"></i>
                                                        </span>
                                                    </div>
                                                    <span class="invalid-feedback d-block" id="error_confirm_password"
                                                        role="alert"></span>
                                                </div>

                                                <div class="col-md-12">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary float-end change-password"
                                                        data-route="{{ route('change-password') }}">{{ __('user::message.Submit') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end change password model -->
                        <!-- start changlayoutModal  model -->
                        <div class="modal fade" id="changlayoutModal" tabindex="-1" aria-labelledby="exampleModalLabel1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header bg-transparent border-bottom p-2">
                                        <h4 class="card-title mb-0">{{ __('user::message.changelayout') }} </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form id="change_layout_form" action="javascript:void(0)" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 mb-1">
                                                    <label class="form-label" for="menu_style">
                                                        {{ __('user::message.menu_style') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="menu_style" id="menu_style" class="form-select">
                                                        <option value="vertical"
                                                            {{ auth()->user()->menu_style === 'vertical' ? 'selected' : '' }}>
                                                            Vertical</option>
                                                        <option value="horizontal"
                                                            {{ auth()->user()->menu_style === 'horizontal' ? 'selected' : '' }}>
                                                            Horizontal</option>
                                                    </select>
                                                    <span class="invalid-feedback d-block" id="error_menu_style"
                                                        role="alert"></span>
                                                </div>

                                                <div class="col-12 mb-1">
                                                    <label class="form-label" for="theme">
                                                        {{ __('user::message.theme') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="theme" id="theme" class="form-select">
                                                        <option value="light"
                                                            {{ auth()->user()->theme === 'light' ? 'selected' : '' }}>
                                                            Light</option>
                                                        <option value="dark"
                                                            {{ auth()->user()->theme === 'dark' ? 'selected' : '' }}>
                                                            Dark</option>
                                                    </select>
                                                    <span class="invalid-feedback d-block" id="error_theme"
                                                        role="alert"></span>
                                                </div>

                                                <div class="col-md-12">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary float-end change-layout"
                                                        data-route="{{ route('change-layout') }}">{{ __('user::message.Submit') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- start changlayoutModal  model -->

                        <!-- Footer -->
                        <footer class="content-footer footer bg-footer-theme">
                            <div class="container-xxl">
                                <div
                                    class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                    <div class="text-body">
                                        Powered By <a href="https://techfirst.co.in/" target="_blank"
                                            class="footer-link">TechFirst ERP Pvt. Ltd.</a>
                                    </div>

                                </div>
                            </div>
                        </footer>
                        <!-- / Footer -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!--/ Content wrapper -->
                </div>
                <!--/ Layout container -->
            </div>
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
        <!--/ Layout wrapper -->
        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
        <!-- endbuild -->

        <!-- Main JS -->
        <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/js/axios.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/select2/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/jquery-ui/js/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/jquery-ui/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom-demo.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
        @yield('pagescript')
        <script src="{{ asset('assets/custom/password.js') }}"></script>
        <script src="{{ asset('assets/custom/changelayout.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/block-ui/block-ui.js') }}"></script>
        <script>
            @if($message = Session::get('error'))
            toastr.error("{{ addslashes($message) }}", "Opps");
            @endif

            @if($message = Session::get('warning'))
            toastr.warning("{{ addslashes($message) }}", "Warning");
            @endif

            @if($message = Session::get('success'))
            toastr.success("{{ addslashes($message) }}", "Success");
            @endif
            //change language
            var url = "{{ route('language') }}";
            $(".lang-change").click(function() {
                window.location.href = url + "?lang=" + $(this).data('value');
            });

            $(".year-change").click(function() {
                window.location.href = "{{route('years')}}" + "?year=" + $(this).data('value');
            });

            $(document).ready(function() {
                var activeYear = $('.year-change.active').text();
                $('#selected_year').text(activeYear);
            });

            $('.table').on('show.bs.dropdown', '.btn-group,.dropdown', function() {
                let dropdownMenu = $(this).find('.dropdown-menu');
                dropdownMenu.appendTo('body');
            });

            function previewImage(event, imagePreview) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
</body>

</html>

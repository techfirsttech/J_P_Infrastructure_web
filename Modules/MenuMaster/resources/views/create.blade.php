@extends('layouts.app')
@section('title', __('menumaster::message.add'))
@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="content-header-title mb-0">{{ __('menumaster::message.create_new_menu_item') }}</h4>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group float-end" role="group">
                        <a href="{{ route('menumasters.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('menumaster::message.back_to_menu_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card p-1">
                <div class="card-body">
                    <form action="{{ route('menumasters.store') }}" method="POST" id="menuForm" autocomplete="none">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="menu_title">
                                    {{ __('menumaster::message.menu_title') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('menu_title') is-invalid @enderror"
                                    id="menu_title" name="menu_title" value="{{ old('menu_title') }}"
                                    placeholder="{{ __('menumaster::message.menu_title') }}" required>
                                <span class="invalid-feedback d-block" id="error_menu_title" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="menu_icon">
                                    {{ __('menumaster::message.menu_icon') }}
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('menu_icon') is-invalid @enderror"
                                        id="menu_icon" name="menu_icon" value="{{ old('menu_icon') }}"
                                        placeholder="{{ __('menumaster::message.icon_class_example') }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showIconPicker()">
                                        <i class="fas fa-icons"></i>
                                    </button>
                                </div>
                                <span class="invalid-feedback d-block" id="error_menu_icon" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="menu_route">
                                    {{ __('menumaster::message.menu_route_url') }}
                                </label>
                                <input type="text" class="form-control @error('menu_route') is-invalid @enderror"
                                    id="menu_route" name="menu_route" value="{{ old('menu_route') }}"
                                    placeholder="/admin/dashboard">
                                <span class="invalid-feedback d-block" id="error_menu_route" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="parent_id">
                                    {{ __('menumaster::message.parent_menu') }}
                                </label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id"
                                    name="parent_id">
                                    <option value="">{{ __('menumaster::message.select_parent_menu') }}</option>
                                    @foreach ($parentOptions as $option)
                                        <option value="{{ $option['id'] }}"
                                            {{ old('parent_id') == $option['id'] ? 'selected' : '' }}>
                                            {{ $option['title'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block" id="error_parent_id" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="module_name">
                                    {{ __('menumaster::message.module_name') }}
                                </label>
                                <input type="text" class="form-control @error('module_name') is-invalid @enderror"
                                    id="module_name" name="module_name" value="{{ old('module_name') }}"
                                    placeholder="admin, inventory, sales, etc." list="modulesList">
                                <datalist id="modulesList">
                                    @foreach ($modules as $module)
                                        <option value="{{ $module }}">
                                    @endforeach
                                </datalist>
                                <span class="invalid-feedback d-block" id="error_module_name" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 form-group custom-input-group">
                                <label class="form-label" for="if_can">
                                    {{ __('menumaster::message.permission_required') }}
                                </label>
                                <input type="text" class="form-control @error('if_can') is-invalid @enderror"
                                    id="if_can" name="if_can" value="{{ old('if_can') }}"
                                    placeholder="manage-users, view-reports, etc.">
                                <span class="invalid-feedback d-block" id="error_if_can" role="alert"></span>
                            </div>

                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group custom-input-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                        class="form-check-input @error('is_main_menu') is-invalid @enderror"
                                        id="is_main_menu" name="is_main_menu" value="1"
                                        {{ old('is_main_menu') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_main_menu">
                                        {{ __('menumaster::message.mark_as_main_menu') }}
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        {{ __('menumaster::message.main_menu_description') }}
                                    </small>
                                    <span class="invalid-feedback d-block" id="error_is_main_menu" role="alert"></span>
                                </div>
                            </div>

                            <!-- Live Preview -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-4">
                                <h5>{{ __('menumaster::message.live_preview') }}:</h5>
                                <div class="border rounded p-3 bg-light">
                                    <div id="menuPreview" class="d-flex align-items-center">
                                        <i id="previewIcon" class="fas fa-circle me-2"></i>
                                        <span id="previewTitle">Menu Title</span>
                                        <span id="previewRoute" class="text-muted ms-2"></span>
                                        <span id="previewModule" class="badge bg-secondary ms-2"
                                            style="display: none;"></span>
                                        <span id="previewPermission" class="badge bg-info ms-1" style="display: none;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-5">
                                <a href="{{ route('menumasters.index') }}" class="btn btn-label-secondary float-start">
                                    <i class="fas fa-times"></i> {{ __('menumaster::message.cancel') }}
                                </a>
                                <button type="submit" id="save" class="btn btn-primary float-end save">
                                    <i class="fas fa-save"></i> {{ __('menumaster::message.create_menu_item') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Icon Picker Modal -->

    <div class="modal fade" id="iconPickerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('menumaster::message.select_icon') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <input type="text" class="form-control" id="iconSearch"
                            placeholder="{{ __('menumaster::message.search_icons') }}" autocomplete="off">
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="iconTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="fontawesome-tab" data-bs-toggle="tab"
                                data-bs-target="#fontawesome" type="button" role="tab">
                                Font Awesome
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabler-tab" data-bs-toggle="tab" data-bs-target="#tabler"
                                type="button" role="tab">
                                Tabler Icons
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="iconTabsContent" style="height: 700px; overflow-y: auto; ">

                        <!-- Font Awesome Tab -->
                        <div class="tab-pane fade show active" id="fontawesome" role="tabpanel">
                            <div class="accordion" id="fontawesomeAccordion">
                                @php
                                    $fontAwesomeCategories = [
                                        'General Icons' => [
                                            'fas fa-home',
                                            'fas fa-bars',
                                            'fas fa-th',
                                            'fas fa-list',
                                            'fas fa-plus',
                                            'fas fa-minus',
                                            'fas fa-times',
                                            'fas fa-check',
                                            'fas fa-search',
                                            'fas fa-filter',
                                            'fas fa-cog',
                                            'fas fa-sliders-h',
                                            'fas fa-ellipsis-h',
                                            'fas fa-ellipsis-v',
                                            'fas fa-star',
                                            'fas fa-flag',
                                            'fas fa-ban',
                                            'fas fa-bell',
                                            'fas fa-bell-slash',
                                            'fas fa-exclamation-triangle',
                                            'fas fa-exclamation-circle',
                                            'fas fa-question-circle',
                                            'fas fa-info-circle',
                                            'fas fa-sync',
                                            'fas fa-redo',
                                            'fas fa-undo',
                                            'fas fa-trash',
                                            'fas fa-trash-alt',
                                            'fas fa-edit',
                                            'fas fa-pencil-alt',
                                            'fas fa-save',
                                            'fas fa-download',
                                            'fas fa-upload',
                                            'fas fa-print',
                                            'fas fa-share',
                                            'fas fa-share-alt',
                                            'fas fa-external-link-alt',
                                            'fas fa-link',
                                            'fas fa-unlink',
                                            'fas fa-eye',
                                            'fas fa-eye-slash',
                                            'fas fa-lock',
                                            'fas fa-unlock',
                                            'fas fa-key',
                                            'fas fa-wrench',
                                            'fas fa-tools',
                                            'fas fa-cogs',
                                        ],
                                        'User & People' => [
                                            'fas fa-user',
                                            'fas fa-users',
                                            'fas fa-user-plus',
                                            'fas fa-user-minus',
                                            'fas fa-user-cog',
                                            'fas fa-user-edit',
                                            'fas fa-user-tie',
                                            'fas fa-user-md',
                                            'fas fa-user-graduate',
                                            'fas fa-user-nurse',
                                            'fas fa-user-shield',
                                            'fas fa-user-lock',
                                            'fas fa-user-alt',
                                            'fas fa-user-circle',
                                            'fas fa-id-card',
                                            'fas fa-id-badge',
                                            'fas fa-address-book',
                                            'fas fa-address-card',
                                            'fas fa-child',
                                            'fas fa-baby',
                                        ],
                                        'Files & Folders' => [
                                            'fas fa-file',
                                            'fas fa-file-alt',
                                            'fas fa-file-archive',
                                            'fas fa-file-audio',
                                            'fas fa-file-code',
                                            'fas fa-file-excel',
                                            'fas fa-file-image',
                                            'fas fa-file-pdf',
                                            'fas fa-file-powerpoint',
                                            'fas fa-file-video',
                                            'fas fa-file-word',
                                            'fas fa-file-signature',
                                            'fas fa-file-upload',
                                            'fas fa-file-download',
                                            'fas fa-file-export',
                                            'fas fa-file-import',
                                            'fas fa-file-invoice',
                                            'fas fa-file-invoice-dollar',
                                            'fas fa-folder',
                                            'fas fa-folder-open',
                                            'fas fa-folder-plus',
                                            'fas fa-folder-minus',
                                            'fas fa-archive',
                                            'fas fa-box',
                                            'fas fa-boxes',
                                            'fas fa-database',
                                            'fas fa-server',
                                            'fas fa-hdd',
                                            'fas fa-save',
                                        ],
                                        'Commerce & Shopping' => [
                                            'fas fa-shopping-cart',
                                            'fas fa-shopping-basket',
                                            'fas fa-shopping-bag',
                                            'fas fa-cash-register',
                                            'fas fa-receipt',
                                            'fas fa-tags',
                                            'fas fa-tag',
                                            'fas fa-barcode',
                                            'fas fa-qrcode',
                                            'fas fa-percentage',
                                            'fas fa-money-bill',
                                            'fas fa-money-bill-wave',
                                            'fas fa-money-bill-alt',
                                            'fas fa-money-check',
                                            'fas fa-money-check-alt',
                                            'fas fa-credit-card',
                                            'fas fa-wallet',
                                            'fas fa-gem',
                                            'fas fa-gift',
                                            'fas fa-store',
                                            'fas fa-store-alt',
                                            'fas fa-truck',
                                            'fas fa-shipping-fast',
                                            'fas fa-box-open',
                                            'fas fa-pallet',
                                            'fas fa-warehouse',
                                        ],
                                        'Charts & Analytics' => [
                                            'fas fa-chart-line',
                                            'fas fa-chart-bar',
                                            'fas fa-chart-pie',
                                            'fas fa-chart-area',
                                            'fas fa-chart-gantt',
                                            'fas fa-chart-simple',
                                            'fas fa-table',
                                            'fas fa-table-cells',
                                            'fas fa-table-list',
                                        ],
                                        'Communication' => [
                                            'fas fa-envelope',
                                            'fas fa-envelope-open',
                                            'fas fa-envelope-square',
                                            'fas fa-mail-bulk',
                                            'fas fa-inbox',
                                            'fas fa-comment',
                                            'fas fa-comments',
                                            'fas fa-comment-alt',
                                            'fas fa-comment-dots',
                                            'fas fa-comment-medical',
                                            'fas fa-comment-slash',
                                            'fas fa-commenting',
                                            'fas fa-comment-dollar',
                                            'fas fa-phone',
                                            'fas fa-phone-alt',
                                            'fas fa-phone-square',
                                            'fas fa-phone-volume',
                                            'fas fa-mobile',
                                            'fas fa-mobile-alt',
                                            'fas fa-sms',
                                            'fas fa-fax',
                                            'fas fa-rss',
                                            'fas fa-bullhorn',
                                        ],
                                        'Date & Time' => [
                                            'fas fa-clock',
                                            'fas fa-calendar',
                                            'fas fa-calendar-alt',
                                            'fas fa-calendar-check',
                                            'fas fa-calendar-day',
                                            'fas fa-calendar-week',
                                            'fas fa-calendar-times',
                                            'fas fa-calendar-minus',
                                            'fas fa-calendar-plus',
                                            'fas fa-calendar-xmark',
                                            'fas fa-stopwatch',
                                            'fas fa-hourglass',
                                            'fas fa-hourglass-start',
                                            'fas fa-hourglass-half',
                                            'fas fa-hourglass-end',
                                            'fas fa-history',
                                            'fas fa-business-time',
                                        ],
                                        'Health & Medical' => [
                                            'fas fa-heart',
                                            'fas fa-heartbeat',
                                            'fas fa-heart-broken',
                                            'fas fa-hospital',
                                            'fas fa-hospital-alt',
                                            'fas fa-clinic-medical',
                                            'fas fa-ambulance',
                                            'fas fa-briefcase-medical',
                                            'fas fa-first-aid',
                                            'fas fa-pills',
                                            'fas fa-prescription-bottle',
                                            'fas fa-prescription-bottle-alt',
                                            'fas fa-tablets',
                                            'fas fa-capsules',
                                            'fas fa-syringe',
                                            'fas fa-thermometer',
                                            'fas fa-stethoscope',
                                            'fas fa-microscope',
                                            'fas fa-brain',
                                            'fas fa-dna',
                                            'fas fa-allergies',
                                            'fas fa-band-aid',
                                            'fas fa-bone',
                                            'fas fa-lungs',
                                            'fas fa-procedures',
                                            'fas fa-teeth',
                                            'fas fa-teeth-open',
                                            'fas fa-tooth',
                                            'fas fa-virus',
                                            'fas fa-virus-slash',
                                            'fas fa-weight',
                                        ],
                                        'Transportation' => [
                                            'fas fa-car',
                                            'fas fa-car-alt',
                                            'fas fa-car-battery',
                                            'fas fa-car-crash',
                                            'fas fa-car-side',
                                            'fas fa-truck',
                                            'fas fa-truck-pickup',
                                            'fas fa-truck-moving',
                                            'fas fa-truck-monster',
                                            'fas fa-truck-loading',
                                            'fas fa-truck-field',
                                            'fas fa-truck-field-un',
                                            'fas fa-truck-front',
                                            'fas fa-truck-medical',
                                            'fas fa-truck-pickup',
                                            'fas fa-truck-ramp-box',
                                            'fas fa-bus',
                                            'fas fa-bus-alt',
                                            'fas fa-bus-simple',
                                            'fas fa-plane',
                                            'fas fa-plane-arrival',
                                            'fas fa-plane-departure',
                                            'fas fa-ship',
                                            'fas fa-rocket',
                                            'fas fa-bicycle',
                                            'fas fa-motorcycle',
                                            'fas fa-train',
                                            'fas fa-subway',
                                            'fas fa-taxi',
                                            'fas fa-shuttle-van',
                                            'fas fa-trailer',
                                            'fas fa-gas-pump',
                                            'fas fa-oil-can',
                                            'fas fa-route',
                                        ],
                                        'Education' => [
                                            'fas fa-graduation-cap',
                                            'fas fa-school',
                                            'fas fa-university',
                                            'fas fa-book',
                                            'fas fa-book-open',
                                            'fas fa-book-reader',
                                            'fas fa-bookmark',
                                            'fas fa-book-medical',
                                            'fas fa-atlas',
                                            'fas fa-bible',
                                            'fas fa-quran',
                                            'fas fa-globe',
                                            'fas fa-globe-americas',
                                            'fas fa-globe-africa',
                                            'fas fa-globe-asia',
                                            'fas fa-globe-europe',
                                            'fas fa-map',
                                            'fas fa-map-marked',
                                            'fas fa-map-marked-alt',
                                            'fas fa-map-marker',
                                            'fas fa-map-marker-alt',
                                            'fas fa-map-pin',
                                            'fas fa-map-signs',
                                            'fas fa-passport',
                                            'fas fa-pen',
                                            'fas fa-pen-alt',
                                            'fas fa-pen-fancy',
                                            'fas fa-pen-nib',
                                            'fas fa-pen-square',
                                            'fas fa-pencil-alt',
                                            'fas fa-pencil-ruler',
                                            'fas fa-ruler',
                                            'fas fa-ruler-combined',
                                            'fas fa-ruler-horizontal',
                                            'fas fa-ruler-vertical',
                                            'fas fa-paint-brush',
                                            'fas fa-paint-roller',
                                            'fas fa-palette',
                                            'fas fa-highlighter',
                                            'fas fa-marker',
                                            'fas fa-stamp',
                                            'fas fa-eraser',
                                            'fas fa-calculator',
                                            'fas fa-chalkboard',
                                            'fas fa-chalkboard-teacher',
                                            'fas fa-clipboard',
                                            'fas fa-clipboard-check',
                                            'fas fa-clipboard-list',
                                            'fas fa-clipboard-user',
                                        ],
                                        'Technology' => [
                                            'fas fa-laptop',
                                            'fas fa-laptop-code',
                                            'fas fa-laptop-medical',
                                            'fas fa-desktop',
                                            'fas fa-server',
                                            'fas fa-database',
                                            'fas fa-hdd',
                                            'fas fa-memory',
                                            'fas fa-microchip',
                                            'fas fa-mobile',
                                            'fas fa-mobile-alt',
                                            'fas fa-tablet',
                                            'fas fa-tablet-alt',
                                            'fas fa-mouse',
                                            'fas fa-keyboard',
                                            'fas fa-gamepad',
                                            'fas fa-headphones',
                                            'fas fa-headset',
                                            'fas fa-display',
                                            'fas fa-tv',
                                            'fas fa-plug',
                                            'fas fa-power-off',
                                            'fas fa-battery-full',
                                            'fas fa-battery-three-quarters',
                                            'fas fa-battery-half',
                                            'fas fa-battery-quarter',
                                            'fas fa-battery-empty',
                                            'fas fa-bolt',
                                            'fas fa-lightbulb',
                                            'fas fa-satellite',
                                            'fas fa-satellite-dish',
                                            'fas fa-wifi',
                                            'fas fa-bluetooth',
                                            'fas fa-broadcast-tower',
                                            'fas fa-ethernet',
                                            'fas fa-hashtag',
                                            'fas fa-code',
                                            'fas fa-code-branch',
                                            'fas fa-terminal',
                                            'fas fa-window-maximize',
                                            'fas fa-window-minimize',
                                            'fas fa-window-restore',
                                            'fas fa-bug',
                                            'fas fa-virus',
                                            'fas fa-virus-slash',
                                            'fas fa-shield-alt',
                                            'fas fa-shield-virus',
                                            'fas fa-fire',
                                            'fas fa-fire-alt',
                                            'fas fa-fire-extinguisher',
                                            'fas fa-network-wired',
                                            'fas fa-robot',
                                            'fas fa-sim-card',
                                        ],
                                        'Business & Finance' => [
                                            'fas fa-briefcase',
                                            'fas fa-suitcase',
                                            'fas fa-suitcase-rolling',
                                            'fas fa-building',
                                            'fas fa-landmark',
                                            'fas fa-industry',
                                            'fas fa-city',
                                            'fas fa-hotel',
                                            'fas fa-store',
                                            'fas fa-store-alt',
                                            'fas fa-warehouse',
                                            'fas fa-pallet',
                                            'fas fa-boxes',
                                            'fas fa-box-open',
                                            'fas fa-truck-loading',
                                            'fas fa-dolly',
                                            'fas fa-dolly-flatbed',
                                            'fas fa-forklift',
                                            'fas fa-handshake',
                                            'fas fa-handshake-alt',
                                            'fas fa-piggy-bank',
                                            'fas fa-coins',
                                            'fas fa-money-bill',
                                            'fas fa-money-bill-wave',
                                            'fas fa-money-bill-alt',
                                            'fas fa-money-check',
                                            'fas fa-money-check-alt',
                                            'fas fa-credit-card',
                                            'fas fa-chart-line',
                                            'fas fa-chart-bar',
                                            'fas fa-chart-pie',
                                            'fas fa-chart-area',
                                            'fas fa-balance-scale',
                                            'fas fa-balance-scale-left',
                                            'fas fa-balance-scale-right',
                                            'fas fa-receipt',
                                            'fas fa-file-invoice',
                                            'fas fa-file-invoice-dollar',
                                            'fas fa-file-contract',
                                            'fas fa-signature',
                                            'fas fa-stamp',
                                            'fas fa-trophy',
                                            'fas fa-award',
                                            'fas fa-medal',
                                            'fas fa-crown',
                                            'fas fa-gem',
                                            'fas fa-certificate',
                                            'fas fa-scroll',
                                        ],
                                        'Food & Restaurant' => [
                                            'fas fa-utensils',
                                            'fas fa-utensil-spoon',
                                            'fas fa-hamburger',
                                            'fas fa-pizza-slice',
                                            'fas fa-hotdog',
                                            'fas fa-bacon',
                                            'fas fa-drumstick-bite',
                                            'fas fa-egg',
                                            'fas fa-cheese',
                                            'fas fa-bread-slice',
                                            'fas fa-cookie',
                                            'fas fa-cookie-bite',
                                            'fas fa-cake-candles',
                                            'fas fa-ice-cream',
                                            'fas fa-fish',
                                            'fas fa-apple-alt',
                                            'fas fa-lemon',
                                            'fas fa-pepper-hot',
                                            'fas fa-mug-hot',
                                            'fas fa-coffee',
                                            'fas fa-wine-glass',
                                            'fas fa-wine-glass-alt',
                                            'fas fa-beer',
                                            'fas fa-cocktail',
                                            'fas fa-glass-martini',
                                            'fas fa-glass-martini-alt',
                                            'fas fa-wine-bottle',
                                            'fas fa-concierge-bell',
                                            'fas fa-blender',
                                            'fas fa-mortar-pestle',
                                        ],
                                        'Weather & Nature' => [
                                            'fas fa-sun',
                                            'fas fa-moon',
                                            'fas fa-cloud',
                                            'fas fa-cloud-sun',
                                            'fas fa-cloud-moon',
                                            'fas fa-cloud-rain',
                                            'fas fa-cloud-showers-heavy',
                                            'fas fa-cloud-sun-rain',
                                            'fas fa-cloud-moon-rain',
                                            'fas fa-snowflake',
                                            'fas fa-wind',
                                            'fas fa-smog',
                                            'fas fa-tornado',
                                            'fas fa-hurricane',
                                            'fas fa-thunderstorm',
                                            'fas fa-volcano',
                                            'fas fa-umbrella',
                                            'fas fa-umbrella-beach',
                                            'fas fa-water',
                                            'fas fa-fire',
                                            'fas fa-mountain',
                                            'fas fa-tree',
                                            'fas fa-leaf',
                                            'fas fa-seedling',
                                            'fas fa-spa',
                                            'fas fa-feather',
                                            'fas fa-feather-alt',
                                            'fas fa-paw',
                                            'fas fa-dog',
                                            'fas fa-cat',
                                            'fas fa-horse',
                                            'fas fa-horse-head',
                                            'fas fa-fish',
                                            'fas fa-kiwi-bird',
                                            'fas fa-crow',
                                            'fas fa-dove',
                                            'fas fa-spider',
                                            'fas fa-bug',
                                        ],
                                    ];
                                @endphp

                                @foreach ($fontAwesomeCategories as $category => $icons)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="fa-heading-{{ Str::slug($category) }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#fa-collapse-{{ Str::slug($category) }}"
                                                aria-expanded="false"
                                                aria-controls="fa-collapse-{{ Str::slug($category) }}">
                                                {{ $category }}
                                            </button>
                                        </h2>
                                        <div id="fa-collapse-{{ Str::slug($category) }}"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="fa-heading-{{ Str::slug($category) }}"
                                            data-bs-parent="#fontawesomeAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach ($icons as $icon)
                                                        <div class="col-md-2 col-sm-3 col-4 mb-2 icon-item">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary w-100 icon-option"
                                                                data-icon="{{ $icon }}"
                                                                title="{{ $icon }}">
                                                                <i class="{{ $icon }}"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tabler Icons Tab -->
                        <div class="tab-pane fade" id="tabler" role="tabpanel">
                            <div class="accordion" id="tablerAccordion">
                                @php
                                    $tablerCategories = [
                                        'General Icons' => [
                                            'ti tabler-2fa',
                                            'ti tabler-3d-cube-sphere',
                                            'ti tabler-a-b',
                                            'ti tabler-a-b-off',
                                            'ti tabler-abacus',
                                            'ti tabler-access-point',
                                            'ti tabler-access-point-off',
                                            'ti tabler-adjustments',
                                            'ti tabler-adjustments-alt',
                                            'ti tabler-adjustments-horizontal',
                                            'ti tabler-aerial-lift',
                                            'ti tabler-affiliate',
                                            'ti tabler-alarm',
                                            'ti tabler-album',
                                            'ti tabler-alert-circle',
                                            'ti tabler-alert-octagon',
                                            'ti tabler-alert-triangle',
                                            'ti tabler-alien',
                                            'ti tabler-align-center',
                                            'ti tabler-align-justified',
                                            'ti tabler-align-left',
                                            'ti tabler-align-right',
                                            'ti tabler-ambulance',
                                            'ti tabler-anchor',
                                            'ti tabler-angle',
                                            'ti tabler-antenna',
                                            'ti tabler-apple',
                                            'ti tabler-archive',
                                            'ti tabler-armchair',
                                            'ti tabler-arrow-back',
                                            'ti tabler-arrow-bar-down',
                                            'ti tabler-arrow-bar-left',
                                            'ti tabler-arrow-bar-right',
                                            'ti tabler-arrow-bar-up',
                                            'ti tabler-arrow-bear-left',
                                            'ti tabler-arrow-bear-right',
                                            'ti tabler-arrow-big-down',
                                            'ti tabler-arrow-big-left',
                                            'ti tabler-arrow-big-right',
                                            'ti tabler-arrow-big-up',
                                            'ti tabler-arrow-curve-left',
                                            'ti tabler-arrow-curve-right',
                                            'ti tabler-arrow-down',
                                            'ti tabler-arrow-down-circle',
                                            'ti tabler-arrow-down-left',
                                            'ti tabler-arrow-down-right',
                                            'ti tabler-arrow-fork',
                                            'ti tabler-arrow-forward',
                                            'ti tabler-arrow-left',
                                            'ti tabler-arrow-left-circle',
                                        ],
                                        'User & People' => [
                                            'ti tabler-user',
                                            'ti tabler-users',
                                            'ti tabler-user-circle',
                                            'ti tabler-user-plus',
                                            'ti tabler-user-minus',
                                            'ti tabler-user-x',
                                            'ti tabler-user-check',
                                            'ti tabler-user-off',
                                            'ti tabler-user-exclamation',
                                            'ti tabler-user-search',
                                            'ti tabler-friends',
                                            'ti tabler-man',
                                            'ti tabler-woman',
                                            'ti tabler-baby',
                                            'ti tabler-businessman',
                                            'ti tabler-nurse',
                                            'ti tabler-chef',
                                            'ti tabler-meeple',
                                            'ti tabler-robot',
                                            'ti tabler-mask',
                                            'ti tabler-id',
                                            'ti tabler-id-badge',
                                            'ti tabler-id-off',
                                            'ti tabler-login',
                                            'ti tabler-logout',
                                            'ti tabler-shield',
                                            'ti tabler-shield-check',
                                            'ti tabler-shield-lock',
                                            'ti tabler-shield-off',
                                            'ti tabler-shield-x',
                                            'ti tabler-mood-happy',
                                            'ti tabler-mood-smile',
                                            'ti tabler-mood-neutral',
                                            'ti tabler-mood-sad',
                                            'ti tabler-mood-sick',
                                            'ti tabler-mood-tongue',
                                            'ti tabler-mood-angry',
                                            'ti tabler-mood-confuzed',
                                            'ti tabler-mood-kid',
                                            'ti tabler-mood-nervous',
                                            'ti tabler-mood-sing',
                                            'ti tabler-mood-suprised',
                                            'ti tabler-mood-wink',
                                            'ti tabler-mood-wrr',
                                            'ti tabler-mood-empty',
                                            'ti tabler-mood-cry',
                                            'ti tabler-mood-heart',
                                            'ti tabler-mood-laugh',
                                            'ti tabler-mood-off',
                                            'ti tabler-mood-up',
                                        ],
                                        'Files & Folders' => [
                                            'ti tabler-file',
                                            'ti tabler-files',
                                            'ti tabler-file-text',
                                            'ti tabler-file-upload',
                                            'ti tabler-file-download',
                                            'ti tabler-file-check',
                                            'ti tabler-file-x',
                                            'ti tabler-file-search',
                                            'ti tabler-file-plus',
                                            'ti tabler-file-minus',
                                            'ti tabler-file-zip',
                                            'ti tabler-file-code',
                                            'ti tabler-file-image',
                                            'ti tabler-file-music',
                                            'ti tabler-file-video',
                                            'ti tabler-file-scissors',
                                            'ti tabler-folder',
                                            'ti tabler-folder-plus',
                                            'ti tabler-folder-minus',
                                            'ti tabler-folder-x',
                                            'ti tabler-folder-check',
                                            'ti tabler-folder-search',
                                            'ti tabler-folder-symlink',
                                            'ti tabler-bookmark',
                                            'ti tabler-bookmarks',
                                            'ti tabler-notebook',
                                            'ti tabler-archive',
                                            'ti tabler-database',
                                            'ti tabler-stack',
                                            'ti tabler-layers',
                                            'ti tabler-file-database',
                                            'ti tabler-file-certificate',
                                            'ti tabler-file-chart',
                                            'ti tabler-file-export',
                                            'ti tabler-file-import',
                                            'ti tabler-file-invoice',
                                            'ti tabler-file-lock',
                                            'ti tabler-file-move',
                                            'ti tabler-file-pencil',
                                            'ti tabler-file-power',
                                            'ti tabler-file-report',
                                            'ti tabler-file-settings',
                                            'ti tabler-file-shredder',
                                            'ti tabler-file-star',
                                            'ti tabler-file-time',
                                            'ti tabler-file-typography',
                                            'ti tabler-file-unknown',
                                            'ti tabler-file-user',
                                            'ti tabler-file-vector',
                                            'ti tabler-file-zip',
                                        ],
                                        'Commerce & Shopping' => [
                                            'ti tabler-shopping-cart',
                                            'ti tabler-basket',
                                            'ti tabler-credit-card',
                                            'ti tabler-cash',
                                            'ti tabler-coins',
                                            'ti tabler-tag',
                                            'ti tabler-tags',
                                            'ti tabler-receipt',
                                            'ti tabler-receipt-2',
                                            'ti tabler-discount',
                                            'ti tabler-percentage',
                                            'ti tabler-coin',
                                            'ti tabler-wallet',
                                            'ti tabler-gift',
                                            'ti tabler-package',
                                            'ti tabler-truck',
                                            'ti tabler-truck-delivery',
                                            'ti tabler-truck-return',
                                            'ti tabler-scale',
                                            'ti tabler-barcode',
                                            'ti tabler-qrcode',
                                            'ti tabler-store',
                                            'ti tabler-building-store',
                                            'ti tabler-building-warehouse',
                                            'ti tabler-ticket',
                                            'ti tabler-coupon',
                                            'ti tabler-medal',
                                            'ti tabler-crown',
                                            'ti tabler-trophy',
                                            'ti tabler-shopping-bag',
                                            'ti tabler-coin-bitcoin',
                                            'ti tabler-coin-euro',
                                            'ti tabler-coin-pound',
                                            'ti tabler-coin-rupee',
                                            'ti tabler-coin-yen',
                                            'ti tabler-currency-dollar',
                                            'ti tabler-currency-euro',
                                            'ti tabler-currency-pound',
                                            'ti tabler-currency-rupee',
                                            'ti tabler-currency-yen',
                                            'ti tabler-discount-check',
                                            'ti tabler-discount-off',
                                            'ti tabler-pig-money',
                                            'ti tabler-shopping-cart-off',
                                            'ti tabler-shopping-cart-plus',
                                            'ti tabler-shopping-cart-x',
                                            'ti tabler-tag-off',
                                            'ti tabler-wallet-off',
                                            'ti tabler-cash-banknote',
                                            'ti tabler-cash-banknote-off',
                                        ],
                                        'Charts & Analytics' => [
                                            'ti tabler-chart-line',
                                            'ti tabler-chart-bar',
                                            'ti tabler-chart-pie',
                                            'ti tabler-chart-donut',
                                            'ti tabler-chart-area',
                                            'ti tabler-chart-candle',
                                            'ti tabler-chart-bubble',
                                            'ti tabler-chart-arrows',
                                            'ti tabler-chart-radar',
                                            'ti tabler-trending-up',
                                            'ti tabler-trending-down',
                                            'ti tabler-calculator',
                                            'ti tabler-abacus',
                                            'ti tabler-sum',
                                            'ti tabler-chart-infographic',
                                            'ti tabler-arrow-up',
                                            'ti tabler-arrow-down',
                                            'ti tabler-arrow-left',
                                            'ti tabler-arrow-right',
                                            'ti tabler-graph',
                                            'ti tabler-stats',
                                            'ti tabler-stats-up',
                                            'ti tabler-stats-down',
                                            'ti tabler-target',
                                            'ti tabler-bullseye',
                                            'ti tabler-gauge',
                                            'ti tabler-speedometer',
                                            'ti tabler-dashboard',
                                            'ti tabler-meter',
                                            'ti tabler-infinity',
                                            'ti tabler-chart-arcs',
                                            'ti tabler-chart-arrows-vertical',
                                            'ti tabler-chart-candle-filled',
                                            'ti tabler-chart-dots',
                                            'ti tabler-chart-dots-2',
                                            'ti tabler-chart-dots-3',
                                            'ti tabler-chart-grid-dots',
                                            'ti tabler-chart-histogram',
                                            'ti tabler-chart-line-filled',
                                            'ti tabler-chart-pie-2',
                                            'ti tabler-chart-pie-3',
                                            'ti tabler-chart-pie-4',
                                            'ti tabler-chart-ppf',
                                            'ti tabler-chart-radar-filled',
                                            'ti tabler-chart-sankey',
                                            'ti tabler-chart-treemap',
                                            'ti tabler-coin-bitcoin',
                                            'ti tabler-currency-bitcoin',
                                            'ti tabler-growth',
                                            'ti tabler-report-analytics',
                                        ],
                                        'Communication' => [
                                            'ti tabler-mail',
                                            'ti tabler-mail-opened',
                                            'ti tabler-mail-forward',
                                            'ti tabler-mailbox',
                                            'ti tabler-inbox',
                                            'ti tabler-send',
                                            'ti tabler-send-off',
                                            'ti tabler-message',
                                            'ti tabler-message-2',
                                            'ti tabler-message-circle',
                                            'ti tabler-message-dots',
                                            'ti tabler-message-plus',
                                            'ti tabler-message-report',
                                            'ti tabler-message-off',
                                            'ti tabler-chat',
                                            'ti tabler-chat-off',
                                            'ti tabler-phone',
                                            'ti tabler-phone-call',
                                            'ti tabler-phone-incoming',
                                            'ti tabler-phone-outgoing',
                                            'ti tabler-phone-pause',
                                            'ti tabler-phone-plus',
                                            'ti tabler-phone-off',
                                            'ti tabler-address-book',
                                            'ti tabler-at',
                                            'ti tabler-hash',
                                            'ti tabler-voicemail',
                                            'ti tabler-rss',
                                            'ti tabler-brand-telegram',
                                            'ti tabler-brand-whatsapp',
                                            'ti tabler-brand-messenger',
                                            'ti tabler-brand-slack',
                                            'ti tabler-brand-discord',
                                            'ti tabler-brand-twitter',
                                            'ti tabler-brand-facebook',
                                            'ti tabler-brand-instagram',
                                            'ti tabler-brand-youtube',
                                            'ti tabler-brand-linkedin',
                                            'ti tabler-brand-reddit',
                                            'ti tabler-brand-tiktok',
                                            'ti tabler-brand-pinterest',
                                            'ti tabler-brand-skype',
                                            'ti tabler-brand-zoom',
                                            'ti tabler-brand-google',
                                            'ti tabler-brand-apple',
                                            'ti tabler-brand-android',
                                            'ti tabler-brand-windows',
                                            'ti tabler-brand-github',
                                            'ti tabler-brand-gitlab',
                                            'ti tabler-brand-figma',
                                        ],
                                        'Date & Time' => [
                                            'ti tabler-clock',
                                            'ti tabler-clock-hour-1',
                                            'ti tabler-clock-hour-2',
                                            'ti tabler-clock-hour-3',
                                            'ti tabler-clock-hour-4',
                                            'ti tabler-clock-hour-5',
                                            'ti tabler-clock-hour-6',
                                            'ti tabler-clock-hour-7',
                                            'ti tabler-clock-hour-8',
                                            'ti tabler-clock-hour-9',
                                            'ti tabler-clock-hour-10',
                                            'ti tabler-clock-hour-11',
                                            'ti tabler-clock-hour-12',
                                            'ti tabler-alarm',
                                            'ti tabler-alarm-off',
                                            'ti tabler-calendar',
                                            'ti tabler-calendar-event',
                                            'ti tabler-calendar-minus',
                                            'ti tabler-calendar-plus',
                                            'ti tabler-calendar-off',
                                            'ti tabler-calendar-time',
                                            'ti tabler-calendar-stats',
                                            'ti tabler-calendar-check',
                                            'ti tabler-hourglass',
                                            'ti tabler-hourglass-high',
                                            'ti tabler-hourglass-low',
                                            'ti tabler-hourglass-off',
                                            'ti tabler-timeline',
                                            'ti tabler-time-duration-0',
                                            'ti tabler-time-duration-10',
                                            'ti tabler-time-duration-15',
                                            'ti tabler-time-duration-30',
                                            'ti tabler-time-duration-45',
                                            'ti tabler-time-duration-60',
                                            'ti tabler-time-duration-90',
                                            'ti tabler-time-duration-off',
                                            'ti tabler-world',
                                            'ti tabler-world-off',
                                            'ti tabler-timezone',
                                            'ti tabler-sunrise',
                                            'ti tabler-sunset',
                                            'ti tabler-moon',
                                            'ti tabler-moon-stars',
                                            'ti tabler-moon-2',
                                            'ti tabler-bell',
                                            'ti tabler-bell-ringing',
                                            'ti tabler-bell-off',
                                            'ti tabler-stopwatch',
                                            'ti tabler-history',
                                            'ti tabler-history-toggle',
                                        ],
                                        'Health & Medical' => [
                                            'ti tabler-heart',
                                            'ti tabler-heartbeat',
                                            'ti tabler-heart-off',
                                            'ti tabler-heart-plus',
                                            'ti tabler-heart-minus',
                                            'ti tabler-pill',
                                            'ti tabler-medicine-syrup',
                                            'ti tabler-stethoscope',
                                            'ti tabler-first-aid-kit',
                                            'ti tabler-ambulance',
                                            'ti tabler-hospital',
                                            'ti tabler-nurse',
                                            'ti tabler-bandage',
                                            'ti tabler-bone',
                                            'ti tabler-brain',
                                            'ti tabler-eye',
                                            'ti tabler-eye-off',
                                            'ti tabler-ear',
                                            'ti tabler-lungs',
                                            'ti tabler-mood-sick',
                                            'ti tabler-device-heart-monitor',
                                            'ti tabler-device-thermometer',
                                            'ti tabler-device-aed',
                                            'ti tabler-device-imac-heart',
                                            'ti tabler-device-watch-heart-rate',
                                            'ti tabler-wheelchair',
                                            'ti tabler-crutches',
                                            'ti tabler-infinity',
                                            'ti tabler-massage',
                                            'ti tabler-medical-cross',
                                            'ti tabler-microscope',
                                            'ti tabler-thermometer',
                                            'ti tabler-vaccine',
                                            'ti tabler-vaccine-bottle',
                                            'ti tabler-virus',
                                            'ti tabler-virus-off',
                                            'ti tabler-virus-search',
                                            'ti tabler-dna',
                                            'ti tabler-dna-2',
                                            'ti tabler-dna-off',
                                            'ti tabler-bed',
                                            'ti tabler-bed-off',
                                            'ti tabler-bed-flat',
                                            'ti tabler-bed-patient',
                                            'ti tabler-activity',
                                            'ti tabler-armchair',
                                            'ti tabler-armchair-off',
                                            'ti tabler-bath',
                                            'ti tabler-bath-off',
                                            'ti tabler-toilet-paper',
                                        ],
                                        'Transportation' => [
                                            'ti tabler-car',
                                            'ti tabler-car-off',
                                            'ti tabler-car-crash',
                                            'ti tabler-car-turbine',
                                            'ti tabler-bike',
                                            'ti tabler-bike-off',
                                            'ti tabler-bus',
                                            'ti tabler-bus-off',
                                            'ti tabler-bus-stop',
                                            'ti tabler-truck',
                                            'ti tabler-truck-off',
                                            'ti tabler-truck-delivery',
                                            'ti tabler-truck-return',
                                            'ti tabler-truck-loading',
                                            'ti tabler-truck-export',
                                            'ti tabler-truck-import',
                                            'ti tabler-train',
                                            'ti tabler-train-off',
                                            'ti tabler-plane',
                                            'ti tabler-plane-off',
                                            'ti tabler-plane-inflight',
                                            'ti tabler-plane-arrival',
                                            'ti tabler-plane-departure',
                                            'ti tabler-helicopter',
                                            'ti tabler-sailboat',
                                            'ti tabler-sailboat-off',
                                            'ti tabler-ship',
                                            'ti tabler-ship-off',
                                            'ti tabler-rocket',
                                            'ti tabler-rocket-off',
                                            'ti tabler-gas-station',
                                            'ti tabler-gas-station-off',
                                            'ti tabler-charging-pile',
                                            'ti tabler-charging-pile-off',
                                            'ti tabler-parking',
                                            'ti tabler-parking-off',
                                            'ti tabler-road',
                                            'ti tabler-road-off',
                                            'ti tabler-steering-wheel',
                                            'ti tabler-steering-wheel-off',
                                            'ti tabler-traffic-cone',
                                            'ti tabler-traffic-lights',
                                            'ti tabler-traffic-lights-off',
                                            'ti tabler-wheel',
                                            'ti tabler-wheelchair',
                                            'ti tabler-ufo',
                                            'ti tabler-ufo-off',
                                            'ti tabler-parachute',
                                            'ti tabler-parachute-off',
                                            'ti tabler-submarine',
                                        ],
                                        'Education' => [
                                            'ti tabler-school',
                                            'ti tabler-school-off',
                                            'ti tabler-book',
                                            'ti tabler-book-off',
                                            'ti tabler-book-2',
                                            'ti tabler-notebook',
                                            'ti tabler-notebook-off',
                                            'ti tabler-bookmark',
                                            'ti tabler-bookmark-off',
                                            'ti tabler-bookmarks',
                                            'ti tabler-pencil',
                                            'ti tabler-pencil-off',
                                            'ti tabler-pen',
                                            'ti tabler-pen-off',
                                            'ti tabler-highlighter',
                                            'ti tabler-marker',
                                            'ti tabler-marker-off',
                                            'ti tabler-writing',
                                            'ti tabler-writing-off',
                                            'ti tabler-ruler',
                                            'ti tabler-ruler-2',
                                            'ti tabler-ruler-3',
                                            'ti tabler-ruler-off',
                                            'ti tabler-calculator',
                                            'ti tabler-calculator-off',
                                            'ti tabler-abacus',
                                            'ti tabler-abacus-off',
                                            'ti tabler-atom',
                                            'ti tabler-atom-2',
                                            'ti tabler-award',
                                            'ti tabler-award-off',
                                            'ti tabler-certificate',
                                            'ti tabler-certificate-2',
                                            'ti tabler-certificate-off',
                                            'ti tabler-backpack',
                                            'ti tabler-backpack-off',
                                            'ti tabler-brain',
                                            'ti tabler-brain-off',
                                            'ti tabler-bulb',
                                            'ti tabler-bulb-off',
                                            'ti tabler-flask',
                                            'ti tabler-flask-2',
                                            'ti tabler-flask-off',
                                            'ti tabler-graduation-cap',
                                            'ti tabler-graduation-cap-off',
                                            'ti tabler-microscope',
                                            'ti tabler-microscope-off',
                                            'ti tabler-telescope',
                                            'ti tabler-telescope-off',
                                            'ti tabler-vocabulary',
                                        ],
                                        'Technology' => [
                                            'ti tabler-device-desktop',
                                            'ti tabler-device-desktop-off',
                                            'ti tabler-device-laptop',
                                            'ti tabler-device-laptop-off',
                                            'ti tabler-device-tablet',
                                            'ti tabler-device-tablet-off',
                                            'ti tabler-device-mobile',
                                            'ti tabler-device-mobile-off',
                                            'ti tabler-device-watch',
                                            'ti tabler-device-watch-off',
                                            'ti tabler-device-tv',
                                            'ti tabler-device-tv-off',
                                            'ti tabler-device-gamepad',
                                            'ti tabler-device-gamepad-2',
                                            'ti tabler-device-gamepad-off',
                                            'ti tabler-device-headphones',
                                            'ti tabler-device-headphones-off',
                                            'ti tabler-device-speaker',
                                            'ti tabler-device-speaker-off',
                                            'ti tabler-device-camera',
                                            'ti tabler-device-camera-off',
                                            'ti tabler-device-flash',
                                            'ti tabler-device-flash-off',
                                            'ti tabler-device-sd-card',
                                            'ti tabler-device-sd-card-off',
                                            'ti tabler-device-ssd',
                                            'ti tabler-device-ssd-off',
                                            'ti tabler-device-usb',
                                            'ti tabler-device-usb-off',
                                            'ti tabler-device-airpods',
                                            'ti tabler-device-airpods-case',
                                            'ti tabler-device-imac',
                                            'ti tabler-device-imac-off',
                                            'ti tabler-device-ipad',
                                            'ti tabler-device-ipad-off',
                                            'ti tabler-device-iphone',
                                            'ti tabler-device-iphone-off',
                                            'ti tabler-device-landline-phone',
                                            'ti tabler-device-landline-phone-off',
                                            'ti tabler-device-nintendo',
                                            'ti tabler-device-nintendo-off',
                                            'ti tabler-device-playstation',
                                            'ti tabler-device-playstation-off',
                                            'ti tabler-device-xbox',
                                            'ti tabler-device-xbox-off',
                                            'ti tabler-device-bluetooth',
                                            'ti tabler-device-bluetooth-off',
                                            'ti tabler-device-remote',
                                            'ti tabler-device-remote-off',
                                            'ti tabler-device-projector',
                                        ],
                                        'Business & Finance' => [
                                            'ti tabler-briefcase',
                                            'ti tabler-briefcase-off',
                                            'ti tabler-building',
                                            'ti tabler-building-off',
                                            'ti tabler-building-bank',
                                            'ti tabler-building-bank-off',
                                            'ti tabler-building-warehouse',
                                            'ti tabler-building-warehouse-off',
                                            'ti tabler-building-store',
                                            'ti tabler-building-store-off',
                                            'ti tabler-building-skyscraper',
                                            'ti tabler-building-skyscraper-off',
                                            'ti tabler-building-community',
                                            'ti tabler-building-community-off',
                                            'ti tabler-building-factory',
                                            'ti tabler-building-factory-2',
                                            'ti tabler-building-hospital',
                                            'ti tabler-building-hospital-off',
                                            'ti tabler-building-lighthouse',
                                            'ti tabler-building-lighthouse-off',
                                            'ti tabler-building-pavilion',
                                            'ti tabler-building-pavilion-off',
                                            'ti tabler-building-church',
                                            'ti tabler-building-church-off',
                                            'ti tabler-building-castle',
                                            'ti tabler-building-castle-off',
                                            'ti tabler-building-monument',
                                            'ti tabler-building-monument-off',
                                            'ti tabler-building-circus',
                                            'ti tabler-building-circus-off',
                                            'ti tabler-cash',
                                            'ti tabler-cash-banknote',
                                            'ti tabler-cash-banknote-off',
                                            'ti tabler-coin',
                                            'ti tabler-coin-off',
                                            'ti tabler-coins',
                                            'ti tabler-coins-off',
                                            'ti tabler-credit-card',
                                            'ti tabler-credit-card-off',
                                            'ti tabler-currency-dollar',
                                            'ti tabler-currency-euro',
                                            'ti tabler-currency-pound',
                                            'ti tabler-currency-rupee',
                                            'ti tabler-currency-yen',
                                            'ti tabler-currency-bitcoin',
                                            'ti tabler-currency-ethereum',
                                            'ti tabler-currency-litecoin',
                                            'ti tabler-currency-dogecoin',
                                            'ti tabler-currency-solana',
                                            'ti tabler-currency-tether',
                                        ],
                                        'Food & Restaurant' => [
                                            'ti tabler-apple',
                                            'ti tabler-apple-off',
                                            'ti tabler-baguette',
                                            'ti tabler-baguette-off',
                                            'ti tabler-beer',
                                            'ti tabler-beer-off',
                                            'ti tabler-bottle',
                                            'ti tabler-bottle-off',
                                            'ti tabler-bowl',
                                            'ti tabler-bowl-off',
                                            'ti tabler-bread',
                                            'ti tabler-bread-off',
                                            'ti tabler-cake',
                                            'ti tabler-cake-off',
                                            'ti tabler-carrot',
                                            'ti tabler-carrot-off',
                                            'ti tabler-cheese',
                                            'ti tabler-cheese-off',
                                            'ti tabler-coffee',
                                            'ti tabler-coffee-off',
                                            'ti tabler-cookie',
                                            'ti tabler-cookie-off',
                                            'ti tabler-cup',
                                            'ti tabler-cup-off',
                                            'ti tabler-eggs',
                                            'ti tabler-eggs-off',
                                            'ti tabler-fish',
                                            'ti tabler-fish-off',
                                            'ti tabler-fork',
                                            'ti tabler-fork-off',
                                            'ti tabler-glass',
                                            'ti tabler-glass-off',
                                            'ti tabler-grill',
                                            'ti tabler-grill-off',
                                            'ti tabler-hamburger',
                                            'ti tabler-hamburger-off',
                                            'ti tabler-ice-cream',
                                            'ti tabler-ice-cream-2',
                                            'ti tabler-ice-cream-off',
                                            'ti tabler-knife',
                                            'ti tabler-knife-off',
                                            'ti tabler-lemon',
                                            'ti tabler-lemon-off',
                                            'ti tabler-meat',
                                            'ti tabler-meat-off',
                                            'ti tabler-milk',
                                            'ti tabler-milk-off',
                                            'ti tabler-mushroom',
                                            'ti tabler-mushroom-off',
                                            'ti tabler-pepper',
                                            'ti tabler-pepper-off',
                                            'ti tabler-pizza',
                                            'ti tabler-pizza-off',
                                            'ti tabler-salad',
                                            'ti tabler-salad-off',
                                        ],
                                        'Weather & Nature' => [
                                            'ti tabler-sun',
                                            'ti tabler-sun-off',
                                            'ti tabler-moon',
                                            'ti tabler-moon-off',
                                            'ti tabler-moon-stars',
                                            'ti tabler-cloud',
                                            'ti tabler-cloud-off',
                                            'ti tabler-cloud-rain',
                                            'ti tabler-cloud-snow',
                                            'ti tabler-cloud-storm',
                                            'ti tabler-cloud-lightning',
                                            'ti tabler-cloud-fog',
                                            'ti tabler-cloud-wind',
                                            'ti tabler-wind',
                                            'ti tabler-wind-off',
                                            'ti tabler-snowflake',
                                            'ti tabler-snowflake-off',
                                            'ti tabler-umbrella',
                                            'ti tabler-umbrella-off',
                                            'ti tabler-flame',
                                            'ti tabler-flame-off',
                                            'ti tabler-droplet',
                                            'ti tabler-droplet-off',
                                            'ti tabler-rainbow',
                                            'ti tabler-rainbow-off',
                                            'ti tabler-tree',
                                            'ti tabler-tree-off',
                                            'ti tabler-seeding',
                                            'ti tabler-seeding-off',
                                            'ti tabler-flower',
                                            'ti tabler-flower-off',
                                            'ti tabler-leaf',
                                            'ti tabler-leaf-off',
                                            'ti tabler-mountain',
                                            'ti tabler-mountain-off',
                                            'ti tabler-hills',
                                            'ti tabler-hills-off',
                                            'ti tabler-volcano',
                                            'ti tabler-volcano-off',
                                            'ti tabler-beach',
                                            'ti tabler-beach-off',
                                            'ti tabler-cactus',
                                            'ti tabler-cactus-off',
                                            'ti tabler-campfire',
                                            'ti tabler-campfire-off',
                                            'ti tabler-fence',
                                            'ti tabler-fence-off',
                                            'ti tabler-feather',
                                            'ti tabler-feather-off',
                                            'ti tabler-paw',
                                        ],
                                    ];
                                @endphp

                                @foreach ($tablerCategories as $category => $icons)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="tabler-heading-{{ Str::slug($category) }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#tabler-collapse-{{ Str::slug($category) }}"
                                                aria-expanded="false"
                                                aria-controls="tabler-collapse-{{ Str::slug($category) }}">
                                                {{ $category }}
                                            </button>
                                        </h2>
                                        <div id="tabler-collapse-{{ Str::slug($category) }}"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="tabler-heading-{{ Str::slug($category) }}"
                                            data-bs-parent="#tablerAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach ($icons as $icon)
                                                        <div class="col-md-2 col-sm-3 col-4 mb-2 icon-item">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary w-100 icon-option"
                                                                data-icon="{{ $icon }}"
                                                                title="{{ $icon }}">
                                                                <i class="{{ $icon }}"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function() {
            // Live preview updates
            $('#menu_title').on('input', updatePreview);
            $('#menu_icon').on('input', updatePreview);
            $('#menu_route').on('input', updatePreview);
            $('#module_name').on('input', updatePreview);
            $('#if_can').on('input', updatePreview);

            // Initial preview update
            updatePreview();

            // Icon picker
            $('.icon-option').click(function() {
                const icon = $(this).data('icon');
                $('#menu_icon').val(icon);
                updatePreview();
                $('#iconPickerModal').modal('hide');
            });

            // Auto-update is_main_menu based on parent selection
            $('#parent_id').change(function() {
                const hasParent = $(this).val() !== '';
                $('#is_main_menu').prop('checked', !hasParent);
            });

            // Form validation and submission
            $('.save').click(function(e) {
                e.preventDefault();
                var formData = new FormData($("#menuForm")[0]);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('menumasters.store') }}",
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $(".invalid-feedback").html('');
                        $("#save").html(
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait`
                        );
                        $("#save").attr('disabled', true);
                    },
                    success: function(response) {
                        $("#save").html(
                            '<i class="fas fa-save"></i> {{ __('menumaster::message.create_menu_item') }}'
                        );
                        $("#save").attr('disabled', false);

                        if (response.status_code == 500) {
                            toastr.error("Something went wrong. Please try again.", "Error");
                        } else if (response.status_code == 403) {
                            toastr.warning("Please input proper data.", "Warning");
                        } else {
                            $('#menuForm')[0].reset();
                            setTimeout(function() {
                                location.href = response.data;
                            }, 500);
                            toastr.success("Menu item created successfully.", "Success");
                        }
                    },
                    error: function(xhr) {
                        $("#save").html(
                            '<i class="fas fa-save"></i> {{ __('menumaster::message.create_menu_item') }}'
                        );
                        $("#save").attr('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#error_' + key).html(value[0]);
                            });
                        }
                        toastr.error("Please check the form for errors.", "Error");
                    }
                });
            });
        });

        function updatePreview() {
            const title = $('#menu_title').val() || '{{ __('menumaster::message.menu_title') }}';
            const icon = $('#menu_icon').val() || 'fas fa-circle';
            const route = $('#menu_route').val();
            const module = $('#module_name').val();
            const permission = $('#if_can').val();

            $('#previewTitle').text(title);
            $('#previewIcon').attr('class', icon + ' me-2');

            if (route) {
                $('#previewRoute').text('(' + route + ')').show();
            } else {
                $('#previewRoute').hide();
            }

            if (module) {
                $('#previewModule').text(module).show();
            } else {
                $('#previewModule').hide();
            }

            if (permission) {
                $('#previewPermission').attr('title', 'Permission: ' + permission).show();
            } else {
                $('#previewPermission').hide();
            }
        }

        function showIconPicker() {
            $('#iconPickerModal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const iconSearch = document.getElementById('iconSearch');
            if (iconSearch) {
                iconSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const activeTabPane = document.querySelector('.tab-pane.active');

                    if (activeTabPane) {
                        const iconItems = activeTabPane.querySelectorAll('.icon-item');

                        iconItems.forEach(item => {
                            const icon = item.querySelector('.icon-option');
                            const iconName = icon.getAttribute('title').toLowerCase();

                            if (iconName.includes(searchTerm)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        // Open all accordion sections when searching
                        if (searchTerm.length > 0) {
                            const accordions = activeTabPane.querySelectorAll('.accordion-collapse');
                            accordions.forEach(accordion => {
                                const bsCollapse = new bootstrap.Collapse(accordion, {
                                    toggle: true
                                });
                                bsCollapse.show();
                            });
                        }
                    }
                });
            }

            // Icon selection
            document.querySelectorAll('.icon-option').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.getAttribute('data-icon');
                    // Dispatch event or set value in your form
                    // Example: document.getElementById('iconInput').value = icon;

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'iconPickerModal'));
                    modal.hide();
                });
            });
        });
    </script>
@endsection

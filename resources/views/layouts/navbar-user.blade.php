<ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- User -->
    <li class="nav-item dropdown dropdown-user mx-1">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary"
                id="selected_year">{{ session()->get('year') }}</button>
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-start" style="overflow-y: scroll;height: 200px !important;">
                {!! getYear() !!}
            </div>
        </div>
    </li>
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="d-flex">
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user" class="rounded-circle" />
                </div>
                <div class="ms-2">
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="text-muted">{{ Auth::user()->roles[0]->name }}</small>
                </div>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <div class="d-grid px-2 pt-2 pb-1">
                    <a class="btn btn-sm btn-light d-flex mb-2 @if (Request::segment(1) == 'change-layout') : active @endif"
                        data-bs-toggle="modal" data-bs-target="#changlayoutModal" href="javascript:void(0);">
                        <i class="me-50" data-feather="lock"></i> {{ __('user::message.changelayout') }}
                    </a>
                    @can('menu-list')
                        <a class="btn btn-sm btn-light d-flex mb-2 " href="{{ route('menumasters.index') }}">
                            <i class="me-50" data-feather="lock"></i> {{ __('menumaster::message.add') }}
                        </a>
                    @endcan
                    @can('password-change')
                        <a class="btn btn-sm btn-light d-flex mb-2 {{ request()->routeIs('change-password.*') ? 'active open' : '' }}"
                            data-bs-toggle="modal" data-bs-target="#changeModal" href="javascript:void(0);">
                            <i class="me-50" data-feather="lock"></i> {{ __('user::message.change_password') }}
                        </a>
                    @endcan



                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="btn btn-sm btn-danger d-flex" href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <small class="align-middle">{{ __('user::message.Logout') }}</small>
                            <i class="fa fa-sign-out ms-2"></i>
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </li>
    <!--/ User -->
</ul>

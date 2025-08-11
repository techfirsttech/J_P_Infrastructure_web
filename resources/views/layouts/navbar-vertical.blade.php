<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="fa fa-bars"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{-- <ul class="navbar-nav flex-row align-items-left">
            <!-- User -->
            <li class="nav-item">
                <h5 class="content-header-title float-start mb-0">@yield('title')</h5>
            </li>
        </ul> --}}
        @include('layouts.navbar-user')
    </div>
</nav>

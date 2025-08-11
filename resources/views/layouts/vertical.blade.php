<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="menu-fav">
                <img src="{{ asset('setting/favicon/' . setting()->favicon) }}" width="40" height="auto"
                    class="d-none" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold {{ auth()->user()->theme }}">
                @if (auth()->user()->theme == 'light')
                    <img src="{{ setting()->logo != '' ? asset('setting/logo/' . setting()->logo) : asset('assets/img/sample.png') }}"
                        class="rounded" width="150" height="auto" />
                @else
                    <img src="{{ setting()->logo_dark != '' ? asset('setting/logo_dark/' . setting()->logo_dark) : asset('assets/img/sample-white.png') }}"
                        class="rounded" width="150" height="auto" />
                @endif
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="fas fa-circle-dot d-none d-xl-block align-middle"></i>
            <i class="fas fa-circle-dot d-block d-xl-none align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    @include('layouts.menu')
</aside>

 <!-- Navbar -->
 <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
     <div class="container-xxl">
         <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
             <a href="{{ route('dashboard') }}" class="app-brand-link">
                 <span class="app-brand-logo demo">

                 </span>
                 <span class="app-brand-text demo menu-text fw-bold" >
                     @if (auth()->user()->theme == 'light')
                         <img src="{{ (setting()->logo != '') ? asset('setting/logo/' . setting()->logo) : asset('assets/img/sample.png') }}" class="rounded" width="130"
                             height="auto" />
                     @else
                         <img src="{{ (setting()->logo_dark != '') ? asset('setting/logo_dark/' . setting()->logo_dark) : asset('assets/img/sample-white.png') }}" class="rounded" width="130"
                             height="auto" />
                     @endif
                 </span>
             </a>
             <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                 <i class="fa fa-bars"></i>
             </a>
         </div>

         <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
             <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                 <i class="fa fa-bars"></i>
             </a>
         </div>

         <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

             @include('layouts.navbar-user')

         </div>
     </div>
 </nav>
 <!-- / Navbar -->

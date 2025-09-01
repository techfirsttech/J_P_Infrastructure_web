@php
    use Modules\MenuMaster\Models\MenuMaster;
    use Nwidart\Modules\Facades\Module;

    $menus = MenuMaster::parentMenus()
        ->with('children.children')
        ->orderBy('order_display', 'ASC')
        ->orderBy('menu_title', 'ASC')
        ->orderBy('id', 'ASC')
        ->get();

    // Helper functions using closures to avoid redeclaration
    $isMenuActive = function ($menu) use (&$isMenuActive) {
        // Check if current menu is active

        $route = str_replace('.index', '', $menu->menu_route);

        /*if (request()->routeIs($menu->menu_route )) {
return true;
}*/
        $returnStatus = false;
        if (request()->routeIs($route . '.*')) {
            $returnStatus = true;
        }
        if (request()->routeIs($route . '-*')) {
            $returnStatus = true;
        }
        if (request()->routeIs($route)) {
            $returnStatus = true;
        }
      //  echo '<br />...' . $route . '....' . (int)$returnStatus;
        if ($returnStatus) {
            return $returnStatus;
        }
        // Check if any child is active
        if ($menu->children && $menu->children->count() > 0) {
            foreach ($menu->children as $child) {
                if ($isMenuActive($child)) {
                    return true;
                }
            }
        }

        return false;
    };

    $isMenuEnabled = function ($menu, $isChild = false) {
        if (empty($menu->module_name)) {
            return true; // Show if no module_name specified
        }
        $moduleExists = $menu->module_name ? Module::collections()->has(strtolower($menu->module_name)) : false;

        if (!$moduleExists) {
            return $isChild ? false : true;
        } else {
            return Module::isEnabled(strtolower($menu->module_name));
        }
    };

    // Check if user has permission for any child menu
    $hasPermissionForAnyChild = function ($menu) use (&$hasPermissionForAnyChild, $isMenuEnabled) {
        if (!$menu->children || $menu->children->count() == 0) {
            return false;
        }

        foreach ($menu->children as $child) {
            // Check if child is enabled
            $childEnabled =
                $child->children->count() > 0 ? $isMenuEnabled($child, false) : $isMenuEnabled($child, true);

            if ($childEnabled) {
                // Check if user has permission for this child
                if (empty($child->if_can) || auth()->user()->canany($child->if_can)) {
                    return true;
                }

                // Check if user has permission for any grandchild
                if ($hasPermissionForAnyChild($child)) {
                    return true;
                }
            }
        }

        return false;
    };

    // Check if user has permission for any grandchild
    $hasPermissionForAnyGrandchild = function ($child) use ($isMenuEnabled) {
        if (!$child->children || $child->children->count() == 0) {
            return false;
        }

        foreach ($child->children as $grandchild) {
            $grandchildEnabled = $isMenuEnabled($grandchild, true);

            if ($grandchildEnabled && (empty($grandchild->if_can) || auth()->user()->canany($grandchild->if_can))) {
                return true;
            }
        }

        return false;
    };
@endphp

<ul class="menu-inner pb-2 pb-xl-0">    
   {{-- <li class="menu-item {{ request()->routeIs('dashboard') ? 'active open' : '' }}">
        <a href="/" class="menu-link">
            <i class="menu-icon icon-base ti tabler-smart-home"></i>
            <div>{{ __('message.dashboard') }} AA</div>
        </a>
    </li>--}}

    @foreach ($menus as $menu)
        @php
            if ($menu->module_name != null) {
                $moduleName = Module::getModulePath($menu->module_name);

                if (!file_exists($moduleName)) {
                    continue;
                }
            }

            $enabled = $isMenuEnabled($menu, false);
            $isActive = $isMenuActive($menu);

            $menuUrlActive = false;

            // echo "came here...'$menu->menu_route'....".is_null($menu->menu_route);

            if ($menu->menu_route != 'javascript:void(0)' && !is_null($menu->menu_route) && $menu->menu_route != '') {
                if (Route::has($menu->menu_route)) {
                    $menuUrlActive = true;
                }
            } else {
                $menuUrlActive = true;
            }

            // Check if user has permission for this menu OR any of its children
            $hasOwnPermission = empty($menu->if_can) || auth()->user()->canany($menu->if_can);
            $hasChildPermission = $hasPermissionForAnyChild($menu);

            // Show menu ONLY if user has permission (either own permission OR child permission)
            // Remove the condition for is_main_menu - don't show parent if no child permission
            $shouldShowMenu = $enabled && ($hasOwnPermission || $hasChildPermission);

        @endphp

        @if ($shouldShowMenu && $menuUrlActive)
            <li
                class="menu-item {{ $menu->children->count() > 0 ? 'has-sub' : '' }} {{ $isActive ? 'active open' : '' }}">

                <a href="{{ $menu->children->count() > 0 || $menu->menu_route === 'javascript:void(0)' ? 'javascript:void(0)' : route($menu->menu_route) }}"
                    class="menu-link {{ $menu->children->count() > 0 ? 'menu-toggle' : '' }}">
                    <i class="menu-icon {{ $menu->menu_icon }}"></i>
                    <div>{{ __($menu->menu_title) }}</div>
                </a>

                @if ($menu->children->count() > 0)
                    <ul class="menu-sub">
                        @foreach ($menu->children as $child)
                            @php

                                if ($child->module_name != null) {
                                    $childmoduleName = Module::getModulePath($child->module_name);

                                    if (!file_exists($childmoduleName)) {
                                        continue;
                                    }
                                }

                                $childEnabled =
                                    $child->children->count() > 0
                                        ? $isMenuEnabled($child, false)
                                        : $isMenuEnabled($child, true);
                                $childIsActive = $isMenuActive($child);

                                // Check if user has permission for this child OR any of its grandchildren
                                $hasChildOwnPermission = empty($child->if_can) || auth()->user()->canany($child->if_can);
                                $hasGrandchildPermission = $hasPermissionForAnyGrandchild($child);

                                // Show child if: enabled AND (has own permission OR has grandchild permission)
                                $shouldShowChild =
                                    $childEnabled && ($hasChildOwnPermission || $hasGrandchildPermission);

                                $menuChildUrlActive = false;
                                if (
                                    $child->menu_route !== 'javascript:void(0)' &&
                                    $child->menu_route !== null &&
                                    $child->menu_route !== ''
                                ) {
                                    if (Route::has($child->menu_route)) {
                                        $menuChildUrlActive = true;
                                    }
                                } else {
                                    $menuChildUrlActive = true;
                                }

                            @endphp

                            @if ($shouldShowChild && $menuChildUrlActive)
                                <li
                                    class="menu-item {{ $child->children->count() > 0 ? 'has-sub' : '' }} {{ $childIsActive ? 'active open' : '' }}">
                                    <a href="{{ $child->children->count() > 0 || $child->menu_route === 'javascript:void(0)' ? 'javascript:void(0)' : route($child->menu_route) }}"
                                        class="menu-link {{ $child->children->count() > 0 ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon {{ $child->menu_icon }}"></i>
                                        <div>{{ __($child->menu_title) }}</div>
                                    </a>

                                    @if ($child->children->count() > 0)
                                        <ul class="menu-sub">
                                            @foreach ($child->children as $grandchild)
                                                @php
                                                    if ($grandchild->module_name != null) {
                                                        $grandchildmoduleName = Module::getModulePath(
                                                            $grandchild->module_name,
                                                        );

                                                        if (!file_exists($grandchildmoduleName)) {
                                                            continue;
                                                        }
                                                    }
                                                    $grandchildEnabled = $isMenuEnabled($grandchild, true);
                                                    $grandchildIsActive =
                                                        $grandchild->menu_route !== 'javascript:void(0)' &&
                                                        request()->routeIs($grandchild->menu_route . '*');

                                                    $menuGrandChildUrlActive = false;

                                                    if (
                                                        $grandchild->menu_route !== 'javascript:void(0)' &&
                                                        $grandchild->menu_route !== null &&
                                                        $grandchild->menu_route !== ''
                                                    ) {
                                                        if (Route::has($grandchild->menu_route)) {
                                                            $menuGrandChildUrlActive = true;
                                                        }
                                                    } else {
                                                        $menuGrandChildUrlActive = true;
                                                    }
                                                @endphp

                                                @if ($grandchildEnabled && $menuGrandChildUrlActive)
                                                    && (empty($grandchild->if_can) ||
                                                    auth()->user()->canany($grandchild->if_can)))
                                                    <li class="menu-item {{ $grandchildIsActive ? 'active' : '' }}">
                                                        <a href="{{ route($grandchild->menu_route) }}"
                                                            class="menu-link">
                                                            <i class="menu-icon {{ $grandchild->menu_icon }}"></i>
                                                            <div>{{ __($grandchild->menu_title) }}</div>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
</ul>

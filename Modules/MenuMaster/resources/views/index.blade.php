@extends('layouts.app')
@section('title', __('menumaster::message.menumaster'))
@section('content')

<div class="row">
    <div class="col-12 mb-2">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="content-header-title mb-0">{{ __('menumaster::message.menumaster') }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group float-end" role="group">
                <a href="{{ route('menumasters.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> {{ __('menumaster::message.menuManagementAddItem') }}
                </a>
                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <i class="fas fa-tools"></i> {{ __('menumaster::message.menutools') }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="normalizeOrders()">
                            <i class="fas fa-sort-numeric-down"></i>
                            {{ __('menumaster::message.menuToolsNormalizeOrders') }}
                        </a></li>
                    <li><a class="dropdown-item" href="#" onclick="rebuildHierarchy()">
                            <i class="fas fa-sitemap"></i>
                            {{ __('menumaster::message.menuToolsRebuildHierarchy') }}
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="{{ route('menumasters.export') }}">
                            <i class="fas fa-download"></i>
                            {{ __('menumaster::message.menuToolsExportStructure') }}
                        </a></li>
                    <li><a class="dropdown-item" href="#" onclick="showStatistics()">
                            <i class="fas fa-chart-bar"></i>
                            {{ __('menumaster::message.menuToolsShowStatistics') }}
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <div class="col-12">
        <div class="card p-1">
            <div class="card-body">
                <!-- Module Filter -->
                <div class="row mb-3">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                        <label for="moduleFilter" class="form-label">{{ __('menumaster::message.menuFilterByModule') }}</label>
                        <select id="moduleFilter" class="form-select" onchange="filterByModule()">
                            <option value="">{{ __('menumaster::message.menuAllModules') }}</option>
                            @foreach ($modules as $module => $menuTitle)
                            @php
                            $moduleName = $module ?? '';
                            $menuTitle = $menuTitle ?? 'general';
                            @endphp
                            <option value="{{ $module }}" {{ $moduleName == $module ? 'selected' : '' }}>
                                {{ ucfirst(__($menuTitle)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Menu Structure -->
                <div class="row">
                    <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">{{ __('menumaster::message.treestructure') }}</h5>
                            <div id="menu-tree" style="overflow-y: auto;">
                                @include('menumaster::tree', ['items' => $menuTree])
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-7">
                        <div class="table-responsive">
                            <h5 class="mb-3">{{ __('menumaster::message.menuFlattenedList') }}</h5>
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="15%">{{ __('menumaster::message.menuSequence') }}</th>
                                        <th>{{ __('menumaster::message.menutitle') }}</th>
                                        <th width="15%">{{ __('menumaster::message.menumodulename') }}</th>
                                        <th width="15%">{{ __('menumaster::message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($menuItems as $item)
                                    <tr>
                                        <td>
                                            <small class="text-muted">{{ $item->getHumanReadableOrder() }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {!! str_repeat('&nbsp;&nbsp;&nbsp;', $item->getLevel() - 1) !!}
                                                @if ($item->menu_icon)
                                                <i class="{{ $item->menu_icon }} me-2"></i>
                                                @endif
                                                <span>{{ __($item->menu_title) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($item->module_name)
                                            <span class="badge bg-secondary">{{ $item->module_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('menumasters.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info"
                                                    title="{{ __('menumaster::message.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('menumasters.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="{{ __('menumaster::message.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-success duplicate-item"
                                                    data-id="{{ $item->id }}"
                                                    title="{{ __('menumaster::message.duplicate') }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-item"
                                                    data-id="{{ $item->id }}"
                                                    title="{{ __('menumaster::message.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            {{ __('menumaster::message.noMenuItemsFoundModule') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ __('menumaster::message.menuStatistics') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statisticsContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('menumaster::message.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
<style>
    /* Enhanced drag and drop styles */
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f8f9fa !important;
        border: 2px dashed #007bff !important;
    }

    .sortable-chosen {
        background-color: #e3f2fd !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: rotate(5deg);
    }

    .menu-item {
        transition: all 0.2s ease;
        cursor: default;
    }

    .menu-item:hover {
        background-color: #f8f9fa !important;
    }

    .drag-handle {
        cursor: grab;
        padding: 5px;
        border-radius: 3px;
        transition: background-color 0.2s ease;
    }

    .drag-handle:hover {
        background-color: #e9ecef;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    /* Improve visual hierarchy */
    .menu-item .menu-item {
        margin-left: 10px;
        border-left: 2px solid #e9ecef;
        padding-left: 10px;
    }

    /* Drop zone indication */
    ul.list-unstyled {
        min-height: 20px;
        transition: background-color 0.2s ease;
    }

    ul.list-unstyled:empty::before {
        content: "Drop items here";
        color: #6c757d;
        font-style: italic;
        font-size: 0.875rem;
        display: block;
        text-align: center;
        padding: 10px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    /* Show drop zone when dragging */
    .sortable-ghost~ul.list-unstyled:empty::before,
    body.sortable-dragging ul.list-unstyled:empty::before {
        opacity: 1;
    }

    /* Prevent text selection during drag */
    .sortable-chosen * {
        user-select: none;
    }

    /* Smooth animations */
    .menu-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .menu-item.sortable-chosen {
        z-index: 1000;
        position: relative;
    }
</style>
@endsection

@section('pagescript')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize sortable for all menu levels
        initializeSortable();

        // Delete item
        $(document).on('click', '.delete-item', function() {
            const itemId = $(this).data('id');
            const confirmMessage = '{{ __("menumaster::message.confirmDeleteMenuItem") }}';

            if (confirm(confirmMessage)) {
                deleteMenuItem(itemId);
            }
        });

        // Duplicate item
        $(document).on('click', '.duplicate-item', function() {
            const itemId = $(this).data('id');
            duplicateMenuItem(itemId);
        });
    });

    function initializeSortable() {
        // Find all ul elements that contain menu items
        const sortableContainers = document.querySelectorAll('#menu-tree ul.list-unstyled');

        sortableContainers.forEach(function(container) {
            if (container.children.length > 0) {
                Sortable.create(container, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    group: 'menu-items', // Allow items to be moved between different levels
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: function(evt) {
                        const itemId = evt.item.dataset.id;
                        const newIndex = evt.newIndex;

                        // Find the parent menu item (not the immediate ul, but the li that contains it)
                        let parentElement = evt.to.closest('.menu-item');

                        // If the target ul is directly under menu-tree, it's a root level
                        if (evt.to.closest('#menu-tree') && !parentElement) {
                            parentElement = null;
                        }

                        const parentId = parentElement ? parentElement.dataset.id : null;

                        console.log('Moving item:', itemId, 'to parent:', parentId, 'at position:',
                            newIndex);
                        moveMenuItem(itemId, parentId, newIndex);
                    }
                });
            }
        });
    }

    function filterByModule() {
        const module = document.getElementById('moduleFilter').value;
        const url = new URL(window.location);

        if (module) {
            url.searchParams.set('module_name', module);
        } else {
            url.searchParams.delete('module_name');
        }

        window.location.href = url.toString();
    }

    function moveMenuItem(itemId, parentId, position) {
        $.post(`menumasters/${itemId}/move`, {
                parent_id: parentId,
                position: position,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message || '{{ __("menumaster::message.errorMovingMenuItem") }}');
                    location.reload();
                }
            })
            .fail(function(xhr) {
                console.error('Move failed:', xhr.responseText);
                toastr.error('{{ __("menumaster::message.errorMovingMenuItem") }}');
                location.reload();
            });
    }

    function deleteMenuItem(itemId) {
        $.ajax({
                url: `menumasters/${itemId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .fail(function() {
                toastr.error('{{ __("menumaster::message.errorDeletingMenuItem") }}');
            });
    }

    function duplicateMenuItem(itemId) {
        $.post(`menumasters/${itemId}/duplicate`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .fail(function() {
                toastr.error('{{ __("menumaster::message.error_duplicating") }}');
            });
    }

    function normalizeOrders() {
        const confirmMessage = '{{ __("menumaster::message.confirmNormalizeOrders") }}';

        if (confirm(confirmMessage)) {
            $.post('menumasters/normalize-orders', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .fail(function() {
                    toastr.error('{{ __("menumaster::message.errorNormalizingOrders") }}');
                });
        }
    }

    function rebuildHierarchy() {
        const confirmMessage = '{{ __("menumaster::message.confirmRebuildHierarchy") }}';

        if (confirm(confirmMessage)) {
            $.post('menumasters/rebuild-hierarchy', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .fail(function() {
                    toastr.error('{{ __("menumaster::message.errorRebuildingHierarchy") }}');
                });
        }
    }

    function showStatistics() {
        $.get(`menumasters/statistics`)
            .done(function(response) {
                if (response.success) {
                    const stats = response.data;
                    const content = `
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.totalMenus") }}:</strong></div>
                            <div class="col-6">${stats.total_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.mainMenus") }}:</strong></div>
                            <div class="col-6">${stats.main_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.subMenus") }}:</strong></div>
                            <div class="col-6">${stats.sub_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.withRoutes") }}:</strong></div>
                            <div class="col-6">${stats.menus_with_routes}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.withPermissions") }}:</strong></div>
                            <div class="col-6">${stats.menus_with_permissions}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __("menumaster::message.modules") }}:</strong></div>
                            <div class="col-6">${stats.modules}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>{{ __("menumaster::message.maxDepth") }}:</strong></div>
                            <div class="col-6">${stats.max_depth}</div>
                        </div>
                    `;
                    document.getElementById('statisticsContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('statisticsModal')).show();
                }
            })
            .fail(function() {
                toastr.error('{{ __("menumaster::message.errorLoadingStatistics") }}');
            });
    }
</script>
@endsection
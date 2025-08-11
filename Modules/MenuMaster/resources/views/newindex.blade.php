@extends('layouts.app')
@section('title', __('menumaster::message.list'))
@section('content')

<div class="container-fluid ruchit">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="content-header-title float-start mb-0">{{ __('menumaster::message.list') }}</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                @can('menumaster-create')
                                <a href="{{ route('menumasters.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus me-1"></i> {{ __('message.common.addNew') }}
                                </a>
                                @endcan
                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-tools me-1"></i> {{ __('message.common.tools') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="normalizeOrders()">
                                        <i class="fas fa-sort-numeric-down me-1"></i> Normalize Orders
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="rebuildHierarchy()">
                                        <i class="fas fa-sitemap me-1"></i> Rebuild Hierarchy
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('menumasters.export') }}">
                                        <i class="fas fa-download me-1"></i> Export Structure
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="showStatistics()">
                                        <i class="fas fa-chart-bar me-1"></i> Show Statistics
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Module Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="moduleFilter" class="form-label">{{ __('menumaster::message.filter_by_module') }}</label>
                            <select id="moduleFilter" class="form-select" onchange="filterByModule()">
                                <option value="">{{ __('message.common.all') }}</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}" {{ $moduleName == $module ? 'selected' : '' }}>
                                        {{ ucfirst($module) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Menu Structure -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Menu Tree Structure</h5>
                                </div>
                                <div class="card-body">
                                    <div id="menu-tree" class="border rounded p-3" style="max-height: 600px; overflow-y: auto;">
                                        @include('menumaster::partials.tree', ['items' => $menuTree])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Flattened Menu List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th width="80">{{ __('menumaster::message.order') }}</th>
                                                    <th>{{ __('menumaster::message.title') }}</th>
                                                    <th width="100">{{ __('menumaster::message.module') }}</th>
                                                    <th width="120">{{ __('message.common.action') }}</th>
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
                                                                <span>{{ $item->menu_title }}</span>
                                                                @if ($item->menu_route)
                                                                    <small class="text-muted ms-2">({{ $item->menu_route }})</small>
                                                                @endif
                                                                @if ($item->if_can)
                                                                    <span class="badge bg-info ms-2" title="Permission: {{ $item->if_can }}">
                                                                        <i class="fas fa-lock"></i>
                                                                    </span>
                                                                @endif
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
                                                                   data-bs-toggle="tooltip" 
                                                                   title="{{ __('message.common.view') }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('menumasters.edit', $item) }}" 
                                                                   class="btn btn-sm btn-outline-primary" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="{{ __('message.common.edit') }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-outline-success duplicate-item" 
                                                                        data-id="{{ $item->id }}" 
                                                                        data-bs-toggle="tooltip" 
                                                                        title="{{ __('message.common.duplicate') }}">
                                                                    <i class="fas fa-copy"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger delete-item" 
                                                                        data-id="{{ $item->id }}" 
                                                                        data-bs-toggle="tooltip" 
                                                                        title="{{ __('message.common.delete') }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            {{ __('menumaster::message.no_menu_items') }}
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
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ __('menumaster::message.menu_statistics') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statisticsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('message.common.close') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('pagecss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #e9ecef;
    }

    .sortable-chosen {
        background-color: #e3f2fd;
    }

    .menu-item:hover {
        background-color: #f8f9fa !important;
    }

    .drag-handle {
        cursor: move;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .drag-handle:hover {
        opacity: 1;
    }
</style>
@endpush

@section('pagescript')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize sortable
        if (document.getElementById('menu-tree')) {
            new Sortable(document.getElementById('menu-tree'), {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const itemId = evt.item.querySelector('.menu-item').dataset.id;
                    const newIndex = evt.newIndex;
                    const parentElement = evt.to.closest('.menu-item');
                    const parentId = parentElement ? parentElement.dataset.id : null;

                    moveMenuItem(itemId, parentId, newIndex);
                }
            });
        }

        // Delete item
        $(document).on('click', '.delete-item', function() {
            const itemId = $(this).data('id');
            const itemName = $(this).closest('tr').find('td:nth-child(2)').text().trim();

            if (confirm(`Are you sure you want to delete "${itemName}"? All child items will also be deleted.`)) {
                deleteMenuItem(itemId);
            }
        });

        // Duplicate item
        $(document).on('click', '.duplicate-item', function() {
            const itemId = $(this).data('id');
            duplicateMenuItem(itemId);
        });
    });

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
        $.ajax({
            url: `{{ route('menumasters.move', '') }}/${itemId}`,
            method: 'POST',
            data: {
                parent_id: parentId,
                position: position,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                toastr.error('Error moving menu item');
                location.reload();
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    function deleteMenuItem(itemId) {
        $.ajax({
            url: `{{ route('menumasters.destroy', '') }}/${itemId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                toastr.error('Error deleting menu item');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    function duplicateMenuItem(itemId) {
        $.ajax({
            url: `{{ route('menumasters.duplicate', '') }}/${itemId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                toastr.error('Error duplicating menu item');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    function normalizeOrders() {
        if (confirm('This will normalize all menu order displays to zero-padded format. Continue?')) {
            $.ajax({
                url: '{{ route('menumasters.normalize-orders') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function() {
                    toastr.error('Error normalizing menu orders');
                },
                complete: function() {
                    hideLoader();
                }
            });
        }
    }

    function rebuildHierarchy() {
        if (confirm('This will rebuild the entire menu hierarchy. This should only be done if the hierarchy is corrupted. Continue?')) {
            $.ajax({
                url: '{{ route('menumasters.rebuild-hierarchy') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function() {
                    toastr.error('Error rebuilding menu hierarchy');
                },
                complete: function() {
                    hideLoader();
                }
            });
        }
    }

    function showStatistics() {
        $.ajax({
            url: '{{ route('menumasters.statistics') }}',
            method: 'GET',
            beforeSend: function() {
                $('#statisticsContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    const content = `
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.total_menus') }}:</strong></div>
                            <div class="col-6">${stats.total_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.main_menus') }}:</strong></div>
                            <div class="col-6">${stats.main_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.sub_menus') }}:</strong></div>
                            <div class="col-6">${stats.sub_menus}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.with_routes') }}:</strong></div>
                            <div class="col-6">${stats.menus_with_routes}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.with_permissions') }}:</strong></div>
                            <div class="col-6">${stats.menus_with_permissions}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('menumaster::message.modules') }}:</strong></div>
                            <div class="col-6">${stats.modules}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>{{ __('menumaster::message.max_depth') }}:</strong></div>
                            <div class="col-6">${stats.max_depth}</div>
                        </div>
                    `;
                    $('#statisticsContent').html(content);
                }
            },
            error: function() {
                $('#statisticsContent').html(`
                    <div class="alert alert-danger">
                        {{ __('message.common.error_loading_data') }}
                    </div>
                `);
            }
        });
        
        const modal = new bootstrap.Modal(document.getElementById('statisticsModal'));
        modal.show();
    }

    function showLoader() {
        $('body').append(`
            <div class="loader-overlay">
                <div class="loader-spinner"></div>
            </div>
        `);
    }

    function hideLoader() {
        $('.loader-overlay').remove();
    }
</script>
@endsection
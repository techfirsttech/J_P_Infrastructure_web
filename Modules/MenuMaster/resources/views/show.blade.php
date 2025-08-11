@extends('layouts.app')
@section('title', __('menumaster::message.details'))
@section('content')

    <div class="row">
        <div class="col-12 mb-2">
            <h5 class="content-header-title float-start mb-0">
                @if ($menuMaster->menu_icon)
                    <i class="{{ $menuMaster->menu_icon }} me-2"></i>
                @endif
                {{ __($menuMaster->menu_title) }}
                @if ($menuMaster->module_name)
                    <span class="badge bg-secondary ms-2">{{ $menuMaster->module_name }}</span>
                @endif
            </h5>
            <div class="btn-group float-end" role="group">
                <a href="{{ route('menumasters.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('message.common.back') }}
                </a>
                <a href="{{ route('menumasters.edit', $menuMaster) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> {{ __('menumaster::message.edit') }}
                </a>
                <button type="button" class="btn btn-sm btn-success" onclick="duplicateMenu()">
                    <i class="fas fa-copy"></i> {{ __('menumaster::message.duplicate') }}
                </button>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('menumaster::message.basic_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="30%">{{ __('menumaster::message.id') }}:</th>
                                            <td>{{ $menuMaster->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.title') }}:</th>
                                            <td>{{ __($menuMaster->menu_title) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.icon') }}:</th>
                                            <td>
                                                @if ($menuMaster->menu_icon)
                                                    <i class="{{ $menuMaster->menu_icon }} me-2"></i>
                                                    <code>{{ $menuMaster->menu_icon }}</code>
                                                @else
                                                    <span class="text-muted">{{ __('menumaster::message.no_icon') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.route_url') }}</th>
                                            <td>
                                                @if ($menuMaster->menu_route)
                                                    <a href="{{ $menuMaster->getFullRouteAttribute() }}" target="_blank"
                                                        class="text-decoration-none">
                                                        {{ $menuMaster->menu_route }}
                                                        <i class="fas fa-external-link-alt ms-1"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __('menumaster::message.no_route') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.permission') }}:</th>
                                            <td>
                                                @if ($menuMaster->if_can)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-lock me-1"></i>{{ $menuMaster->if_can }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('menumaster::message.no_permission') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Hierarchy Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>{{ __('menumaster::message.hierarchy_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="30%">{{ __('menumaster::message.order') }}:</th>
                                            <td><strong>{{ $menuMaster->getHumanReadableOrder() }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.level') }}:</th>
                                            <td><span class="badge bg-primary">{{ __('menumaster::message.level') }} {{ $menuMaster->getLevel() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.parent_menu') }}:</th>
                                            <td>
                                                @if ($menuMaster->parent)
                                                    <a href="{{ route('menumasters.show', $menuMaster->parent) }}"
                                                        class="text-decoration-none">
                                                        @if ($menuMaster->parent->menu_icon)
                                                            <i class="{{ $menuMaster->parent->menu_icon }} me-1"></i>
                                                        @endif
                                                        {{ __($menuMaster->parent->menu_title) }} | {{ $menuMaster->parent->menu_title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __('menumaster::message.no_parent') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('menumaster::message.has_children') }}:</th>
                                            <td>
                                                @if ($menuMaster->hasChildren())
                                                    <span class="badge bg-info">{{ $menuMaster->children->count() }}
                                                        {{ __('menumaster::message.children') }}</span>
                                                @else
                                                    <span class="text-muted">{{ __('menumaster::message.no_children') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Children Menus -->
                    @if ($menuMaster->children && $menuMaster->children->count() > 0)
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-0">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-layer-group me-2"></i>
                                            {{ __('menumaster::message.child_menus') }} ({{ $menuMaster->children->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('menumaster::message.title') }}</th>
                                                        <th>{{ __('menumaster::message.route') }}</th>
                                                        <th>{{ __('menumaster::message.permission') }}</th>
                                                        <th>{{ __('menumaster::message.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($menuMaster->children->sortBy('order_display') as $child)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                @if ($child->menu_icon)
                                                                    <i class="{{ $child->menu_icon }} me-2"></i>
                                                                @endif
                                                                {{ __($child->menu_title) }}
                                                            </td>
                                                            <td>
                                                                @if ($child->menu_route)
                                                                    <code>{{ $child->menu_route }}</code>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($child->if_can)
                                                                    <span
                                                                        class="badge bg-warning text-dark">{{ $child->if_can }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('menumasters.show', $child) }}"
                                                                        class="btn btn-sm btn-outline-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('menumasters.edit', $child) }}"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Preview Section -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-primary  d-flex align-items-center">
                                    <i class="fas fa-eye me-2"></i>
                                    <h5 class="mb-0">{{ __('menumaster::message.navigation_preview') }}</h5>
                                </div>
                                
                                    <div class="bg-light border rounded p-3">
                                        <div class="d-flex align-items-center text-primary mb-2">
                                            @if ($menuMaster->menu_icon)
                                                <i class="{{ $menuMaster->menu_icon }} me-2 fs-5"></i>
                                            @endif
                                            <span class="fw-semibold fs-5">{{ __($menuMaster->menu_title) }}</span>
                                            @if ($menuMaster->hasChildren())
                                                <i class="fas fa-chevron-down ms-2 text-secondary"></i>
                                            @endif
                                        </div>

                                        @if ($menuMaster->children && $menuMaster->children->count() > 0)
                                            <div class="ms-4 mt-2">
                                                @foreach ($menuMaster->children->sortBy('order_display')->take(3) as $child)
                                                    <div class="d-flex align-items-center text-dark py-1">
                                                        @if ($child->menu_icon)
                                                            <i class="{{ $child->menu_icon }} me-2 text-secondary"></i>
                                                        @endif
                                                        <span>{{ __($child->menu_title) }}</span>
                                                    </div>
                                                @endforeach

                                                @if ($menuMaster->children->count() > 3)
                                                    <div class="py-1 text-muted fst-italic">
                                                        ... and {{ $menuMaster->children->count() - 3 }} more
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                
                            </div>
                        </div>
                    </div>


                    <!-- Audit Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('menumaster::message.audit_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>{{ __('menumaster::message.created_at') }}:</th>
                                                    <td>{{ $menuMaster->created_at ? $menuMaster->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('menumaster::message.created_by') }}:</th>
                                                    <td>
                                                        @if ($menuMaster->creator)
                                                            {{ $menuMaster->creator->name }}
                                                        @else
                                                            <span class="text-muted">{{ __('menumaster::message.unknown') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>{{ __('menumaster::message.updated_at') }}:</th>
                                                    <td>{{ $menuMaster->updated_at ? $menuMaster->updated_at->format('Y-m-d H:i:s') : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('menumaster::message.updated_by') }}:</th>
                                                    <td>
                                                        @if ($menuMaster->updater)
                                                            {{ $menuMaster->updater->name }}
                                                        @else
                                                            <span class="text-muted">{{ __('menumaster::message.unknown') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            @if ($menuMaster->deleted_at)
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>{{ __('menumaster::message.deleted_at') }}:</th>
                                                        <td>{{ $menuMaster->deleted_at->format('Y-m-d H:i:s') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('menumaster::message.deleted_by') }}:</th>
                                                        <td>
                                                            @if ($menuMaster->deleter)
                                                                {{ $menuMaster->deleter->name }}
                                                            @else
                                                                <span class="text-muted">{{ __('menumaster::message.unknown') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('menumasters.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('message.common.back') }}
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success" onclick="duplicateMenu()">
                                <i class="fas fa-copy"></i> {{ __('menumaster::message.duplicate') }}
                            </button>
                            <a href="{{ route('menumasters.edit', $menuMaster) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ __('menumaster::message.edit') }}
                            </a>
                            <button type="button" class="btn btn-danger" onclick="deleteMenu()">
                                <i class="fas fa-trash"></i> {{ __('menumaster::message.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('pagescript')
    <script>
        function duplicateMenu() {
            if (confirm('{{ __('menumaster::message.duplicate_confirmation') }}')) {
                $.post('{{ route('menumasters.duplicate', $menuMaster) }}', {
                        _token: '{{ csrf_token() }}'
                    })
                    .done(function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = '{{ route('menumasters.index') }}';
                        }
                    })
                    .fail(function() {
                        toastr.error('{{ __('menumaster::message.duplicate_error') }}');
                    });
            }
        }

        function deleteMenu() {
            const hasChildren = {{ $menuMaster->children->count() }};
            const confirmMessage = hasChildren > 0 ?
                '{{ __('menumaster::message.delete_confirmation_with_children', ['count' => $menuMaster->children->count()]) }}' :
                '{{ __('menumaster::message.delete_confirmation') }}';

            if (confirm(confirmMessage)) {
                $.ajax({
                        url: '{{ route('menumasters.destroy', $menuMaster) }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .done(function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = '{{ route('menumasters.index') }}';
                        }
                    })
                    .fail(function() {
                        toastr.error('Error deleting menu item');
                    });
            }
        }
    </script>
@endsection

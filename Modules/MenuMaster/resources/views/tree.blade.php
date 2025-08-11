@if ($items->count() > 0)
    <ul class="list-unstyled">
        @foreach ($items as $item)
            <li class="mb-2 list-group-item menu-item drag-handle" style="cursor: move;" title="Drag to reorder"
                data-id="{{ $item->id }}">
                <div class="d-flex align-items-center  p-2 border rounded bg-light">
                    <span class=" me-2">
                        <i class="fas fa-grip-vertical text-muted"></i>
                    </span>

                    @if ($item->menu_icon)
                        <i class="{{ $item->menu_icon }} me-2"></i>
                    @endif

                    <div class="flex-grow-1">
                        <strong>{{ $item->getHumanReadableOrder() }}</strong>
                        {{ __($item->menu_title) }}

                        @if ($item->menu_route)
                            {{-- <small class="text-muted">({{ $item->menu_route }})</small> --}}
                        @endif
                        @if ($item->module_name)
                            <span class="badge bg-secondary ms-2">{{ $item->module_name }}</span>
                        @endif
                        @if ($item->if_can)
                            <span class="badge bg-info ms-1" title="Permission: {{ $item->if_can }}">
                                <i class="fas fa-lock"></i>
                            </span>
                        @endif
                    </div>

                    <div class="btn-group btn-group-sm ms-2">
                        <a href="{{ route('menumasters.show', $item) }}" class="btn btn-outline-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('menumasters.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-outline-success duplicate-item" data-id="{{ $item->id }}"
                            title="Duplicate">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-item" data-id="{{ $item->id }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                @if ($item->children->count() > 0)
                    <div class="ms-4 mt-2">
                        @include('menumaster::tree', ['items' => $item->children])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted text-center">
        {{ __('menumaster::message.noMenuItemsFound') }}
    </p>
@endif

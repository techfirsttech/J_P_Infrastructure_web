<?php

namespace Modules\MenuMaster\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class MenuMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menu_masters';

    protected $fillable = [
        'menu_icon',
        'menu_title',
        'menu_route',
        'is_main_menu',
        'parent_id',
        'module_name',
        'order_display',
        'display_order',
        'if_can',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'is_main_menu' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method to automatically set audit fields
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }

    /**
     * Get the parent menu item
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuMaster::class, 'parent_id');
    }

    /**
     * Scope for getting parent menus
     */
    public function scopeParentMenus($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get child menu items
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuMaster::class, 'parent_id')
            ->orderBy('order_display');
    }

    /**
     * Get all descendants
     */
    public function descendants()
    {
        return $this->hasMany(MenuMaster::class, 'parent_id')
            ->with('descendants')
            ->orderBy('order_display');
    }

    /**
     * Get the user who created this menu
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this menu
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this menu
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope for getting items in hierarchical order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_display');
    }

    /**
     * Scope for getting root level items (main menus)
     */
    public function scopeMainMenu($query)
    {
        return $query->where('is_main_menu', true)
            ->orWhereNull('parent_id');
    }

    /**
     * Scope for getting items by parent
     */
    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }


    /**
     * Scope for getting items by module
     */
    public function scopeByModule($query, $moduleName)
    {
        return $query->where('module_name', $moduleName);
    }

    /**
     * Scope for items with permissions
     */
    public function scopeWithPermission($query, $permission = null)
    {
        if ($permission) {
            return $query->where('if_can', $permission);
        }
        return $query->whereNotNull('if_can');
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumb()
    {
        $breadcrumb = collect([$this]);
        $parent = $this->parent;

        while ($parent) {
            $breadcrumb->prepend($parent);
            $parent = $parent->parent;
        }

        return $breadcrumb;
    }

    /**
     * Check if this item has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the depth level of this item
     */
    public function getLevel(): int
    {
        if (empty($this->order_display) || $this->order_display === '0.00') {
            return 1;
        }
        return substr_count($this->order_display, '.') + 1;
    }

    /**
     * Convert display order to human readable format
     */
    public function getHumanReadableOrder(): string
    {
        if ($this->display_order) {
            return $this->display_order;
        }

        // Convert "001.002.003" to "1.2.3"
        if (empty($this->order_display) || $this->order_display === '0.00') {
            return '1';
        }

        $parts = explode('.', $this->order_display);
        $humanParts = array_map(fn($part) => (int)$part, $parts);
        return implode('.', $humanParts);
    }

    /**
     * Check if user can access this menu based on permission
     */
    public function canAccess($user = null): bool
    {
        if (!$this->if_can) {
            return true; // No permission required
        }

        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        // Check if user has the required permission
        // Assuming you're using Spatie Laravel Permission or similar
        return $user->can($this->if_can);
    }

    /**
     * Get full route URL
     */
    public function getFullRouteAttribute(): string
    {
        if (!$this->menu_route) {
            return '#';
        }

        // If route starts with http, return as is
        if (str_starts_with($this->menu_route, 'http')) {
            return $this->menu_route;
        }

        // If route doesn't start with /, add it
        if (!str_starts_with($this->menu_route, '/')) {
            return '/' . $this->menu_route;
        }

        return $this->menu_route;
    }

    /**
     * Check if this menu is active based on current route
     */
    public function isActive($currentRoute = null): bool
    {
        $currentRoute = $currentRoute ?: request()->path();

        if (!$this->menu_route) {
            return false;
        }

        // Remove leading slash for comparison
        $menuRoute = ltrim($this->menu_route, '/');
        $currentRoute = ltrim($currentRoute, '/');

        return $menuRoute === $currentRoute || str_starts_with($currentRoute, $menuRoute . '/');
    }

    /**
     * Get menu with children for navigation
     */
    public static function getNavigationMenu($moduleName = null, $user = null)
    {
        $query = static::with(['children' => function ($q) use ($user) {
            $q->ordered();
            // Filter by permissions if user is provided
            if ($user) {
                $q->where(function ($subQ) use ($user) {
                    $subQ->whereNull('if_can')
                        ->orWhere(function ($permQ) use ($user) {
                            $permQ->whereNotNull('if_can');
                            // Add your permission check logic here
                        });
                });
            }
        }])
            ->mainMenu()
            ->ordered();

        if ($moduleName) {
            $query->byModule($moduleName);
        }

        return $query->get();
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'name', 'module_name');
    }
}
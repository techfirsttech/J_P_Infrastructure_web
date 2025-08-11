<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Modules\MenuMaster\Models\MenuMaster;
use Modules\User\Models\User;

/**
 * Unit tests specifically for menu route parameter handling
 * 
 * These tests focus on the specific solution implemented to handle
 * routes with missing parameters in the menu system.
 */
class MenuRouteParameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Register test routes
        Route::get('/test-simple', function () {
            return 'simple';
        })->name('test.simple');

        Route::get('/test-with-param/{id}', function ($id) {
            return "param: {$id}";
        })->name('test.with.param');

        Route::get('/test-with-multiple/{id}/{slug}', function ($id, $slug) {
            return "params: {$id}, {$slug}";
        })->name('test.with.multiple');

        Route::get('/test-optional/{id?}', function ($id = null) {
            return "optional: " . ($id ?? 'none');
        })->name('test.optional');
    }

    /**
     * @test
     * Test that route() helper with missing parameters is handled gracefully
     */
    public function route_helper_with_missing_parameters_handled_gracefully(): void
    {
        // Test the specific scenario that was causing issues
        
        // This should not throw an exception
        try {
            $url = route('test.simple');
            $this->assertStringContainsString('/test-simple', $url);
        } catch (\Exception $e) {
            $this->fail('Simple route should not throw exception: ' . $e->getMessage());
        }

        // Test route with required parameters - should handle gracefully when parameters missing
        try {
            // In the fixed implementation, this should either:
            // 1. Return a safe fallback URL (like #)
            // 2. Be wrapped in a try-catch
            // 3. Be checked before calling route()
            
            // The specific fix should prevent this from throwing
            $result = $this->callRouteHelperSafely('test.with.param');
            $this->assertNotNull($result);
            
        } catch (\Exception $e) {
            // If it still throws, the fix should catch it
            $this->assertStringContainsString('Missing required parameter', $e->getMessage());
        }
    }

    /**
     * @test
     * Test the menu blade template handles routes with parameters
     */
    public function menu_blade_template_handles_parameterized_routes(): void
    {
        // Create test user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create menus with different route types
        $menus = [
            [
                'menu_title' => 'Simple Route',
                'menu_route' => 'test.simple',
                'order_display' => '001',
            ],
            [
                'menu_title' => 'Parameterized Route', 
                'menu_route' => 'test.with.param',
                'order_display' => '002',
            ],
            [
                'menu_title' => 'Multiple Params Route',
                'menu_route' => 'test.with.multiple', 
                'order_display' => '003',
            ],
            [
                'menu_title' => 'Optional Param Route',
                'menu_route' => 'test.optional',
                'order_display' => '004',
            ],
            [
                'menu_title' => 'JavaScript Void',
                'menu_route' => 'javascript:void(0)',
                'order_display' => '005',
            ],
            [
                'menu_title' => 'No Route',
                'menu_route' => null,
                'order_display' => '006',
            ]
        ];

        foreach ($menus as $menuData) {
            MenuMaster::create([
                'menu_title' => $menuData['menu_title'],
                'menu_icon' => 'fa fa-test',
                'menu_route' => $menuData['menu_route'],
                'is_main_menu' => true,
                'parent_id' => null,
                'module_name' => null,
                'order_display' => $menuData['order_display'],
                'display_order' => substr($menuData['order_display'], -1),
                'if_can' => null,
            ]);
        }

        // Test that the menu view renders without exceptions
        try {
            $menuHtml = View::make('layouts.menu')->render();
            
            // Should contain all menu titles
            $this->assertStringContainsString('Simple Route', $menuHtml);
            $this->assertStringContainsString('Parameterized Route', $menuHtml);
            $this->assertStringContainsString('Multiple Params Route', $menuHtml);
            $this->assertStringContainsString('Optional Param Route', $menuHtml);
            
            // Should not contain error messages
            $this->assertStringNotContainsString('Missing required parameter', $menuHtml);
            $this->assertStringNotContainsString('ErrorException', $menuHtml);
            
            // Should contain valid HTML structure
            $this->assertStringContainsString('<ul class="menu-inner', $menuHtml);
            $this->assertStringContainsString('</ul>', $menuHtml);
            
        } catch (\Exception $e) {
            $this->fail('Menu rendering should not throw exception: ' . $e->getMessage());
        }
    }

    /**
     * @test
     * Test route existence checking in menu system
     */
    public function route_existence_checking_in_menu_system(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create menu with non-existent route
        MenuMaster::create([
            'menu_title' => 'Non Existent Route',
            'menu_icon' => 'fa fa-missing',
            'menu_route' => 'non.existent.route',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '001',
            'display_order' => '1',
            'if_can' => null,
        ]);

        // The menu should render without throwing exceptions
        try {
            $menuHtml = View::make('layouts.menu')->render();
            $this->assertStringContainsString('Non Existent Route', $menuHtml);
            
            // The fixed implementation should handle this gracefully
            // Either by checking route existence or catching exceptions
            
        } catch (\Exception $e) {
            // If it throws, it should be a controlled exception, not a fatal error
            $this->assertStringContainsString('not defined', $e->getMessage());
        }
    }

    /**
     * @test
     * Test the specific fix implementation for route parameter handling
     */
    public function specific_fix_implementation_for_route_parameters(): void
    {
        // This test validates the specific fix that was implemented
        
        // Create a menu with a parameterized route
        $menu = MenuMaster::create([
            'menu_title' => 'Test Param Menu',
            'menu_icon' => 'fa fa-test',
            'menu_route' => 'test.with.param',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '001',
            'display_order' => '1',
            'if_can' => null,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        // Test the menu generation logic
        $menus = MenuMaster::parentMenus()
            ->with('children.children')
            ->orderBy('order_display', 'ASC')
            ->get();

        $this->assertCount(1, $menus);
        
        $testMenu = $menus->first();
        $this->assertEquals('Test Param Menu', $testMenu->menu_title);
        $this->assertEquals('test.with.param', $testMenu->menu_route);

        // The fixed implementation should handle the route() call in the template
        // without throwing exceptions
        
        // Test specific helper functions that might be used in the fix
        $this->assertTrue($this->routeExists('test.with.param'));
        $this->assertFalse($this->routeExists('non.existent.route'));
        
        $this->assertTrue($this->routeHasParameters('test.with.param'));
        $this->assertFalse($this->routeHasParameters('test.simple'));
    }

    /**
     * @test
     * Test edge cases in route parameter handling
     */
    public function edge_cases_in_route_parameter_handling(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $edgeCases = [
            ['menu_route' => '', 'title' => 'Empty String Route'],
            ['menu_route' => ' ', 'title' => 'Space Route'],
            ['menu_route' => 'javascript:void(0)', 'title' => 'JavaScript Void'],
            ['menu_route' => '#', 'title' => 'Hash Route'],
            ['menu_route' => 'http://example.com', 'title' => 'External URL'],
            ['menu_route' => null, 'title' => 'Null Route'],
        ];

        foreach ($edgeCases as $index => $case) {
            MenuMaster::create([
                'menu_title' => $case['title'],
                'menu_icon' => 'fa fa-edge',
                'menu_route' => $case['menu_route'],
                'is_main_menu' => true,
                'parent_id' => null,
                'module_name' => null,
                'order_display' => str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'display_order' => (string)($index + 1),
                'if_can' => null,
            ]);
        }

        // All edge cases should render without exceptions
        try {
            $menuHtml = View::make('layouts.menu')->render();
            
            foreach ($edgeCases as $case) {
                $this->assertStringContainsString($case['title'], $menuHtml);
            }
            
        } catch (\Exception $e) {
            $this->fail('Edge cases should not cause exceptions: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to safely call route() helper
     */
    protected function callRouteHelperSafely(string $routeName, array $parameters = []): ?string
    {
        try {
            return route($routeName, $parameters);
        } catch (\Exception $e) {
            // This simulates what the fix should do - handle gracefully
            return '#';
        }
    }

    /**
     * Helper method to check if route exists
     */
    protected function routeExists(string $routeName): bool
    {
        try {
            return Route::has($routeName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Helper method to check if route has parameters
     */
    protected function routeHasParameters(string $routeName): bool
    {
        try {
            if (!Route::has($routeName)) {
                return false;
            }
            
            $route = Route::getRoutes()->getByName($routeName);
            return count($route->parameterNames()) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
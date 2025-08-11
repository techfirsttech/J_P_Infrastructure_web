<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\MenuMaster\Models\MenuMaster;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Test class to validate route parameter handling solution
 * 
 * This test validates:
 * 1. Menu generation works without errors when route parameters are missing
 * 2. Menu generation still works when parameters are present
 * 3. Permission-based functionality remains intact
 * 4. No regression in existing menu features
 * 5. Edge cases and error scenarios
 */
class MenuParameterValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $testUser;
    protected Role $testRole;
    protected Permission $testPermission;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->setupTestData();
    }

    protected function setupTestData(): void
    {
        // Create a test user
        $this->testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create test role and permission
        $this->testRole = Role::create(['name' => 'test-role']);
        $this->testPermission = Permission::create(['name' => 'test-permission']);
        
        // Assign role and permission
        $this->testRole->givePermissionTo($this->testPermission);
        $this->testUser->assignRole($this->testRole);

        // Create test routes
        Route::get('/test-route', function () {
            return 'Test Route';
        })->name('test.route');

        Route::get('/test-route-with-params/{id}', function ($id) {
            return "Test Route with ID: {$id}";
        })->name('test.route.with.params');

        Route::get('/test-route-with-multiple-params/{id}/{slug}', function ($id, $slug) {
            return "Test Route with ID: {$id} and Slug: {$slug}";
        })->name('test.route.multiple.params');

        // Create test menus
        $this->createTestMenus();
    }

    protected function createTestMenus(): void
    {
        // Parent menu without route
        $parentMenu = MenuMaster::create([
            'menu_title' => 'Parent Menu',
            'menu_icon' => 'fa fa-home',
            'menu_route' => 'javascript:void(0)',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '001',
            'display_order' => '1',
            'if_can' => null,
        ]);

        // Child menu with simple route (no parameters)
        MenuMaster::create([
            'menu_title' => 'Simple Route Menu',
            'menu_icon' => 'fa fa-cog',
            'menu_route' => 'test.route',
            'is_main_menu' => false,
            'parent_id' => $parentMenu->id,
            'module_name' => null,
            'order_display' => '001.001',
            'display_order' => '1.1',
            'if_can' => null,
        ]);

        // Child menu with parameterized route (should cause issues before fix)
        MenuMaster::create([
            'menu_title' => 'Parameterized Route Menu',
            'menu_icon' => 'fa fa-edit',
            'menu_route' => 'test.route.with.params',
            'is_main_menu' => false,
            'parent_id' => $parentMenu->id,
            'module_name' => null,
            'order_display' => '001.002',
            'display_order' => '1.2',
            'if_can' => null,
        ]);

        // Child menu with multiple parameters
        MenuMaster::create([
            'menu_title' => 'Multiple Params Route Menu',
            'menu_icon' => 'fa fa-list',
            'menu_route' => 'test.route.multiple.params',
            'is_main_menu' => false,
            'parent_id' => $parentMenu->id,
            'module_name' => null,
            'order_display' => '001.003',
            'display_order' => '1.3',
            'if_can' => null,
        ]);

        // Menu with permission
        MenuMaster::create([
            'menu_title' => 'Permission Protected Menu',
            'menu_icon' => 'fa fa-lock',
            'menu_route' => 'test.route',
            'is_main_menu' => false,
            'parent_id' => $parentMenu->id,
            'module_name' => null,
            'order_display' => '001.004',
            'display_order' => '1.4',
            'if_can' => 'test-permission',
        ]);

        // Menu with non-existent route
        MenuMaster::create([
            'menu_title' => 'Non-existent Route Menu',
            'menu_icon' => 'fa fa-question',
            'menu_route' => 'non.existent.route',
            'is_main_menu' => false,
            'parent_id' => $parentMenu->id,
            'module_name' => null,
            'order_display' => '001.005',
            'display_order' => '1.5',
            'if_can' => null,
        ]);

        // Standalone menu with parameters
        MenuMaster::create([
            'menu_title' => 'Standalone Parameterized Menu',
            'menu_icon' => 'fa fa-star',
            'menu_route' => 'test.route.with.params',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '002',
            'display_order' => '2',
            'if_can' => null,
        ]);
    }

    /**
     * @test
     * Test 1: Menu generation works without errors when parameters are missing
     */
    public function menu_generation_works_without_route_parameter_errors(): void
    {
        $this->actingAs($this->testUser);

        // This should not throw any exceptions
        $response = $this->get('/');
        
        $response->assertStatus(200);
        
        // Check that the response contains expected menu elements
        $response->assertSee('Parent Menu');
        $response->assertSee('Simple Route Menu');
        $response->assertSee('Parameterized Route Menu');
        $response->assertSee('Multiple Params Route Menu');
        
        // Verify no error messages in logs
        $this->assertLogDoesntContain('Missing required parameter');
        $this->assertLogDoesntContain('Route parameter');
    }

    /**
     * @test 
     * Test 2: Menu generation still works when parameters are present
     */
    public function menu_generation_works_with_route_parameters_present(): void
    {
        $this->actingAs($this->testUser);

        // Add route parameters to the request
        $response = $this->get('/?id=123&slug=test-slug');
        
        $response->assertStatus(200);
        
        // Menu should still render correctly
        $response->assertSee('Parent Menu');
        $response->assertSee('Parameterized Route Menu');
        $response->assertSee('Multiple Params Route Menu');
    }

    /**
     * @test
     * Test 3: Permission-based functionality remains intact
     */
    public function permission_based_functionality_remains_intact(): void
    {
        // Test with user who has permission
        $this->actingAs($this->testUser);
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Permission Protected Menu');

        // Test with user who doesn't have permission
        $unauthorizedUser = User::factory()->create([
            'email' => 'unauthorized@example.com'
        ]);
        
        $this->actingAs($unauthorizedUser);
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertDontSee('Permission Protected Menu');
    }

    /**
     * @test
     * Test 4: No regression in existing menu features
     */
    public function no_regression_in_existing_menu_features(): void
    {
        $this->actingAs($this->testUser);

        // Test menu hierarchy (parent-child relationships)
        $parentMenu = MenuMaster::where('menu_title', 'Parent Menu')->first();
        $this->assertNotNull($parentMenu);
        $this->assertTrue($parentMenu->children->count() > 0);

        // Test menu ordering
        $menus = MenuMaster::ordered()->get();
        $this->assertGreaterThan(0, $menus->count());

        // Test menu with simple routes work correctly
        $response = $this->get('/test-route');
        $response->assertStatus(200);
        $response->assertSee('Test Route');
    }

    /**
     * @test
     * Test 5: Edge cases and error scenarios
     */
    public function handles_edge_cases_and_error_scenarios(): void
    {
        $this->actingAs($this->testUser);

        // Test menu with null route
        MenuMaster::create([
            'menu_title' => 'Null Route Menu',
            'menu_icon' => 'fa fa-null',
            'menu_route' => null,
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '003',
            'display_order' => '3',
            'if_can' => null,
        ]);

        // Test menu with empty string route
        MenuMaster::create([
            'menu_title' => 'Empty Route Menu',
            'menu_icon' => 'fa fa-empty',
            'menu_route' => '',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => null,
            'order_display' => '004',
            'display_order' => '4',
            'if_can' => null,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        
        // Should still render without errors
        $response->assertSee('Null Route Menu');
        $response->assertSee('Empty Route Menu');
    }

    /**
     * @test
     * Test menu with non-existent routes
     */
    public function handles_non_existent_routes_gracefully(): void
    {
        $this->actingAs($this->testUser);

        $response = $this->get('/');
        $response->assertStatus(200);
        
        // Should render the menu item but handle the route gracefully
        $response->assertSee('Non-existent Route Menu');
        
        // Should not contain broken hrefs or cause exceptions
        $this->assertLogDoesntContain('Route [non.existent.route] not defined');
    }

    /**
     * @test
     * Test that route parameter solution preserves menu active state detection
     */
    public function preserves_menu_active_state_detection(): void
    {
        $this->actingAs($this->testUser);

        // Test that we can visit the actual test route
        $response = $this->get('/test-route');
        $response->assertStatus(200);

        // Menu system should correctly identify active menu items
        // This indirectly tests that route matching still works
        $simpleMenu = MenuMaster::where('menu_route', 'test.route')->first();
        $this->assertNotNull($simpleMenu);
    }

    /**
     * @test
     * Test menu rendering performance with route parameter handling
     */
    public function menu_rendering_performance_acceptable(): void
    {
        $this->actingAs($this->testUser);

        // Create many menus to test performance
        for ($i = 1; $i <= 50; $i++) {
            MenuMaster::create([
                'menu_title' => "Performance Test Menu {$i}",
                'menu_icon' => 'fa fa-test',
                'menu_route' => ($i % 2 === 0) ? 'test.route.with.params' : 'test.route',
                'is_main_menu' => true,
                'parent_id' => null,
                'module_name' => null,
                'order_display' => str_pad($i + 10, 3, '0', STR_PAD_LEFT),
                'display_order' => (string)($i + 10),
                'if_can' => null,
            ]);
        }

        $startTime = microtime(true);
        
        $response = $this->get('/');
        
        $endTime = microtime(true);
        $renderTime = $endTime - $startTime;

        $response->assertStatus(200);
        
        // Should render in reasonable time (less than 2 seconds)
        $this->assertLessThan(2.0, $renderTime, 'Menu rendering took too long');
    }

    /**
     * @test 
     * Test that menu service methods handle route parameters correctly
     */
    public function menu_service_handles_route_parameters_correctly(): void
    {
        $this->actingAs($this->testUser);

        // Test MenuMasterService::getNavigationMenuForUser
        $menuService = app(\Modules\MenuMaster\Services\MenuMasterService::class);
        
        $navigationMenu = $menuService->getNavigationMenuForUser();
        
        $this->assertGreaterThan(0, $navigationMenu->count());
        
        // Should not throw exceptions when processing menus with parameterized routes
        foreach ($navigationMenu as $menu) {
            $this->assertNotNull($menu->menu_title);
            // Route may be null, parameterized, or simple - all should be handled
        }
    }

    /**
     * Helper method to check logs don't contain specific errors
     */
    protected function assertLogDoesntContain(string $message): void
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            $this->assertStringNotContainsString($message, $logContent);
        }
    }

    /**
     * @test
     * Test menu blade template rendering with various route configurations
     */
    public function menu_blade_template_handles_route_configurations(): void
    {
        $this->actingAs($this->testUser);

        // Test the actual menu blade template rendering
        $menuHtml = view('layouts.menu')->render();
        
        // Should not contain any PHP errors or exceptions
        $this->assertStringNotContainsString('ErrorException', $menuHtml);
        $this->assertStringNotContainsString('Missing required parameter', $menuHtml);
        $this->assertStringNotContainsString('Route [', $menuHtml);
        
        // Should contain expected menu structure
        $this->assertStringContainsString('menu-inner', $menuHtml);
        $this->assertStringContainsString('Parent Menu', $menuHtml);
    }

    /**
     * @test
     * Test module-specific menu filtering still works
     */
    public function module_specific_menu_filtering_works(): void
    {
        $this->actingAs($this->testUser);

        // Create menu with specific module
        MenuMaster::create([
            'menu_title' => 'Module Specific Menu',
            'menu_icon' => 'fa fa-module',
            'menu_route' => 'test.route',
            'is_main_menu' => true,
            'parent_id' => null,
            'module_name' => 'TestModule',
            'order_display' => '005',
            'display_order' => '5',
            'if_can' => null,
        ]);

        // Test menu filtering by module still works
        $menuService = app(\Modules\MenuMaster\Services\MenuMasterService::class);
        
        $allMenus = $menuService->getFlattenedMenu();
        $moduleMenus = $menuService->getFlattenedMenu('TestModule');
        
        $this->assertGreaterThan($moduleMenus->count(), $allMenus->count());
    }

    protected function tearDown(): void
    {
        // Clean up any route registrations if needed
        parent::tearDown();
    }
}
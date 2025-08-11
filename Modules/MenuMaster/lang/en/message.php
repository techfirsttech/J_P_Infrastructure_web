<?php

return [
    // Core Menu Terms
    'menu' => 'Menu',
    'menumaster' => 'Menu Master',
    'menu_item' => 'Menu Item',
    'menu_item_list' => 'Menu Item List',
    'menu_item_details' => 'Menu Item Details',
    'menu_item_edit' => 'Edit Menu Item',
    'menu_item_create' => 'Create Menu Item',
    'menu_item_delete' => 'Delete Menu Item',
    'menu_item_duplicate' => 'Duplicate Menu Item',
    'menu_options' => 'Menu Options',
    'mark_as_main_menu' => 'Mark as Main Menu',

    // Menu Fields
    'menuicon' => 'Icon',
    'menutitle' => 'Title',
    'menuroute' => 'Route',
    'menuparent' => 'Parent',
    'menumodulename' => 'Module Name',
    'menu_icon' => 'Menu Icon',
    'menu_title' => 'Menu Title',
    'menu_route_url' => 'Menu Route/URL',
    'parent_menu' => 'Parent Menu',
    'module_name' => 'Module Name',
    'ifcan' => 'If Can',
    'menuSequence' => 'Seq.',
    'is_main_menu' => 'Is Main Menu',
    'permission_required' => 'Permission Required',
    'search_icons' => 'Search Icons',

    // Placeholders
    'enter_menu_icon' => 'Pls Enter Menu Icon',
    'enter_menu_title' => 'Pls Enter Menu Title',
    'enter_menu_route' => 'Pls Enter Menu Route',
    'enter_module_name' => 'Pls Enter Module Name',
    'select_parent_menu' => 'Select Parent Menu (Leave empty for main menu)',
    'icon_class_example' => 'FontAwesome icon class (e.g., fas fa-home)',
    'route_placeholder' => 'Leave empty if this is a parent menu with no direct link',
    'module_placeholder' => 'Used to group related menus together',
    'permission_placeholder' => 'Laravel permission required to access this menu',

    // Menu Management
    'menuManagement' => 'Menu Management',
    'menuManagementList' => 'Menu Management List',
    'menuManagementAddItem' => 'Add Menu Item',
    'menuManagementEditItem' => 'Edit Menu Item',
    'menuManagementDeleteItem' => 'Delete Menu Item',
    'menuManagementViewItem' => 'View Menu Management',
    'menuManagementStatus' => 'Status',
    'menuFlattenedList' => 'Flattened Menu List',
    'menuList' => 'Menu List',
    'menuOptions' => 'Menu Options',
    'treestructure' => 'Menu Tree Structure',

    // Menu Tools
    'menutools' => 'Menu Tools',
    'menuToolsNormalizeOrders' => 'Normalize Orders',
    'menuToolsRebuildHierarchy' => 'Rebuild Hierarchy',
    'menuToolsExportStructure' => 'Export Structure',
    'menuToolsShowStatistics' => 'Show Statistics',
    'menuToolsShowStatisticsDescription' => 'View menu statistics including total items, active items, and inactive items.',
    'menuToolsShowStatisticsTotalItems' => 'Total Items',
    'menuToolsShowStatisticsActiveItems' => 'Active Items',
    'menuToolsShowStatisticsInactiveItems' => 'Inactive Items',

    // Filters
    'menuFilterByModule' => 'Menu Filter By Module',
    'menuAllModules' => 'All Modules',
    'menuFilterByStatus' => 'Menu Filter By Status',
    'menuFilterByStatusAll' => 'All Statuses',

    // Statistics
    'menuStatistics' => 'Menu Statistics',
    'totalMenus' => 'Total Menus',
    'mainMenus' => 'Main Menus',
    'subMenus' => 'Sub Menus',
    'withRoutes' => 'With Routes',
    'withPermissions' => 'With Permissions',
    'modules' => 'Modules',
    'maxDepth' => 'Max Depth',

    // Hierarchy Information
    'hierarchy_information' => 'Hierarchy Information',
    'order' => 'Order',
    'level' => 'Level',
    'parent' => 'Parent',
    'none_main_menu' => 'None (Main Menu)',
    'child_menus' => 'Child Menus',
    'has_children' => 'Has Children',
    'children' => 'Children',
    'children_count' => ':count children',
    'hierarchy_change_warning' => 'Changing parent will update the menu hierarchy',
    'children_warning' => 'Moving this menu to a different parent will also move all its child menus.',

    // Basic Information
    'basic_information' => 'Basic Information',
    'id' => 'ID',
    'title' => 'Title',
    'icon' => 'Icon',
    'select_icon' => 'Select Icon',
    'route_url' => 'Route URL',
    'route' => 'Route',
    'permission' => 'Permission',

    // Audit Information
    'audit_information' => 'Audit Information',
    'created_at' => 'Created At',
    'created_by' => 'Created By',
    'updated_at' => 'Updated At',
    'updated_by' => 'Updated By',
    'deleted_at' => 'Deleted At',
    'deleted_by' => 'Deleted By',

    // Status Messages
    'no_icon' => 'No Icon',
    'no_route' => 'No Route',
    'no_permission' => 'No Permission',
    'no_parent' => 'No Parent',
    'no_children' => 'No Children',
    'unknown' => 'Unknown',
    'noMenuItemsFoundModule' => 'No menu items found for the selected module.',
    'noMenuItemsFound' => 'No menu items found for the selected module.',

    // Actions
    'add' => 'Add Menu',
    'create' => 'Create Menu',
    'edit' => 'Edit',
    'update' => 'Update Menu',
    'view' => 'View',
    'view_details' => 'View Details',
    'close' => 'Close',
    'duplicate' => 'Duplicate',
    'delete' => 'Delete',
    'details' => 'Details',
    'back' => 'Back',
    'back_to_menu_list' => 'Back to Menu List',
    'cancel' => 'Cancel',
    'action' => 'Action',
    'create_menu_item' => 'Create Menu Item',
    'update_menu_item' => 'Update Menu Item',
    'create_new_menu_item' => 'Create New Menu Item',
    'live_preview' => 'Live Preview',
    'navigation_preview' => 'Navigation Preview',
    'current_position' => 'Current Position',

    // Confirmation Messages
    'confirmDeleteMenuItem' => 'Are you sure you want to delete this menu item?',
    'confirm_duplicate' => 'Are you sure you want to duplicate this menu item?',
    'confirm_delete' => 'Are you sure you want to delete this menu item?',
    'confirm_delete_with_children' => 'This menu has :count child menu(s). Are you sure you want to delete it? All child menus will also be deleted.',
    'confirmNormalizeOrders' => 'Are you sure you want to normalize the menu orders? This will reset the order of all menu items.',
    'confirmRebuildHierarchy' => 'Are you sure you want to rebuild the menu hierarchy?',
    'duplicate_confirmation' => 'Are you sure you want to duplicate this menu item?',
    'delete_confirmation' => 'Are you sure you want to delete this menu item?',
    'delete_confirmation_with_children' => 'This menu has :count child items that will also be deleted. Are you sure?',

    // Error Messages
    'error_duplicating' => 'Error duplicating menu item.',
    'error_deleting' => 'Error deleting menu item.',
    'errorDeletingMenuItem' => 'Error deleting menu item. Please try again.',
    'errorMovingMenuItem' => 'Error moving menu item. Please try again.',
    'errorNormalizingOrders' => 'Error normalizing menu orders. Please try again.',
    'errorRebuildingHierarchy' => 'Error rebuilding menu hierarchy. Please try again.',
    'errorLoadingStatistics' => 'Error loading menu statistics. Please try again.',
    'duplicate_error' => 'Error occurred while duplicating menu item',

    // Notes
    'note' => 'Note',
    'main_menu_description' => 'Main menus are typically top-level navigation items'
];
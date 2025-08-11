# Laravel 12 Enterprise Modular Application - Complete Setup Guide

## 🚀 Overview

This is a sophisticated Laravel 12 enterprise application built with a modular architecture using the `nwidart/laravel-modules` package. The application features a comprehensive business management system with pre-built modules for User Management, Authentication, Role-based Permissions, Settings, and various master data modules.

### 🏗️ Architecture Highlights

-   **Modular Design**: Clean separation using Laravel Modules
-   **Role-Based Access Control**: Spatie Laravel Permission integration
-   **Multi-Database Support**: Local and production database configurations
-   **Custom Artisan Commands**: Enhanced development workflow
-   **Image Processing**: Advanced image upload and thumbnail generation
-   **Hierarchical User Management**: Multi-level user organization
-   **Audit Logging**: Complete change tracking system
-   **Advanced Helper Functions**: Custom business logic helpers

## 📋 System Requirements

### Core Requirements

-   **PHP**: ^8.3 (with extensions: pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo, gd)
-   **Composer**: Latest version
-   **Node.js**: Latest LTS version (18+)
-   **NPM**: Latest version
-   **Database**: MySQL/MariaDB 8.0+
-   **Web Server**: Apache/Nginx (or use Laravel's built-in server)

### PHP Extensions Required

```bash
# Ubuntu/Debian
sudo apt install php8.3-cli php8.3-fpm php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath

# For image processing
sudo apt install php8.3-imagick
```

## 🚀 Quick Setup

### 1. Clone and Install Dependencies

```bash
# Clone the repository (if from git)
# cd to project directory

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration

> **⚠️ Important**: This application supports dual database configuration for local and production environments.

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Configuration

#### Auto Database Creation

This application includes a custom command to automatically create databases:

```bash
# Create main database
php artisan db:create your_database_name

# Or let it use the default from .env
php artisan db:create
```

#### Environment Database Settings

Edit your `.env` file with your database credentials:

```env
# Primary Database Connection
DB_CONNECTION=mysql
DB_DRIVER=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_STRICT=true

# Local Database Connection (for development)
DB_CONNECTION_LOCAL=mysql
DB_DRIVER_LOCAL=mariadb
DB_HOST_LOCAL=127.0.0.1
DB_PORT_LOCAL=3306
DB_DATABASE_LOCAL=your_local_database_name
DB_USERNAME_LOCAL=your_local_username
DB_PASSWORD_LOCAL=your_local_password
DB_CHARSET_LOCAL=utf8mb4
DB_COLLATION_LOCAL=utf8mb4_unicode_ci
DB_STRICT_LOCAL=true
```

> **💡 Tip**: The dual database setup allows you to maintain separate local and production databases simultaneously.

### 4. Advanced Application Configuration

#### Core Application Settings

```env
APP_NAME="Your Application Name"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Kolkata
LIVE_APP_DOMAIN="your-production-domain.com"
```

#### UI & Layout Configuration

```env
# Menu Layout (vertical or horizontal)
MENU_STYLE=vertical
# MENU_STYLE=horizontal
```

#### Business Logic Configuration

```env
# Location and Regional Settings
STATE_ID=11  # Gujarat state ID (customize as needed)


# Security Settings
LOGOUT_OTHER=1  # Force logout other sessions
AUTH_PASSWORD_TIMEOUT=10800  # 3 hours

```

### 5. Database Setup & Seeding

#### Migration Process

```bash
# Run all migrations (core + modules)
php artisan migrate


# Or run module-specific migrations
php artisan module:migrate User
php artisan module:migrate Country
# ... for each module
```

#### Essential Seeding

```bash
# 1. Create permission structure (REQUIRED)
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionTableSeeder

# 2. Create admin users (REQUIRED)
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\CreateAdminUserSeeder
```

**Default Admin Credentials:**

-   **Super Admin**: `super@adm.com` / `Tech@#311`
-   **Company Admin**: `company_admin@adm.com` / `Company@#311`

#### Module Data Seeding

```bash
# Seed specific modules
php artisan module:seed Country  # Countries, states, cities data
php artisan module:seed User      # Additional user data
php artisan module:seed Setting   # Application settings

# Seed all modules at once
php artisan module:seed
```

### 6. Asset Compilation

```bash
# Production build
npm run build

# Development with hot reload
npm run dev

# Module-specific asset compilation
# Each module has its own package.json and can be built independently
cd Modules/ModuleName
npm run dev
```

### 7. Start the Application

#### Quick Start (Recommended)

```bash
# All-in-one development command (runs everything in parallel)
composer run dev
# This starts: server, queue worker, log viewer, and vite dev server
```

#### Individual Services

```bash
# Laravel development server
php artisan serve

# Queue worker (for background jobs)
php artisan queue:work

# Log viewer (Laravel Pail)
php artisan pail

# Asset compilation with hot reload
npm run dev
```

#### Access the Application

-   **URL**: http://localhost:8000
-   **Login**: Use the admin credentials mentioned above
-   **Dashboard**: Available after successful login

## 📦 Available Modules

The application comes with 12 pre-built, fully functional modules:

### 🔐 Core System Modules

#### **User Module** (`Modules/User/`)

-   **Features**: Complete user management system
-   **Components**: User profiles, hierarchical organization, change logging
-   **Models**: User, UserProfile, UserHierarchy, ChangeLog
-   **Special Features**:
    -   Multi-level user hierarchy support
    -   Login attempt tracking
    -   Profile management with parent-child relationships
    -   Designation and role assignment
-   **Migrations**: 10+ migration files for comprehensive user management

#### **Login Module** (`Modules/Login/`)

-   **Features**: Authentication and session management
-   **Components**: Login forms, session handling, authentication requests
-   **Integration**: Works with User module for complete auth flow

#### **Role Module** (`Modules/Role/`)

-   **Features**: Role-based access control using Spatie Laravel Permission
-   **Components**: Role creation, permission assignment, role hierarchy
-   **Integration**: Seamlessly integrates with User module

#### **Dashboard Module** (`Modules/Dashboard/`)

-   **Features**: Main dashboard interface with widgets and analytics
-   **Components**: Dashboard controller, customizable views

#### **Setting Module** (`Modules/Setting/`)

-   **Features**: Application-wide settings management
-   **Components**: Company info, logos, system configurations
-   **Special Features**: Cached settings for performance

#### **MenuMaster Module** (`Modules/MenuMaster/`)

-   **Features**: Dynamic menu management system
-   **Components**: Hierarchical menu creation, tree view, menu services
-   **Models**: MenuMaster with tree structure support

### 🗺️ Master Data Modules

#### **Country Module** (`Modules/Country/`)

-   **Features**: Global country management
-   **Data**: Pre-seeded with world countries
-   **Integration**: Base for State and City modules

#### **State Module** (`Modules/State/`)

-   **Features**: State/Province management
-   **Relationships**: Belongs to Country, has many Cities
-   **Helper Functions**: Custom state helper functions

#### **City Module** (`Modules/City/`)

-   **Features**: City/Location management
-   **Relationships**: Belongs to State and Country
-   **Helper Functions**: City-specific helper functions

#### **Currency Module** (`Modules/Currency/`)

-   **Features**: Multi-currency support
-   **Components**: Currency rates, conversion, management
-   **Recent Addition**: Added July 2025

#### **Unit Module** (`Modules/Unit/`)

-   **Features**: Unit of measurement management
-   **Special Features**: Unit gravity system for complex calculations
-   **Models**: Unit, UnitGravity
-   **Helper Functions**: Unit conversion helpers

#### **Year Module** (`Modules/Year/`)

-   **Features**: Financial/Academic year management
-   **Components**: Year selection, default year setting
-   **Helper Functions**: Year selection and management helpers

### 📡 Module Architecture

Each module follows a consistent structure:

```
Modules/ModuleName/
├── app/
│   ├── Http/Controllers/     # Module controllers
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   └── Services/            # Business logic services
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Data seeders
├── resources/
│   ├── assets/              # CSS/JS assets
│   └── views/               # Blade templates
├── routes/                  # Module routes
├── tests/                   # Module tests
├── Helpers/                 # Custom helper files
├── composer.json            # Module dependencies
├── module.json              # Module metadata
└── package.json             # Frontend dependencies
```

## 🛠️ Module Management Commands

### 🎭 Custom Artisan Commands

This application includes several custom commands for enhanced development workflow:

#### **Enhanced Module Creation**

```bash
# 🆕 Custom command: Create module with model and migration in one go
php artisan module:make-with-model ModuleName
# This creates: Module + Model + Migration + Controller + Routes + Views

# Create database automatically
php artisan db:create database_name
# Supports both SQLite and MySQL/MariaDB
```

### 📝 Standard Module Commands

#### Creating New Modules

```bash
# Create a new module
php artisan module:make ModuleName

# Create migration in module
php artisan module:make-migration create_table_name ModuleName

# Create model in module with migration
php artisan module:make-model ModelName ModuleName --migration

# Create controller in module
php artisan module:make-controller ControllerName ModuleName

# Create seeder for module
php artisan module:make-seed SeederName ModuleName
# php artisan module:make-seeder SeederName ModuleName

# Create request class
php artisan module:make-request RequestName ModuleName

# Create service class
php artisan module:make-service ServiceName ModuleName
```

### 📋 Module Operations

#### Database Operations

```bash
# Run migrations for specific module
php artisan module:migrate ModuleName

# Rollback module migrations
php artisan module:migrate-rollback ModuleName

# Refresh module migrations
php artisan module:migrate-refresh ModuleName

# Reset module migrations
php artisan module:migrate-reset ModuleName

# Seed specific module
php artisan module:seed ModuleName

# Seed specific seeder class
php artisan module:seed ModuleName --class=SeederName
```

#### Module Management

```bash
# List all modules with status
php artisan module:list

# Show detailed module status
php artisan module:status

# Enable/Disable modules
php artisan module:enable ModuleName
php artisan module:disable ModuleName

# Enable multiple modules
php artisan module:enable ModuleOne ModuleTwo

# Check module details
php artisan module:show ModuleName
```

### 📤 Publishing & Asset Management

#### Publishing Assets

```bash
# Publish module assets to public directory
php artisan module:publish ModuleName

# Publish all module assets
php artisan module:publish

# Force publish (overwrite existing)
php artisan module:publish ModuleName --force

# Publish specific module migrations to main migrations folder
php artisan module:publish-migration ModuleName

# Publish module configuration
php artisan module:publish-config ModuleName
```

#### Asset Compilation per Module

```bash
# Each module has independent asset compilation
cd Modules/ModuleName
npm install  # Install module-specific dependencies
npm run dev  # Compile module assets
npm run build  # Production build
```

## 💻 Development Workflow

### 1. Starting Development Environment

#### 🚀 Quick Start (All Services)

```bash
# 🆕 Custom composer script - runs everything in parallel
composer run dev
# 📦 This starts:
# ✨ PHP development server (localhost:8000)
# 📜 Queue worker (for background jobs)
# 📄 Log viewer (Laravel Pail with real-time logs)
# ⚡ Vite dev server (hot reload for assets)
```

#### 🔧 Individual Services

```bash
# Web server
php artisan serve --host=0.0.0.0 --port=8000

# Queue processing
php artisan queue:work --tries=1

# Real-time log monitoring
php artisan pail --timeout=0

# Asset compilation with hot reload
npm run dev

# Database migrations with automatic database creation
php artisan migrate:fresh --seed
```

### 2. Creating a New Feature Module

#### 🎯 Example: Creating a Product Module

```bash
# 🆕 Use the custom command for full module setup
php artisan module:make-with-model Product

# 📦 This automatically creates:
# 📁 Modules/Product/ directory structure
# 📊 Product model with migration
# 🎮 ProductController with CRUD methods
# 🛍️ Routes (web.php, api.php)
# 🎨 Views with master layout
# ⚙️ Service providers and configuration
# 📦 package.json and composer.json
# 🎨 Vite configuration for assets
```

#### 🛠️ Advanced Module Customization

```bash
# Create with additional components
php artisan module:make-model ProductVariant Product --migration
php artisan module:make-controller ProductController Product
php artisan module:make-request StoreProductRequest Product
php artisan module:make-seeder ProductSeeder Product
php artisan module:make-factory ProductFactory Product

# Add helper functions
echo '<?php\n// Product helper functions' > Modules/Product/Helpers/product_helper.php
```

### 3. Advanced Database Operations

#### 📋 Migration Management

```bash
# Create migration with foreign keys
php artisan module:make-migration create_products_table Product
php artisan module:make-migration add_category_id_to_products_table Product

# Run specific module migration
php artisan module:migrate Product

# Check migration status
php artisan migrate:status

# Rollback specific steps
php artisan module:migrate-rollback Product --step=1
```

#### 🌱 Seeding Strategy

```bash
# Create comprehensive seeders
php artisan module:make-seeder ProductSeeder Product
php artisan module:make-seeder ProductCategorySeeder Product

# Run specific module seeding
php artisan module:seed Product

# Run specific seeder class
php artisan module:seed Product --class=ProductSeeder

# Seed with fresh migrations
php artisan migrate:fresh --seed
```

#### 📊 Using Helper Functions in Migrations

```php
// In your migration file
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);

    // Use the defaultMigration helper for standard fields
    defaultMigration($table);

    $table->timestamps();
});
```

## 🏢 Project Structure

```
🏢 Laravel 12 Enterprise Modular Application
├── 📎 app/                          # Core Laravel application
│   ├── Console/Commands/            # 🆕 Custom artisan commands
│   │   ├── CreateDatabase.php       # Auto database creation
│   │   └── ModuleWithModelCommand.php # Enhanced module creation
│   ├── Helpers/                     # 🔧 Global helper functions
│   └── Http/                        # Core controllers & middleware
├── 📦 Modules/                      # All custom modules (12 modules)
│   ├── User/                     # 👥 Complete user management
│   ├── Login/                    # 🔐 Authentication system
│   ├── Role/                     # 📊 Role-based access control
│   ├── Dashboard/                # 📈 Main dashboard
│   ├── Setting/                  # ⚙️ Application settings
│   ├── MenuMaster/               # 🎨 Dynamic menu system
│   ├── Country/                  # 🌍 Master data
│   ├── State/                    # 🗺️ Geographic data
│   ├── City/                     # 🏞️ Location management
│   ├── Currency/                 # 💰 Multi-currency support
│   ├── Unit/                     # 📊 Measurement units
│   └── Year/                     # 🗺 Financial year management
├── ⚙️ config/
│   ├── modules.php               # Module configuration
│   ├── auth.php                  # 🔐 Custom auth with User module
│   ├── permission.php            # Spatie permission config
│   └── [other configs]/
├── 📋 database/                     # Core database files
│   └── migrations/                # Core migrations only
├── 🎨 public/                       # Public assets & uploads
│   ├── assets/                    # Compiled assets
│   └── setting/                   # Dynamic uploads (logos, etc)
├── 📚 resources/                    # Main resources
│   ├── views/layouts/             # Master layouts
│   └── css/ & js/                 # Core assets
├── 🛍️ routes/                       # Main application routes
├── 🧪 stubs/nwidart-stubs/           # 🆕 Custom module templates
└── 📋 modules_statuses.json          # Module enable/disable status
```

### 📦 Standard Module Structure

Each module follows this comprehensive structure:

```
Modules/ModuleName/
├── 📎 app/                          # Application logic
│   ├── Http/
│   │   ├── Controllers/          # 🎮 Module controllers
│   │   └── Requests/             # 📋 Form validation requests
│   ├── Models/                   # 📊 Eloquent models
│   ├── Providers/                # ⚙️ Service providers
│   │   ├── ModuleServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Services/                 # 🛠️ Business logic services
│   └── Transformers/             # 🔄 API resource transformers
├── ⚙️ config/
│   └── config.php                # Module configuration
├── 📋 database/
│   ├── factories/                # 🏠 Model factories
│   ├── migrations/               # 📊 Database migrations
│   └── seeders/                  # 🌱 Data seeders
├── 🌍 Helpers/                     # 🔧 Module-specific helpers
│   └── module_helper.php
├── 🎨 resources/
│   ├── assets/                   # Frontend assets
│   │   ├── js/app.js
│   │   └── sass/app.scss
│   └── views/                    # 📝 Blade templates
│       ├── components/layouts/master.blade.php
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── 🛍️ routes/                      # Module routes
│   ├── web.php                   # Web routes
│   └── api.php                   # API routes
├── 🧪 tests/                       # Module tests
│   ├── Feature/
│   └── Unit/
├── 🐛 lang/en/                     # Localization
│   └── message.php
├── 📦 composer.json                 # PHP dependencies
├── 📋 module.json                   # Module metadata
├── 📦 package.json                  # JS dependencies
└── ⚡ vite.config.js                # Asset compilation config
```

## ✨ Advanced Features & Architecture

### 🔐 Advanced Authentication & Authorization

-   **Hierarchical User System**: Multi-level parent-child user relationships
-   **Role-Based Access Control**: Powered by Spatie Laravel Permission package
-   **Login Security**:
    -   Login attempt tracking and blocking
    -   Session management with forced logout capability
    -   Password confirmation timeout (configurable)
    -   Multi-device session control
-   **Custom Auth Integration**: Uses `Modules\User\Models\User` as auth model

### 🏢 Enterprise Modular Architecture

-   **Clean Module Separation**: Each module is completely independent
-   **Hot Module Management**: Enable/disable modules without code changes
-   **Independent Assets**: Each module has its own Vite configuration
-   **Module-Specific Routes**: Isolated web and API routes per module
-   **Service Provider Auto-Discovery**: Automatic module registration
-   **Custom Module Templates**: Enhanced stub files for consistent structure

### 🛠️ Development & DevOps Tools

-   **Laravel Debugbar**: Advanced debugging with module-specific insights
-   **Vite Integration**: Modern asset compilation with hot reload
-   **Tailwind CSS 4.0**: Latest utility-first CSS framework
-   **Laravel Pail**: Real-time log monitoring
-   **DataTables**: Advanced data grid with server-side processing
-   **Custom Artisan Commands**: Enhanced workflow automation

### 🎨 Built-in Business Features

-   **Dynamic Menu System**: Tree-structured, role-based menu management
-   **Cached Settings**: High-performance application configuration
-   **Complete Audit Trail**: Change logging with user tracking
-   **Advanced Image Processing**: WebP conversion with thumbnail generation
-   **Multi-Language Ready**: Localization support per module
-   **Helper Function Library**: 20+ custom business logic helpers
-   **Master Data Management**: Countries, states, cities with pre-seeded data

### 📋 Database & Performance Features

-   **Dual Database Support**: Separate local and production configurations
-   **Auto Database Creation**: Custom command for database setup
-   **Migration Helpers**: Standardized migration patterns with `defaultMigration()`
-   **Soft Deletes**: Built into helper functions
-   **Foreign Key Management**: Automatic user tracking (created_by, updated_by)
-   **Caching Layer**: Settings and frequently accessed data cached
-   **Query Optimization**: Hierarchical data with efficient tree queries

## 🛠️ Troubleshooting & Maintenance

### 🐛 Common Issues & Solutions

#### 1. **Module Not Found/Loading Issues**

```bash
# Check module status
php artisan module:list

# Enable disabled modules
php artisan module:enable ModuleName

# Verify module registration
php artisan module:show ModuleName

# Regenerate module cache
composer dump-autoload -o
```

#### 2. **Asset Compilation Problems**

```bash
# Clear and rebuild all assets
npm run build

# Module-specific asset issues
cd Modules/ModuleName
npm install
npm run build

# Vite development server issues
killall node  # Kill existing processes
npm run dev
```

#### 3. **Permission & Authentication Issues**

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild permissions
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionTableSeeder

# Reset autoloader
composer dump-autoload -o
```

#### 4. **Database Connection Issues**

```bash
# Test connections
php artisan tinker
# Then run: DB::connection()->getPdo()

# Create database if missing
php artisan db:create

# Check database configuration
php artisan config:show database
```

#### 5. **Image Upload Problems**

```bash
# Check storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Create symbolic link for public storage
php artisan storage:link

# Verify GD extension
php -m | grep -i gd
```

#### 6. **Module-Specific Issues**

```bash
# Check module dependencies
cd Modules/ModuleName
composer validate

# Verify module migrations
php artisan module:migrate-status ModuleName

# Reset module data
php artisan module:migrate-refresh ModuleName --seed
```

### 📋 Cache Management & Performance

#### Complete Cache Reset

```bash
# Nuclear option - clear everything
php artisan optimize:clear

# Or individually
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan queue:clear

# Module-specific cache clearing
php artisan module:publish --force
```

#### Performance Optimization

```bash
# Production optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Composer optimization
composer dump-autoload -o --no-dev

# Asset optimization
npm run build
```

#### Debug Mode Management

```bash
# Enable debug mode for troubleshooting
php artisan down --with-secret="debug-token"
# Visit: yoursite.com/debug-token

# Monitor logs in real-time
php artisan pail

# Check application health
php artisan about
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific module tests
php artisan test Modules/ModuleName/tests/
```

## Production Deployment

### 1. Environment Setup

-   Set `APP_ENV=production`
-   Set `APP_DEBUG=false`
-   Configure production database
-   Set up proper caching

### 2. Optimization

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
npm run build
```

### 3. Permissions

Ensure proper file permissions for:

-   `storage/` directory
-   `bootstrap/cache/` directory
-   Module assets in `public/modules/`

## Additional Resources

-   [Laravel Documentation](https://laravel.com/docs)
-   [Laravel Modules Package](https://nwidart.com/laravel-modules/)
-   [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/)
-   [Tailwind CSS](https://tailwindcss.com/docs)

## Support

For issues and questions:

1. Check the existing documentation
2. Review module-specific configurations
3. Check Laravel and package documentation
4. Ensure all dependencies are properly installed

---

_This guide covers the essential setup and usage of the Laravel 12 Modular Application. For specific module documentation, refer to individual module directories._

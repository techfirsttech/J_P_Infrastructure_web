<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (empty(env('APP_KEY'))) {
            // Execute the Artisan command programmatically
            Artisan::call('key:generate');
        }
        $this->ensureDatabaseExists();
        $this->setDynamicDatabaseByDomain();
    }

    private function setDynamicDatabaseByDomain()
    {
        $request = request();

        if (!$request) {
            return;
        }

        $domain = $request->getHost();
        $databaseConfig = $this->getDatabaseConfigForDomain($domain);

        if ($databaseConfig) {
            $this->createAndSetDynamicConnection($databaseConfig);
        }
    }

    private function getDatabaseConfigForDomain($domain)
    {
        // Domain to database mapping - completely dynamic

        if ($domain == env('LIVE_APP_DOMAIN')) {
            $domainConfigs = [
                'driver' => env('DB_DRIVER'),
                'url' => env('DB_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'laravel'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => env('DB_CHARSET', 'utf8mb4'),
                'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => env('DB_STRICT'),
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ];
        } else {
            $domainConfigs = [
                'driver' => env('DB_DRIVER_LOCAL'),
                'url' => env('DB_URL'),
                'host' => env('DB_HOST_LOCAL', '127.0.0.1'),
                'port' => env('DB_PORT_LOCAL', '3306'),
                'database' => env('DB_DATABASE_LOCAL', 'laravel'),
                'username' => env('DB_USERNAME_LOCAL', 'root'),
                'password' => env('DB_PASSWORD_LOCAL', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => env('DB_CHARSET_LOCAL', 'utf8mb4'),
                'collation' => env('DB_COLLATION_LOCAL', 'utf8mb4_unicode_ci'),
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => env('DB_STRICT_LOCAL'),
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ];
        }


        return $domainConfigs;
    }

    private function createAndSetDynamicConnection($config)
    {
        $connectionName = $config['driver'];


        // Set the connection in Laravel's config
        Config::set("database.connections.{$connectionName}", $config);

        // Set this as the default connection
        Config::set('database.default', $connectionName);

        // Purge any existing connections to ensure fresh connection
        DB::purge($connectionName);
    }

    private function ensureDatabaseExists()
    {
        // echo "....<pre>" . print_r($_SERVER['argv'], true) . "</pre>";
        if (app()->runningInConsole() && (in_array('migrate', $_SERVER['argv'] ?? []) || in_array('migrate:fresh', $_SERVER['argv'] ?? []))) {
            $this->createDatabaseIfNotExists();
        }
    }

    private function createDatabaseIfNotExists()
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $database = $config['database'] ?? null;

        if (!$database) {
            return;
        }

        if ($config['driver'] === 'sqlite') {
            $this->createSqliteDatabase($database);
        } elseif (in_array($config['driver'], ['mysql', 'mariadb'])) {
            $this->createMysqlDatabase($config, $database);
        }
    }

    private function createSqliteDatabase($database)
    {
        $path = database_path(basename($database));

        if (file_exists($path)) {
            return;
        }

        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        touch($path);
    }

    private function createMysqlDatabase($config, $database)
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']}";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");

            if ($stmt->rowCount() === 0) {
                $pdo->exec("CREATE DATABASE `$database` CHARACTER SET {$config['charset']} COLLATE {$config['collation']}");
            }
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application
        }
    }
}

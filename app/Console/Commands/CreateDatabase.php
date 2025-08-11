<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {name?}';
    protected $description = 'Create a new database if it doesn\'t exist';

    public function handle()
    {
        $database = $this->argument('name') ?: config('database.connections.' . config('database.default') . '.database');
        
        if (!$database) {
            $this->error('Database name not provided');
            return 1;
        }

        $connection = config('database.default');
        $config = config("database.connections.$connection");

        if ($config['driver'] === 'sqlite') {
            $this->createSqliteDatabase($database);
        } else {
            $this->createMysqlDatabase($config, $database);
        }

        return 0;
    }

    private function createSqliteDatabase($database)
    {
        $path = database_path($database);
        
        if (file_exists($path)) {
            $this->info("Database already exists at: $path");
            return;
        }

        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        touch($path);
        $this->info("SQLite database created at: $path");
    }

    private function createMysqlDatabase($config, $database)
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']}";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
            
            if ($stmt->rowCount() > 0) {
                $this->info("Database '$database' already exists");
                return;
            }

            $pdo->exec("CREATE DATABASE `$database` CHARACTER SET {$config['charset']} COLLATE {$config['collation']}");
            $this->info("Database '$database' created successfully");
        } catch (\Exception $e) {
            $this->error("Failed to create database: " . $e->getMessage());
        }
    }
}
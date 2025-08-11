<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateWithCreate extends Command
{
    protected $signature = 'migrate:auto {--seed : Run seeders after migration}';
    protected $description = 'Create database if not exists, then run migrations';

    public function handle()
    {
        $this->info('Creating database if it doesn\'t exist...');
        Artisan::call('db:create');
        $this->line(Artisan::output());

        $this->info('Running migrations...');
        $exitCode = Artisan::call('migrate');
        $this->line(Artisan::output());

        if ($this->option('seed')) {
            $this->info('Running seeders...');
            Artisan::call('db:seed');
            $this->line(Artisan::output());
        }

        if ($exitCode === 0) {
            $this->info('Migration completed successfully!');
        }

        return $exitCode;
    }
}
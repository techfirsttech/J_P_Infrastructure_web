<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Commands\ModuleMakeCommand;
use Nwidart\Modules\Commands\ModelMakeCommand;

class ModuleWithModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-with-model {name} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module with its model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('name');
        $force = $this->option('force');

        // First, create the module
        $this->info("Creating module: {$moduleName}");
        $this->call('module:make', [
            'name' => [$moduleName],
            '--force' => $force,
        ]);

        // Then, create the model
        $this->info("Creating model for module: {$moduleName}");
        $this->call('module:make-model', [

            'model' => $moduleName,
            'module' => $moduleName,
            '--migration' => true
        ]);

        $this->info("Module and model created successfully!");
    }
}
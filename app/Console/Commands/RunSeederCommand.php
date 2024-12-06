<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DatabaseSeeder;

class RunSeederCommand extends Command
{
    protected $signature = 'db:seed-module {module? : Name of the module}';
    protected $description = 'Seed the database with a specified module';

    public function handle()
    {
        $module = $this->argument('module');
        $seeder = new DatabaseSeeder();
        $this->info('Running seeders ' . (is_null($module) ? 'base' : 'for module ' . $module));
        // Call the run method with the module parameter
        $seeder->run($module);
        $this->info('Seeders executed successfully');
    }
}

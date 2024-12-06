<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* $baseSeeders = [];
        $this->call($baseSeeders); */

        $seederPath = database_path('seeders');
        $seedersListFile = $seederPath . '/seeder_list.php';

        $seeders = file_exists($seedersListFile) ? include $seedersListFile : [];

        foreach ($seeders as $seeder) {
            $seederPath = 'database/seeders/' . $seeder;
            echo $seederPath . PHP_EOL;
            if (!$this->isSeederExecuted($seeder)) {
                $this->callSeederFromFile($seederPath);
                $this->markSeederAsExecuted($seeder);
            }
        }
    }

    /**
     * Call a seeder from a given file path.
     *
     * @param string $filePath
     * @return void
     */
    protected function callSeederFromFile(string $filePath)
    {
        if (file_exists($filePath)) {
            $seeder = include $filePath;
            if ($seeder instanceof Seeder) {
                $this->call(get_class($seeder));
            }
        }
    }

    /**
     * Check if a seeder has been executed.
     *
     * @param string $seeder
     * @return bool
     */
    protected function isSeederExecuted($seeder)
    {
        return DB::table('seeders')->where('seeder', $seeder)->exists();
    }

    /**
     * Mark a seeder as executed.
     *
     * @param string $seeder
     * @return void
     */
    protected function markSeederAsExecuted($seeder)
    {
        DB::table('seeders')->insert(['seeder' => $seeder]);
    }
}

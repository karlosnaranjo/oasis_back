<?php

namespace App\Console\Commands;

use App\Http\Services\Codebuilder\MigrationService;
use App\Http\Services\Codebuilder\SeederService;
use Illuminate\Console\Command;

use Exception;

class CreateMigrationCommand extends Command
{
    // ! php artisan
    protected $signature = 'migration:schema
                        {table : Name of the table}
                        {label : The label for the table}';

    protected $description = 'Create or edit a table and its attributes generating a migration file with the information provided as parameters';

    protected $migrationService;

    public function __construct(MigrationService $migrationService)
    {
        parent::__construct();

        $this->migrationService = $migrationService;
    }

    public function handle()
    {
        $table = $this->arguments()['table'];
        $label = $this->arguments()['label'];
        $attributesFile = $table . '_schema.json';

        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $attributesFilePath = database_path('migrations/json/' . $attributesFile);
        if (!file_exists($attributesFilePath)) {
            $this->error('The specified attributes file does not exist in json folder.' . $attributesFilePath);
            return 1;
        }

        // Leer el contenido del archivo
        $attributesContent = file_get_contents($attributesFilePath);

        // Convertir el contenido del archivo JSON a un array
        $attributes = json_decode($attributesContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON string for attributes.');
            return 1;
        }

        // Ruta al archivo data_types.json
        $dataTypesFilePath = database_path('migrations/json/data_types.json');
        if (!file_exists($dataTypesFilePath)) {
            $this->error('The data_types.json file does not exist in json folder.');
            return 1;
        }
        // Actualizar los atributos con los tipos de datos
        try {
            $attributes = $this->migrationService->updateAttributesWithTypes($attributes, $dataTypesFilePath);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        // Generate the migrations
        $this->migrationService->generateSchemaMigration($table, $label, $attributes);

        $this->info('Migration generated successfully.');
        return 0;
    }
}

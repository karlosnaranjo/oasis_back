<?php

namespace App\Http\Services\Codebuilder;

use Exception;
use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MigrationService
{
    use UtilTrait;
    public function generateSchemaMigration($tableName, $label, $attributes)
    {

        // Generar la migración obj_
        $this->createObjMigration($tableName, $label, $attributes);
    }

    protected function createObjMigration($tableName, $label, $attributes)
    {
        // Verificar si la tabla existe
        $tableExists = Schema::hasTable($tableName);
        $tableExists = false;
        $migrationName = ($tableExists ? 'change_' : 'create_') . $tableName . '_table';

        // Create migration content
        $migrationContent = $this->buildObjMigrationContent($tableName, $label, $attributes, $tableExists);

        // Save migration file
        $this->saveMigrationFile($migrationName, $migrationContent);
    }


    protected function generateEnumMigration($data)
    {
        $enumValues = array_map(function ($choice) {
            return $choice['value'];
        }, $data['attrChoices']);

        // Generar el código para la migración
        $enumValuesString = "['" . implode("', '", $enumValues) . "']";
        return $enumValuesString;
    }

    protected function buildObjMigrationContent($tableName, $label, $attributes, $tableExists)
    {

        // Initialize the migration content
        $fields = '';

        if ($tableExists) {
            $fields .= "table('$tableName', function (Blueprint \$table) {\n";
        } else {
            $fields .= "create('$tableName', function (Blueprint \$table) {\n";
            // Add default fields for new table
            $fields .= "            \$table->id();\n";
        }

        // Process each attribute
        foreach ($attributes as $field) {
            // if field name starts with 'rel_' it must be ignored
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                continue;
            }

            $attrChoices = "";
            if ($field['type'] == 'field_enum' && isset($field['attrChoices'])) {
                $attrChoices = $this->generateEnumMigration($field);
            }

            if (!isset($field['action'])) {
                $field['action'] = 'Add';
            }

            $comment = "";
            if (isset($field['columnLabel'])) {
                $comment = "->comment('{$field['columnLabel']}')";
            }

            if ($field['action'] === 'Delete') {
                $fields .= "            \$table->dropColumn('{$field['columnName']}');\n";
            } elseif (strpos($field['action'], 'Add') === 0) {

                $afterField = '';
                $actionTmp = explode(':', $field['action']);
                if (isset($actionTmp[1])) {
                    $afterField = $actionTmp[1];
                }

                $fields .= "            \$table->{$field['laravel_type']}('{$field['columnName']}'" . (!empty($field['length']) ? ",{$field['length']}" : "") . (!empty($attrChoices) ? ",{$attrChoices}" : "") . ")" . (!empty($afterField) ? "->after('$afterField')" : "");
                if (isset($field['nullable']) && $field['nullable']) {
                    $fields .= "->nullable()";
                }
                $fields .= $comment . ";\n";
                // Define la clave foránea
                if (isset($field['references']) and !empty($field['references'])) {
                    $fields .= "            \$table->foreign('{$field['columnName']}')->references('id')->on('{$field['references']}')->onDelete('cascade');\n";
                }
            } elseif ($field['action'] === 'Change') {
                $fields .= "            \$table->{$field['laravel_type']}('{$field['columnName']}'" . (!empty($field['length']) ? ",{$field['length']}" : "") . ")->change();\n";
            } else {
                $fields .= "            \$table->{$field['laravel_type']}('{$field['columnName']}'" . (!empty($field['length']) ? ",{$field['length']}" : "") . ")";
                if (isset($field['nullable']) && $field['nullable']) {
                    $fields .= "->nullable()";
                }
                $fields .= ";\n";
                // Define la clave foránea
                if (isset($field['references']) and !empty($field['references'])) {
                    $fields .= "            \$table->foreign('{$field['columnName']}')->references('id')->on('{$field['references']}')->onDelete('cascade');\n";
                }
            }
        }

        // Add fixed fields for new table
        if (!$tableExists) {
            //$fields .= "            \$table->comment('$label');\n";
            $fields .= "            \$table->timestamps();\n";
            $fields .= "            \$table->softDeletes();\n";
        }

        $fields .= "        });";

        // Generate the migration content
        return <<<EOD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::$fields
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('$tableName');
    }
};
EOD;
    }

    public function updateAttributesWithTypes(array $attributes, string $dataTypesFilePath): array
    {
        // Leer el contenido del archivo JSON data_types.json
        $dataTypesJson = file_get_contents($dataTypesFilePath);
        $dataTypes = json_decode($dataTypesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in data_types file.');
        }

        // Recorrer y actualizar los atributos
        foreach ($attributes as &$attribute) {
            if (isset($dataTypes[$attribute['type']])) {
                $attribute['laravel_type'] = $dataTypes[$attribute['type']]['laravel_type'];
                $attribute['length'] = $dataTypes[$attribute['type']]['length'];
            } else {
                $attribute['laravel_type'] = 'string';
                $attribute['length'] = '255';
                // throw new Exception("Type {$attribute['type']} not found in data_types.");
            }
        }

        return $attributes;
    }

    protected function saveMigrationFile($migrationName, $migrationContent)
    {
        $migrationPath = database_path('migrations');
        $this->createDirectoryIfNotExists($migrationPath);

        $timestamp = date('Y_m_d_His');
        $migrationFile = $migrationPath . '/' . $timestamp . '_' . $migrationName . '.php';

        File::put($migrationFile, $migrationContent);
    }
}

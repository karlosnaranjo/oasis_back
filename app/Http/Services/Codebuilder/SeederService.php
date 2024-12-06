<?php

namespace App\Http\Services\Codebuilder;

use Illuminate\Support\Facades\File;
use App\Http\Services\Codebuilder\UtilTrait;


class SeederService
{
    use UtilTrait;
    public $seederList = [];
    public $seedersListFile = '';

    public function generatePermissionsSeeder($tableName, $label = null)
    {
        $this->checkOrCreateSeederListFile();
        // Generate the Controller file
        $attributes = $this->getJsonFile($tableName, 'schema');
        if (!$attributes) {
            return false;
        }

        $this->buildPermissionsContent($tableName, $label);

        // recorremos loos atributos, si hay tablas REL (hijas), generamos los permisos de cada una de ellas
        foreach ($attributes as $field) {
            // if field name starts with 'rel_' it has to create a new Controller with this tableName as parent
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                $childTable = str_replace("rel_", "", $field['columnName']);
                $this->buildPermissionsContent($childTable, $field['columnLabel']);
                continue;
            }
        }
        return true;
    }

    public function buildPermissionsContent($tableName, $label)
    {
        $optionName = $this->nameToPascalCase($tableName);
        $fileName = "Permissions{$optionName}Seeder.php";
        $content = <<<EOD
<?php

namespace Database\Seeders;

use App\Enums\ModulesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create([
            'name' => 'general:masters:$tableName:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de $label'
        ]);

        Permission::create([
            'name' => 'general:masters:$tableName:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de $label'
        ]);

        Permission::create([
            'name' => 'general:masters:$tableName:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar $label'
        ]);

        Permission::create([
            'name' => 'general:masters:$tableName:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar $label'
        ]);

        Permission::create([
            'name' => 'general:masters:$tableName:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de $label'
        ]);
    }
};
EOD;
        $this->saveSeederFile($fileName, $content);
    }

    public function saveSeederFile($fileName, $content)
    {
        $seederPath = database_path('seeders/' . $fileName);
        File::put($seederPath, $content);

        $this->registerSeeder($fileName);
    }

    public function registerSeeder($fileName)
    {
        $seederFileName = $fileName;
        echo 'Registering seeder ' . $seederFileName . PHP_EOL;
        if (!in_array($seederFileName, $this->seederList)) {
            $this->seederList[] = $seederFileName;
            file_put_contents($this->seedersListFile, "<?php\n\nreturn " . var_export($this->seederList, true) . ";\n");
        }
    }

    protected function checkOrCreateSeederListFile()
    {
        $this->seedersListFile = database_path("seeders/seeder_list.php");
        if (!file_exists($this->seedersListFile)) {
            file_put_contents($this->seedersListFile, "<?php\n\nreturn [];\n");
        }
        $this->seederList = include $this->seedersListFile;
        dump($this->seederList);
    }
}

<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class RepositoryService
{
    use UtilTrait;
    public $application = '';
    public function generateRepository($application, $tableName, $parentTable = null)
    {
        $this->application = $application;

        // Generate the Repository file
        $attributes = $this->getJsonFile($tableName, 'schema');
        if (!$attributes) {
            return false;
        }

        // Create repository content
        $repositoryContent = $this->buildRepositoryContent($tableName, $attributes, $parentTable);
        $repositoryName = $this->nameToPascalCase($tableName);

        // Save repository file
        $this->saveRepositoryFile($repositoryName, $repositoryContent);
        return true;
    }

    protected function buildRepositoryContent($tableName, $attributes, $parentTable)
    {

        $baseName = $this->nameToPascalCase($tableName);
        $modelName = $baseName . $this->getParameter($this->application, 'suffixModel');
        $repositoryName = $baseName . $this->getParameter($this->application, 'suffixRepository');
        $resourceName = $baseName . $this->getParameter($this->application, 'suffixResource');
        $repositoryName = $baseName . $this->getParameter($this->application, 'suffixRepository');

        $referencedModel = '';
        $relatedArray = "\t\t\t\"" . $this->nameToCamelCase($tableName) . "\" => new {$resourceName}(\$data),\n";
        $listFields = "\t\t\t'$tableName.id',\n";
        $joins = '';

        $modelUses = "use App\Models\\$modelName;\n";

        // Process each attribute
        foreach ($attributes as $field) {
            // if field name starts with 'rel_' it has to create a new Repository with this tableName as parent
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                $childTable = str_replace("rel_", "", $field['columnName']);
                $this->generateRepository($this->application, $childTable, $tableName);
                continue;
            }

            if (isset($field['columnName']) && $field['columnName'] == 'id') {
                continue;
            } else {
                $fieldName = $field['columnName'];

                if (isset($field['type']) && $field['type'] == 'field_fk') {
                    $referencedTable = $field['references'];
                    $referencedAlias = substr($fieldName, 0, strlen($fieldName) - 3);

                    // los campos de la consulta del index, deben excluir el campo id de la tabla padre
                    if (isset($field['references']) && $field['references'] != $parentTable) {
                        $listFields .= "\t\t\t'$referencedAlias.name as " . str_replace("_id", "_name", $fieldName) . "',\n";
                        $joins .= "\t\t\t->leftJoin('$referencedTable as $referencedAlias', function(\$join) {\n";
                        $joins .= "\t\t\t\t\$join->on('$referencedAlias.id', '=', '$tableName.$fieldName')\n";
                        $joins .= "\t\t\t\t\t->whereNull('$referencedAlias.deleted_at');\n\t\t\t})\n";
                    }
                } else {
                    $listFields .= "\t\t\t'$tableName.$fieldName',\n";
                }
            }
        }
        $joins = substr($joins, 0, strlen($joins) - 1);
        $relatedArray = substr($relatedArray, 0, strlen($relatedArray) - 1);


        // Generate the repository content
        return <<<EOD
<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

$modelUses
use App\Traits\IndexTrait;

/**
 * Class {$repositoryName}
 *
 */
class {$repositoryName}
{
    use IndexTrait;
    public function get{$baseName}List(Request \$request)
    {
        \$query =  {$modelName}::select(
$listFields
            )
$joins;
        return \$this->indexGrid(\$request, \$query);
    }
}

EOD;
    }

    protected function saveRepositoryFile($repositoryName, $repositoryContent)
    {
        $suffix = $this->getParameter($this->application, 'suffixRepository');

        $repositoryPath = app_path('/Http/Repositories');
        $this->createDirectoryIfNotExists($repositoryPath);

        $repositoryFile = $repositoryPath . '/' . $repositoryName . $suffix . '.php';

        File::put($repositoryFile, $repositoryContent);
    }
}

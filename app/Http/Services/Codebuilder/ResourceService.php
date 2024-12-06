<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class ResourceService
{
    use UtilTrait;
    public $application = '';

    public function generateResource($application, $tableName, $parentTable = null)
    {
        $this->application = $application;
        // Generate the Resource file
        $attributes = $this->getJsonFile($tableName, 'schema');
        if (!$attributes) {
            return false;
        }

        // Create resource content
        $resourceContent = $this->buildResourceContent($tableName, $attributes, $parentTable);
        $resourceName = $this->nameToPascalCase($tableName);

        // Save resource file
        $this->saveResourceFile($resourceName, $resourceContent);
        return true;
    }

    protected function buildResourceContent($tableName, $attributes, $parentTable)
    {

        $baseName = $this->nameToPascalCase($tableName);
        $resourceName = $baseName . $this->getParameter($this->application, 'suffixResource');
        $apiVersion = $this->getParameter($this->application, 'apiVersion');
        $apiVersion = !empty($apiVersion) ? '\\' . $apiVersion : $apiVersion;

        $listFields = "'id' => \$this->id,\n";
        $listRelations = '';

        // Process each attribute
        foreach ($attributes as $field) {
            // if field name starts with 'rel_' it has to create a new Resource with this tableName as parent
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                $childTable = str_replace("rel_", "", $field['columnName']);
                $this->generateResource($this->application, $childTable, $tableName);
                continue;
            }

            $fieldName = $field['columnName'];
            $listFields .= "\t\t\t'$fieldName' => \$this->$fieldName,\n";

            if (isset($field['type']) && $field['type'] == 'field_fk') {
                $referencedTable = $field['references'];
                $relationName = $this->nameToCamelCase($referencedTable) . $this->getParameter($this->application, 'suffixRelation');
                $resourceChildName = $this->nameToPascalCase($referencedTable) . $this->getParameter($this->application, 'suffixResource');

                // los campos de la consulta del resource, deben excluir el campo id de la tabla padre
                if (isset($field['references']) && $field['references'] != $parentTable) {
                    $listRelations = "'$referencedTable' => \$this->whenLoaded('$relationName', function () {
                return new $resourceChildName(\$this->$relationName);
            }),\n";
                }
            }
        }
        $listFields = substr($listFields, 0, strlen($listFields) - 1);
        $listRelations = substr($listRelations, 0, strlen($listRelations) - 1);


        // Generate the resource content
        return <<<EOD
<?php

namespace App\Http\Resources{$apiVersion};

use Illuminate\Http\Resources\Json\JsonResource;

class {$resourceName} extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(\$request)
    {
        return [
            $listFields
            $listRelations
        ];
    }
}

EOD;
    }

    protected function saveResourceFile($resourceName, $resourceContent)
    {
        $suffix = $this->getParameter($this->application, 'suffixResource');
        $apiVersion = $this->getParameter($this->application, 'apiVersion');
        $apiVersion = !empty($apiVersion) ? '/' . $apiVersion : $apiVersion;

        $resourcePath = app_path('/Http/Resources' . $apiVersion);
        $this->createDirectoryIfNotExists($resourcePath);

        $resourceFile = $resourcePath . '/' . $resourceName . $suffix . '.php';

        File::put($resourceFile, $resourceContent);
    }
}

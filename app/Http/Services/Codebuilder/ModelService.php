<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class ModelService
{
    use UtilTrait;
    public $application = '';

    public function generateModel($application, $tableName, $parentTable = null)
    {
        $this->application = $application;
        // Generate the Model file
        $attributes = $this->getJsonFile($tableName, 'schema');
        if (!$attributes) {
            return false;
        }

        // Create model content
        $modelContent = $this->buildModelContent($tableName, $attributes, $parentTable);
        $modelName = $this->nameToPascalCase($tableName);


        // Save model file
        $this->saveModelFile($modelName, $modelContent);
        return true;
    }

    protected function buildModelContent($tableName, $attributes, $parentTable)
    {
        $suffixModel = $this->getParameter($this->application, 'suffixModel');
        $modelName = $this->nameToPascalCase($tableName) . $suffixModel;
        $primaryKey = 'id';
        $fillable = '';
        $relationUses = '';
        $relationTypes = '';
        $relationFunctions = '';

        // Process each attribute
        foreach ($attributes as $field) {
            //************
            // REL_
            //************/
            // if field name starts with 'rel_' it has to create a new Model with this tableName as parent
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                // creamos la relacion de Uno a Muchos
                $rel = $this->buildRelationContent($field, $parentTable);
                $relationTypes .= (strpos($relationTypes, $rel[0]) === false) ? $rel[0] : '';
                $relationFunctions .= $rel[1];

                $childTable = str_replace("rel_", "", $field['columnName']);
                $this->generateModel($this->application, $childTable, $tableName);
                continue;
            }

            if (isset($field['columnName']) && $field['columnName'] == 'id') {
                $primaryKey = $field['columnName'];
            } else {
                $fillable .= "\t\t'{$field['columnName']}',\n";
                if (isset($field['type']) && $field['type'] == 'field_fk') {
                    $rel = $this->buildRelationContent($field, $parentTable);
                    $relationTypes .= (strpos($relationTypes, $rel[0]) === false) ? $rel[0] : '';
                    $relationFunctions .= $rel[1];
                }
            }
        }
        $relationTypes = substr($relationTypes, 0, strlen($relationTypes) - 1);
        $relations = explode(",", $relationTypes);
        foreach ($relations as $relation) {
            if (!empty($relation)) {
                $relationUses .= "use Illuminate\Database\Eloquent\Relations\\$relation;\n";
            }
        }

        // Generate the model content
        return <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

$relationUses

class $modelName extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected \$table = '$tableName';

     /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public \$incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public \$timestamps = true;

    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */

    protected \$primaryKey = '$primaryKey';

    /**
     * We define the fields of the table in the var \$fillable directly.
     */
    protected \$fillable = [
$fillable
    ];

$relationFunctions

}

EOD;
    }



    protected function buildRelationContent($field, $parentTable)
    {
        $suffixRelation = $this->getParameter($this->application, 'suffixRelation');
        $suffixModel = $this->getParameter($this->application, 'suffixModel');

        // si es un campo rel verificamos el tipo para determinar la relacion
        if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
            $relationType = 'HasMany';

            switch ($field['type']) {
                    // si es de tipo Grid, creamos una relacion de 1 a Muchos (HasMany)
                case 'field_grid':
                    $relationType = 'HasMany';
                    break;
                    // si es de tipo Form, creamos una relacion de 1 a 1 (HasOne)
                case 'field_form':
                    $relationType = 'HasOne';
                    break;
            }
            $relationName = $this->nameToCamelCase(substr($field['columnName'], 4)) . $suffixRelation;
            $relationReference = $this->nameToPascalCase(substr($field['columnName'], 4)) . $suffixModel;
            $fieldId = '';
        } else {

            $relationName = $this->fkToCamelCase($field['columnName']) . $suffixRelation;
            $relationType = '';
            $relationReference = $this->nameToPascalCase($field['references']) . $suffixModel;

            $fieldId = ", '{$field['columnName']}'";
            if ($field['references'] == $parentTable) {
                // si en el json tiene atributo "references" igual a la tabla padre, es porque es una tabla hija, osea que la relacion es BelongsTo
                $relationType = 'BelongsTo';
            } else {
                // si el "references" es otra tabla diferente, es una FK, o sea relacion 1 a 1 (HasOne)
                $relationType = 'HasOne';
            }
        }
        $relationMethod = lcfirst($relationType);

        $relationFunction = "\tpublic function $relationName(): $relationType
    {
        return \$this->$relationMethod($relationReference::class {$fieldId});
    }\n";
        return [$relationType . ',', $relationFunction];
    }

    protected function saveModelFile($modelName, $modelContent)
    {
        $suffix = $this->getParameter($this->application, 'suffixModel');

        $modelPath = app_path('/Models');
        $this->createDirectoryIfNotExists($modelPath);

        $modelFile = $modelPath . '/' . $modelName . $suffix . '.php';

        File::put($modelFile, $modelContent);
    }
}

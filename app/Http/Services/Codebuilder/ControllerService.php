<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class ControllerService
{
    use UtilTrait;
    public $application = '';
    public function generateController($application, $tableName, $parentTable = null)
    {
        echo 'CONTROLLER de ' . $tableName . ' CON PADRE ' . $parentTable . "\n";
        $this->application = $application;
        // Generate the Controller file
        $attributes = $this->getJsonFile($tableName, 'schema');
        if (!$attributes) {
            return false;
        }

        // Create controller content
        $controllerContent = $this->buildControllerContent($tableName, $attributes, $parentTable);
        $controllerName = $this->nameToPascalCase($tableName);


        // Save controller file
        $this->saveControllerFile($controllerName, $controllerContent);
        return true;
    }

    protected function buildControllerContent($tableName, $attributes, $parentTable)
    {
        $baseName = $this->nameToPascalCase($tableName);
        $apiFolder = $this->getParameter($this->application, 'apiFolder');
        $apiFolder = !empty($apiFolder) ? '\\' . $apiFolder : $apiFolder;
        $apiVersion = $this->getParameter($this->application, 'apiVersion');
        $apiVersion = !empty($apiVersion) ? '\\' . $apiVersion : $apiVersion;
        $modelName = $baseName . $this->getParameter($this->application, 'suffixModel');
        $controllerName = $baseName . $this->getParameter($this->application, 'suffixController');
        $resourceName = $baseName . $this->getParameter($this->application, 'suffixResource');
        $repositoryName = $baseName . $this->getParameter($this->application, 'suffixRepository');

        $relatedModels = '';
        $referencedModel = '';
        $relatedLists = '';
        $relatedArray = "\t\t\t\"" . $this->nameToCamelCase($tableName) . "\" => new {$resourceName}(\$data),\n";
        $listFields = "\t\t\t'$tableName.id',\n";
        $joins = '';

        $modelUses = "use App\Models\\$modelName;\n";
        $repositoriesUses = "use App\Http\Repositories\\$repositoryName;\n";
        $resourcesUses = "use App\Http\Resources{$apiVersion}\\$resourceName;\n";

        // Process each attribute
        dump($attributes);
        $parentId = '';
        foreach ($attributes as $field) {
            // if field name starts with 'rel_' it has to create a new Controller with this tableName as parent
            if (isset($field['columnName']) && substr($field['columnName'], 0, 4) == 'rel_') {
                $childTable = str_replace("rel_", "", $field['columnName']);
                $this->generateController($this->application, $childTable, $tableName);
                continue;
            }

            if (isset($field['columnName']) && $field['columnName'] == 'id') {
                continue;
            } else {
                $fieldName = $field['columnName'];

                if (isset($field['type']) && $field['type'] == 'field_fk') {
                    $referencedModel = $this->nameToPascalCase($field['references']) . $this->getParameter($this->application, 'suffixModel');
                    $relationName = $this->fkToCamelCase($fieldName);   // Ejemplo: tercero_despacho_id se convierte en TerceroDespacho 

                    // Para cada relacion de tablas FK debemos usar su modelo (sin repetir)
                    $modelUses .= strpos($modelUses, "use App\Models\\$referencedModel;\n") === false ? "use App\Models\\$referencedModel;\n" : "";
                    // nombre de las relaciones con otras tablas para la conuls del metodo list (with)
                    $relatedModels .= "'$referencedModel',";

                    // los campos de la consulta del index, deben excluir el campo id de la tabla padre
                    if (isset($field['references']) && $field['references'] != $parentTable) {
                        // INITFORMCOMPONENT
                        // para el initForm necesitamos una consulta de lista desplegable para cada tabla relacionada
                        $relatedLists .= "\t\t\$$relationName = $referencedModel::select('id','name')->get();\n";
                        $relatedArray .= "\t\t\t\"$relationName\" => \$$relationName,\n";
                    } else {
                        // si es el id de la tabla padre, lo guardamos en una variable para usarlo luego
                        $parentId = $field['columnName'];
                    }
                } else {
                    $listFields .= "\t\t\t'$tableName.$fieldName',\n";
                }
            }
        }
        $joins = substr($joins, 0, strlen($joins) - 1);
        $relatedArray = substr($relatedArray, 0, strlen($relatedArray) - 1);

        if (!empty($relatedModels)) {
            $relatedModels = "->with($relatedModels)";
        }

        // Generate the controller content
        return <<<EOD
<?php

namespace App\Http\Controllers{$apiFolder}{$apiVersion};

use App\Http\Controllers{$apiFolder}\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Traits\IndexTrait;

// Models
$modelUses
// Repositories
$repositoriesUses
// Resources
$resourcesUses


class {$controllerName} extends ApiController
{
    use IndexTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request \$request)
    {
        \$repo = new {$repositoryName}();
        return \$repo->get{$baseName}List(\$request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        \$dataIn = \$request->all();
        \$dataIn['status'] = true;

        // insert the new record into the database
        \$result = {$modelName}::create(\$dataIn);

        // send a successful response
        return \$this->successResponse(
            \$code = Response::HTTP_CREATED,
            \$data = new {$resourceName}(\$result),
            \$message = 'Record created successfully.',
            \$pastId = null,
            \$dataIn
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Models\{$modelName}  \${$modelName}
     * @return \Illuminate\Http\Response
     */
    public function update(\$id, Request \$request)
    {
        // Find the id into the database using its model
        \$result = {$modelName}::find(\$id);
        // if not found, return a 404 response
        if (is_null(\$result)) {
            // send an error response
            return \$this->errorResponse(
                \$code = Response::HTTP_NOT_FOUND,
                \$data = null,
                \$message = 'Record not found',
                \$errors = null,
                \$pastId = \$id,
                \$dataIn = \$request->all()
            );
        } else {

            // We have to be sure that the variables contain its values before to assign them
            \$result->update(\$request->all());

            // send a successful response
            return \$this->successResponse(
                \$code = Response::HTTP_OK,
                \$data = new {$resourceName}(\$result),
                \$message = 'Record updated successfully.',
                \$pastId = \$id,
                \$dataIn = \$request->all()
            );
        }
    }

    /**
     * Update or create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Models\{$modelName}  \${$modelName}
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate(\$id, Request \$request)
    {
        // Find the parent id from the request
        \$parentId = \$request->get('$parentId');
        // Retrieve the record based on parent id
        \$result = {$modelName}::where('$parentId', \$parentId)->first(); 

        // if not found, create a new record
        if (is_null(\$result)) {
            \$dataIn = \$request->all();
            \$dataIn['status'] = true;

            // insert the new record into the database
            \$result = {$modelName}::create(\$dataIn);
            \$message = 'Record created successfully.';
        } else {
            \$result->update(\$request->all());
            \$message = 'Record updated successfully.';
        }
        // send a successful response
        return \$this->successResponse(
            \$code = Response::HTTP_OK,
            \$data = new {$resourceName}(\$result),
            \$message,
            \$pastId = \$id,
            \$dataIn = \$request->all()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\{$modelName}  \$seg_{$modelName}
     * @return \Illuminate\Http\Response
     */
    public function destroy(\$id)
    {
    // Find the id into the database using its model
        \$result = {$modelName}::find(\$id);

        // if not found, return a 404 response
        if (is_null(\$result)) {
            // send an error response
            return \$this->errorResponse(
                \$code = Response::HTTP_NOT_FOUND,
                \$data = null,
                \$message = 'Record not found to delete.',
                \$errors = null,
                \$pastId = \$id,
                \$dataIn = null
            );
        } else {
            // we can save the result's main description to send a better response to the user
            \$description = \$result->name ?? '';

            // delete the record and send a successful response
            \$result->delete();

            // send a successful response
            return \$this->successResponse(
                \$code = Response::HTTP_OK,
                \$data = null,
                \$message = 'The record ' . \$description . ' has been deleted successfully',
                \$pastId = \$id,
                \$dataIn = null
            );
        }
    }

    /**
     * Change status for the specified resource from storage.
     *
     * @param  \App\Models\{$modelName} 
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(\$id)
    {
        // Find the id into the database using its model
        \$result = {$modelName}::find(\$id);

        // if not found, return a 404 response
        if (is_null(\$result)) {
            // send an error response
            return \$this->errorResponse(
                \$code = Response::HTTP_NOT_FOUND,
                \$data = null,
                \$message = 'Record not found to change status.',
                \$errors = null,
                \$pastId = \$id,
                \$dataIn = null
            );
        } else {
            // we need to flip the status value
            \$result->status = !\$result->status;

            // save the record and send a successful response
            \$result->save();

            // send a successful response
            return \$this->successResponse(
                \$code = Response::HTTP_OK,
                \$data = new {$resourceName}(\$result),
                \$message = 'Status changed successfully.',
                \$pastId = \$id,
                \$dataIn = null
            );
        }
    }

    public function initForm(Request \$request)
    {
        //tabla de principal debe devolver sus campos sin hacer join de los foreingkey
        \$id = \$request->get('id');
        \$data =  ((isset(\$id) and !is_null(\$id)) ? {$modelName}::where('id', '=', \$id)->first()
            : new {$modelName}());

        //por cada FK que tenga la tabla principal hacer una consulta independiente a esa maestra con los campos
        //ID y Nombre
$relatedLists
        \$respuesta = [
$relatedArray
        ];
        return response(\$respuesta, Response::HTTP_OK);
    }
}


EOD;
    }

    protected function saveControllerFile($controllerName, $controllerContent)
    {
        $suffix = $this->getParameter($this->application, 'suffixController');
        $apiFolder = $this->getParameter($this->application, 'apiFolder');
        $apiFolder = !empty($apiFolder) ? '/' . $apiFolder : $apiFolder;

        $apiVersion = $this->getParameter($this->application, 'apiVersion');
        $apiVersion = !empty($apiVersion) ? '/' . $apiVersion : $apiVersion;

        $controllerPath = app_path('/Http/Controllers' . $apiFolder . $apiVersion);
        $this->createDirectoryIfNotExists($controllerPath);

        $controllerFile = $controllerPath . '/' . $controllerName . $suffix . '.php';

        File::put($controllerFile, $controllerContent);
    }
}

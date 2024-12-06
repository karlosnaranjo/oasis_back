<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class RequestService
{
    use UtilTrait;

    public $application = '';
    public $validationRules = '';
    public $validationMessages = '';

    public function generateRequest($application, $tableName)
    {
        $this->application = $application;
        // Generate the Request file
        $attributes = $this->getJsonFile($tableName, 'form');
        if (!$attributes) {
            return false;
        }

        // Create Request content
        $requestContent = $this->buildRequestContent($tableName, $attributes);
        $requestName = $this->nameToPascalCase($tableName);


        // Save request file
        $this->saveRequestFile($requestName, $requestContent);
        return true;
    }

    protected function buildRequestContent($tableName, $attributes)
    {
        $modelName = $this->nameToPascalCase($tableName);

        $this->validationRules = '';
        $this->validationMessages = '';

        // Process each attribute
        $this->traverseJson($attributes);

        $this->validationRules = substr($this->validationRules, 0, strlen($this->validationRules) - 1);
        $this->validationMessages = substr($this->validationMessages, 0, strlen($this->validationMessages) - 1);

        // Generate the request content
        return <<<EOD
<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

// Use the ApiResponserTrait to get a standard response in whole application
use App\Traits\ApiResponserTrait;

class {$modelName}Request extends FormRequest
{
    use ApiResponserTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
$this->validationRules
        ];
    }

    /**
     * When validation fails, show the response with the validation errors
     *
     * @param  \Illuminate\Contracts\Validation\Validator  \$validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator \$validator)
    {
       throw new HttpResponseException(
            \$this->errorResponse(
               [ 
               "code" => Response::HTTP_NOT_FOUND,
               "data" => null,
               "message" => 'Validation errors',
               "errors" => \$validator->errors(),
               "pastId" => null,
               "dataIn" => \$this->all()
               ],
               Response::HTTP_NOT_FOUND
            )
        );
    }

    public function messages()
    {
        return [
$this->validationMessages
        ];
    }
}

EOD;
    }

    protected function traverseJson($json)
    {
        foreach ($json as $element) {
            switch ($element['type']) {
                case 'column':
                    //$this->renderField($element, $formParent);
                    if ((isset($element['data']) && $element['data'] == 'id') || (isset($element['required']) && $element['required'] == "false")) {
                        break;
                    } else {
                        $this->validationRules .= "\t\t\t\"{$element['data']}\" => \"required\",\n";
                        $this->validationMessages .= "\t\t\t\"{$element['data']}.required\" => \"{$element['label']} es requerido\",\n";
                    }

                    break;

                case 'rel':
                    // if field name starts with 'rel_' it has to create a new Request
                    if (isset($element['data']) && substr($element['data'], 0, 4) == 'rel_') {
                        $childTable = str_replace("rel_", "", $element['data']);
                        $this->generateRequest($this->application, $childTable);
                    }
                    break;

                case 'children':
                    $this->traverseJson($element['children']);
                    break;
                default:
                    if (isset($element['children'])) {
                        $this->traverseJson($element['children']);
                        break;
                    }
            }
        }
        return;
    }

    protected function saveRequestFile($requestName, $requestContent)
    {
        $suffix = $this->getParameter($this->application, 'suffixRequest');
        $version = $this->getParameter($this->application, 'version');
        $version = (!empty($version) ? "/" . $version : '');

        $requestPath = app_path('/Http/Requests' . $version);

        $this->createDirectoryIfNotExists($requestPath);

        $requestFile = $requestPath . '/' . $requestName . $suffix . '.php';

        File::put($requestFile, $requestContent);
    }
}

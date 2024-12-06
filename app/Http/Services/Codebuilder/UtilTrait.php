<?php

namespace App\Http\Services\Codebuilder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait UtilTrait
{

    // Almacena la ruta al archivo JSON de parámetros
    protected $parametersFile;
    // Almacena los parámetros cargados
    protected $parameters = [];

    public function __construct()
    {
        // Inicializar la ruta al archivo JSON
        $this->parametersFile = database_path('migrations/json/parameters.json');

        // Cargar parámetros desde el archivo JSON una sola vez
        if (file_exists($this->parametersFile)) {
            $this->parameters = json_decode(file_get_contents($this->parametersFile), true);
        } else {
            echo 'No existe el archivo de parametros';
        }
    }

    /**
     * Obtener el valor de un parámetro para una aplicación específica.
     *
     * @param string $application Nombre de la aplicación (e.g., 'paco', 'plannerx').
     * @param string $parameter Nombre del parámetro que se desea obtener.
     * @return mixed Valor del parámetro o false si no existe.
     */
    public function getParameter($application, $parameter)
    {
        if (isset($this->parameters[$application]) && array_key_exists($parameter, $this->parameters[$application])) {
            return $this->parameters[$application][$parameter];
        }
        return '';
    }

    // Convierte a label con iniciales mayusculas, con espacios "Palabra1 Palabra2 Palabra3"
    protected function nameToLabel($name)
    {
        // Convierte todo el nombre de la tabla a minúsculas
        $name = strtolower($name);

        // Reemplaza los guiones bajos con espacios
        $name = str_replace('_', ' ', $name);

        // Convierte la primera letra de cada palabra a mayúscula
        $name = ucwords($name);

        return $name;
    }

    // Convierte a camelCase con primera inicial minúscula, las demas mayusculas, sin espacios "palabra1Palabra2Palabra3"
    protected function nameToCamelCase($name)
    {
        // Convierte todo el nombre a palabras separadas con Mayusculas iniciales
        $name = $this->nameToLabel($name);

        // Reemplaza los espacios con nada para obtener la notación PascalCase
        $name = str_replace(' ', '', $name);

        // Convierte la primera letra del resultado a minúscula
        $name = lcfirst($name);
        return $name;
    }

    // Convierte a PascalCase con cada inicial mayúscula, sin espacios "Palabra1Palabra2Palabra3"
    protected function nameToPascalCase($name)
    {
        // Convierte todo el nombre a palabras separadas con Mayusculas iniciales
        $name = $this->nameToLabel($name);

        // Reemplaza los espacios con nada para obtener la notación PascalCase
        $name = str_replace(' ', '', $name);

        return $name;
    }

    // Convierte a PascalCase con cada inicial mayúscula, sin espacios "Palabra1Palabra2Palabra3"
    protected function fkToCamelCase($name)
    {
        // quitamos la terminación "_id"
        $name = substr($name, 0, strlen($name) - 3);

        // Convierte todo el nombre a palabras separadas con Mayusculas iniciales (PascalCase)
        $name = $this->nameToCamelCase($name);

        return $name;
    }

    protected function getJsonFile($tableName, $jsonType)
    {
        $attributesFile = $tableName . "_$jsonType.json";
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $attributesFilePath = database_path('migrations/json/' . $attributesFile);
        if (!file_exists($attributesFilePath)) {
            echo 'The specified attributes file does not exist in json folder.' . $attributesFilePath . PHP_EOL;
            return false;
        }

        // Leer el contenido del archivo
        $attributesContent = file_get_contents($attributesFilePath);

        // Convertir el contenido del archivo JSON a un array
        $attributes = json_decode($attributesContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'Invalid JSON string for attributes.';
            return false;
        }
        return $attributes;
    }

    protected function createDirectoryIfNotExists($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        if (!is_writable($path)) {
            throw new \Exception("El directorio no tiene permisos de escritura: $path");
        }
        return $path;
    }


    function pluralize_spanish($word)
    {
        $pluralRules = [
            // Sustantivos terminados en z
            '/(z)$/i' => '\1ces',          // lápiz -> lápices
            // Sustantivos terminados en vocal
            '/([aeiouáéíóú])$/i' => '\1s',      // casa -> casas, café -> cafés
            // Sustantivos terminados en í, ú tónicas
            '/([íú])$/i' => '\1es',        // tabú -> tabúes
            // Sustantivos terminados en consonante (excepto z) y vocal no acentuada
            '/([^aeiouáéíóú])$/i' => '\1es', // papel -> papeles, flor -> flores
            // Sustantivos terminados en és, án, ón tónicas
            '/(és|án|ón)$/i' => '\1es',    // inglés -> ingleses, capitán -> capitanes
        ];

        foreach ($pluralRules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    function singularize_spanish($word)
    {
        $singularRules = [
            // Sustantivos terminados en ces
            '/(ces)$/i' => 'z',              // lápices -> lápiz
            // Sustantivos terminados en vocal + s
            '/([aeiouáéíóú])s$/i' => '\1',      // casas -> casa, cafés -> café
            // Sustantivos terminados en íes, úes
            '/([íú])es$/i' => '\1',        // tabúes -> tabú
            // Sustantivos terminados en consonante + es
            '/([^aeiouáéíóú])es$/i' => '\1', // papeles -> papel, flores -> flor
            // Sustantivos terminados en éses, ánes, ónos
            '/(éses|anes|ones)$/i' => '\1',    // ingleses -> inglés, capitanes -> capitán
        ];

        foreach ($singularRules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    // Ejemplos de uso
    /* echo pluralize('lápiz') . PHP_EOL;  // lápices
echo pluralize('casa') . PHP_EOL;   // casas
echo pluralize('tabú') . PHP_EOL;   // tabúes
echo pluralize('papel') . PHP_EOL;  // papeles
echo pluralize('flor') . PHP_EOL;   // flores
echo pluralize('capitán') . PHP_EOL; // capitanes
echo pluralize('inglés') . PHP_EOL; // ingleses

echo singularize('lápices') . PHP_EOL; // lápiz
echo singularize('casas') . PHP_EOL;   // casa
echo singularize('tabúes') . PHP_EOL;  // tabú
echo singularize('papeles') . PHP_EOL; // papel
echo singularize('flores') . PHP_EOL;  // flor
echo singularize('capitanes') . PHP_EOL; // capitán
echo singularize('ingleses') . PHP_EOL; // inglés */
}

<?php

namespace App\Console\Commands;

use App\Http\Services\Codebuilder\ModelService;
use App\Http\Services\Codebuilder\RequestService;
use App\Http\Services\Codebuilder\RepositoryService;
use App\Http\Services\Codebuilder\ResourceService;
use App\Http\Services\Codebuilder\ControllerService;
use App\Http\Services\Codebuilder\SeederService;
use Illuminate\Console\Command;

class CreateLaravelBackCommand extends Command
{
    protected $signature = 'back:laravel
                        {application : Name of the application}
                        {table : Name of the table}
                        {label : Label of the table}';

    protected $description = 'Create the back-end laravel files with the information provided as parameters';

    protected $modelService;
    protected $requestService;
    protected $repositoryService;
    protected $resourceService;
    protected $controllerService;
    protected $seederService;



    public function __construct(
        ModelService $modelService,
        RequestService $requestService,
        RepositoryService $repositoryService,
        ResourceService $resourceService,
        ControllerService $controllerService,
        SeederService $seederService
    ) {
        parent::__construct();

        $this->modelService = $modelService;
        $this->requestService = $requestService;
        $this->repositoryService = $repositoryService;
        $this->resourceService = $resourceService;
        $this->controllerService = $controllerService;
        $this->seederService = $seederService;
    }

    public function handle()
    {

        $application = $this->arguments()['application'];
        $table = $this->arguments()['table'];
        $label = $this->arguments()['label'];

        /********************
        //    M O D E L
         *********************/
        $result = $this->modelService->generateModel($application, $table, null);
        if ($result) {
            $this->info('Modelo creado con éxito');
        } else {
            $this->info('Modelo no creado');
        }

        /**********************
        // R E Q U E S T
         ***********************/
        $result = $this->requestService->generateRequest($application, $table, null);
        if ($result) {
            $this->info('Request creado con éxito');
        } else {
            $this->info('Request no creado');
        }

        /**********************
        // R E P O S I T O R Y
         ***********************/
        $result = $this->repositoryService->generateRepository($application, $table, null);
        if ($result) {
            $this->info('Repositorio creado con éxito');
        } else {
            $this->info('Repositorio no creado');
        }

        /**********************
        // R E S O U R C E
         ***********************/
        $result = $this->resourceService->generateResource($application, $table, null);
        if ($result) {
            $this->info('Resource creado con éxito');
        } else {
            $this->info('Resource no creado');
        }

        /**********************
        // C O N T R O L L E R
         ***********************/
        $result = $this->controllerService->generateController($application, $table, null);
        if ($result) {
            $this->info('Controlador creado con éxito');
        } else {
            $this->info('Controlador no creado');
        }

        /**********************
        // S E E D E R S
         ***********************/
        $result = $this->seederService->generatePermissionsSeeder($table, $label);
        if ($result) {
            $this->info('Seeders creados con éxito');
        } else {
            $this->info('Seeders no creados');
        }
        return 0;
    }
}

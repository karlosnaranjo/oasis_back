<?php

use Illuminate\Support\Facades\Route;

// We have to add the controller routes to be used
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\DrugsController;
use App\Http\Controllers\API\V1\EmployeesController;
use App\Http\Controllers\API\V1\EvaluationsController;
use App\Http\Controllers\API\V1\EvolutionsController;
use App\Http\Controllers\API\V1\PatientsController;
use App\Http\Controllers\API\V1\PhasesController;
use App\Http\Controllers\API\V1\PsychologiesController;
use App\Http\Controllers\API\V1\PsychologyDrugsController;
use App\Http\Controllers\API\V1\PsychologyRelativesController;
use App\Http\Controllers\API\V1\RelativesController;
use App\Http\Controllers\API\V1\RoleController;
use App\Http\Controllers\API\V1\RolePermissionController;
use App\Http\Controllers\API\V1\TargetsController;
use App\Http\Controllers\API\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Please, Open the app\providers\RouteServiceProvider  file, method mapApiRoutes(),
| to see how the routes groups are built
*/


Route::post('registro', [AuthController::class, 'registro']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    /**
     * Ruta web para los usuarios
     */
    Route::post('perfil', [AuthController::class, 'perfil']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    //*********************************
    // Psychologies
    //*********************************
    Route::prefix('/psychologies')->group(function () {
        Route::get('', [PsychologiesController::class, 'index']);
        Route::get('/initform', [PsychologiesController::class, 'initForm']);
        Route::get('/{id}', [PsychologiesController::class, 'show']);
        Route::put('/{id}', [PsychologiesController::class, 'update']);
        Route::delete('/{id}', [PsychologiesController::class, 'destroy']);
        Route::post('', [PsychologiesController::class, 'store']);
        Route::put('changestatus/{id}', [PsychologiesController::class, 'changeStatus']);
    });

    //*********************************
    // Psychology_relatives
    //*********************************
    Route::prefix('/psychologies_relatives')->group(function () {
        Route::get('', [PsychologyRelativesController::class, 'index']);
        Route::get('/initform', [PsychologyRelativesController::class, 'initForm']);
        Route::get('/{id}', [PsychologyRelativesController::class, 'show']);
        Route::put('/{id}', [PsychologyRelativesController::class, 'update']);
        Route::delete('/{id}', [PsychologyRelativesController::class, 'destroy']);
        Route::post('', [PsychologyRelativesController::class, 'store']);
        Route::put('changestatus/{id}', [PsychologyRelativesController::class, 'changeStatus']);
    });

     //*********************************
    // Psychology_drugs
    //*********************************
    Route::prefix('/psychologies_drugs')->group(function () {
        Route::get('', [PsychologyDrugsController::class, 'index']);
        Route::get('/initform', [PsychologyDrugsController::class, 'initForm']);
        Route::get('/{id}', [PsychologyDrugsController::class, 'show']);
        Route::put('/{id}', [PsychologyDrugsController::class, 'update']);
        Route::delete('/{id}', [PsychologyDrugsController::class, 'destroy']);
        Route::post('', [PsychologyDrugsController::class, 'store']);
        Route::put('changestatus/{id}', [PsychologyDrugsController::class, 'changeStatus']);
    });

        //*********************************
    // phases
    //*********************************
    Route::prefix('/phases')->group(function () {
        Route::get('', [PhasesController::class, 'index']);
        Route::get('/initform', [PhasesController::class, 'initForm']);
        Route::get('/{id}', [PhasesController::class, 'show']);
        Route::put('/{id}', [PhasesController::class, 'update']);
        Route::delete('/{id}', [PhasesController::class, 'destroy']);
        Route::post('', [PhasesController::class, 'store']);
        Route::put('changestatus/{id}', [PhasesController::class, 'changeStatus']);
    });

    
    //*********************************
    // targets
    //*********************************
    Route::prefix('/targets')->group(function () {
        Route::get('', [TargetsController::class, 'index']);
        Route::get('/initform', [TargetsController::class, 'initForm']);
        Route::get('/{id}', [TargetsController::class, 'show']);
        Route::put('/{id}', [TargetsController::class, 'update']);
        Route::delete('/{id}', [TargetsController::class, 'destroy']);
        Route::post('', [TargetsController::class, 'store']);
        Route::put('changestatus/{id}', [TargetsController::class, 'changeStatus']);
    });

    //*********************************
    // relatives
    //*********************************
    Route::prefix('/relatives')->group(function () {
        Route::get('', [RelativesController::class, 'index']);
        Route::get('/initform', [RelativesController::class, 'initForm']);
        Route::get('/{id}', [RelativesController::class, 'show']);
        Route::put('/{id}', [RelativesController::class, 'update']);
        Route::delete('/{id}', [RelativesController::class, 'destroy']);
        Route::post('', [RelativesController::class, 'store']);
        Route::put('changestatus/{id}', [RelativesController::class, 'changeStatus']);
    });

    //*********************************
    // evolutions
    //*********************************
    Route::prefix('/evolutions')->group(function () {
        Route::get('', [EvolutionsController::class, 'index']);
        Route::get('/initform', [EvolutionsController::class, 'initForm']);
        Route::get('/{id}', [EvolutionsController::class, 'show']);
        Route::put('/{id}', [EvolutionsController::class, 'update']);
        Route::delete('/{id}', [EvolutionsController::class, 'destroy']);
        Route::post('', [EvolutionsController::class, 'store']);
        Route::put('changestatus/{id}', [EvolutionsController::class, 'changeStatus']);
    });

    
    //*********************************
    // evaluations
    //*********************************
    Route::prefix('/evaluations')->group(function () {
        Route::get('', [EvaluationsController::class, 'index']);
        Route::get('/initform', [EvaluationsController::class, 'initForm']);
        Route::get('/targets-by-phase', [EvaluationsController::class, 'targetsByPhase']);
        Route::get('/{id}', [EvaluationsController::class, 'show']);
        Route::put('/{id}', [EvaluationsController::class, 'update']);
        Route::delete('/{id}', [EvaluationsController::class, 'destroy']);
        Route::post('', [EvaluationsController::class, 'store']);
        Route::put('changestatus/{id}', [EvaluationsController::class, 'changeStatus']);
    });

    //*********************************
    // Drugs
    //*********************************
    Route::prefix('/drugs')->group(function () {
        Route::get('', [DrugsController::class, 'index']);
        Route::get('/initform', [DrugsController::class, 'initForm']);
        Route::get('/{id}', [DrugsController::class, 'show']);
        Route::put('/{id}', [DrugsController::class, 'update']);
        Route::delete('/{id}', [DrugsController::class, 'destroy']);
        Route::post('', [DrugsController::class, 'store']);
        Route::put('changestatus/{id}', [DrugsController::class, 'changeStatus']);
    });
    //*********************************
    // Employees
    //*********************************
    Route::prefix('/employees')->group(function () {
        Route::get('', [EmployeesController::class, 'index']);
        Route::get('/initform', [EmployeesController::class, 'initForm']);
        Route::get('/{id}', [EmployeesController::class, 'show']);
        Route::put('/{id}', [EmployeesController::class, 'update']);
        Route::delete('/{id}', [EmployeesController::class, 'destroy']);
        Route::post('', [EmployeesController::class, 'store']);
        Route::put('changestatus/{id}', [EmployeesController::class, 'changeStatus']);
    });
    //*********************************
    // Patients
    //*********************************
    Route::prefix('/patients')->group(function () {
        Route::get('', [PatientsController::class, 'index']);
        Route::get('/initform', [PatientsController::class, 'initForm']);
        Route::get('/{id}', [PatientsController::class, 'show']);
        Route::put('/{id}', [PatientsController::class, 'update']);
        Route::delete('/{id}', [PatientsController::class, 'destroy']);
        Route::post('', [PatientsController::class, 'store']);
        Route::put('changestatus/{id}', [PatientsController::class, 'changeStatus']);
    });

    // roles
    Route::prefix('/seguridad/roles')->group(function () {
        Route::get('', [RoleController::class, 'index']);
        Route::get('/autocomplete', [RoleController::class, 'autoComplete']);
        Route::get('/initFormComponent', [RoleController::class, 'initFormComponent']);
        Route::get('/{id}', [RoleController::class, 'find']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/permisos/{id}', [RoleController::class, 'getPermissions']);
        Route::delete('/permisos/{id}', [RoleController::class, 'destroyDirectPermissions']);
        Route::post('/permisos', [RoleController::class, 'storePermissions']);
    });

    // roles
    Route::prefix('/seguridad/rolespermissions')->group(function () {
        Route::get('/{rolId}', [RolePermissionController::class, 'index']);
        Route::get('/getPermissions/{id}', [RolePermissionController::class, 'getPermissions']);
        Route::delete('/', [RolePermissionController::class, 'destroy']);
        Route::post('/', [RolePermissionController::class, 'storePermissions']);
    });

    // roles
    Route::prefix('/seguridad/usuarios')->group(function () {
        Route::get('', [UserController::class, 'index']);
        Route::get('/autocomplete', [UserController::class, 'autoComplete']);
        Route::get('/initFormComponent', [UserController::class, 'initFormComponent']);
        Route::get('/{id}', [UserController::class, 'find']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/permisos/{id}', [UserController::class, 'getPermissions']);
        Route::delete('/permisos/{id}', [UserController::class, 'destroyDirectPermissions']);
        Route::post('/permisos', [UserController::class, 'storePermissions']);
    });
});

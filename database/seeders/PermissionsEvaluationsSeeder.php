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
            'name' => 'general:tra:evaluations:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Evaluaciones'
        ]);

        Permission::create([
            'name' => 'general:tra:evaluations:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Evaluaciones'
        ]);

        Permission::create([
            'name' => 'general:tra:evaluations:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Evaluaciones'
        ]);

        Permission::create([
            'name' => 'general:tra:evaluations:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Evaluaciones'
        ]);

        Permission::create([
            'name' => 'general:transactions:evaluations:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Evaluaciones'
        ]);
    }
};
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
            'name' => 'general:masters:employees:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Empleados'
        ]);

        Permission::create([
            'name' => 'general:masters:employees:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Empleados'
        ]);

        Permission::create([
            'name' => 'general:masters:employees:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Empleados'
        ]);

        Permission::create([
            'name' => 'general:masters:employees:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Empleados'
        ]);

        Permission::create([
            'name' => 'general:masters:employees:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Empleados'
        ]);
    }
};
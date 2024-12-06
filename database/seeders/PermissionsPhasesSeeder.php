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
            'name' => 'general:masters:phases:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Fases'
        ]);

        Permission::create([
            'name' => 'general:masters:phases:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Fases'
        ]);

        Permission::create([
            'name' => 'general:masters:phases:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Fases'
        ]);

        Permission::create([
            'name' => 'general:masters:phases:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Fases'
        ]);

        Permission::create([
            'name' => 'general:masters:phases:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Fases'
        ]);
    }
};
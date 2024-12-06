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
            'name' => 'general:masters:drugs:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Drogas'
        ]);

        Permission::create([
            'name' => 'general:masters:drugs:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Drogas'
        ]);

        Permission::create([
            'name' => 'general:masters:drugs:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Drogas'
        ]);

        Permission::create([
            'name' => 'general:masters:drugs:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Drogas'
        ]);

        Permission::create([
            'name' => 'general:masters:drugs:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Drogas'
        ]);
    }
};
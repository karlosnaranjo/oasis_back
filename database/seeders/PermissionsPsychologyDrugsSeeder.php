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
            'name' => 'general:transactions:psychology_drugs:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Cuadro de consumo'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_drugs:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Cuadro de consumo'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_drugs:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Cuadro de consumo'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_drugs:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Cuadro de consumo'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_drugs:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Cuadro de consumo'
        ]);
    }
};
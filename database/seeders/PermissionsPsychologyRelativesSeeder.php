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
            'name' => 'general:transactions:psychology_relatives:list',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Listado de Historia Familiar'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_relatives:create',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Creación de Historia Familiar'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_relatives:update',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Actualizar Historia Familiar'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_relatives:delete',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Eliminar Historia Familiar'
        ]);

        Permission::create([
            'name' => 'general:transactions:psychology_relatives:changeStatus',
            'guard_name' => 'api',
            'module' => ModulesEnum::GENERAL,
            'description' => 'Cambiar Estado de Historia Familiar'
        ]);
    }
};
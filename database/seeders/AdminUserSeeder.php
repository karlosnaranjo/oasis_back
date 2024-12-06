<?php

namespace Database\Seeders;

use App\Enums\ModulesEnum;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
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
        // Crear los permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            Permission::create([
                'name' => 'general:seguridad:roles:list',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Listado de roles'
            ]),
            Permission::create([
                'name' => 'general:seguridad:roles:create',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Creación de roles'
            ]),
            Permission::create([
                'name' => 'general:seguridad:roles:update',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Actualizar roles'
            ]),
            Permission::create([
                'name' => 'general:seguridad:roles:delete',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Eliminar roles'
            ]),
            Permission::create([
                'name' => 'general:seguridad:roles:changeStatus',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Cambiar Estado de roles'
            ]),
            Permission::create([
                'name' => 'general:seguridad:usuarios:list',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Listado de usuarios'
            ]),
            Permission::create([
                'name' => 'general:seguridad:usuarios:create',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Creación de usuarios'
            ]),
            Permission::create([
                'name' => 'general:seguridad:usuarios:update',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Actualizar usuarios'
            ]),
            Permission::create([
                'name' => 'general:seguridad:usuarios:delete',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Eliminar usuarios'
            ]),
            Permission::create([
                'name' => 'general:seguridad:usuarios:changeStatus',
                'guard_name' => 'sanctum',
                'module' => ModulesEnum::ADMINISTRATION,
                'description' => 'Cambiar Estado de usuarios'
            ]),
        ];

        dump($permissions);
        // Crear el rol de administrador y asignar los permisos
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'sanctum',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // $adminRole->syncPermissions($permissions);

        // Crear el usuario administrador
        $adminUser = User::firstOrCreate([
            'email' => 'admin',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('Renovatio3241'),
            'role_id' => $adminRole->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asignar el rol de administrador al usuario
        //$adminUser->assignRole($adminRole);

        // Obtener todos los IDs de permisos
        $permissionIds = DB::table('permissions')->pluck('id')->toArray();

        // Preparar los datos para la inserción masiva
        $roleId = $adminRole->id;
        $rolePermissions = array_map(function ($permissionId) use ($roleId) {
            return [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ];
        }, $permissionIds);

        // Insertar en la tabla role_has_permissions
        DB::table('role_has_permissions')->insert($rolePermissions);
    }
};

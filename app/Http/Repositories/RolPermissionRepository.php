<?php

namespace App\Http\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Class AtributoRepository.
 *
 * @author Julian Osorio
 */
class RolPermissionRepository
{

    /**
     * Este metodo retorna la informacio necesaria para listar los permisos.
     */
    public static function byRoleQuery($roleId, $selectFields)
    {
        return Permission::select($selectFields)
            ->whereNotIn('id', function ($query) use ($roleId) {
                $query->select('permission_id')->from('role_has_permissions')->where('role_id', $roleId);
            })
            ->orderBy('name');
    }

    /**
     * Este metodo retorna la informacio necesaria para listar los permisos.
     *
     * @return Permission
     */
    public static function byUserQuery($rolId, $selectFields)
    {
        $fields = array_merge($selectFields, ['permissions.name', 'permissions.description']);

        return Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id', $rolId)
            ->select($fields);
    }

    /**
     * Este metodo retorna los permisos que tiene un usuario en la app de produccion.
     *
     * @param $userId
     * @return Permission
     */
    public static function permissionsAppByUser($user)
    {
        $ids = implode(',', $user->getAllPermissions()->pluck('id')->toarray());

        return Permission::whereRaw("name like 'app%'")
            ->whereRaw("id IN ($ids)")
            ->pluck('name');
    }

    /**
     * Este metodo retorna la informacio necesaria para listar los permisos.
     *
     * @return Permission
     */
    public static function getAllPermissionByRolId($rolId)
    {
        return Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id', $rolId)
            ->select('permissions.name', 'permissions.description');
    }
}

<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\PaginateHelper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\RoleRepository;
use App\Http\Repositories\RolPermissionRepository;
use App\Http\Requests\V1\DeletePermissionsRequest;
use App\Http\Requests\V1\StorePermissionsRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\IndexTrait;

class RolePermissionController extends Controller
{
    use IndexTrait;
    /**
     * @return
     */
    public function index($roleId, Request $request)
    {
        $fields = [
            'id',
            'name',
            'description',
        ];
        $query = RolPermissionRepository::byUserQuery($roleId, $fields);
        return $this->indexGrid($request, $query);
    }


    /* public function index($roleId, Request $request)
    {
        $fields = [
            'id',
            'name',
            'description',
        ];
        return response(PaginateHelper::returnDataPaginate(RolPermissionRepository::byUserQuery($roleId, $fields), $request, $fields));
    } */

    /**
     * @return
     */
    public function destroy(Request $request)
    {
        $name = $request->get('name');
        $permission  = Permission::where('name', $name)->first();
        if (!$permission) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        try {
            DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response($permission, Response::HTTP_OK);
    }

    /**
     * @return
     */
    public function getPermissions($id, Request $request)
    {
        $fields = [
            'id',
            'name',
            'description',
        ];
        return Permission::select($fields)
            ->whereNotIn('id', function ($query) use ($id) {
                $query->select('permission_id')->from('role_has_permissions')->where('role_id', $id);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * @return
     */
    public function storePermissions(StorePermissionsRequest $request)
    {
        $id = $request->get('roleId', null);
        $role = Role::findById($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $permisions = $request->get('permissions');
        DB::beginTransaction();
        try {
            foreach ($permisions as $value) {
                $permission  = Permission::where('name', $value)->first();
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $id,
                ]);
            }
            // $role->givePermissionTo($permisions);
            DB::commit();

            return response($role, Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();

            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return
     */
    public function destroyDirectPermissions($id, DeletePermissionsRequest $request)
    {
        $role = Role::findById($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $role->revokePermissionTo($request->validated());

        return response($role, Response::HTTP_OK);
    }

    /**
     * @return
     */
    public function autoComplete(Request $request)
    {
        $search = $request->get('search');
        $limit = $request->get('limit', 20);

        return response(RoleRepository::autoComplete($search, $limit), Response::HTTP_OK);
    }

    /**
     *
     * @return
     */
    public function initFormComponent(Request $request)
    {
        $id = $request->get('id');
        $role = $id ? Role::find($id) : new Role();
        return $role;
    }
}

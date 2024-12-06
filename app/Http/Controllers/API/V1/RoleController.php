<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\PaginateHelper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\RoleRepository;
use App\Http\Requests\DeletePermissionsRequest;
use App\Http\Requests\StorePermissionsRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Traits\IndexTrait;


class RoleController extends Controller
{
    use IndexTrait;
    /**
     * @OA\Get(
     *     path="/seguridad/roles",
     *     summary="Endpoint para recuperar todos los registros paginadas",
     *     tags={"Seguridad"},
     *     description="Obtiene los registros paginados segun especificaciones del Json",
     *     security={{"passport": {}}},
     *
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="(Opcional) - filas por pagina",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="(Opcional) - Desde que fila extraer los registros",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */

    public function index(Request $request)
    {
        $query = Role::query();
        return $this->indexGrid($request, $query);
    }

    /* public function index(Request $request)
    {
        $fields = [
            'id',
            'name',
        ];

        return response(PaginateHelper::returnDataPaginate(RoleRepository::indexQuery($fields), $request, $fields));
    } */

    /**
     * @OA\Get(
     *     path="/seguridad/roles/{id}",
     *     summary="Endpoint para recuperar permisos según su id",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id del registro que desea consultar",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|\Spatie\Permission\Contracts\Role
     */
    public function find($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $role;
    }

    /**
     * @OA\Put(
     *     path="/seguridad/roles/{id}",
     *     summary="Endpoint para actualizar los roles según su id",
     *     tags={"Seguridad"},
     *     description="Permite actualizar los roles",
     *     security={{"passport": {}}},
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="Body",
     *                     type="string"
     *                 ),
     *                 example={"name":"rol_user"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|\Spatie\Permission\Contracts\Role
     */
    public function update($id, Request $request)
    {
        $role = Role::find($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $role->fill($request->all());
        $role->save();

        return $role;
    }

    /**
     * @OA\Delete(
     *     path="/seguridad/roles/{id}",
     *     summary="Endpoint para elimiar un permiso según su id",
     *     tags={"Seguridad"},
     *     description="Elimina los roles según el id",
     *     security={{"passport": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy($id)
    {
        $role = Role::findById($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $users = User::role($role)->get();
        try {
            foreach ($users as $user) {
                $user->removeRole($role);
            }
            $role->delete();
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response($role, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/seguridad/roles",
     *     summary="Endpoint para crear",
     *     tags={"Seguridad"},
     *     description="Crea, verificar Json",
     *     security={{"passport": {}}},
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="Body",
     *                     type="string"
     *                 ),
     *                 example={"name":"permiso"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Response
     */
    public function store(Request $request)
    {
        $nombre = $request->get('name');
        $role = Role::where('name', $nombre)->first();
        if ($role) {
            return response(['message' => 'Ya existe el rol'], Response::HTTP_CONFLICT);
        }
        return Role::create(['name' => $nombre]);
    }

    /**
     * @OA\Get(
     *     path="/seguridad/roles/permisos/{id}",
     *     summary="Endpoint para recuperar todos los permisos de un rol",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|\Illuminate\Support\Collection
     */
    public function getPermissions($id, Request $request)
    {
        $role = Role::findById($id);
        if (!$role) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $fields = [
            'permissions.id',
            'permissions.name',
            'permissions.description',
            'permissions.module',
        ];

        $modulos = [];

        $permisos = $role->permissions()->select($fields)
            ->orderBy('permissions.module')
            ->get();

        $permisos->each(function ($row) use (&$modulos) {
            $key = $row->module;
            if (!array_key_exists($key, $modulos)) {
                $modulos[$key] = new \stdClass();
                $modulos[$key]->nombre = $row->module;
                $modulos[$key]->detalles = collect();
            }
            $modulos[$key]->detalles->push($row);
        });

        return array_values($modulos);
    }

    /**
     * @OA\Post(
     *     path="/seguridad/roles/permisos/{id}",
     *     summary="Endpoint para asignar permisos a un rol",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
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
            $role->givePermissionTo($permisions);
            DB::commit();

            return response($role, Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();

            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/seguridad/roles/permisos/{id}",
     *     summary="Endpoint para eliminar los permisos de un usuario",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
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
     * @OA\Get(
     *     path="/seguridad/roles/autocomplete",
     *     summary="Endpoint para autocompletar los roles de un usuario",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function autoComplete(Request $request)
    {
        $search = $request->get('search');
        $limit = $request->get('limit', 20);

        return response(RoleRepository::autoComplete($search, $limit), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/seguridad/initFormComponent/{id}",
     *     summary="Endpoint para recuperar permisos según su id",
     *     tags={"Seguridad"},
     *     description="",
     *     security={{"passport": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id del registro que desea consultar",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|\Spatie\Permission\Contracts\Role
     */
    public function initFormComponent(Request $request)
    {
        $id = $request->get('id');
        $role = $id ? Role::find($id) : new Role();
        return $role;
    }
}

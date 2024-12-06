<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\PaginateHelper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\V1\InitFormRequest;
use App\Http\Requests\V1\StoreRequest;
use App\Http\Requests\V1\UpdateRequest;
use App\Models\User;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Traits\IndexTrait;

class UserController extends Controller
{
    use IndexTrait;
    /**
     * @OA\Get(
     *     path="/seguridad/usuarios",
     *     summary="Endpoint para recuperar todas las registros paginados",
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
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fields = [
            'id',
            'name',
            'email'
        ];

        $query = UserRepository::indexQuery($fields);
        return $this->indexGrid($request, $query);
    }

    /* public function index(Request $request)
    {
        $fields = [
            'id',
            'name',
            'email'
        ];

        return response(PaginateHelper::returnDataPaginate(UserRepository::indexQuery($fields), $request, $fields));
    } */

    /**
     * @OA\Post(
     *     path="/seguridad/usuarios",
     *     summary="Endpoint para crear los usuarios",
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
     *                 example={"username":"username","name":"usuario","email":"email@d.com","password":"12345678"}
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
     * @return \App\Modules\Seguridad\Models\User|Application|ResponseFactory|\Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function store(StoreRequest $request)
    {
        $user = UserFactory::init();
        $user->fill($request->validated());
        $user->password = bcrypt($user->password);
        try {
            $user->save();
            return $user;
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * @OA\Put(
     *     path="/seguridad/usuarios",
     *     summary="Endpoint para actualizar los usuarios",
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
     *                 example={"username":"username","name":"usuario","email":"email@d.com"}
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
     * @return Application|ResponseFactory|Response
     */
    public function update($id, UpdateRequest $request)
    {
        $user = User::find($id);
        if (!$user) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $user->fill($request->validated());
        DB::beginTransaction();
        try {
            $user->password = bcrypt($user->password);
            $user->save();
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();

            return $this->internalErrorResponse($e);
        }
    }

    protected function internalErrorResponse(Exception $e)
    {
        // Registrar el error
        //Log::error('Error interno en la actualización: ' . $e->getMessage());

        // Devolver una respuesta con un código HTTP 500
        return response()->json([
            'message' => 'Ocurrió un error inesperado. Inténtalo de nuevo más tarde.',
            'error' => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $datos = User::find($id);

        if (!$datos) {
            return response(['message' => 'El Registro no fue encontrado'], Response::HTTP_NOT_FOUND);
        }
        $datos->delete();
        return response(['message' => 'Registro eliminado correctamente'], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/seguridad/usuarios/initFormComponent",
     *     summary="Entrega los datos básicos para el componente formulario",
     *     tags={"Seguridad"},
     *     description="Entrega los datos para el componente formulario de usuario ya sea para crear o para editar",
     *     security={{"passport": {}}},
     *
     *     @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         description="El id del usuario que se está editando o nada si se está creando.",
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="La solicitud fué completada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró el registro indicado"
     *     )
     * )
     *
     * @return Application|ResponseFactory|Response
     */
    public function initFormComponent(InitFormRequest $request)
    {
        $usuarioId = $request->get('id', null);
        $user = $usuarioId ? User::find($usuarioId) : UserFactory::init();
        if (!$user) {
            return response(['message' => 'No se encontró el registro.'], Response::HTTP_NOT_FOUND);
        }
        $roles = Role::orderBy('name')->get();

        return response([
            'user' => $user,
            'roles' => $roles,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/seguridad/usuarios/permisos",
     *     summary="Endpoint para recuperar todos los permisos de un usuario",
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
     * @return Application|ResponseFactory|Response
     */
    public function getPermissions($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(['message' => 'Registro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $user->getAllPermissions()->pluck('name');
    }

    /**
     * @OA\Put(
     *     path="/seguridad/usuarios/perfil/password",
     *     summary="Endpoint para actualizar los usuarios",
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
     *                 example={"username":"username","name":"usuario","email":"email@d.com"}
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
     * @return Application|ResponseFactory|Response
     */
    // public function updatePassword(UpdatePasswordlRequest $request)
    // {
    //     /** @var User $user */
    //     $user = Auth::user();
    //     $user->fill($request->validated());
    //     $user->password = bcrypt($user->password);
    //     $user->save();

    //     return response([], Response::HTTP_OK);
    // }

}

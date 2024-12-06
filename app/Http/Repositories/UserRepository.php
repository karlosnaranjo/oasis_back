<?php

namespace App\Http\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Class AtributoRepository.
 *
 * @author Julian Osorio
 */
class UserRepository
{
/**
     * Este metodo retorna la informacio necesaria para listar los usuarios.
     */
    public static function indexQuery($selectFields): Builder
    {
        return User::query()->select($selectFields)
            ->orderBy('name');
    }

    /**
     * Este metodo retorna la informacio necesaria para listar los usuarios.
     */
    public static function indexPortalQuery($selectFields, $terceroId): Builder
    {
        return User::query()->select($selectFields)
            ->whereIn('id', function ($query) use ($terceroId) {
                $query->select('user_id')->from('terceros_users_portal')->where('tercero_id', $terceroId);
            });
    }

    /**
     * Este metodo retorna la informacio necesaria para listar los usuarios.
     */
    public static function getDirectPermissions($userId, $selectFields): \Illuminate\Database\Query\Builder
    {
        return DB::table('model_has_permissions as mp')
            ->select($selectFields)
            ->join('permissions as p', 'mp.permission_id', '=', 'p.id')
            ->where('model_id', $userId);
    }

    /**
     * Esta función retorna los usuarios que estan activos,
     * dado el caso que un registro este inactivo pero se utiliza lo retorna tambien.
     */
    public static function getActivosOrIdIn(?array $orId = []): Collection
    {
        return User::query()->where('active', true)
            ->when($orId, function ($query) use ($orId) {
                $query->orWhereIn('id', $orId);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')->from('terceros_users_portal');
            })
            ->orderBy('username')
            ->get();
    }

    public static function autocomplete($search, $limit)
    {
        return User::query()->from('users as u')
            ->select(
                'u.id as value',
                'u.name as label',
            )
            ->whereNotIn('id', function ($query) {
                $query->select('user_id')->from('terceros_users_portal');
            })
            ->where('u.name', 'like', "%$search%")
            ->limit($limit)
            ->orderBy('u.name', 'ASC')
            ->get();
    }

    public static function autocompletePortal($search, $limit, $terceroId)
    {
        return User::query()->from('users as u')
            ->select(
                'u.id as value',
                'u.name as label',
            )
            ->whereIn('id', function ($query) use ($terceroId) {
                $query->select('user_id')->from('terceros_users_portal')->where('tercero_id', $terceroId);
            })
            ->where('u.name', 'like', "%$search%")
            ->limit($limit)
            ->orderBy('u.name', 'ASC')
            ->get();
    }

    public static function getUsersAndTokenDevice(int $userId)
    {
        return User::query()->from('users as u')
        ->join('users_token_device as utd', 'u.id', 'utd.user_id')
        ->where('u.id', $userId)
        ->get();
    }

    /**
     * @return int id del usuario
     *
     * @throws Exception
     */
    public static function getActualUserId(): int
    {
        /** @var User */
        $user = Auth::user();
        if (empty($user)) {
            throw new Exception('No se encontro el usuario actual');
        }

        return $user->id;
    }

    /**
     * @return User modelo del usuario
     *
     * @throws Exception
     */
    public static function getActualUser(): User
    {
        /** @var User */
        $user = Auth::user();
        if (empty($user)) {
            throw new Exception('No se encontro el usuario actual');
        }

        return $user;
    }

    public static function getRolByUser($user_id){
        return DB::table('users as u')
        ->join('model_has_roles as mhr', 'u.id', 'mhr.model_id')
        ->join('roles as r', 'mhr.role_id', 'r.id')
        ->where('u.id', $user_id)
        ->select('mhr.role_id as role_id', 'r.name as name')
        ->first();
    }

}

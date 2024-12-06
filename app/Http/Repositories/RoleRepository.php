<?php

namespace App\Http\Repositories;
use App\Models\ColorModel;
use Spatie\Permission\Models\Role;

/**
 * Class RoleRepository.
 *
 * @author Karlos Naranjo
 */
class RoleRepository
{
    public static function indexQuery($selectFields)
    {
        return Role::select($selectFields);
    }

    public static function autoComplete($search, $limit)
    {
        return Role::query()->from('roles as r')
            ->select(
                'r.id as value',
                'r.name as label',
            )
            ->where('r.name', 'like', "%$search%")
            ->limit($limit)
            ->orderBy('r.name', 'ASC')
            ->get();
    }
}

<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function registro(Request $request)
    {
        try {
            //code...

            $request->validate([
                'email' => 'required|unique:users',
                'name' => 'required',
                'password' => 'required',
            ]);

            $user = new User();
            $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->save();
            return Response()->json(['message' => 'Logeado']);
        } catch (\Throwable $th) {
            return Response()->json(['message' => 'Error'], 401);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function login(Request $request)
    {
        $user = User::get();
        // dd($user);
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;

                $usuario = User::find($user->id);
                $permisions = Permission::from('permissions as p')
                    ->join('role_has_permissions as rp', 'p.id', 'rp.permission_id')
                    ->where('rp.role_id', $usuario->role_id)
                    ->pluck('p.name');
                return [
                    'message' => 'Se ha creado el token',
                    'token' => $token,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user' => $user,
                    'permissions' => $permisions
                ];
            } else {
                return Response()->json(['message' => 'Contraseña incorrecta'], 401);
            }
        } else {
            return Response()->json(['message' => 'Usuario no existe'], 401);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function perfil(Request $request)
    {
        return [
            'user' => Auth::user(),
            'permissions' => []
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Se ha cerrado la session']);
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function user(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $usuario = User::find($user->id);
        $permisions = Permission::from('permissions as p')
            ->join('role_has_permissions as rp', 'p.id', 'rp.permission_id')
            ->where('rp.role_id', $usuario->role_id)
            ->pluck('p.name');

        return [
            'user' => $usuario,
            'permissions' => $permisions
        ];

        // return [
        //     'user' => $user,
        //     'permissions' => RolPermissionRepository::getAllPermissionByRolId($user->id)
        // ];
    }
}

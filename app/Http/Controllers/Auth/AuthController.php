<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // Creación de un array con los roles que se pueden descartar
    private $discarded_role_names = ['prisoner'];



    // Función para el manejo del inicio de sesión
    public function login(Request $request)
    {

        // Validación de los datos de entrada
        $request -> validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);



        // Obtener un usuario
        $user = User::where('email', $request['email'])->first();


        // Valida lo siguiente
        //  * Si no existe un usuario
        //  * Si no tiene un estado
        //  * Verificar el rol del usuario existe en el array creado de roles descartados
        //  * Si no es el mismo password
        if (!$user || !$user->state || in_array($user->role->slug, $this->discarded_role_names) ||
            !Hash::check($request['password'], $user->password))
            {
                // Se invoca a la función padre
                return $this->sendResponse(message: 'The provided credentials are incorrect.', code: 404);
            }

            // Valida lo siguiente
        //  * Si el token de usurio no es vacío
        if (!$user->tokens->isEmpty())
        {
            // Se invoca a la función padre
            return $this->sendResponse(message: 'User is already authenticated.', code: 403);
        }

        // Se procede a la creación de un token para el usuario
        $token = $user->createToken('auth-token')->plainTextToken;

        // Se invoca a la función padre
        return $this->sendResponse(message: 'Successful authentication.', result: [
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }



    // Función para el manejo del cierre de sesión
    public function logout(Request $request)
    {
        // Se obtiene el token en el request y eliminar de la BDD
        // https://laravel.com/api/9.x/Illuminate/Http/Request.html
        $request->user()->tokens()->delete();

        // Se invoca a la función padre
        return $this->sendResponse(message: 'Logged out.');
    }






}

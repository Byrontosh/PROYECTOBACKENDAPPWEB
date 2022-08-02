<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    // Se procede a crear la lógica del middleware para
    // validar si el usuario se encuentra activo
    // https://laravel.com/docs/9.x/middleware#before-after-middleware
    public function handle(Request $request, Closure $next)
    {
        // Se obtiene la ruta que gestiona la petición
        $user = $request->route('user');
        // Si el usuario no esta activo   
        if (!$user->state)
        {
            return abort(403, 'This action is unauthorized.');
        }
        // Si el usuario esta activo pasa a realizar las siguientes acciones
        return $next($request);
    }


}



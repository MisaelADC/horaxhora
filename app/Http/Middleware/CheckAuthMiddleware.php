<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $autentificacion = session()->get('autenticado');
        $Rol = session()->get('Rol');

        if ($autentificacion == true) {
            if($Rol == 1){
                return redirect('/Gestion');
            }
            return $next($request); 
        } else {
            session(['urlAnterior' => $request->path()]);
            return redirect('/');
    }
}
}
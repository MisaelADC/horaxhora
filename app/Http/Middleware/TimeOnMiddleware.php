<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TimeOnMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado según la respuesta de la API
        $autentificacion = session()->get('autenticado');
        $Rol = session()->get('Rol');

        if ($autentificacion == true) {      
            if($Rol == 0){
                return redirect('/Inicio');
            }
            
            return $next($request);
        }else{
            return redirect('/');
        }        
    }
}
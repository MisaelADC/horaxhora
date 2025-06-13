<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckAuth1Middleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado según la respuesta de la API
        $autentificacion = session()->get('autenticado');
        $Rol = session()->get('Rol');
        $Estatus = session()->get('Estatus');

        if ($autentificacion == true) {            
            if($Rol == 0){
                return redirect('/Inicio');
            }

                if($Estatus == 2){
                    return redirect("/Gestion");
                }

                if($Estatus == 3){
                    $IdProduccion = session()->get('IdProduccion');
                    return redirect("/TiempoFuera/".$IdProduccion);
                }

            return $next($request); 
        } else {
            session(['urlAnterior' => $request->path()]);
            return redirect('/');
        }
    }
}
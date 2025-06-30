<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckAuthEMiddleware
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

            if($Rol == 1){
                if($Estatus == 1){
                    return redirect("/Iniciar");
                }

                if($Estatus == 3){
                    $IdProduccion = session()->get('IdProduccion');
                    return redirect("/TiempoFuera/".$IdProduccion);
                }
            }

            return $next($request); 
        } else {
            session(['urlAnterior' => $request->path()]);
            return redirect('/');
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Production;
use App\Models\CatShiftHxh;
use App\Models\Shift;
use App\Models\CatTimeOut;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;

class IniciosController extends Controller
{

    public function VistaEmp()
    {
       $userId = session('user_id');

$Produccion = Production::where('IdUser', $userId)
    ->where('Status', 1)
    ->orderByDesc('Id') // Ordena de forma descendente por Id
    ->get();

       return view("/InicioEmp", compact('Produccion'));

    }

    public function VistaAdm()
    {

        $Inicios = UserLogin::whereDate('LoginAt', '>=', Carbon::today())
        ->orderByDesc('LoginAt') // Ordena por fecha de login en orden descendente
        ->take(10)                // Limita el resultado a los 10 más recientes
        ->get();


        foreach ($Inicios as $I) {

            $I->LoginAt = new \DateTime($I->LoginAt);
            $I->LoginAt = $I->LoginAt->format('Y-m-d H:i');
        }

        $Inicios ->toArray();

        foreach ($Inicios as $I) {

        $Usuario = User::where('IdUser', $I->IdUser)->first();

        $I->Name = $Usuario->Name;
        $I->Imagen = $Usuario->Image;

        }

        return view("/Inicio", compact('Inicios'));

    }


}

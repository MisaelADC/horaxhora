<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http; // Importa la clase Http
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RedirectResponse;
use Illuminate\Support\Facades\Config; 
use App\Models\User;
use App\Models\CatTimeOut;
use App\Models\Shift;
use App\Models\Production;
use App\Models\TimeWorked;
use App\Models\UserLogin;

class IniSesController extends Controller
{
    
    public function create(Request $request)
    { 

        session()->flush();
        session(['autenticado' => false]);   
        $backendApi = config('app.backend_api');


            $Usuario = User::where('CodeEmp', $request->email)
            ->whereIn('Status', [1, 2, 3])
            ->first();
            
            if(is_null($Usuario)){
            return redirect("/")->withErrors(['api_error' => 'Error, No se encontro el correo o el código']);
        }

        if (!$Usuario  || ! Hash::check($request->password, $Usuario->Password)) {
            return redirect("/")->withErrors(['api_error' => 'Error, La contraseña es incorrecta']);
        }

        $Ingreso = new UserLogin();
        $Ingreso->IdUser = $Usuario->IdUser;
        $Ingreso->save();
        
        if($Usuario->Status == 1 && $Usuario->Machine != 0){
         $Usuario->Machine = 0;
         $Usuario->save();
        }
         

                session([
                    'autenticado' => true,
                    "Estatus" => $Usuario->Status,
                    'user_id' => $Usuario->IdUser,
                    'user_name' => $Usuario->Name,
                    'Rol' =>  $Usuario->Type,
                ]);

                if($Usuario->Type == 0){
                    return redirect("/Inicio");
                    }

                if($Usuario->Machine != 0){
                    session([
                        'MaquinaActiva' =>  $Usuario->Machine,
                    ]);


                if($Usuario->Status == 3){

                $idUser = session()->get('user_id');

                $fechaH = Carbon::now()->format('Y-m-d'); 
                $horaH = Carbon::now()->format('H:i:s'); 
                
                $Turno = Shift::where('status', '1')
                ->where(function ($query) use ($horaH) {
                  $query->whereTime('StartTime', '<=', $horaH)
                  ->whereTime('EndTime', '>=', $horaH);
                     $query->orWhere(function ($subQuery) use ($horaH) {
                        $subQuery->whereTime('StartTime', '>', '12:00:00') 
                          ->whereTime('EndTime', '<', '12:00:00')     
                          ->where(function ($subSubQuery) use ($horaH) {
                                  $subSubQuery->whereTime('StartTime', '<=', $horaH)  
                                  ->orWhereTime('EndTime', '>=', $horaH);   
                                      });
                                 });
                             })
                             ->first(); 

                     if(empty($Turno->IdShift)){
                                return redirect("/")->withErrors(['api_error' => 'No exixte un turno para esta hora, intenta mas tarde']);
                         }
                
                         $Production = Production::where('IdMachine', $Usuario->Machine)
                             ->whereDate('Date', $fechaH)
                             ->where('IdShift', $Turno->IdShift)
                             ->where('status', '3')
                             ->first();
                
                             if($Turno->StartTime > $Turno->EndTime && $horaH < $Turno->StartTime){
                
                             $FechaAyer = Carbon::yesterday()->format('Y-m-d');
                             $Production = Production::where('IdMachine', $Usuario->Machine)
                             ->whereDate('Date', $FechaAyer)
                             ->where('IdShift', $Turno->IdShift)
                             ->where('status', '3')
                             ->first();
                             }
                
                             if(empty($Production->IdProduction)){
                
                                 $idUsuario = session()->get('user_id' );
                                 $Usuario = User::Where('IdUser', $idUsuario)->first();
                                 $Usuario->status = 1;
                                 $Usuario->save();
                        
                                 session([
                                     'Estatus' =>  1,
                                 ]);
                
                                 return redirect("/Iniciar");
                             }

                             $TiemposMuertos = CatTimeOut::where('IdProduction', $Production->IdProduction)
                             ->whereNull('EndTime')
                             ->get();

                             if(empty($TiemposMuertos["0"])){

                                 $idUsuario = session()->get('user_id' );
                                 $Usuario = User::Where('IdUser', $idUsuario)->first();
                                 $Usuario->status = 1;
                                 $Usuario->save();

                                 session([
                                     'Estatus' =>  1,
                                 ]);
                
                                 return redirect("/Gestion");
                             }

                         return redirect("/TiempoFuera/".$Production->IdProduction);
                      }


                }

                    if($Usuario->Status == 2){
                         return redirect("/Gestion");
                     }

                if($Usuario->Status == 1){
                        return redirect("/Iniciar");
                    }
                }
    

    public function salir()
    {
        session()->flush();
        session(['autenticado' => false]);   

        return redirect()->route('Loguearse');
    }
    
}


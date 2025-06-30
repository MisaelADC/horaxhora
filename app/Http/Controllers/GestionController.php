<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Shift;
use App\Models\Cycle;
use App\Models\Product;
use App\Models\Machine;
use App\Models\CatShiftHxh;
use App\Models\CatTimeOut;
use App\Models\Wo;
use App\Models\TimeWorked;
use App\Models\Production;
use App\Models\DowntimeReason;
use Carbon\Carbon;

class GestionController extends Controller
{

    public function IniciarTrabajo()
    {
        $idUsuario = session()->get('user_id');

        $Usuarios = User::where('IdUser', $idUsuario)->first();

        if($Usuarios->Type == 1){
            $Maquinas = Machine::where('Status', '1')->get();
        }elseif($Usuarios->Type == 2){
            $Maquinas = Machine::where('Status', '1')->where("Tipo", "Maquina")->get();
        }else{
            $Maquinas = Machine::where('Status', '1')->where("Tipo", "Mesa")->get();
        }
             

        return view('Operador.TimeOn')->with('Maquinas', $Maquinas);
        
    }

    public function VistaTiempoMuerto($id){

        $idUsuario = session()->get('user_id');

        $Usuarios = User::whereIn('Status', [1, 2, 3])
        ->where('Type', "1")
        ->where('IdUser', '!=', $idUsuario)
        ->get();  

        $Produccion = Production::where('IdProduction', $id)->with("wo.product")->first();

        // dd($Produccion->wo->product->tipo);
        // $Razones = DowntimeReason::where('Status', "1")->where("Tipo", $Produccion->wo->product->Tipo)->get();

    $Razones = DowntimeReason::where('Status', "1")
    ->where(function ($query) use ($Produccion) {
        $query->where("Tipo", $Produccion->wo->product->Tipo)
              ->orWhere("Tipo", "Ambos");
    })
    ->get();

        $idProduccion = $id;

        return view('/Operador/IniciarTimeOut',compact('Usuarios', "idProduccion", "Razones"));
    }

    public function TiempoFuera($id)
    {    
        $idProduccion = $id;
        return view('/Operador/TimeOut',compact("idProduccion"));
    }

    public function SalirTiempoMuerto($id)
    {  
        $idUsuario = session()->get('user_id' );

        $Produccion = Production::where('IdProduction', $id)->first();
        $Produccion->status = 1;
        

        
        $UsuarioSus = User::Where('Machine', $Produccion->IdMachine)
                            ->Where("Status", 2) 
                            ->first();

        if(!empty($UsuarioSus->Machine)){
            return back()->withErrors(['ErrorTOFF' => 'El usuario que Reemplaza aun no deja de trabajar en esta maquina']);
        }

        $Produccion->save();

        $Usuario = User::Where('IdUser', $idUsuario)->first();
        $Usuario->Status = 2;
        session([
            'Estatus' =>  2,
        ]);
        $Usuario->save();

        $TiempoMuerto = CatTimeOut::where('IdProduction', $id)
        ->whereNull('EndTime')
        ->where('Status', 1)
        ->orderBy('StartTime', 'desc') 
        ->first();

        $TiempoMuerto->EndTime =  Carbon::now()->toTimeString();
        $TiempoMuerto->save();

        return redirect("/Gestion");
    }

    public function TerminarProduccion($id)
    {

        $ultimoHxh = CatShiftHxh::where('IdProduction', $id)
        ->get();

        $Produccion = Production::where('IdProduction', $id)->first();
        $Produccion->status = 2;
        $Produccion->save();


        $idUsuario = session()->get('user_id' );

        $fechaHora = Carbon::now()->format('Y-m-d H:i:s');

           $Usuario = User::Where('IdUser', $idUsuario)->first();
           $Usuario->status = 1;
           $Usuario->Machine = 0;
           $Usuario->save();
           session([
               'Estatus' =>  1,
          ]);    

        $TiempoTrabajado = TimeWorked::where('IdProduction', $id)
        ->where("IdUser", $idUsuario)
        ->orderBy('HStart', 'desc')  
        ->first();

        $TiempoTrabajado->HEnd = $fechaHora;
        $TiempoTrabajado->save();


        $horaH = Carbon::now()->format('H:i:s'); 
        // $horaH = Carbon::parse('00:40:00')->format('H:i:s');

        $Turno = Shift::where('IdShift',  $Produccion->IdShift);

            $ultimoHxh = CatShiftHxh::where('IdProduction', $id)
            ->orderBy('IdShiftHxh', 'desc')
            ->first();

            if ($ultimoHxh) {
                $ultimoHxh->HEnd = Carbon::now()->format('H:i:s');
                // $ultimoHxh->HEnd = Carbon::parse('00:40:00')->format('H:i:s');
                $ultimoHxh->save();
                } else {
                // Manejo de error: no se encontró un registro Hxh
                return redirect("/Iniciar");
                }

        return redirect("/Iniciar");
    }




    public function SalirTrabajo($id)
    {

        $idUsuario = session()->get('user_id' );

        $fechaHora = Carbon::now()->format('Y-m-d H:i:s');

        // dd($fechaHora);
          $Usuario = User::Where('IdUser', $idUsuario)->first();
          $Usuario->status = 1;
          $Usuario->Machine = 0;
          $Usuario->save();
          session([
              'Estatus' =>  1,
         ]);    

        $TiempoTrabajado = TimeWorked::where('IdProduction', $id)
        ->where("IdUser", $idUsuario)
        ->orderBy('HStart', 'desc')  
        ->first();

        $TiempoTrabajado->HEnd = $fechaHora;
        $TiempoTrabajado->save();

        return redirect("/Iniciar");
    }




        public function TiempoMuertoR(Request $request, $id)
    {
        $request->validate([
            'Descripcion' => 'required|max:255',
        ], [
 
            'Descripcion.required' => 'El campo obligatorio.',
            'Descripcion.max' => 'El campo no debe superar los 255 caracteres.',
        ]);

        $Razon = DowntimeReason::where("IdDowntimeReason", $request->Descripcion)->first();
  

        if($Razon->replacement == "Si"){
            $request->validate([
                'Usuario' => 'required|max:255',
            ], [
                'Usuario.required' => 'El campo obligatorio.',
                'Usuario.max' => 'El campo no debe superar los 255 caracteres.',
            ]);
        }

        $UsuarioRemplazo = User::Where('IdUser', $request->Usuario)->first(); 

        if(!empty($UsuarioRemplazo)){
        if( $UsuarioRemplazo->Machine != 0 ){
            return back()->withErrors(['Usuario' => 'El usuario esta activo en otra maquina']);
        }
    }

        $idUsuario = session()->get('user_id');

        $Usuario = User::Where('IdUser', $idUsuario)->first(); 
        $Usuario->status = 3;
        $Usuario->save();

        session([
            'Estatus' =>  3,
        ]);  

        $Produccion = Production::where('IdProduction', $id)->first();
        $Produccion->status = 3;
        $Produccion->save();

        if(!empty($UsuarioRemplazo)){
        $UsuarioRemplazo->Status = "2";
        $UsuarioRemplazo->Machine = $Produccion->IdMachine;
        $UsuarioRemplazo->save();
        }

        if(!empty($UsuarioRemplazo)){
        $TiempoT = new TimeWorked;
        $TiempoT->IdProduction = $id;
        $TiempoT->IdUser = $UsuarioRemplazo->IdUser;
        $TiempoT->HStart = Carbon::now();
        $TiempoT->save();
        }


        $TiempoFuera = new CatTimeOut;
        $TiempoFuera->IdProduction = $id;
        $TiempoFuera->IdDowntimeReason = $request->Descripcion;
        if(!empty($UsuarioRemplazo)){
        $TiempoFuera->IdUser = $request->Usuario;
        }
        $TiempoFuera->StartTime = Carbon::now()->toTimeString();
        $TiempoFuera->save();

        return redirect("/TiempoFuera/".$id);
    }



    public function IniciarConteo(Request $request)
    {

        if($request->Maquina == null){
            return back()->withErrors(['Maquina' => 'Elige una maquina para empezar a trabajar']);
        }

        $idUsuario = session()->get('user_id' );

         $UsuMaq = User::where('Machine', $request->Maquina)->first();

         if(!empty($UsuMaq->Machine)){
             return back()->withErrors(['Maquina' => 'Esta maquina ya esta en uso']);
         }

        $fechaHora = Carbon::now()->format('Y-m-d H:i:s');

        $NuevoTiempo = new TimeWorked();
        $NuevoTiempo->HStart = $fechaHora;
        $NuevoTiempo->IdUser = $idUsuario;
        

        $Usuario = User::Where('IdUser', $idUsuario)->first();
        $Usuario->status = 2;
        $Usuario->Machine = $request->Maquina;
       
        session([
            'MaquinaActiva' => $request->Maquina,
        ]);   
        
        $MaquinaActiva = session()->get('MaquinaActiva');

        $fechaH = Carbon::now()->format('Y-m-d'); 
        //$fechaH = Carbon::parse('2024-12-02')->format('Y-m-d');
        $horaH = Carbon::now()->format('H:i:s'); 
        //$horaH = Carbon::parse('03:35:00')->format('H:i:s');

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
                })->first();


        if(empty($Turno->IdShift)){
         // dd($fechaHora);
            $Usuario = User::Where('IdUser', $idUser)->first();
            $Usuario->status = 1;
            $Usuario->Machine = 0;
            $Usuario->save();
            session([
                'Estatus' =>  1,
            ]);    

            return redirect("/Iniciar");
        }

            if($Turno->StartTime > $Turno->EndTime && $horaH < $Turno->StartTime){

            $FechaAyer = Carbon::yesterday()->format('Y-m-d');
            //$FechaAyer = Carbon::parse('2024-12-02')->format('Y-m-d');

            $Production = Production::where('IdMachine', $MaquinaActiva)
            ->whereDate('Date', $FechaAyer)
            ->where('IdShift', $Turno->IdShift)
            ->wherein('Status', [1,3])
            ->with("wo")
            ->first();

            //dd($Production);

            }else{
                    $Production = Production::where('IdMachine', $MaquinaActiva)
                    ->whereDate('Date', $fechaH)
                    ->where('IdShift', $Turno->IdShift)
                    ->wherein('Status', [1,3])
                    ->with("wo")
                    ->first();
            }   


           if(empty($Production->IdProduction)){
                return redirect("Iniciar")->withErrors(['Maquina' => 'No tiene ningun trabajo asignado']);
        }
        
        $NuevoTiempo->IdProduction = $Production->IdProduction;
        $NuevoTiempo->save();
        $Usuario->save();

        session([
            'Estatus' =>  2,
        ]);   

        return redirect("/Gestion");
    }

    public function Suma(Request $request)
    {   

        if($request->CantidadMalas == NULL && $request->CantidadBuenas == NULL){
                return redirect()->back()->withErrors(['CantidadMalas' => "Elije al menos un campo",
                "CantidadBuenas" => "Elije al menos un campo"])->withInput();
        }

        $Hxh = CatShiftHxh::where('IdShiftHxh', $request->HxhActual)->first();
        $Pro = Production::where('IdProduction', $request->IdProduccion)->first();

        if(!($request->CantidadMalas == NULL)){
            $Hxh->Scrap = $Hxh->Scrap + $request->CantidadMalas;
            $Pro->Scrap = $Pro->Scrap + $request->CantidadMalas;
    }

        if(!($request->CantidadBuenas == NULL)){
            $Wo = Wo::where('IdWo', $Pro->IdWo)->with("TotalReal")->first();

            $Hxh->Real = $Hxh->Real + $request->CantidadBuenas;
            $Pro->Real = $Pro->Real + $request->CantidadBuenas;

            if($Wo->TotalReal->TotalProducido >= $Wo->Meta){
                $Wo->status = 2;
            }
        }
        $Pro->save();
        $Hxh->save();

        return redirect("/Gestion");
    }
    

    public function Gestion()  
    {
        $idUser = session()->get('user_id');
        $MaquinaActiva = session()->get('MaquinaActiva');
        $BanderaTolerancia = false;

        session([
            'OcultarToff' => true,
        ]);

        do{

            $Repetir = false;

            $fechaH = Carbon::now()->format('Y-m-d'); 
            // $fechaH = Carbon::parse('2025-03-14')->format('Y-m-d');
            $horaH = Carbon::now()->format('H:i:s'); 
            // $horaH = Carbon::parse(' 03:35:00')->format('H:i:s');

            if($BanderaTolerancia == false){
                if (is_string( $horaH)) {
                    $horaH = new \DateTime($horaH);
                }
                $horaH->modify("-10 minutes");
            }
   
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
                })->first();

             if($BanderaTolerancia == false){
                 $horaH->modify("+10 minutes");
                 $horaH =  $horaH->format('H:i:s');
             }

        if(empty($Turno->IdShift)){
         // dd($fechaHora);
         $Usuario = User::Where('IdUser', $idUser)->first();
         $Usuario->status = 1;
         $Usuario->Machine = 0;
         $Usuario->save();
         session([
             'Estatus' =>  1,
        ]);    

       return redirect("/Iniciar")->withErrors(['Maquina' => 'No se encontro el turno']);;
        }

            if($Turno->StartTime > $Turno->EndTime && $horaH < $Turno->StartTime){
            $FechaAyer = Carbon::yesterday()->format('Y-m-d');
            // $FechaAyer = Carbon::parse('2025-03-13')->format('Y-m-d');

            $Production = Production::where('IdMachine', $MaquinaActiva)
            ->whereDate('Date', $FechaAyer)
            ->where('IdShift', $Turno->IdShift)
            ->wherein('Status', [1,3])
            ->with(['wo.product', 'wo.design', "wo.TotalReal"])
            ->first();

            }else{
                    $Production = Production::where('IdMachine', $MaquinaActiva)
                    ->whereDate('Date', $fechaH)
                    ->where('IdShift', $Turno->IdShift)
                    ->wherein('Status', [1,3])
                    ->with(['wo.product', 'wo.design', "wo.TotalReal"])
                    ->first();
            }   

                if($BanderaTolerancia == false && empty($Production->IdProduction)){
                   $BanderaTolerancia = true;
                   $Repetir = true;
                }

           }while($Repetir == true);

           if(empty($Production->IdProduction)){

            $Usuario = User::Where('IdUser', $idUser)->first();
            $Usuario->status = 1;
            $Usuario->Machine = 0;
            $Usuario->save();
            session([
                'Estatus' =>  1,
            ]);
                return redirect("Iniciar")->withErrors(['Maquina' => 'No tiene ningun trabajo asignado']);
        }

        //OKK


            if($Production->Status == 1){
                session([
                    'OcultarToff' => false,
                ]);
                }


            $Producto = Product::where('IdProduct', $Production->wo->IdProduct)->first();
            $Maquina = Machine::where('IdMachine', $Production->IdMachine)->first();
            $Production->Product = $Producto->Description;
            $Production->Machine = $Maquina->Description;

            session([
                'IdProduccion' =>  $Production->IdProduction,
            ]); 
   

        $start = new \DateTime($Turno->StartTime);
        $end = new \DateTime($Turno->EndTime);
        $endComp =  $end;
        
        $startT = $start->format('H:i');
        $endT = $end->format('H:i');

        // $endT10 = new \DateTime($Turno->EndTime);
        // $endT10->modify("+10 minutes");

        $interval = $end->diff($end);

        $horas = $interval->h;
        $minutos = $interval->i;

        $CantidadOptimaH =  round(3600 / $Producto->Cycle);

        $Produccion = $Production;

        $Bandera = false;

        $Hxh = CatShiftHxh::where('IdProduction', $Production->IdProduction)->get();

        foreach ($Hxh as $I) {

            if (is_string($I->HEnd)) {
                $I->HEnd = new \DateTime($I->HEnd);
            }

            if (is_string($I->HStart)) {
                $I->HStart= new \DateTime($I->HStart);
            }

            if (is_string($horaH)) {
                $horaH = new \DateTime($horaH);
            }
    
            $I->HEnd->modify("+10 minutes");
            $endComp->modify("+10 minutes");
    
            $I->HEnd = $I->HEnd->format('H:i:s');
            $I->HStart = $I->HStart->format('H:i:s');
            $horaH = $horaH->format('H:i:s');

            if($I->HEnd < $I->HStart){

                if("23:00:00.0" <= $horaH && $horaH <= "23:59:59.9"){
                    $Bandera = true;    
                }

                if("00:00:00.0" <= $horaH && $horaH <= "00:09:59.9"){
                    $Bandera = true;  
                }

            }else{
                if($horaH >= $I->HStart && $horaH <= $I->HEnd){
                    $Bandera = true;
                 }  
            }
        }

        //oK

        $Hora = Carbon::now();
        // $Hora = Carbon::parse('2025-03-14 03:35:00');
        $HoraInicialCompleta = $Hora->format('H:i');

        if($Bandera === false){
        if(empty($Hxh["0"]["IdProduction"])){
            
         $hxh = new CatShiftHxh();
         $Hora->addHours(1);
         $HoraSqlF = $Hora->format('H:00');
         $hxh->HEnd=$HoraSqlF;
                
         if($HoraSqlF > $endT){
                 $hxh->HEnd = $endT;
         }

        if($endT < $startT){
            if($HoraSqlF < $startT && $HoraSqlF > $endT){
                $hxh->HEnd = $endT;
            }else{
                $hxh->HEnd=$HoraSqlF;
            }
        }

         $hxh->HStart = $HoraInicialCompleta;
         $hxh->Real="0";
         $hxh->Scrap="0";
         $hxh->IdProduction=$Produccion->IdProduction;
         $hxh->save();


        }else{

         do{
            $HxHs = CatShiftHxh::where('IdProduction', $Production->IdProduction)
            ->orderBy('Idshifthxh', 'desc') // Ordenar por el campo 'Id' de mayor a menor
            ->first();

            $hxh = new CatShiftHxh();
            $hxh->HStart = $HxHs->HEnd;

            if (is_string($HxHs->HEnd)) {
                $HxHs->HEnd = new \DateTime($HxHs->HEnd);
            }

            $hxh->HStart = $HxHs->HEnd->format('H:i');
            $HxHs->HEnd->modify("+1 hour");

            $hxh->HEnd = $HxHs->HEnd->format('H:00');
            $hxh->Real="0";
            $hxh->Scrap="0";
            $hxh->IdProduction=$Produccion->IdProduction;

            $BanderaWhile = false;
            $BanderaGuardar = false;

            if($endT < $startT){
                
                if( $hxh->HStart >= $endT && $hxh->HStart < $startT){
                    $BanderaWhile = false;
                    $BanderaGuardar = false;
                }

                if($startT < $HoraInicialCompleta && $HoraInicialCompleta < "23:00"){
                    if($hxh->HStart < $HoraInicialCompleta && $HoraInicialCompleta > $hxh->HEnd){
                        $BanderaGuardar = true;
                        $BanderaWhile = true;
                    }
                }

                if($hxh->HEnd < $hxh->HStart){
                    // dd("hola");
                    if("23:00" <= $HoraInicialCompleta && $HoraInicialCompleta <= "23:59"){
                            $BanderaWhile = false;
                            $BanderaGuardar = true;
                        }else{
                            $BanderaGuardar = true;
                            $BanderaWhile = true;
                        }
                    }  

                if($endT >= $HoraInicialCompleta && $HoraInicialCompleta >= "00:00"){
                    if($startT < $hxh->HStart && $hxh->HStart < "23:59"){
                        $BanderaGuardar = true;
                        $BanderaWhile = true;
                    }elseif($hxh->HStart < $HoraInicialCompleta && $HoraInicialCompleta > $hxh->HEnd){
                        $BanderaGuardar = true;
                        $BanderaWhile = true;
                    }
                }// Verificar repeticiones

            }else{

            if($endT < $hxh->HEnd){
                $BanderaGuardar = false;
                $BanderaWhile = false;
            }   

            if($hxh->HStart < $HoraInicialCompleta && $HoraInicialCompleta > $hxh->HEnd){
                $BanderaGuardar = true;
                $BanderaWhile = true;
            }  
        }

        if($hxh->HStart < $HoraInicialCompleta && $HoraInicialCompleta < $hxh->HEnd){
            $BanderaGuardar = true; 
            $BanderaWhile = false;
        }     

            if( $hxh->HEnd > $endT && $endT > $hxh->HStart){
                $hxh->HEnd = $endT;
                $BanderaGuardar = true;
                $BanderaWhile = false;
            }//guardar el final del turnoh

        $YAhayHxH = CatShiftHxh::where('IdProduction', $Production->IdProduction)
        ->where("HStart", $hxh->HStart)
        ->where("HEnd", $hxh->HEnd)
        ->first();


        // Ordena los registros Hxh por HStart antes de procesarlos
        $Hxh = CatShiftHxh::where('IdProduction', $Production->IdProduction)->get();

        foreach ($Hxh as $I) {
        $start = new \DateTime($I->HStart);
        $end = new \DateTime($I->HEnd);

        $I->HStart = $start->format('H:i');
        $I->HEnd = $end->format('H:i');

        if($I->HStart <= $HoraInicialCompleta && $HoraInicialCompleta <= $I->HEnd){
           $BanderaGuardar = false;
           $BanderaWhile = false;
          }
        } 

 
        if(empty($YAhayHxH->HStart) && $BanderaGuardar == true){
            $hxh->save();
         }
              }while($BanderaWhile == true);
        }}

        // Ordena los registros Hxh por HStart antes de procesarlos
        $Hxh = CatShiftHxh::where('IdProduction', $Production->IdProduction)->get();
        $Contador = -1;

        foreach ($Hxh as $I) {
       
            $start = new \DateTime($I->HStart);
            $end = new \DateTime($I->HEnd);
        
            $I->HStart = $start->format('H:i');
            $I->HEnd = $end->format('H:i');
        
            // Calcular la diferencia en segundos entre HStart y HEnd
            $interval = $start->diff($end);
            if($I->HEnd == "00:00"){
                $I->Segundos = (24 * 3600)-(($interval->h * 3600) + ($interval->i * 60) + $interval->s);
            }else{
            $I->Segundos = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            }
            $Contador++;
    }


        $HxhActual = $Contador;
        return view("Operador/Principal", compact('CantidadOptimaH', "Produccion", "Hxh", "HxhActual", "Turno"));
    }
}
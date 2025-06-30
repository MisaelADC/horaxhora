<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Production;
use App\Models\CatShiftHxh;
use App\Models\Shift;
use App\Models\Machine;
use App\Models\Product;  
use App\Models\Wo;  
use App\Models\DowntimeReason;
use App\Models\CatTimeOut;
use App\Models\User;
use App\Models\Cycle;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProduccionDetallesTodoExport;

class ProductionsController extends Controller
{

    public function index()
    {
        $Produccion= Production::Wherein('Status', [1,3])->with("wo", "Shift", "machine")->get();
    
        // Convertir la colección a un array
        $Array = $Produccion->toArray();
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);
    
        $BotonVerTodos = "NoVer";

        return view('/Produccion/Produccion', compact('DatosPaginados', "BotonVerTodos"));
    }
    

    public function create()
    {
 
        $Turno = Shift::Where('Status', "1")->get();
        $Maquinas = Machine::Where('Status', "1")->get();
        $Wo = Wo::Where('Status', "1")->with("product")->get();

        return view('/Produccion/NuevaProduccion', compact("Turno", "Maquinas", "Wo"));
    }
    public function exportDetallesTodo(Request $request)
    {
          dd($request->all());
         // ← Captura con los mismos nombres que en el formulario
        $fechaInicio = $request->input('Fecha');
        $fechaFin = $request->input('FechaF');
        $turno = $request->input('Turno');
        $tipo = $request->input('tipo');

        // Valida turno para evitar errores de tipo
        $turno = is_numeric($turno) ? (int) $turno : null;

        return Excel::download(
            new ProduccionDetallesTodoExport($fechaInicio, $fechaFin, $turno, $tipo),
            'detalles_produccion.xlsx'
        );
    }

     public function ExcelDetalladoTodo()
    {
       $Turno = Shift::Where('Status', "1")->get();
       $Maquinas = Machine::where('Status', 1)->get();

       return view('Produccion.ExcelDetalladoTodo', compact("Turno", "Maquinas"));
        //return Excel::download(new ProduccionDetallesTodoExport, 'todos_los_detalles_produccion.xlsx');
    }

    public function ExcelDetallado()
    {
 
        $Turno = Shift::Where('Status', "1")->get();
        $Maquinas = Machine::Where('Status', "1")->get();

        return view('/Produccion/ExcelDetallado', compact("Turno", "Maquinas"));
    }

        
    public function GenerarExcelDetalladoTodo(Request $request)
    {
        
        $exportador = new ProduccionDetallesTodoExport(
            $request->input('Fecha'),
            $request->input('FechaF'),
            $request->input('Turno'),
            $request->input('tipo')
        );

        return $exportador->export(); 
    }

    public function store(Request $request)
    {
           
        $request->validate([
            'Fecha' => 'required|max:255',
            'Wo' => 'required|max:255',
            "Maquina" => "required|max:255",
            "Turno" => "required|max:255",
        ], [
            'Fecha.required' => 'El campo Fecha es obligatorio.',
            'Fecha.max' => 'El campo Fecha no debe exceder los 255 caracteres.',
            'KG.required' => 'El campo Kilogramos (KG) es obligatorio.',
            'KG.max' => 'El campo Kilogramos (KG) no debe exceder los 255 caracteres.',
            'Maquina.required' => 'El campo Máquina es obligatorio.',
            'Maquina.max' => 'El campo Máquina no debe exceder los 255 caracteres.',
            'Turno.required' => 'El campo Turno es obligatorio.',
            'Turno.max' => 'El campo Turno no debe exceder los 255 caracteres.',
        ]);

            $Pro = Production::where('Date',$request->Fecha)
                                   ->where("IdMachine", $request->Maquina)
                                   ->where("IdShift", $request->Turno)
                                   ->where("Status", "1")
                                   ->first();


                                   if ($Pro != NULL) {
                                    return back()->withErrors([
                                        "Fecha" => 'Ya hay un trabajo asignado sin terminar',
                                        "Maquina" => 'Ya hay un trabajo asignado sin terminar',
                                        "Turno" => 'Ya hay un trabajo asignado sin terminar',
                                    ])->withInput();
                                }

            $Turno = Shift::where('IdShift', $request->Turno)->first();
            $fechaInput = Carbon::parse($request->Fecha);
            $horaActual = Carbon::now();  
            $start = Carbon::parse($fechaInput->format('Y-m-d') . ' ' . $Turno->StartTime);

            if ($Turno->EndTime < $Turno->StartTime) {
            $end = Carbon::parse($fechaInput->copy()->addDay()->format('Y-m-d') . ' ' . $Turno->EndTime);
            } else {
            $end = Carbon::parse($fechaInput->format('Y-m-d') . ' ' . $Turno->EndTime);
            }

            // Verificamos si el turno ya comenzó hoy
            if ($fechaInput->isToday() && $horaActual->greaterThan($start)) {
               $start = $horaActual; // Ajustamos la hora de inicio al momento actual
            }

            // Calculamos el intervalo de tiempo usando la diferencia entre `start` y `end`
            $interval = $start->diff($end);
            $horas = $interval->h + ($interval->days * 24); // Sumamos las horas considerando los días completos
            $minutos = $interval->i;
            
            $Wo = Wo::where('IdWo',$request->Wo)->with("TotalReal")->first();

            $Producto = Product::where('IdProduct',$Wo->IdProduct)->first();

            $segundosHxH = ((($horas * 60) + $minutos) * 60);

            $CantidadMax =  round($segundosHxH / $Producto->Cycle);

            if(($Wo->Meta - $Wo->TotalReal->TotalProducido) < $CantidadMax){
                $CantidadMax = round($Wo->Meta - $Wo->TotalReal->TotalProducido);
            }

        $Produccion = new Production();
            
        $Produccion->Date=$request->Fecha;
        $Produccion->IdWo=$request->Wo;
        $Produccion->Meta= $CantidadMax;
        $Produccion->Real="0";
        $Produccion->Scrap="0";
        $Produccion->IdMachine=$request->Maquina;
        $Produccion->IdShift=$request->Turno;
        
        $Produccion->save();

        return redirect("/Produccion");
    }

    public function RedirgirCTO($id){

        return view('/TimesOuts/EditarCTO', compact('id'));
    }


    public function RegistrarTiempoFuera(Request $request)
    {
           
        $request->validate([
            'Inicio' => 'required|max:255',
            'Final' => 'required|max:255',
            'Descripcion' => 'required|max:255',

        ], [
            'Inicio.required' => 'El campo de hora de inicio obligatorio.',
            'Inicio.max' => 'El campo de hora de inicio no debe exceder los 255 caracteres.',
            'Final.required' => 'El campo de hora final es obligatorio.',
            'Final.max' => 'El campo de hora final no debe exceder los 255 caracteres.',
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo Descripcion no debe exceder los 255 caracteres.',
        ]);

        $Produccion= Production::Where('IdProduction', $request->IdProduccion)->first();
        $Turno= Shift::Where('IdShift', $Produccion->IdShift)->first();

        //dd($request->Inicio);

        $endTime = new \DateTime($Turno->EndTime);
        $startTime = new \DateTime($Turno->StartTime);

        // Tiempo muerto ingresado por el usuario
        $userStartTime = Carbon::createFromFormat('H:i', $request->Inicio);
        $userEndTime = Carbon::createFromFormat('H:i', $request->Final);

        if(!$userStartTime->between($startTime, $endTime)){
            return back()->withErrors(["Inicio" => "La hora esta fuera del horario"]);
        }

        if(!$userEndTime->between($startTime, $endTime)){
            return back()->withErrors(["Final" => "La hora esta fuera del horario"]);
        }

        $CTO = new CatTimeOut();
            
        $CTO->StartTime=$request->Inicio;
        $CTO->EndTime=$request->Final;
        $CTO->Description=$request->Descripcion;
        $CTO->IdProduction=$request->IdProduccion;

        
        $CTO->save();
        
        return redirect("/CTO/".$request->IdProduccion);
    }
    
    public function CanHXH(Request $request, $id){
        
        $HXH=CatShiftHxh::find($id);
        $Produccion=Production::find($HXH->IdProduction);
        $Producto=Product::find($Produccion->IdProduct);

        switch ($request->Operacion) {
            case 'SumaMeta':

            $HXH->Real = $HXH->Real + $request->Cant;
            $HXH->save();

            $Produccion->Real = $Produccion->Real + $request->Cant;
            $Produccion->save();

            $Producto->Quantity = $Producto->Quantity + $request->Cant;
            $Producto->save();

                break;
            
            case 'RestaMeta':

            $HXH->Real = $HXH->Real - $request->Cant;
            $HXH->save();

            $Produccion->Real = $Produccion->Real - $request->Cant;
            $Produccion->save();

            $Producto->Quantity = $Producto->Quantity - $request->Cant;
            $Producto->save();

                break;
            
            case 'SumaMerma':

            $HXH->Scrap = $HXH->Scrap + $request->Cant;
            $HXH->save();

            $Produccion->Scrap = $Produccion->Scrap + $request->Cant;
            $Produccion->save();

                break;

            case 'RestaMerma':

            $HXH->Scrap = $HXH->Scrap - $request->Cant;
            $HXH->save();

            $Produccion->Scrap = $Produccion->Scrap - $request->Cant;
            $Produccion->save();

                break;
        }
        

        return redirect("HxH/".$Produccion->IdProduction);
    }

    public function show($id)
    {
        $Produccion = Production::where('IdProduction', $id)->first();

            $Wo = Wo::where('IdWo', $Produccion->IdWo)->first();
            $Maquina = Machine::where('IdMachine', $Produccion->IdMachine)->first();
            $Turno = Shift::where('IdShift', $Produccion->IdShift)->first();

            $Produccion->Wo = $Wo->Wo;
            $Produccion->Machine = $Maquina->Description;
            $Produccion->Shift = $Turno->Shift;

        return view('/Produccion/EliminarProduccion', compact('Produccion'));
    }

    public function verCTO($id){

        $CTO = CatTimeOut::where('IdProduction', $id)->where('Status', 1)
        ->with([ 'user', 'downtimeReason'])
        ->get();

        session([
            'CTO' => $id
        ]);

        foreach ($CTO as $I) {

            $I->StartTime = new \DateTime($I->StartTime);
            $I->StartTime = $I->StartTime->format('H:i');
            $I->EndTime = new \DateTime($I->EndTime);
            $I->EndTime = $I->EndTime->format('H:i');

            $Usuario= User::where('IdUser', $I->IdUser)->first();

            if(!empty($I->User)){
            $I->User = $Usuario->Name;
            }
        }

        return view('/TimesOuts/CTO', compact('CTO', "id"));
    }


    public function verHxH($id)
    {

        $Produccion = Production::where('IdProduction', $id)->with("machine", "wo.product")->first();
        $Hxh = CatShiftHxh::where('IdProduction', $id)->get();

        $MaximoPosible = 0;

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
                $MaximoPosible += round($I->Segundos /$Produccion->Wo->Product->Cycle);
        }

        $Hxh->MaximoPosible = $MaximoPosible;

        $CTO = CatTimeOut::where('IdProduction', $id)->where('Status', 1)
        ->with([ 'user', 'downtimeReason'])
        ->get();

        session([
            'CTO' => $id
        ]);

        foreach ($CTO as $I) {

            $I->StartTime = new \DateTime($I->StartTime);
            $I->StartTime = $I->StartTime->format('H:i');
            $I->EndTime = new \DateTime($I->EndTime);
            $I->EndTime = $I->EndTime->format('H:i');

            $Usuario= User::where('IdUser', $I->IdUser)->first();
            if(!empty($I->User)){
            $I->User = $Usuario->Name;
            }
        }

        //dd($Hxh);

        return view('/Produccion/HxH', compact('Hxh',"id", 'CTO', "Produccion"));
    }


    public function HxHREdit($id)
    {
        $Hxh = CatShiftHxh::where('IdShiftHxh', $id)->first();

        return view('/Produccion/HxHedit', compact('Hxh'));
    }

    public function HxHEdit(Request $request, $id)
    {
        $Hxh = CatShiftHxh::where('IdShiftHxh', $id)->first();

        $Hxh->Real = $request->Real;
        $Hxh->Scrap = $request->Scrap;
        $Hxh->save();

        $Hxh = CatShiftHxh::where('IdProduction', $request->IdProduccion)->get();

        $Scrap = 0;
        $Real = 0;

        foreach ($Hxh as $H) {
           $Scrap = $Scrap + $H->Scrap;
           $Real = $Real + $H->Real;
        }

        $Produccion = Production::where('IdProduction', $request->IdProduccion)->first();

        $Produccion->Scrap = $Scrap;
        $Produccion->Real = $Real;

        $Produccion->save();

        $Producciones = Production::where('IdWo', $Produccion->IdWo)->whereIn('Status', [1,2])->get();

        $Real = 0;

        foreach ($Producciones as $Pro) {
            $Real = $Real + $Pro->Real;
         }

        $Wo = Wo::where('IdWo', $Produccion->IdWo)->first();

        $Wo->Real = $Real;

        $Wo->save();

        return redirect("/HxH/".$request->IdProduccion);
    }


    public function edit($id)
    {
        $Produccion = Production::where('IdProduction', $id)->first();
        $Turno = Shift::Where('Status', "1")->get();
        $Maquinas = Machine::Where('Status', "1")->get();
        $Wo = Wo::Where('Status', "1")->get();

        return view('/Produccion/EditarProduccion', compact('Produccion',"Turno", "Maquinas", "Wo"));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'Fecha' => 'required|max:255',
            'Wo' => 'required|max:255',
            'Cantidad' => 'required|max:255',
            "Maquina" => "required|max:255",
            "Turno" => "required|max:255",
        ], [
            'Fecha.required' => 'El campo Fecha es obligatorio.',
            'Fecha.max' => 'El campo Fecha no debe exceder los 255 caracteres.',
            'Wo.required' => 'El campo Work Order (Wo) es obligatorio.',
            'Wo.max' => 'El campo Work Order (Wo) no debe exceder los 255 caracteres.',
            'Maquina.required' => 'El campo Máquina es obligatorio.',
            'Maquina.max' => 'El campo Máquina no debe exceder los 255 caracteres.',
            'Turno.required' => 'El campo Turno es obligatorio.',
            'Turno.max' => 'El campo Turno no debe exceder los 255 caracteres.',
            'Cantidad.required' => 'El campo Cantidad es obligatorio.',
            'Cantidad.max' => 'El campo Cantidad no debe exceder los 255 caracteres.',
        ]);

        $Pro = Production::where('Date',$request->Fecha)
        ->where("IdMachine", $request->Maquina)
        ->where("IdShift", $request->Turno)
        ->where("IdProduction", '!=', $id)
        ->where("Status", "1")
        ->first();


        if ($Pro != NULL) {
         return back()->withErrors([
            "Fecha" => 'Ya hay un trabajo asignado sin terminar',
            "Maquina" => 'Ya hay un trabajo asignado sin terminar',
            "Turno" => 'Ya hay un trabajo asignado sin terminar',
         ])->withInput();
     }

        $Produccion=Production::find($id);

        $Turno = Shift::where('IdShift', $request->Turno)->first();

        $diaP = Carbon::parse($Produccion->Date)->format('Y-m-d');
        $diaUA = Carbon::parse($Produccion->UpdateDate)->format('Y-m-d');
        $horaUA = Carbon::parse($Produccion->UpdateDate)->format('Y-m-d');

        $start = new \DateTime($Turno->startTime);

        if($Turno->startTime < $horaUA && $horaUA < $Turno->EndTime){
            if($diaUA == $diaP){
            $start = new \DateTime($horaUA);
            }
        }

        $end = new \DateTime($Turno->EndTime);
        $interval = $start->diff($end);
        $horas = $interval->h;
        $minutos = $interval->i;

        $Wo = Wo::where('IdWo',$request->Wo)->with("TotalReal")->first();

        $Producto = Product::where('IdProduct',$Wo->IdProduct)->first();

        $segundosHxH = ((($horas * 60) + $minutos) * 60);

        $CantidadMax =  round($segundosHxH / $Producto->Cycle);

        if(($Wo->Meta - $Wo->TotalReal->TotalProducido) < $CantidadMax){
            $CantidadMax = round($Wo->Meta - $Wo->TotalReal->TotalProducido);
        }
            
        $Produccion->Date=$request->Fecha;
        $Produccion->IdWo=$request->Wo;
        $Produccion->Meta=$request->Cantidad;
        $Produccion->IdMachine=$request->Maquina;
        $Produccion->IdShift=$request->Turno;
        $Produccion->save();

        return redirect("/Produccion");

    }


    public function destroy($id)
    {
        $Pro=Production::find($id);

        $UsuariosMaq = user::where("Machine", $Pro->IdMachine)->get();

        foreach ($UsuariosMaq as $Usu) {
            $Usu->Machine = 0;
            $Usu->Status = 1;
            $Usu->save();
        }

        $Pro->status="0";
        $Pro->save();

        return redirect("/Produccion");
    }
}

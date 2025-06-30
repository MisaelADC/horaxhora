<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TimeWorked;
use App\Models\User;
use App\Models\Machine;
use Carbon\Carbon;

class TimeWorkedController extends Controller
{

    public function index()
    {

        $TiemposTrabajados = TimeWorked::orderBy('HStart', 'desc')                               
        ->limit(30) 
        ->with("machine", "user")
        ->get();

        foreach( $TiemposTrabajados as $TT){
        
            // Verificar si HStart no es nulo antes de formatear
            if (!is_null($TT->HStart)) {
                $HStart = new \DateTime($TT->HStart);
                $TT->HStart = $HStart->format('Y-m-d H:i');
            } else {
                $TT->HStart = 'No disponible'; // Valor por defecto si HStart es nulo
            }
        
            // Verificar si HEnd no es nulo antes de formatear
            if (!is_null($TT->HEnd)) {
                $HEnd = new \DateTime($TT->HEnd);
                $TT->HEnd = $HEnd->format('Y-m-d H:i');
            } else {
                $TT->HEnd = 'No disponible'; // Valor por defecto si HEnd es nulo
            }
        }

         $Array = $TiemposTrabajados->toArray();
         $perPage = 10; // Cantidad de elementos por página
         $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
         $offset = ($currentPage - 1) * $perPage;
         $items = array_slice($Array, $offset, $perPage);
     
         $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);
 
         $Busqueda = 0;

         $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

         $BotonVerTodos = "NoVer";
     
         return view('/TiempoTrabajado/TiempoT', compact('DatosPaginados',"Busqueda","Usuarios", "BotonVerTodos"));
    }
    

    public function create()
    {
        $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

        $Maquinas = Machine::where('Status', "1")->get();

        return view('/TiempoTrabajado/NuevoTiempoT',compact("Usuarios", "Maquinas"));
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Inicio' => 'required',
            'Final' => 'required',
            'Usuario' => 'required|exists:Users,IdUser',
            'Maquina' => 'required',
            'Fecha' => 'required|date',
        ], [
            'Inicio.required' => 'El campo Inicio es obligatorio.',
            'Final.required' => 'El campo Final es obligatorio.',
            'Maquina.required' => 'Debe seleccionar una Maquina.',
            'Usuario.required' => 'Debe seleccionar un Empleado.',
            'Usuario.exists' => 'El empleado seleccionado no es válido.',
            'Fecha.required' => 'El campo Fecha de comienzo es obligatorio.',
            'Fecha.date' => 'El campo Fecha de comienzo debe ser una fecha válida.',
        ]);


        $fecha = $request->input('Fecha'); // La fecha en formato 'Y-m-d'
        $inicio = $request->input('Inicio'); // La hora de inicio en formato 'H:i'
        $final = $request->input('Final'); // La hora de final en formato 'H:i'

        $fechaInicio = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$inicio}");
        $fechaFinal = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$final}");

        if ($fechaFinal->lessThan($fechaInicio)) {
            $fechaFinal->addDay();
        }

        $NuevoRegistro = new TimeWorked();
        $NuevoRegistro->HStart = $fechaInicio;
        $NuevoRegistro->HEnd = $fechaFinal;
        $NuevoRegistro->IdUser = $request->input('Usuario');
        $NuevoRegistro->IdMachine = $request->input('Maquina');
        $NuevoRegistro->save();

        return redirect("/TiempoTrabajado");
    }


    public function show($id)
    {
        $TiempoT= TimeWorked::where('IdTimeWorked', $id)->with("machine", "user")->first();

        return view('/TiempoTrabajado/EliminarTiempoT', compact('TiempoT'));
    }


    public function edit($id)
    {
        $TiempoT= TimeWorked::where('IdTimeWorked', $id)->first();

        $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

        $Maquinas = Machine::where('Status', "1")->get();

        return view('/TiempoTrabajado/EditarTiempoT', compact('TiempoT',"Usuarios", "Maquinas"));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'Inicio' => 'required',
            'Final' => 'required',
            'Fecha' => 'required|date',
        ], [
            'Inicio.required' => 'El campo Inicio es obligatorio.',
            'Final.required' => 'El campo Final es obligatorio.',
            'Fecha.required' => 'El campo Fecha de comienzo es obligatorio.',
            'Fecha.date' => 'El campo Fecha de comienzo debe ser una fecha válida.',
        ]);

        $fecha = $request->input('Fecha'); // La fecha en formato 'Y-m-d'
        $inicio = $request->input('Inicio'); // La hora de inicio en formato 'H:i'
        $final = $request->input('Final'); // La hora de final en formato 'H:i'

        $fechaInicio = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$inicio}");
        $fechaFinal = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$final}");

        if ($fechaFinal->lessThan($fechaInicio)) {
            $fechaFinal->addDay();
        }

        $TiempoTrabajado = TimeWorked::find($id);

        $TiempoTrabajado->HStart = $fechaInicio;
        $TiempoTrabajado->HEnd = $fechaFinal;
        $TiempoTrabajado->save();

        return redirect("/TiempoTrabajado");

    }


    public function destroy($id)
    {

    $TT = TimeWorked::find($id);
    $TT->delete();

    return redirect("/TiempoTrabajado");
    
}
}

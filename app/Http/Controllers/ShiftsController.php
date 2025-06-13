<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Shift;
use Carbon\Carbon;

class ShiftsController extends Controller
{

    public function index()
    {
        $Turnos= Shift::Where('Status', "1")->get();

        foreach ($Turnos as $I) {
            $I->StartTime = new \DateTime($I->StartTime);
            $I->StartTime = $I->StartTime->format('H:i');
            $I->EndTime = new \DateTime($I->EndTime);
            $I->EndTime = $I->EndTime->format('H:i');
        }

        // Convertir la colección a un array
        $Array = $Turnos->toArray();
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Turnos/Turnos', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
    }
    

    public function create()
    {
        return view('/Turnos/NuevoTurno');
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Turno' => 'required|max:255',
            'Inicio' => 'required|max:255',
            'Final' => 'required|max:255'
        ], [
            'Turno.required' => 'El campo Turno es obligatorio.',
            'Turno.max' => 'El campo de Turno no debe exceder los 255 caracteres.',
            'Inicio.required' => 'El campo es obligatorio.',
            'Inicio.max' => 'El campo no debe exceder los 255 caracteres.',
            'Final.required' => 'El campo es obligatorio.',
            'Final.max' => 'El campo no debe exceder los 255 caracteres.'
        ]);

        //dd($request);

        $Turno = Shift::where('Shift', $request->Turno)->where('Status', 1)->first();

        if(!is_null($Turno)){
            return redirect()->back()->withErrors(['Turno' => 'Este Turno ya esta registrado'])->withInput();
        }

        $Turno = new Shift();
            
        $Turno->Shift=$request->Turno;
        $Turno->StartTime=$request->Inicio;
        $Turno->EndTime=$request->Final;
        $Turno->save();

        return redirect("/Turnos");
    }


    public function show($id)
    {
        $Turno = Shift::where('IdShift', $id)->first();

        $Turno->StartTime = new \DateTime($Turno->StartTime);
        $Turno->StartTime = $Turno->StartTime->format('H:i');
        $Turno->EndTime = new \DateTime($Turno->EndTime);
        $Turno->EndTime = $Turno->EndTime->format('H:i');
        

        return view('/Turnos/EliminarTurno', compact('Turno'));
    }


    public function edit($id)
    {
        $Turno = Shift::where('IdShift', $id)->first();

        $Turno->StartTime = new \DateTime($Turno->StartTime);
        $Turno->StartTime = $Turno->StartTime->format('H:i');
        $Turno->EndTime = new \DateTime($Turno->EndTime);
        $Turno->EndTime = $Turno->EndTime->format('H:i');
        

        return view('/Turnos/EditarTurno', compact('Turno'));
    }


    public function update(Request $request, $id)
    {

        //dd($request->Descripcion);

        $Turno=Shift::find($id);

        $Turno->Shift=$request->Turno;
        $Turno->StartTime=$request->Inicio;
        $Turno->EndTime=$request->Final;
        $Turno->UpdateDate = Carbon::now()->format('Y-m-d H:i:s');

        $Turno->save();

        return redirect("/Turnos");

    }


    public function destroy($id)
    {
        $Maquina=Shift::find($id);
        $Maquina->status="0";
        $Maquina->save();

        return redirect("/Turnos");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;

class MachinesController extends Controller
{

    public function index()
    {
        $Maquinas = Machine::Where('Status', "1")->get();
        $MaquinasArray = $Maquinas->toArray();
    
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($MaquinasArray, $offset, $perPage);
    
        $maquinasPaginadas = new LengthAwarePaginator($items, count($MaquinasArray), $perPage, $currentPage);
        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Maquinas/Maquinas', compact('maquinasPaginadas',"Busqueda","BotonVerTodos"));
    }
    

    public function create()
    {
        return view('/Maquinas/NuevaMaquina');
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Descripcion' => 'required|max:255',
            'Codigo' => 'required|max:255'
        ], [
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
            'Codigo.required' => 'El campo Codigo es obligatorio.',
            'Codigo.max' => 'El campo Codigo no debe exceder los 255 caracteres.'
        ]);

        $Prueba = Machine::where('MachineCode', $request->Codigo)->Where('Status', "1")->first();

        if(!is_null($Prueba)){
            return redirect()->back()->withErrors(['Codigo' => 'Este codigo ya esta en uso'])->withInput();
        }

        $Maquina = new Machine();
          
        $Maquina->Tipo=$request->Tipo;
        $Maquina->Description=$request->Descripcion;
        $Maquina->MachineCode=$request->Codigo;

        $Maquina->save();

        return redirect("/Maquinas");
    }

    public function Limpiar($id)
    {
        $Usuario = User::where('Machine', $id)->first();

        if(!empty($Usuario->Machine)){
            $Usuario->Machine = 0;
            $Usuario->save();
        }

        return redirect('/Maquinas');
    }


    public function show($id)
    {
        $Maquina = Machine::where('IdMachine', $id)->first();

        return view('/Maquinas/EliminarMaquina', compact('Maquina'));
    }


    public function edit($id)
    {
        $Maquina = Machine::where('IdMachine', $id)->first();

        return view('/Maquinas/EditarMaquina', compact('Maquina'));
    }


    public function update(Request $request, $id)
    {
        
        $Prueba = Machine::where('MachineCode', $request->Codigo)->Where('Status', "1")->where('IdMachine', '!=', $id)->first();

        if(!is_null($Prueba)){
            return redirect()->back()->withErrors(['Codigo' => 'Este codigo ya esta en uso'])->withInput();
        }

        $Maquina=Machine::find($id);

        $Maquina->Tipo=$request->Tipo;
        $Maquina->Description=$request->Descripcion;
        $Maquina->MachineCode=$request->Codigo;
        $Maquina->UpdateDate = Carbon::now()->format('Y-m-d H:i:s');
        $Maquina->save();

        return redirect("/Maquinas");

    }


    public function destroy($id)
    {
        $Maquina=Machine::find($id);
        $Maquina->status="0";
        $Maquina->save();

        return redirect("/Maquinas");
    }
}

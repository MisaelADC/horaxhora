<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Cycle;
use App\Models\Product;
use App\Models\Machine;
use Carbon\Carbon;

class CyclesController extends Controller
{

    // public function index()
    // {
    //     $Ciclos = Cycle::Where('Status', "1")->limit(30)->get();
    //     $Maquinas = Machine::Where('Status', "1")->get();
    //     $Productos = Product::Where('Status', "1")->get();

    //     foreach( $Ciclos as $ci){
            
    //         $Maquina = Machine::Where('IdMachine', $ci->IdMachine)->first();
    //         $Producto = Product::Where("IdProduct", $ci->IdProduct)->first();

    //         $ci->Maquina = $Maquina->MachineCode;
    //         $ci->Producto = $Producto->ItemCode;
    //         $ci->MaquinaD = $Maquina->Description;
    //         $ci->ProductoD = $Producto->Description;
    //     }

    //     $Array = $Ciclos->toArray();
    //     $perPage = 10; 
    //     $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
    //     $offset = ($currentPage - 1) * $perPage;
    //     $items = array_slice($Array, $offset, $perPage);
    
    //     $CiclosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);
    //     $Busqueda = 0;

    //     $BotonVerTodos = "NoVer";
    
    //     return view('/Ciclos/Ciclos', compact('CiclosPaginados',"Maquinas","Productos", "BotonVerTodos"));
    // }
    

    // public function create()
    // {
    //     $Maquinas = Machine::Where('Status', "1")->get();
    //     $Productos = Product::Where('Status', "1")->get();
        
    //     return view('/Ciclos/NuevoCiclo', compact("Maquinas","Productos"));
    // }


    // public function store(Request $request)
    // {
           
    //     $request->validate([
    //         'Producto' => 'required|not_in:',
    //         'Maquina' => 'required|not_in:',
    //         'Duracion' => 'required|max:255',
    //     ], [
    //         'Maquina.required' => 'Debe seleccionar una Maquina.',
    //         'Maquina.not_in' => 'Debe seleccionar una Maquina válida.',
    //         'Producto.required' => 'Debe seleccionar un producto.',
    //         'Producto.not_in' => 'Debe seleccionar un producto válido.',
    //         'Duracion.required' => 'El campo Descripcion es obligatorio.',
    //         'Duracion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
    //     ]);

    //     $Cic = Cycle::Where('IdMachine', $request->Maquina)->Where("IdProduct", $request->Producto)->Where('Status', "1")   ->first();

    //     if(!empty($Cic->duration)){
    //         return redirect()->back()->withErrors(['Producto' => "Ya existe este ciclo",
    //         "Maquina" => "Ya existe este ciclo"])->withInput();
    //     }

    //     $Ciclo = new Cycle();
            
    //     $Ciclo->duration=$request->Duracion;
    //     $Ciclo->IdProduct=$request->Producto;
    //     $Ciclo->IdMachine=$request->Maquina;

    //     $Ciclo->save();

    //     return redirect("/Ciclos");
    // }


    // public function show($id)
    // {
    //     $Ciclo = Cycle::where('IdCycle', $id)->first();
            
    //     $Maquina = Machine::Where('IdMachine', $Ciclo->IdMachine)->first();
    //     $Producto = Product::Where("IdProduct", $Ciclo->IdProduct)->first();

    //     $Ciclo->Maquina = $Maquina->MachineCode;
    //     $Ciclo->Producto = $Producto->ItemCode;
    //     $Ciclo->MaquinaD = $Maquina->Description;
    //     $Ciclo->ProductoD = $Producto->Description;

    //     return view('/Ciclos/EliminarCiclo', compact('Ciclo'));
    // }


    // public function edit($id)
    // {
    //     $Ciclo = Cycle::find($id);
    //     $Maquinas = Machine::Where('Status', "1")->get();
    //     $Productos = Product::Where('Status', "1")->get();

    //     return view('/Ciclos/EditarCiclo', compact('Ciclo',"Maquinas","Productos"));
    // }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'Duracion' => 'required|max:255',
    //     ], [
    //         'Duracion.required' => 'El campo Descripcion es obligatorio.',
    //         'Duracion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
    //     ]);

    //     $Cic = Cycle::Where('IdMachine', $request->Maquina)->Where("IdProduct", $request->Producto)->Where('Status', "1")->where('IdCycle', '!=', $id)->first();

    //     if(!empty($Cic->duration)){
    //         return redirect()->back()->withErrors(['Producto' => "Ya existe este ciclo",
    //         "Maquina" => "Ya existe este ciclo"])->withInput();
    //     }

    //     $Ciclo=Cycle::find($id);

    //     $Ciclo->duration=$request->Duracion;
    //     $Ciclo->IdMachine=$request->Maquina;
    //     $Ciclo->IdProduct=$request->Producto;
    //     $Ciclo->UpdateDate = Carbon::now()->format('Y-m-d H:i:s');
    //     $Ciclo->save();

    //     return redirect("/Ciclos");

    // }


    // public function destroy($id)
    // {

    //     $Ciclo=Cycle::find($id);
    //     $Ciclo->status="0";
    //     $Ciclo->save();

    //     return redirect("/Ciclos");
    // }
}

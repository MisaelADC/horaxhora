<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB; 
use App\Models\Wo;
use App\Models\Product;
use Carbon\Carbon;

class WoController extends Controller
{

    public function index()
    {

        $FinalizarWo = Wo::where("Status", "1")
        ->whereHas('TotalReal', function($query) {
            $query->where('TotalProducido', '>=', DB::raw('Wo.Meta'));
        })
        ->with("TotalReal")
        ->get();

        if(!empty($FinalizarWo["0"]["IdWo"])){
            foreach ($FinalizarWo as $FW) {
                $FW->Status = 2;
                $FW->save();
            }
        }

        $Wo = Wo::with('product')->where("Status", "1")->with("TotalReal")->get();

        $Array = $Wo->toArray();
    
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Wo/Wo', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
    }
    

    public function create()
    {
        $Productos = Product::where('Status', "1")->get();
        
        return view('/Wo/NuevaWo', compact("Productos"));
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Producto' => 'required|max:255',
            'Cantidad' => 'required|numeric|min:0',
        ], [
            'Producto.required' => 'El campo es obligatorio.',
            'Producto.max' => 'El campo no debe exceder los 255 caracteres.',
            'Wo.required' => 'El campo es obligatorio.',
            'Wo.max' => 'El campo no debe exceder los 255 caracteres.',
            'Cantidad.required' => 'El campo es obligatorio.',
            'Cantidad.numeric' => 'El campo debe ser un número.', // Mensaje si no es un número
            'Cantidad.min' => 'El campo no puede ser menor a 0.', // Mensaje si es menor que 0
        ]);
        
        $Wo = new  Wo(); 
        $Wo->IdProduct = $request->Producto;
        $Wo->Wo = $request->Wo;
        $Wo->Meta = $request->Cantidad;

        if(!is_null($request->Diseño)){
            $Wo->Design = $request->Diseño;
        }

        $Wo->save();

        return redirect("/Wo");
    }


    public function show($id)
    {
        $Productos = Product::where('Status', "1")->get();

        $Wo = Wo::where('IdWo', $id)->first();

        return view('/Wo/EliminarWo', compact("Wo"));
    }


    public function edit($id)
    {
        $Productos = Product::where('Status', "1")->get();
        
        $Wo = Wo::where('IdWo', $id)->with("product", "design")->first();

        return view('/Wo/EditarWo', compact('Productos', "Wo"));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'Producto' => 'required|max:255',
            'Cantidad' => 'required|numeric|min:0', // Aquí validamos que no sea negativo
        ], [
            'Producto.required' => 'El campo es obligatorio.',
            'Producto.max' => 'El campo no debe exceder los 255 caracteres.',
            'Wo.required' => 'El campo es obligatorio.',
            'Wo.max' => 'El campo no debe exceder los 255 caracteres.',
            'Cantidad.required' => 'El campo es obligatorio.',
            'Cantidad.numeric' => 'El campo debe ser un número.', // Mensaje si no es un número
            'Cantidad.min' => 'El campo no puede ser menor a 0.', // Mensaje si es menor que 0
        ]);

        $Wo = Wo::find($id); 

        if($request->Diseño == null && $Wo->IdProduct != $request->Producto){
                return back()->withErrors(['Diseno' => 'Selecciona un diseño al cambiar de producto']);
        }   

        $Wo->IdProduct = $request->Producto;
        $Wo->Wo = $request->Wo;
        $Wo->Meta = $request->Cantidad;


        if(!is_null($request->Diseño)){
            $Wo->Design = $request->Diseño;
        }

        $Wo->save();

        return redirect("/Wo");
    }

    public function destroy($id)
    {

        $Wo = Wo::where('IdWo', $id)->first();

        $Wo->Status = "0";

        $Wo->save();

        return redirect("/Wo");
    }
}

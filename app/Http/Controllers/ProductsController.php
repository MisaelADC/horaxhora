<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Product;
use Carbon\Carbon;

class ProductsController extends Controller
{

    public function index()
    {
        $Productos = Product::Where('Status', "1")->get();
    
        // Convertir la colección a un array
        $Array = $Productos->toArray();
    
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        //dd($DatosPaginados);

        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Productos/Productos', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
    }
    

    public function create()
    {
        return view('/Productos/NuevoProducto');
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Descripcion' => 'required|max:255',
            'Codigo' => 'required|max:255',
            'Ciclo' => 'required|max:255|numeric',
        ], [
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
            'Codigo.required' => 'El campo Codigo es obligatorio.',
            'Codigo.max' => 'El campo Codigo no debe exceder los 255 caracteres.',
            'Ciclo.required' => 'El campo es obligatorio.',
            'Ciclo.max' => 'El campo no debe exceder los 255 caracteres.',
            'Ciclo.numeric' => 'El campo se llena solo con numeros enteros.',
        ]);

        // $Prueba = Product::where('ItemCode', $request->Codigo)->first();

        // if(!is_null($Prueba)){
        //     return redirect()->back()->withErrors(['Codigo' => 'Este codigo ya esta en uso'])->withInput();
        // }

        $Producto = new Product();
            
        $Producto->Description=$request->Descripcion;
        $Producto->ItemCode=$request->Codigo;
        $Producto->Quantity="0";
        $Producto->Cycle=$request->Ciclo;
        $Producto->Tipo = $request->Tipo;

        $Producto->save();

        return redirect("/Productos");
    }


    public function show($id)
    {
        
        $Producto = Product::where('IdProduct', $id)->first();

        return view('/Productos/EliminarProducto', compact('Producto'));
    }


    public function edit($id)
    {
        $Producto = Product::where('IdProduct', $id)->first();

        return view('/Productos/EditarProducto', compact('Producto'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'Descripcion' => 'required|max:255',
            'Codigo' => 'required|max:255',
            'Ciclo' => 'required|max:255|numeric'
        ], [
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
            'Codigo.required' => 'El campo Codigo es obligatorio.',
            'Codigo.max' => 'El campo Codigo no debe exceder los 255 caracteres.',
            'Ciclo.required' => 'El campo es obligatorio.',
            'Ciclo.max' => 'El campo no debe exceder los 255 caracteres.',
            'Ciclo.numeric' => 'El campo se llena solo con numeros enteros.',
        ]);

        $Producto = Product::find($id);

        $Producto->Description=$request->Descripcion;
        $Producto->ItemCode=$request->Codigo;
        $Producto->Cycle=$request->Ciclo;
        $Producto->Tipo = $request->Tipo;
        $Producto->UpdateDate= Carbon::now()->format('Y-m-d H:i:s');
        $Producto->save();

        return redirect("/Productos");

    }


    public function destroy($id)
    {
        $Maquina=Product::find($id);
        $Maquina->status="0";
        $Maquina->save();

        return redirect("/Productos");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Design;
use App\Models\Product;

class DesignsController extends Controller
{

    public function getDesigns($productId)
    {
        $designs = Design::where('IdProduct', $productId)->where("Status", 1)->get();
        return response()->json($designs);
    }
 
    public function index()
    {
      
    }
    

    public function create()
    {

    }


    public function store(Request $request)
    {

        $request->validate([
            'Nombre' => 'required|max:50',
        ], [
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
        ]);

        $Diseño = new Design();

        $Diseño->Name=$request->Nombre;
        $Diseño->Image = "/";
        $Diseño->IdProduct = $request->IdProducto;

        $Diseño->save();

        if ($request->hasFile('Imagen')) {
        $Imagen = $request->file('Imagen');
        $extension = $Imagen->extension();
        $new_name = $Diseño->IdDesign .".". $extension;
        $path = public_path('/Images'); 
        $Imagen->move($path, $new_name);
        $Diseño->Image = '/Images/' . $new_name;
        $Diseño->save();
         }

         return redirect("/Diseño/".$request->IdProducto);
    }


    public function show($id)
    {
        
        $Diseños = Design::where('IdProduct', $id)->where("Status", 1)->get();
        $Producto = Product::find($id);

        return view('/Diseños/Diseños', compact('Diseños', "Producto"));
    }


    public function edit($id)
    {
        $Diseño = Design::find($id);

        return view('/Diseños/EditarDiseño', compact('Diseño'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'Nombre' => 'required|max:50',
        ], [
            'Nombre.required' => 'El campo Descripcion es obligatorio.',
            'Nombre.max' => 'El campo de Descripcion no debe exceder los 255 caracteres.',
        ]);

        $Diseño = Design::find($id);
        $Diseño->Name=$request->Nombre;
        $Diseño->save();

        if ($request->hasFile('Imagen')) {
            $imagenAntigua = public_path($Diseño->Image);
            
            if (file_exists($imagenAntigua) && !empty($Diseño->Image)) {
                unlink($imagenAntigua);
            }
        
            $Imagen = $request->file('Imagen');
            $extension = $Imagen->extension();
            $new_name = $id .".". $extension;
            $path = public_path('/Images'); 
            $Imagen->move($path, $new_name);
        }

        return redirect("/Diseño/".$Diseño->IdProduct);

    }


    public function destroy($id)
    {
        $Diseño = Design::with("producto")->find($id);
        $Diseño->Status="0";
        $Diseño->save();

        return redirect("/Diseño/".$Diseño->producto->IdProduct);
    }
}

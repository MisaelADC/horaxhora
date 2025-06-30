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
use App\Models\Production;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function index()
    {
        $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

        $Array = $Usuarios->toArray();
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Usuarios/Usuarios', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
    }

    public function create()
    {
        return view('Usuarios.NuevoUsuario');
    }

    public function store(Request $request)
    {   

        $request->validate([
            'Codigo' => [
                'required',
                Rule::unique('Users', 'CodeEmp')->Where("status", "1"),
            ],
            'Nombre' => 'required|string|max:255',
            'Rol' => 'required', 
            'Contraseña' => 'required|string'
        ], [
            'Codigo.required' => 'El código de empleado es obligatorio.',
            'Codigo.unique' => 'El código de empleado ya existe en la base de datos.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            'Rol.required' => 'El tipo de empleado es obligatorio.',
            'Contraseña.required' => 'La contraseña es obligatoria.'
        ]);

        $Usuario = new User;
        $Usuario->CodeEmp=$request->Codigo;
        $Usuario->Name=$request->Nombre;
        $Usuario->Type=$request->Rol;
        $Usuario->Password=Hash::make($request->Contraseña);
        $Usuario->save();

        // if ($request->hasFile('Imagen')) {
        //     $Imagen = $request->file('Imagen');
        //     $extension = $Imagen->extension();
        //     $new_name = $Usuario->IdUser . "-1." . $extension;
        //     $path = public_path('/Images'); 
        //     $Imagen->move($path, $new_name);
        //     $Usuario->Image = '/Images/' . $new_name;
        //     $Usuario->save();
        // }

        return redirect("/Usuarios");
    }

    public function show($id)  
    {
        $User = User::where('IdUser', $id)->first();
        return view('Usuarios.EliminarUsuario', compact('User'));
    }
    
    public function CContraseña(Request $request, $id)  
    {
        $request->validate([
            'NContraseña' => 'required|string|max:255',
        ], [
            'NContraseña.required' => 'El campo es obligatorio.',
            'NContraseña.string' => 'Debe tener caracteres',
            'NContraseña.max:255' => 'La Contraseña no debe ser tan larga'
        ]);

        $Respuesta = Hash::make($request->NContraseña);
        $Usuario = User::find($id);
        $Usuario->Password=Hash::make($request->NContraseña);
        $Usuario->save();

        return redirect("/Usuarios");
    }

    public function edit($id)
    {
        $User = User::where('IdUser', $id)->first();
        return view('Usuarios.EditarUsuario', compact('User'));
    }

    public function update(Request $request, $id)
    {
   
        $request->validate([
            'Codigo' => [
                'required',
                Rule::unique('Users', 'CodeEmp')->Where("status", "1")->ignore($id, 'IdUser'),
            ],
            'Nombre' => 'required|string|max:255', 
            'Rol' => 'required', 
        ], [
            'Codigo.required' => 'El código de empleado es obligatorio.',
            'Codigo.unique' => 'El código de empleado ya existe en la base de datos.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Nombre.max' => 'El nombre no debe superar los 255 caracteres.',
        ]);

        $Usuario = User::find($id);
        $Usuario->CodeEmp=$request->Codigo;
        $Usuario->Name=$request->Nombre;
        $Usuario->Type=$request->Rol;
        $Usuario->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $Usuario->save();

        // if ($request->hasFile('Imagen')) {
        // $imagenAntigua = public_path($Usuario->Image);
        // // Eliminar la imagen anterior si existe
        // if (file_exists($imagenAntigua) && !empty($Usuario->Image)) {
        // unlink($imagenAntigua);
        // }
        //     $Imagen = $request->file('Imagen');
        //     $extension = $Imagen->extension();
        //     $new_name = $Usuario->IdUser . "-1." . $extension;
        //     $path = public_path('/Images'); 
        //     $Imagen->move($path, $new_name);
        //     $Usuario->Image = '/Images/' . $new_name;
        //     $Usuario->save();
        // }

        return redirect("/Usuarios");
    }

    public function destroy($id)
    {
      
        $Usuario = User::find($id);

        $Usuario->Status="0";

        $Usuario->save();

        return redirect("/Usuarios");
    }
}

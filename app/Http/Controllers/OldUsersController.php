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
            'Telefono' => 'required|string|regex:/^\d{10}$/',
            'Area' => 'required|string|max:255',
            'Rol' => 'required|in:0,1', 
            'Contraseña' => 'required|string'
        ], [
            'Codigo.required' => 'El código de empleado es obligatorio.',
            'Codigo.unique' => 'El código de empleado ya existe en la base de datos.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            'Telefono.required' => 'El número de teléfono es obligatorio.',
            'Telefono.regex' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'Area.required' => 'El área es obligatoria.',
            'Area.max' => 'El área no debe superar los 255 caracteres.',
            'Rol.required' => 'El tipo de empleado es obligatorio.',
            'Rol.in' => 'El tipo de empleado debe ser "Empleado" o "Administrador".',
            'Contraseña.required' => 'La contraseña es obligatoria.',
        ]);

        $Usuario = new User;

        $Usuario->CodeEmp=$request->Codigo;
        $Usuario->Name=$request->Nombre;
        $Usuario->PhoneNumber=$request->Telefono;
        $Usuario->Area=$request->Area;
        $Usuario->Type=$request->Rol;
        $Usuario->Password=Hash::make($request->Contraseña);
        $Usuario->save();

        if ($request->hasFile('Imagen')) {
            $Imagen = $request->file('Imagen');
            $extension = $Imagen->extension();
            $new_name = $Usuario->IdUser . "-1." . $extension;
            $path = public_path('/Images'); 
            $Imagen->move($path, $new_name);
            $Usuario->Image = '/Images/' . $new_name;
            $Usuario->save();
        }

        return redirect("/Usuarios");
    }

    public function show($id)  
    {
        $User = User::where('IdUser', $id)->first();
        return view('Usuarios.EliminarUsuario', compact('User'));
    }

    public function Gestion()  
    {
        $idUser = session()->get('user_id');

        $fechaH = Carbon::now()->format('Y-m-d'); 
        $horaH = Carbon::now()->format('H:i:s'); 

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
            })
            ->first(); 

        if(empty($Turno->IdShift)){
            $CantidadOptimaH = null;
                $Produccion = null;
                $Hxh = null;
                $HxhActual = null;

                return view("Operador/Principal", compact('CantidadOptimaH', "Produccion", "Hxh", "HxhActual"));
        }

            if($Turno->StartTime > $Turno->EndTime && $horaH < $Turno->StartTime){

            $FechaAyer = Carbon::yesterday()->format('Y-m-d');
            $Production = Production::where('IdUser', $idUser)
            ->whereDate('Date', $FechaAyer)
            ->where('IdShift', $Turno->IdShift)
            ->where('status', "1")
            ->first();
            }else{
                $Production = Production::where('IdUser', $idUser)
                ->whereDate('Date', $fechaH)
                ->where('IdShift', $Turno->IdShift)
                ->where('status', "1")
                ->first();
            }

            if(empty($Production->IdProduction)){

                $idUsuario = session()->get('user_id' );
                $Usuario = User::Where('IdUser', $idUsuario)->get();
                $Usuario->status = 2;
        
                session([
                    'Estatus' =>  2,
                ]);            

                $CantidadOptimaH = null;
                $Produccion = null;
                $Hxh = null;
                $HxhActual = null;

                return view("Operador/Principal", compact('CantidadOptimaH', "Produccion", "Hxh", "HxhActual"));
            }

            $Producto = Product::where('IdProduct', $Production->IdProduct)->first();
            $Maquina = Machine::where('IdMachine', $Production->IdMachine)->first();
            $Production->Product = $Producto->Description;
            $Production->Machine = $Maquina->Description;

            session([
                'IdProduccion' =>  $Production->IdProduction,
            ]); 

        $Producto = Product::where('IdProduct',$Production->IdProduct)
        ->first();    

        $start = new \DateTime($Turno->StartTime);
        $end = new \DateTime($Turno->EndTime);
        $interval = $start->diff($end);

        $horas = $interval->h;
        $minutos = $interval->i;

        $CantidadOptimaH = 3600 / $Producto->Cycle;

        $Produccion = $Production;

        $Bandera = false;

        $Hxh = CatShiftHxh::where('IdProduction', $Production->IdProduction)->get();
        foreach ($Hxh as $I) {
            if($horaH > $I->HStart && $horaH < $I->HEnd){
               $Bandera = true;
            }
        }

        if($Bandera === false){
            $Hora = Carbon::now();
            $hxh = new CatShiftHxh();
            $HoraSql = $Hora->format('H:00');
            $hxh->HStart = $HoraSql;
            $Hora->addHours(1);
            $HoraSql = $Hora->format('H:00');
            $hxh->HEnd=$HoraSql;
            $hxh->Real="0";
            $hxh->Scrap="0";
            $hxh->IdProduction=$Produccion->IdProduction;
            $hxh->save();
            }

        $Hxh = CatShiftHxh::where('IdProduction',$Production->IdProduction)->get();

        $Contador = -1;

        foreach ($Hxh as $I) {

            $I->HStart = new \DateTime($I->HStart);
            $I->HStart  = $I->HStart->format('H:i');
            $I->HEnd = new \DateTime($I->HEnd);
            $I->HEnd = $I->HEnd->format('H:i');
            $Contador++;
        }

        $HxhActual = $Contador;

        return view("Operador/Principal", compact('CantidadOptimaH', "Produccion", "Hxh", "HxhActual"));

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
            'Telefono' => 'required|string|regex:/^\d{10}$/',
            'Area' => 'required|string|max:255',
            'Rol' => 'required|in:0,1', 
        ], [
            'Codigo.required' => 'El código de empleado es obligatorio.',
            'Codigo.unique' => 'El código de empleado ya existe en la base de datos.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            'Telefono.required' => 'El número de teléfono es obligatorio.',
            'Telefono.regex' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'Area.required' => 'El área es obligatoria.',
            'Area.max' => 'El área no debe superar los 255 caracteres.',
        ]);

        $Usuario = User::find($id);

        $Usuario->CodeEmp=$request->Codigo;
        $Usuario->Name=$request->Nombre;
        $Usuario->PhoneNumber=$request->Telefono;
        $Usuario->Area=$request->Area;
        $Usuario->Type=$request->Rol;
        $Usuario->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $Usuario->save();

        if ($request->hasFile('Imagen')) {

        $imagenAntigua = public_path($Usuario->Image);

        // Eliminar la imagen anterior si existe
        if (file_exists($imagenAntigua) && !empty($Usuario->Image)) {
        unlink($imagenAntigua);
        }

            $Imagen = $request->file('Imagen');
            $extension = $Imagen->extension();
            $new_name = $Usuario->IdUser . "-1." . $extension;
            $path = public_path('/Images'); 
            $Imagen->move($path, $new_name);
            $Usuario->Image = '/Images/' . $new_name;
            $Usuario->save();
        }

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

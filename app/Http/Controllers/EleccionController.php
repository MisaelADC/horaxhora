<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Machine;
use App\models\Shift;
use App\models\Product;
use App\models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;


class EleccionController extends Controller

{

    public function E_Producto($R)
    {
        $Maquinas = Product::Where('Status', "1")->get();

        $MaquinasArray = $Maquinas->toArray();
    
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($MaquinasArray, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($MaquinasArray), $perPage, $currentPage);

        $Busqueda = 1;
    
        return view('/Productos/Productos', compact('DatosPaginados',"Busqueda","R"));
    }
    
    public function E_Maquina($R)
    {
        $Maquinas = Machine::Where('Status', "1")->get();
    
        $MaquinasArray = $Maquinas->toArray();
    
        // Paginar los resultados
        
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($MaquinasArray, $offset, $perPage);
    
        $maquinasPaginadas = new LengthAwarePaginator($items, count($MaquinasArray), $perPage, $currentPage);

        $Busqueda = 1;
    
        return view('/Maquinas/Maquinas', compact('maquinasPaginadas',"Busqueda","R"));
    }

    public function E_Turno($R)
    {
        $Turnos= Shift::Where('Status', "1")->get();
    
        // Convertir la colección a un array
        $Array = $Turnos->toArray();
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 1;
    
        return view('/Turnos/Turnos', compact('DatosPaginados',"Busqueda","R"));
    }

    public function E_Usuario($R)
    {
        $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

        // Convertir la colección a un array
        $Array = $Usuarios->toArray();
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 1;

        return view('/Usuarios/Usuarios', compact('DatosPaginados',"Busqueda","R"));
    }

    public function Elegir(Request $request)
    {

        switch ($request->Tabla) {
            case 'Productos':
            session([
                'E_ProductoId' => $request->Id,
                'E_Producto' => $request->Nombre,
            ]);

            if($request->R == 0){
            return redirect("/Produccion/create");
            }

            return redirect("/Produccion/".$request->R."/edit");

            break;

            case 'Maquinas':
            session([
                'E_MaquinaId' => $request->Id,
                'E_Maquina' => $request->Nombre,
            ]);

            if($request->R == 0){
                return redirect("/Produccion/create");
            }
    
            return redirect("/Produccion/".$request->R."/edit");
            break;

            case 'Usuarios':
            session([
                'E_UsuarioId' => $request->Id,
                'E_Usuario' => $request->Nombre,
            ]);

            if($request->R == 0){
                return redirect("/Produccion/create");
            }
    
            return redirect("/Produccion/".$request->R."/edit");
            break;

            case 'Turnos':
            session([
                'E_TurnoId' => $request->Id,
                'E_Turno' => $request->Nombre,
            ]);
            if($request->R == 0){
                return redirect("/Produccion/create");
            }
    
            return redirect("/Produccion/".$request->R."/edit");
            break;

        }
    }
}

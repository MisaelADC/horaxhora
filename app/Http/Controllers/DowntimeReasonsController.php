<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\DowntimeReason;
use Carbon\Carbon;

class DowntimeReasonsController extends Controller
{

    public function index()
    {
        $Razones = DowntimeReason::where('Status', "1")->get();

        // Convertir la colección a un array
        $Array = $Razones->toArray();
    
        $perPage = 10; // Cantidad de elementos por página
        $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($Array, $offset, $perPage);
    
        $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

        $Busqueda = 0;

        $BotonVerTodos = "NoVer";
    
        return view('/Razones/Razones', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
    }
    

    public function create()
    {
        return view('/Razones/NuevaRazon');
    }


    public function store(Request $request)
    {
           
        $request->validate([
            'Razon' => 'required|max:255',
            'Tipo' => 'required',
            'Sustituto' => 'required',
        ], [
            'Razon.required' => 'El campo es obligatorio.',
            'Razon.max' => 'El campo de no debe exceder los 255 caracteres.',
            'Tipo.required' => 'El campo es obligatorio.',
            'Sustituto|.required' => 'El campo es obligatorio.',
        ]);

        $Razon = new  DowntimeReason(); 
        $Razon->Reason = $request->Razon;
        $Razon->Tipo = $request->Tipo;
        $Razon->replacement = $request->Sustituto;
        $Razon->save();

        return redirect("/Razones");
    }


    public function show($id)
    {
        $Razon = DowntimeReason::where('IdDowntimeReason', $id)->first();

        return view('/Razones/EliminarRazon', compact('Razon'));
    }


    public function edit($id)
    {
        $Razon = DowntimeReason::where('IdDowntimeReason', $id)->first();

        return view('/Razones/EditarRazon', compact('Razon'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'Razon' => 'required|max:255',
        ], [
            'Razon.required' => 'El campo es obligatorio.',
            'Razon.max' => 'El campo de no debe exceder los 255 caracteres.',
        ]);



        $Razon= DowntimeReason::find($id);
        $Razon->Reason = $request->Razon;
        $Razon->Tipo = $request->Tipo;
        $Razon->replacement = $request->Sustituto;
        $Razon->save();

        return redirect("/Razones");
    }


    public function destroy($id)
    {

        $Razon = DowntimeReason::where('IdDowntimeReason', $id)->first();

        $Razon->status="0";
        $Razon->save();
        
        return redirect("/Razones");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Production;
use App\Models\CatShiftHxh;
use App\Models\Shift;
use App\Models\CatTimeOut;
use App\Models\User;
use App\Models\DowntimeReason;
use Carbon\Carbon;

class TimesOffsController extends Controller
{

    public function index()
    {

    }
    

    public function create()
    {
        return view("TimesOuts/NuevoCTO");
    }


    public function store(Request $request)
    {
        $request->validate([
            'Inicio' => 'required|max:255',
            'Final' => 'required|max:255',
            'Descripcion' => 'required|max:255',

        ], [
            'Inicio.required' => 'El campo de hora de inicio obligatorio.',
            'Inicio.max' => 'El campo de hora de inicio no debe exceder los 255 caracteres.',
            'Final.required' => 'El campo de hora final es obligatorio.',
            'Final.max' => 'El campo de hora final no debe exceder los 255 caracteres.',
            'Descripcion.required' => 'El campo Descripcion es obligatorio.',
            'Descripcion.max' => 'El campo Descripcion no debe exceder los 255 caracteres.',
        ]);

        $Produccion= Production::Where('IdProduction', $request->IdProduccion)->first();
        $Turno= Shift::Where('IdShift', $Produccion->IdShift)->first();

        //dd($request->Inicio);

        $endTime = new \DateTime($Turno->EndTime);
        $startTime = new \DateTime($Turno->StartTime);

        // Tiempo muerto ingresado por el usuario
        $userStartTime = Carbon::createFromFormat('H:i', $request->Inicio);
        $userEndTime = Carbon::createFromFormat('H:i', $request->Final);

        //dd($startTime);

        if(!$userStartTime->between($startTime, $endTime)){
            return back()->withErrors(["Inicio" => "La hora esta fuera del horario"]);
        }

        if(!$userEndTime->between($startTime, $endTime)){
            return back()->withErrors(["Final" => "La hora esta fuera del horario"]);
        }

        $CTO = new CatTimeOut();
            
        $CTO->StartTime=$request->Inicio;
        $CTO->EndTime=$request->Final;
        $CTO->IdDowntimeReason=$request->Descripcion;
        $CTO->IdProduction=$request->IdProduccion;

        
        $CTO->save();
        
        return redirect("/HxH/".$request->IdProduccion);
    }


    public function show($id)
    {
        // $CTO = CatTimeOut::where('IdTimeOut', $id)->first();

        $CTO = CatTimeOut::where('IdTimeOut', $id)->where('Status', 1)
        ->with([ 'user', 'downtimeReason'])
        ->first();
 
        $CTO->StartTime = new \DateTime($CTO->StartTime);
        $CTO->StartTime = $CTO->StartTime->format('H:i');
        $CTO->EndTime = new \DateTime($CTO->EndTime);
        $CTO->EndTime = $CTO->EndTime->format('H:i');

        return view('/TimesOuts/EliminarCTO', compact('CTO'));
    }


    public function edit($id)
    {

        $CTO = CatTimeOut::where('IdTimeOut', $id)->where('Status', 1)
        ->with([ 'user', 'downtimeReason'])
        ->first();

        $CTO->StartTime = new \DateTime($CTO->StartTime);
        $CTO->StartTime = $CTO->StartTime->format('H:i');
        $CTO->EndTime = new \DateTime($CTO->EndTime);
        $CTO->EndTime = $CTO->EndTime->format('H:i');

        $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

        $Razones = DowntimeReason::all();

        return view('/TimesOuts/EditarCTO', compact('CTO', "Usuarios", "Razones"));
    }


    public function update(Request $request, $id)
    {

        $CTO = CatTimeOut::find($id);

        $CTO->StartTime=$request->Inicio;
        $CTO->EndTime=$request->Final;
        $CTO->IdDowntimeReason=$request->Descripcion;
        $CTO->save();

        $IdProduccion = session("CTO");

        return redirect("/HxH/".$CTO->IdProduction);

    }


    public function destroy($id)
    {
        $CTO=CatTimeOut::find($id);

        $CTO->status="0";
        $CTO->save();
        
        $IdProduccion = session("CTO");

        return redirect("/HxH/".$CTO->IdProduction);
    }
}

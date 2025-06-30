<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Production;
use App\Models\CatShiftHxh;
use App\Models\Shift;
use App\Models\Wo;
use App\Models\Machine;
use App\Models\Product;
use App\Models\DowntimeReason;
use App\Models\CatTimeOut;
use App\Models\User;
use App\Models\Cycle;
use App\Models\TimeWorked;
use Carbon\Carbon;

class HeatEquationExportController extends Controller
{

    public function exportHxHtoExcel($id)
    {
        // Obtener datos de la producción
        $Produccion = Production::where('IdProduction', $id)->with(['wo', "machine", "shift"])
            ->first();

        $Contador = 0;

        $fechaProduccion = $Produccion->Date;

        // Obtener datos de HxH
        $Hxh = CatShiftHxh::where('IdProduction', $id)->get();
        foreach ($Hxh as $I) {

            if ($Contador == 0) {
                $horaInicio = $I->HStart;
                $horaInicio = substr($horaInicio, 0, 8);
                $InicioB = Carbon::createFromFormat('Y-m-d H:i:s', $fechaProduccion . ' ' . $horaInicio);
            }

            $HF = $I->HEnd;

            $I->HStart = (new \DateTime($I->HStart))->format('H:i');
            $I->HEnd = (new \DateTime($I->HEnd))->format('H:i');

            $Contador++;

        }

        $horaFinal = $HF;
        $horaFinal = substr($horaFinal, 0, 8);
        $FinalB = Carbon::createFromFormat('Y-m-d H:i:s', $fechaProduccion . ' ' . $horaFinal);

        if ($FinalB <= $InicioB) {
            $FinalB->addDay(); // Aumenta un día si la hora final es medianoche
        }


        $horaInicio = $Produccion->shift->StartTime;
        $horaInicio = substr($horaInicio, 0, 8);
        $InicioT = Carbon::createFromFormat('Y-m-d H:i:s', $fechaProduccion . ' ' . $horaInicio);

        $horaFinal = $Produccion->shift->EndTime;
        $horaFinal = substr($horaFinal, 0, 8);
        $FinalT = Carbon::createFromFormat('Y-m-d H:i:s', $fechaProduccion . ' ' . $horaFinal);

        $FinalT->addHour();

        if ($InicioT > $FinalT) {
            $FinalT->addDay();
        }

        $TimesWorked = TimeWorked::where('IdProduction', $id)
            ->where('HStart', '>=', $InicioT)
            ->where('HStart', '<=', $FinalB)
            ->where('HEnd', '>=', $InicioB)
            ->where('HEnd', '<=', $FinalT)
            ->with(['user'])
            ->get();

        $Producto = Product::where("IdProduct", $Produccion->wo->IdProduct)->first();

        // Obtener datos de CTO (Tiempo Muerto)
        $CTO = CatTimeOut::where('IdProduction', $id)->where('Status', 1)
            ->with(['user', 'downtimeReason'])->get();
        foreach ($CTO as $I) {
            $I->StartTime = (new \DateTime($I->StartTime))->format('H:i');
            $I->EndTime = (new \DateTime($I->EndTime))->format('H:i');
            $Usuario = User::where('IdUser', $I->IdUser)->first();
            $I->User = !empty($I->User) ? $Usuario->Name : 'Sin Usuario';
        }

        // Crear una nueva instancia de PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $TiempoTotalT = 0;
        $mayorDuracion = 0;

        foreach ($TimesWorked as $time) {
            // Convierte HStart y HEnd en objetos DateTime para calcular la duración
            $inicio = new \DateTime($time->HStart);
            $fin = new \DateTime($time->HEnd);
        
            // Calcula la duración en segundos
            $duracion = $fin->getTimestamp() - $inicio->getTimestamp();

            $TiempoTotalT += $duracion;
        
            // Si la duración es mayor que la mayor duración hasta el momento, actualiza
            if ($duracion > $mayorDuracion) {
                $mayorDuracion = $duracion;
                $usuarioConMasTiempo = $time->user; // Obtén el usuario asociado
            }
        }

        // ========================
        // Tabla 1: Información de Producción
        // ========================
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Turno');
        $sheet->setCellValue('D1', 'Máquina');
        $sheet->setCellValue('E1', 'Wo');
        $sheet->setCellValue('F1', 'Operador');
        $sheet->setCellValue('G1', 'Producto');
        $sheet->setCellValue('H1', 'Meta');
        $sheet->setCellValue('I1', 'Real');
        //dd($Produccion);

        if($Produccion->wo->Tipo == "Maquina"){
            $sheet->setCellValue('J1', 'Scrap');
            $sheet->setCellValue('K1', 'Productividad');
            $sheet->setCellValue('L1', 'Eficiencia');
        }else{
            $sheet->setCellValue('J1', 'Productividad');
        }
       



        $FechaForm = date("d F Y", strtotime($Produccion->Date));
        // $sheet->setCellValue('J1', 'Empleado');
        $sheet->setCellValue('A2', $Produccion->IdProduction);
        $sheet->setCellValue('B2', $FechaForm);
        $sheet->setCellValue('C2', $Produccion->shift->Shift);
        $sheet->setCellValue('D2', $Produccion->machine->MachineCode . " " . $Produccion->machine->Description);
        $sheet->setCellValue('E2', $Produccion->wo->Wo);

        if(!empty($usuarioConMasTiempo->CodeEmp)){

            $sheet->setCellValue('F2', $usuarioConMasTiempo->CodeEmp . " " . $usuarioConMasTiempo->Name);
            
            }

        $sheet->setCellValue('G2', $Producto->ItemCode . " " . $Producto->Description);
        $sheet->setCellValue('H2', $Produccion->Meta);
        $sheet->setCellValue('I2', $Produccion->Real);
        
        if($Produccion->wo->Tipo == "Maquina"){
        $sheet->setCellValue('J2', $Produccion->Scrap);
        $sheet->setCellValue('K2', "=I2/H2");
        $sheet->setCellValue('L2', "=I2/(I2+J2)");
        $sheet->getStyle('K2')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('L2')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00); 
        }else{
            $sheet->setCellValue('J2', "=I2/H2");
            $sheet->getStyle('J2')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        }

        // $sheet->setCellValue('J2', $Produccion->user->CodeEmp." ".$Produccion->user->Name);

        // ========================
        // Tabla 2: Incidencias HxH
        // ========================
        $sheet->setCellValue('A4', 'Inicio');
        $sheet->setCellValue('B4', 'Fin');
        $sheet->setCellValue('C4', 'Buenos');

        if($Produccion->wo->Tipo == "Maquina"){
        $sheet->setCellValue('D4', 'Malos');
        }

        $row = 5;

        foreach ($Hxh as $hx) {
            $sheet->setCellValue('A' . $row, $hx->HStart);
            if (!empty($hx->HEnd)) {
                $sheet->setCellValue('B' . $row, $hx->HEnd);
            } else {
                $sheet->setCellValue('B' . $row, "No Disponible");
            }
            $sheet->setCellValue('C' . $row, $hx->Real);
            if($Produccion->wo->Tipo == "Maquina"){
            $sheet->setCellValue('D' . $row, $hx->Scrap);
            }
            $row++;
        }

        // ========================
        // Tabla 3: Tiempos de trabajo
        // ========================

        $sheet->setCellValue('A' . ($row + 1), 'Inicio');
        $sheet->setCellValue('B' . ($row + 1), 'Fin');
        $sheet->setCellValue('C' . ($row + 1), 'Empleado');

        $row += 2;

        foreach ($TimesWorked as $TW) {
            $sheet->setCellValue('A' . $row, Carbon::parse($TW->HStart)->format('Y-m-d H:i'));
            $sheet->setCellValue('B' . $row, Carbon::parse($TW->HEnd)->format('Y-m-d H:i'));
            if (!empty($TW->user->IdUser)) {
                $sheet->setCellValue('C' . $row, $TW->user->CodeEmp . " " . $TW->user->Name);
            } else {
                $sheet->setCellValue('C' . $row, "No Disponible");
            }
            $row++;
        }

        // ========================
        // Tabla 4: Costos CTO (Costos de Tiempo Muerto)
        // ========================

        $row++;
        $sheet->setCellValue('A' . ($row + 1), 'Inicio');
        $sheet->setCellValue('B' . ($row + 1), 'Fin');
        $sheet->setCellValue('C' . ($row + 1), 'Razón');
        $sheet->setCellValue('D' . ($row + 1), 'Reemplazo');

        $row += 2;
        foreach ($CTO as $cto) {
            $sheet->setCellValue('A' . $row, $cto->StartTime);
            $sheet->setCellValue('B' . $row, $cto->EndTime);
            if (!empty($cto->downtimeReason->Reason)) {
                $sheet->setCellValue('C' . $row, $cto->downtimeReason->Reason);
            } else {
                $sheet->setCellValue('C' . $row, "No Disponible");
            }

            if (!empty($cto->User)) {
                $sheet->setCellValue('D' . $row, $cto->User);
            } else {
                $sheet->setCellValue('D' . $row, "No Disponible");
            }
            $row++;
        }

        // Crear archivo Excel
        $filename = 'produccion_' . $id . '_report.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        // Descargar el archivo generado
        return response()->download($filename)->deleteFileAfterSend(true);
    }


    public function produccionExcel(Request $request)
    {

        $Buscado = $request->Buscado;

        switch ($request->Filtro) {

            case 'Todos':

                $Wo = null;

                $Wo = Wo::where(function ($query) use ($Buscado) {
                    $query->where('Wo', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->first();

                $resultados = Production::where(function ($query) use ($Buscado, $Wo) {
                    $query->where('Date', 'LIKE', "%$Buscado%")
                        ->orWhere('Meta', 'LIKE', "%$Buscado%")
                        ->orWhere('Real', 'LIKE', "%$Buscado%")
                        ->orWhere('Scrap', 'LIKE', "%$Buscado%")
                        ->orWhere('BatchNumber', 'LIKE', "%$Buscado%")
                        ->orWhere('WeightKg', 'LIKE', "%$Buscado%")
                        ->when($Wo && !empty($Wo->IdWo), function ($query) use ($Wo) {
                            return $query->orWhere('IdWo', $Wo->IdWo);
                        });
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();

                break;

            case 'Fecha':
                $resultados = Production::where(function ($query) use ($Buscado) {
                    $query->where('Date', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();
                break;

            case 'WO':

                $Wo = null;
                $Wo = Wo::where('Wo', 'LIKE', "%$Buscado%")->whereIn('Status', [1, 2])->first();

                if (!empty($Wo)) {
                    $resultados = Production::where(function ($query) use ($Wo) {
                        $query->where('IdWo', $Wo->IdWo);
                    })->whereIn('Status', [1, 2])
                        ->with(['wo', "machine", "shift"])
                        ->get();
                } else {
                    $resultados = collect();
                }

                break;

            case 'Meta':
                $resultados = Production::where(function ($query) use ($Buscado) {
                    $query->Where('Meta', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();
                break;

            case 'Real':
                $resultados = Production::where(function ($query) use ($Buscado) {
                    $query->Where('Real', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();
                break;

            case 'Scrap':
                $resultados = Production::where(function ($query) use ($Buscado) {
                    $query->Where('Scrap', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();
                break;

            case 'Lote':
                $resultados = Production::where(function ($query) use ($Buscado) {
                    $query->Where('BatchNumber', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2])->with(['wo', "machine", "shift"])->get();
                break;

            case 'Empleado':


                $Empleado = User::where(function($query) use ($Buscado) {
                    $query->where('CodeEmp', 'LIKE', "%$Buscado%")
                          ->orWhere('Name', 'LIKE', "%$Buscado%")
                          ->orWhere('PhoneNumber', 'LIKE', "%$Buscado%")
                          ->orWhere('Area', 'LIKE', "%$Buscado%");
                })->whereIn('Status', [1, 2, 3])->first();

                $resultados = Production::whereHas('machine.timeWorked', function ($query) use ($Empleado) {
                   $query->where('IdUser', $Empleado->IdUser);
               })
               ->whereIn('Status', [1, 2])
               ->with(['machine', 'shift', "wo"])
               ->get();


                break;

            case 'Producto':

                $Producto = Product::where(function($query) use ($Buscado) {
                    $query->where('ItemCode', 'LIKE', "%$Buscado%")
                          ->orWhere('Description', 'LIKE', "%$Buscado%")
                          ->orWhere('Cycle', 'LIKE', "%$Buscado%");
                })->where('Status', 1)->first();  

                $Wo = Wo::where('IdProduct', $Producto->IdProduct)
                ->whereIn('Status', [1, 2])
                ->pluck('IdWo');  // pluck para obtener solo los IDs de Wo
       
                 // 3. Consultar las producciones que tengan esas órdenes de trabajo
                $resultados = Production::whereIn('IdWo', $Wo) // Filtrar producciones con IdWo en el conjunto de IDs obtenidos
                ->whereIn('Status', [1, 2])
                ->with(['wo', 'shift'])
                ->get();

                break;

            case 'Maquina':

                $Maquina = Machine::where(function ($query) use ($Buscado) {
                    $query->where('MachineCode', 'LIKE', "%$Buscado%")
                        ->orWhere('Description', 'LIKE', "%$Buscado%");
                })->where('Status', 1)->first();

                $resultados = Production::where('IdMachine', $Maquina->IdMachine)
                ->whereIn('Status', [1, 2])
                    ->with(['wo', "machine", "shift"])->get();

                break;

            case 'Turno':

                $Turno = Shift::where(function ($query) use ($Buscado) {
                    $query->where('Shift', 'LIKE', "%$Buscado%")
                        ->orWhere('StartTime', 'LIKE', "%$Buscado%")
                        ->orWhere('EndTime', 'LIKE', "%$Buscado%");
                })->where('Status', 1)->first();

                $resultados = Production::where('IdShift', $Turno->IdShift)
                ->whereIn('Status', [1, 2])
                    ->with(['wo', "machine", "shift"])->get();

                break;
        }

        // Crear una nueva instancia de PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Turno');
        $sheet->setCellValue('D1', 'Máquina');
        $sheet->setCellValue('E1', 'Wo');
        $sheet->setCellValue('F1', 'Producto');
        $sheet->setCellValue('G1', 'Meta');
        $sheet->setCellValue('H1', 'Real');
        $sheet->setCellValue('I1', 'Scrap');

        // $sheet->setCellValue('J1', 'Empleado');

        $row = 2; // Comenzamos en la segunda fila

        foreach ($resultados as $Produccion) {

            $Producto = Product::where("IdProduct", $Produccion->wo->IdProduct)->first();

            $sheet->setCellValue('A' . $row, $Produccion->IdProduction);
            $sheet->setCellValue('B' . $row, $Produccion->Date);
            $sheet->setCellValue('C' . $row, $Produccion->shift->Shift);
            $sheet->setCellValue('D' . $row, $Produccion->machine->MachineCode . " " . $Produccion->machine->Description);
            $sheet->setCellValue('E' . $row, $Produccion->wo->Wo);
            $sheet->setCellValue('F' . $row, $Producto->ItemCode . " " . $Producto->Description);
            $sheet->setCellValue('G' . $row, $Produccion->Meta);
            $sheet->setCellValue('H' . $row, $Produccion->Real);
            $sheet->setCellValue('I' . $row, $Produccion->Scrap);
            // $sheet->setCellValue('J' . $row, $Produccion->user->CodeEmp . " " . $Produccion->user->Name);
            $row++;
        }

        // Guardar el archivo Excel
        $filename = 'produccion_' . time() . '_report.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }


    public function TiempoExcel(Request $request)
    {

        if ($request->Fecha == null && $request->Usuario == null) {
            return redirect()->back()->withErrors([
                'Fecha' => "Elije al menos un campo",
                "Usuario" => "Elije al menos un campo"
            ])->withInput();
        }

        if ($request->Usuario == null) {
            $resultados = TimeWorked::whereDate('HStart', $request->Fecha)
                ->get();
        } elseif ($request->Fecha == null) {
            $resultados = TimeWorked::where('IdUser', $request->Usuario)
                ->get();
        } else {
            $resultados = TimeWorked::whereDate('HStart', $request->Fecha)
                ->where('IdUser', $request->Usuario)
                ->get();
        }

        // Crear una nueva instancia de PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Inicio');
        $sheet->setCellValue('C1', 'Final');
        $sheet->setCellValue('D1', 'Empleado');

        // Llenar la tabla con los datos de $resultados
        $row = 2; // Comenzamos en la segunda fila
        foreach ($resultados as $TT) {

            if (!is_null($TT->IdUser)) {
                $Usuario = User::Where('IdUser', $TT->IdUser)->first();
                $TT->Usuario = $Usuario ? $Usuario->Name : 'Sin nombre';
            } else {
                $TT->Usuario = 'Sin usuario'; // Valor por defecto si IdUser es nulo
            }

            // Verificar si HStart no es nulo antes de formatear
            if (!is_null($TT->HStart)) {
                $HStart = new \DateTime($TT->HStart);
                $TT->HStart = $HStart->format('Y-m-d H:i');
            } else {
                $TT->HStart = 'No disponible';
            }

            // Verificar si HEnd no es nulo antes de formatear
            if (!is_null($TT->HEnd)) {
                $HEnd = new \DateTime($TT->HEnd);
                $TT->HEnd = $HEnd->format('Y-m-d H:i');
            } else {
                $TT->HEnd = 'No disponible'; // Valor por defecto si HEnd es nulo
            }

            $sheet->setCellValue('A' . $row, $TT->IdTimeWorked);
            $sheet->setCellValue('B' . $row, $TT->HStart);
            $sheet->setCellValue('C' . $row, $TT->HEnd);
            $sheet->setCellValue('D' . $row, $TT->Usuario);
            $row++;
        }

        if ($request->Usuario == null) {
            $filename = 'TiempoTrabajado_' . $request->Fecha . '_report.xlsx';

        } elseif ($request->Fecha == null) {
            $filename = 'TiempoTrabajado_' . $request->Usuario . '_report.xlsx';

        } else {
            $filename = 'TiempoTrabajado_' . time() . '_report.xlsx';
        }
        // Guardar el archivo Excel
        //$filename = 'TiempoTrabajado_' .$request->Fecha."_". $request->Usuario . '_report.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }



    public function produccionExcelDetallado(Request $request)
    {
        $Wo = null;

        if(is_null($request->Tipo)){
        $NombreExcelMaquinas = "Máquina/Mesa";
        }elseif($request->Tipo == "Maquina"){
            $NombreExcelMaquinas = "Máquina";
        }else{
            $NombreExcelMaquinas = "Mesa";
        }

        if (!is_null($request->Wo)) {
                           $Wo = Wo::where(function ($query) use ($request) {
                    $query->where('Wo', 'LIKE', "%$request->Wo%");
                })->whereIn('Status', [1, 2])->first();
        }

        if (!(is_null($request->Fecha) && is_null($request->FechaF) && is_null($request->Lote)
        && is_null($request->KG) && is_null($request->Wo) && is_null($request->Turno) 
        && is_null($request->Maquina) && is_null($request->Tipo))
    ) {
        $resultados = Production::whereIn('Status', [1, 2])
            ->when($request->Fecha && $request->FechaF, function ($query) use ($request) {
                return $query->whereBetween('Date', [$request->Fecha, $request->FechaF]);
            })
            ->when($request->Lote, function ($query) use ($request) {
                return $query->where('BatchNumber', 'LIKE', "%{$request->Lote}%");
            })
            ->when($request->KG, function ($query) use ($request) {
                return $query->where('WeightKg', 'LIKE', "%{$request->KG}%");
            })
            ->when($request->Wo, function ($query) use ($request) {
                return $query->where('IdWo', $request->Wo);
            })
            ->when($request->Turno, function ($query) use ($request) {
                return $query->where('IdShift', $request->Turno);
            })
            ->when($request->Maquina, function ($query) use ($request) {
                return $query->where('IdMachine', $request->Maquina);
            })
            ->when($request->Tipo, function ($query) use ($request) {
                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                    $subQuery->where('Tipo', $request->Tipo);
                });
            })
            ->with(['wo.product', 'machine', 'shift', 'WorkTimeView', "TimeOutView", 'ReportEmployee'])
            ->get();
    }

    // dd($resultados);
       
        // Crear una nueva instancia de PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Turno');
        $sheet->setCellValue('D1', $NombreExcelMaquinas);
        $sheet->setCellValue('E1', 'Wo');
        $sheet->setCellValue('F1', 'Producto');
        $sheet->setCellValue('G1', 'Meta');
        $sheet->setCellValue('H1', 'Real');
        $sheet->setCellValue('I1', 'Scrap');
        $sheet->setCellValue('J1', 'Empleado');

        if (!(is_null($request->Fecha) && is_null($request->FechaF) && is_null($request->Lote)
        && is_null($request->KG) && is_null($request->Wo) && is_null($request->Turno) 
        && is_null($request->Maquina) && is_null($request->Tipo))
    ){

        $sheet->setCellValue('k1', 'HorasTrabajadas');
        $sheet->setCellValue('L1', 'TiemposMuertos');

        $row = 2; // Comenzamos en la segunda fila

        foreach ($resultados as $Produccion) {  

           if(!empty($Produccion->WorkTimeView->TotalWorkTime)){
            if( $Produccion->wo->product->Cycle > 0){

                list($horas, $minutos, $segundos) = explode(':', $Produccion->WorkTimeView->TotalWorkTime);
                $totalSegundos = ($horas * 3600) + ($minutos * 60) + $segundos;
                $MaximoPosible = round($totalSegundos /  $Produccion->wo->product->Cycle); 

            }else{
                $MaximoPosible = 0;
            }
           }else{
                $MaximoPosible = 0;
           }
       

            $sheet->setCellValue('A' . $row, $Produccion->IdProduction);
            $sheet->setCellValue('B' . $row, $Produccion->Date);
            $sheet->setCellValue('C' . $row, $Produccion->shift->Shift);
            $sheet->setCellValue('D' . $row, $Produccion->machine->MachineCode . " " . $Produccion->machine->Description);
            $sheet->setCellValue('E' . $row, $Produccion->wo->Wo);
            $sheet->setCellValue('F' . $row, $Produccion->wo->Product->ItemCode . " " . $Produccion->wo->Product->Description);
            $sheet->setCellValue('G' . $row, $MaximoPosible);
            $sheet->setCellValue('H' . $row, $Produccion->Real);
            $sheet->setCellValue('I' . $row, $Produccion->Scrap);

            if(!empty($Produccion->ReportEmployee->EmployeeInfo)){
            $sheet->setCellValue('J' . $row, $Produccion->ReportEmployee->EmployeeInfo);
            }else{
                $sheet->setCellValue('J' . $row,"No disponible");
            }

            if(!empty($Produccion->WorkTimeView->TotalWorkTime)){
                $sheet->setCellValue('K' . $row, $Produccion->WorkTimeView->TotalWorkTime);
            }else{
                $sheet->setCellValue('K' . $row,"No disponible");
            }

            if(!empty($Produccion->TimeOutView->TotalTimeOut)){
                $sheet->setCellValue('L' . $row, $Produccion->TimeOutView->TotalTimeOut);
            }else{
                $sheet->setCellValue('L' . $row,"No disponible");
            }

            $row++;

        }
    }


        // Guardar el archivo Excel
        $filename = 'produccion_' . time() . '_report.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }


}

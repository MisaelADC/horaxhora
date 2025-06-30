<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Shift;
use App\Models\Product;
use App\Models\Cycle;
use App\Models\Wo;
use App\Models\User;
use App\Models\Production;
use App\Models\DowntimeReason;
use App\Models\TimeWorked;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;


class BusquedaController extends Controller

{

    public function store(Request $request)
    {

        if($request->Tabla != "Ciclos" && $request->Tabla != "TiempoT"){
        $request->validate([
            'Buscar' => 'required|max:80',
        ], [
            'Buscar.required' => 'El campo Nombre es obligatorio.',
            "Buscar.max" => "Supera la cantidad permitidas de letras que son 80",
        ]);
    }

        $buscar = $request->Buscar;
        $Busqueda = $request->Elegir;

        switch ($request->Tabla) {
            case 'Maquinas':

                switch($request->BusquedaPor){
                    case "Todos":

                 $resultados = Machine::where(function($query) use ($buscar) {
                     $query->where('MachineCode', 'LIKE', "%$buscar%")
                           ->orWhere('Description', 'LIKE', "%$buscar%");
                 })->where('Status', 1)->take(20)->get();   

                break;

                case "Codigo":

                    $resultados = Machine::where(function($query) use ($buscar) {
                        $query->where('MachineCode', 'LIKE', "%$buscar%");
                    })->where('Status', 1)->take(20)->get();   
   
                   break;

                case 'Descripcion':


                $resultados = Machine::where(function($query) use ($buscar) {
                        $query->Where('Description', 'LIKE', "%$buscar%");
                    })->where('Status', 1)->take(20)->get();   

                break;    
                }

                  // Convertir la colección a un array
                $MaquinasArray = $resultados->toArray();
    
                // Paginar los resultados
                $perPage = 20; // Cantidad de elementos por página
                $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                $offset = ($currentPage - 1) * $perPage;
                $items = array_slice($MaquinasArray, $offset, $perPage);
    
                $maquinasPaginadas = new LengthAwarePaginator($items, count($MaquinasArray), $perPage, $currentPage);

                $BotonVerTodos = "Ver";

                 return view('Maquinas/Maquinas', compact('maquinasPaginadas', "Busqueda", "BotonVerTodos"));
                break;

                case 'Productos':

                    switch ($request->BusquedaPor) {
                        case 'Todos':
                           
                    $resultados = Product::where(function($query) use ($buscar) {
                        $query->where('ItemCode', 'LIKE', "%$buscar%")
                              ->orWhere('Description', 'LIKE', "%$buscar%")
                              ->orWhere('Quantity', 'LIKE', "%$buscar%")
                              ->orWhere('Cycle', 'LIKE', "%$buscar%");
                    })->where('Status', 1)->take(20)->get();   

                            break;

                        case 'Codigo':
                   
                            $resultados = Product::where(function($query) use ($buscar) {
                                $query->where('ItemCode', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->take(20)->get();   

                            break;
                        
                        case 'Descripcion':

                            $resultados = Product::where(function($query) use ($buscar) {
                                $query->Where('Description', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->take(20)->get();   

                            break;

                        case "Cantidad" :

                            $resultados = Product::where(function($query) use ($buscar) {
                                $query->Where('Quantity', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->take(20)->get();   

                            break;

                        case "Ciclo" :

                                $resultados = Product::where(function($query) use ($buscar) {
                                    $query->Where('Cycle', 'LIKE', "%$buscar%");
                                })->where('Status', 1)->take(20)->get();   
    
                            break;
                    }

                      // Convertir la colección a un array
                    $ProductosArray = $resultados->toArray();
    
                    // Paginar los resultados
                    $perPage = 20; // Cantidad de elementos por página
                    $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                    $offset = ($currentPage - 1) * $perPage;
                    $items = array_slice($ProductosArray, $offset, $perPage);
    
                    $DatosPaginados = new LengthAwarePaginator($items, count($ProductosArray), $perPage, $currentPage);

                    $BotonVerTodos = "Ver";

                 return view('Productos/Productos', compact('DatosPaginados', "Busqueda","BotonVerTodos"));

                break;

                case 'Turnos':

                    $resultados = Shift::where(function($query) use ($buscar) {
                        $query->where('Shift', 'LIKE', "%$buscar%")
                              ->orWhere('StartTime', 'LIKE', "%$buscar%")
                              ->orWhere('EndTime', 'LIKE', "%$buscar%");
                    })->where('Status', 1)->take(20)->get();   

                    foreach ($resultados as $I) {
                        $I->StartTime = new \DateTime($I->StartTime);
                        $I->StartTime = $I->StartTime->format('H:i');
                        $I->EndTime = new \DateTime($I->EndTime);
                        $I->EndTime = $I->EndTime->format('H:i');
                    }

                    // Convertir la colección a un array
                    $TurnosArray = $resultados->toArray();
    
                    // Paginar los resultados
                    $perPage = 20; // Cantidad de elementos por página
                    $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                    $offset = ($currentPage - 1) * $perPage;
                    $items = array_slice($TurnosArray, $offset, $perPage);
    
                    $DatosPaginados = new LengthAwarePaginator($items, count($TurnosArray), $perPage, $currentPage);

                    $BotonVerTodos = "Ver";

                 return view('Turnos/Turnos', compact('DatosPaginados', "Busqueda", "BotonVerTodos"));

                break;


                case 'Usuarios':

                    switch ($request->BusquedaPor) {

                    case 'Todos':
                           
                        $resultados = User::where(function($query) use ($buscar) {
                            $query->where('CodeEmp', 'LIKE', "%$buscar%")
                                  ->orWhere('Name', 'LIKE', "%$buscar%")
                                  ->orWhere('PhoneNumber', 'LIKE', "%$buscar%")
                                  ->orWhere('Area', 'LIKE', "%$buscar%");
                        })->whereIn('Status', [1, 2, 3])->take(20)->get();   

                        break;  
                        
                    case 'Codigo':

                        $resultados = User::where(function($query) use ($buscar) {
                            $query->where('CodeEmp', 'LIKE', "%$buscar%");
                        })->whereIn('Status', [1, 2, 3])->take(20)->get();  

                        break;
                    
                    case 'Nombre':

                        $resultados = User::where(function($query) use ($buscar) {
                            $query->Where('Name', 'LIKE', "%$buscar%");
                        })->whereIn('Status', [1, 2, 3])->take(20)->get();   

                        break;
                
                
                    case 'Telefono':

                        $resultados = User::where(function($query) use ($buscar) {
                            $query->Where('PhoneNumber', 'LIKE', "%$buscar%");
                        })->whereIn('Status', [1, 2, 3])->take(20)->get(); 

                        break;

                    case 'Area':
                        $resultados = User::where(function($query) use ($buscar) {
                            $query->Where('Area', 'LIKE', "%$buscar%");
                        })->whereIn('Status', [1, 2, 3])->take(20)->get();   
                        break;
                    }

                    // Convertir la colección a un array
                    $Array = $resultados->toArray();
    
                    // Paginar los resultados
                    $perPage = 20; // Cantidad de elementos por página
                    $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                    $offset = ($currentPage - 1) * $perPage;
                    $items = array_slice($Array, $offset, $perPage);
    
                    $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

                    $BotonVerTodos = "Ver";

                 return view('Usuarios/Usuarios', compact('DatosPaginados', "Busqueda", "BotonVerTodos"));

                break;

                case 'Produccion':

                    //dd($request->Tipo);

                    $tipo = $request->Tipo;

                    //dd($tipo);

                    switch ($request->BusquedaPor) {

                        case 'Todos':

                            $Wo = Wo::where(function($query) use ($buscar) {
                                $query->where('Wo', 'LIKE', "%$buscar%");
                            })->whereIn('Status', [1, 2])->first();   

                            $resultados = Production::where(function($query) use ($buscar, $Wo) {
                                $query->where('Date', 'LIKE', "%$buscar%")
                                      ->orWhere('Meta', 'LIKE', "%$buscar%")
                                      ->orWhere('Real', 'LIKE', "%$buscar%")
                                      ->orWhere('Scrap', 'LIKE', "%$buscar%")
                                      ->orWhere('BatchNumber', 'LIKE', "%$buscar%")
                                      ->when(!empty($Wo->IdWo), function ($query) use ($Wo) {
                                          return $query->orWhere('IdWo', $Wo->IdWo);
                                      });
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })
                            ->whereIn('Status', [1, 2, 3])
                               ->with("wo", "shift","machine")
                              ->take(20)
                              ->get();
                        break;

                        case 'Fecha':
                            $resultados = Production::where(function($query) use ($buscar) {
                                $query->where('Date', 'LIKE', "%$buscar%");
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })->whereIn('Status', [1, 2, 3])->take(20)->with("wo", "shift", "machine")->get();   
                        break;

                        case 'WO':

                        $Wo = Wo::where('Wo', 'LIKE', "%$buscar%")->whereIn('Status', [1, 2])->first();   

                        if (!empty($Wo)) {
                        $resultados = Production::where(function($query) use ($Wo) {
                        $query->where('IdWo', $Wo->IdWo);
                        })->when($tipo !== 'Ambos', function ($query) use ($request) {
                            return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                $subQuery->where('Tipo', $request->Tipo);
                            });
                        })->whereIn('Status', [1, 2, 3])
                        ->take(20)
                        ->with("wo","shift", "machine")
                        ->get();
                        } else {
                        $resultados = collect();  
                    }

                        break;

                        case 'Meta':
                            $resultados = Production::where(function($query) use ($buscar) {
                                $query->Where('Meta', 'LIKE', "%$buscar%");
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })->whereIn('Status', [1, 2, 3])->take(20)->with("wo", "shift", "machine")->get();   
                        break;

                        case 'Real':
                            $resultados = Production::where(function($query) use ($buscar) {
                                $query->Where('Real', 'LIKE', "%$buscar%");
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })->whereIn('Status', [1, 2, 3])->take(20)->with("wo", "shift", "machine")->get();   
                        break;
                        
                        case 'Scrap':
                            $resultados = Production::where(function($query) use ($buscar) {
                                $query->Where('Scrap', 'LIKE', "%$buscar%");
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })->whereIn('Status', [1, 2])->take(20)->with("wo", "shift", "machine")->get();   
                        break;

                        case 'Lote':
                            $resultados = Production::where(function($query) use ($buscar) {
                                $query->Where('BatchNumber', 'LIKE', "%$buscar%");
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })->whereIn('Status', [1, 2, 3])->take(20)->with("wo", "shift", "machine")->get();   
                        break;
                        
                        case 'Empleado':

                            

                             $Empleado = User::where(function($query) use ($buscar) {
                                 $query->where('CodeEmp', 'LIKE', "%$buscar%")
                                       ->orWhere('Name', 'LIKE', "%$buscar%")
                                       ->orWhere('PhoneNumber', 'LIKE', "%$buscar%")
                                       ->orWhere('Area', 'LIKE', "%$buscar%");
                             })->whereIn('Status', [1, 2, 3])->first();

                             $resultados = Production::whereHas('machine.timeWorked', function ($query) use ($Empleado) {
                                $query->where('IdUser', $Empleado->IdUser);
                            })->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })
                            ->whereIn('Status', [1, 2])
                            ->with(['machine', 'shift',"wo"])
                            ->get();


                        break;

                        case 'Maquina':

                            $Maquina = Machine::where(function($query) use ($buscar) {
                                $query->where('MachineCode', 'LIKE', "%$buscar%")
                                      ->orWhere('Description', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->first();   

                            $resultados = Production::where('IdMachine',  $Maquina->IdMachine)
                            ->whereIn('Status', [1, 2, 3])
                            ->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })
                            ->take(20)->with("wo", "shift", "machine")->get();

                        break;

                        case 'Turno':

                            $Turno = Shift::where(function($query) use ($buscar) {
                                $query->where('Shift', 'LIKE', "%$buscar%")
                                      ->orWhere('StartTime', 'LIKE', "%$buscar%")
                                      ->orWhere('EndTime', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->first();  
                            
                            $resultados = Production::where('IdShift',  $Turno->IdShift)
                            ->whereIn('Status', [1, 2, 3])
                            ->when($tipo !== 'Ambos', function ($query) use ($request) {
                                return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                                    $subQuery->where('Tipo', $request->Tipo);
                                });
                            })
                            ->take(20)->with("wo","shift", "machine")->get();

                        break;

                        case 'Producto':

                            $Producto = Product::where(function($query) use ($buscar) {
                                $query->where('ItemCode', 'LIKE', "%$buscar%")
                                      ->orWhere('Description', 'LIKE', "%$buscar%")
                                      ->orWhere('Cycle', 'LIKE', "%$buscar%");
                            })->where('Status', 1)->first();  

                            $Wo = Wo::where('IdProduct', $Producto->IdProduct)
                            ->whereIn('Status', [1, 2, 3])
                            ->pluck('IdWo');  // pluck para obtener solo los IDs de Wo
                   
                   // 3. Consultar las producciones que tengan esas órdenes de trabajo
                   $resultados = Production::whereIn('IdWo', $Wo) // Filtrar producciones con IdWo en el conjunto de IDs obtenidos
                   ->when($tipo !== 'Ambos', function ($query) use ($request) {
                    return $query->whereHas('wo.product', function ($subQuery) use ($request) {
                        $subQuery->where('Tipo', $request->Tipo);
                    });
                })->whereIn('Status', [1, 2, 3])
                       ->take(20)
                       ->with('wo', 'shift',"machine")
                       ->get();

                        break;
                        
                    }

                    $Filtro = $request->BusquedaPor;
                    $Buscado = $buscar;
                    $BotonVerTodos = "Ver";

                    // Convertir la colección a un array
                    $Array = $resultados->toArray();
    
                    // Paginar los resultados
                    $perPage = 20; // Cantidad de elementos por página
                    $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                    $offset = ($currentPage - 1) * $perPage;
                    $items = array_slice($Array, $offset, $perPage);
    
                    $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

                 return view('Produccion/Produccion', compact('DatosPaginados', "Buscado", "Filtro","BotonVerTodos", "tipo"));

                break;


                case 'TiempoT':

                    if($request->Fecha == null  && $request->Usuario == null){
                        return redirect()->back()->withErrors(['Fecha' => "Elije al menos un campo",
                        "Usuario" => "Elije al menos un campo"])->withInput();
                          }

                          if($request->Usuario == null){
                            $resultados = TimeWorked::whereDate('HStart', $request->Fecha)
                            ->take(20)->with("machine", "user")->get();    
                        }elseif($request->Fecha == null){
                            $resultados = TimeWorked::where('IdUser', $request->Usuario)
                            ->take(20)->with("machine", "user")->get();      
                        }else{
                            $resultados = TimeWorked::whereDate('HStart', $request->Fecha)
                            ->where('IdUser', $request->Usuario)
                            ->take(20)->with("machine", "user")->get();    
                        }

                        foreach($resultados as $TT){
                    
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
                    }

                       $Array = $resultados->toArray();
        
                       $perPage = 20; // Cantidad de elementos por página
                       $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                       $offset = ($currentPage - 1) * $perPage;
                       $items = array_slice($Array, $offset, $perPage);
       
                       $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);

                       $Usuarios = User::whereIn('Status', [1, 2, 3])->get();

                       $BotonVerTodos = "Ver";

                       $BFecha = $request->Fecha;
                       $BUsuario = $request->Usuario;
    
                    return view('/TiempoTrabajado/TiempoT', compact('DatosPaginados',"Usuarios","BotonVerTodos", "BFecha", "BUsuario"));
    
                    break;    

                    case 'Razones':

                        $resultados = DowntimeReason::where(function($query) use ($buscar) {
                            $query->where('Reason', 'LIKE', "%$buscar%");
                        })->take(20)->get();  
        
                           $Array = $resultados->toArray();
            
                           $perPage = 20; // Cantidad de elementos por página
                           $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                           $offset = ($currentPage - 1) * $perPage;
                           $items = array_slice($Array, $offset, $perPage);
           
                           $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);
    
                           $BotonVerTodos = "Ver";
        
                        return view('/Razones/Razones', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
        
                        break;    

                    case 'Wo':

                        $Producto = Product::where(function($query) use ($buscar) {
                            $query->where('ItemCode', 'LIKE', "%$buscar%")
                                  ->orWhere('Description', 'LIKE', "%$buscar%");
                        })->where('Status', 1)->first(); 

                        if(!empty($Producto->IdProduction)){

                        $resultados = Wo::where(function($query) use ($buscar) {
                            $query->where('Wo', 'LIKE', "%$buscar%")
                                  ->orWhere("IdProduct", $Producto->IdProducto)
                                  ->orWhere("Meta", 'LIKE', "%$buscar%");
                        })->where('Status', 1)->with("product","TotalReal")->get();  
                    }else{
                        $resultados = Wo::where(function($query) use ($buscar) {
                            $query->where('Wo', 'LIKE', "%$buscar%")
                                  ->orWhere("Meta", 'LIKE', "%$buscar%");
                        })->where('Status', 1)->with("product","TotalReal")->get();  
                    }
            
                            $Array = $resultados->toArray();
                
                            $perPage = 20; // Cantidad de elementos por página
                            $currentPage = request()->get('page', 1); // Obtener el número de página actual desde la URL
                            $offset = ($currentPage - 1) * $perPage;
                            $items = array_slice($Array, $offset, $perPage);
               
                            $DatosPaginados = new LengthAwarePaginator($items, count($Array), $perPage, $currentPage);
        
                            $BotonVerTodos = "Ver";
            
                        return view('/Wo/Wo', compact('DatosPaginados',"Busqueda", "BotonVerTodos"));
            
                        break;    
            
        }

    }
}

<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\MachinesController;
use App\Http\Controllers\IniSesController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ShiftsController;
use App\Http\Controllers\WoController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProductionsController;
use App\Http\Controllers\EleccionController;
use App\Http\Controllers\DesignsController;
use App\Http\Controllers\TimesOffsController;
use App\Http\Controllers\IniciosController;
use App\Http\Controllers\TimeWorkedController;
use App\Http\Controllers\CyclesController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\DowntimeReasonsController;
use App\Http\Controllers\HeatEquationExportController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index');})->name("Loguearse");
Route::get('/Inicio', function () { return view('Inicio');});
Route::get('/Desloguearse', [IniSesController::class, 'salir'])->name("Desloguearse");
Route::post('/Login', [IniSesController::class, 'create'])->name("Login");

Route::middleware(["checkAuth1"])->group(function (){
    Route::get('/Iniciar', [GestionController::class, 'IniciarTrabajo']);
    Route::post('/IniciarConteo', [GestionController::class, 'IniciarConteo']);
});

Route::middleware(["checkAuth2"])->group(function (){
    Route::post('/GestionSuma', [GestionController::class, 'Suma']);
    Route::get('/Gestion', [GestionController::class, 'Gestion']);
    
    Route::get('/SalirTrabajo/{id}', [GestionController::class, 'SalirTrabajo']);
    // Route::get('/TerminarConteo', [GestionController::class, 'TerminarConteo']);
    Route::get('/TerminarProduccion/{id}', [GestionController::class, 'TerminarProduccion']);
    Route::get('/VistaTiempoMuerto/{id}', [GestionController::class, 'VistaTiempoMuerto']);
    Route::put('/TiempoMuertoRedireccion/{id}', [GestionController::class, 'TiempoMuertoR']);
});

Route::middleware(["checkAuth3"])->group(function (){
    Route::get('/TiempoFuera/{id}', [GestionController::class, 'TiempoFuera']);
    Route::get('/SalirTiempoMuerto/{id}', [GestionController::class, 'SalirTiempoMuerto']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(["checkAuth"])->group(function (){
Route::resource('Maquinas', MachinesController::class);
Route::resource('Productos', ProductsController::class);
Route::resource('Diseño', DesignsController::class);
Route::resource('Turnos', ShiftsController::class);
Route::resource('Usuarios', UsersController::class);
Route::resource('Razones', DowntimeReasonsController::class);
Route::resource('Wo', WoController::class);
Route::resource('Produccion', ProductionsController::class);
Route::resource('TimeOut', TimesOffsController::class);
Route::resource('TiempoTrabajado', TimeWorkedController::class);
Route::get('/RedirgirCTO/{id}', [ProductionsController::class, 'RedirgirCTO']);
Route::get('/LimpiarMaquina/{id}', [MachinesController::class, 'Limpiar']);
Route::get('/CTO/{id}', [ProductionsController::class, 'verCTO']);
Route::get('/HxHREdit/{id}', [ProductionsController::class, 'HxHREdit']);
Route::put('/HxHEdit/{id}', [ProductionsController::class, 'HxHEdit']);
Route::get('/HxH/{id}', [ProductionsController::class, 'verHxH']);
Route::put('/CanHXH/{id}', [ProductionsController::class, 'CanHXH']);
Route::put('/CContraseña/{id}', [UsersController::class, 'CContraseña']);
Route::get('/E_Producto/{R}', [EleccionController::class, 'E_Producto']);
Route::get('/E_Maquina/{R}', [EleccionController::class, 'E_Maquina']);
Route::get('/E_Turno/{R}', [EleccionController::class, 'E_Turno']);
Route::get('/E_Usuario/{R}', [EleccionController::class, 'E_Usuario']);
Route::post('/Elegir', [EleccionController::class, 'Elegir']);
Route::post('/Buscar', [BusquedaController::class, 'store'])->name("buscar");

Route::get('/ExcelDetallado', [ProductionsController::class, 'ExcelDetallado']);
Route::get('/ExcelDetalladoTodo', [ProductionsController::class, 'ExcelDetalladoTodo']);




Route::get('/Inicio', [IniciosController::class, 'VistaAdm']);
Route::get('/InicioEmp', [IniciosController::class, 'VistaEmp']); 
Route::post('/TiempoExcel', [HeatEquationExportController::class, 'TiempoExcel']);
Route::get('/export-hxh-excel/{id}', [HeatEquationExportController::class, 'exportHxHtoExcel']);
Route::post('/produccion-excel', [HeatEquationExportController::class, 'produccionExcel']);
Route::post('/GenerarExcelDetallado', [HeatEquationExportController::class, 'produccionExcelDetallado']);

Route::get('/get-designs/{productId}', [DesignsController::class, 'getDesigns']);
});

require __DIR__.'/auth.php';

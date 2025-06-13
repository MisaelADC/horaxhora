<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wo</title>
    <link href="{{ asset('Todo.css') }}" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
  
    <div class="header" style="display: flex;">

      <img src="{{ asset('ADCLogoHeader.png') }}" style="height: 100px;position: absolute;top: 0px;left: 5px;width: 100px;z-index: 1000;">

      <div style="display: flex">
        <a style="font-weight: bold; Position: relative; left: -14px;" href="/Desloguearse" class="LinksPrincipal">Salir</a>
    </div>

      <div class="contenedor-boton-links">
        <button class="menuBoton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
          <i class="bi-list">Menu</i> <!-- Icono de lista, puedes cambiarlo según tu preferencia -->
      </button>
      </div>
        </div> 

        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel" style="width: 180px;">
          <div class="offcanvas-header" style=" background-color: #1b2854;">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel"  style="font-size:26px;color: white;">Enlaces</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="color: white; background-color: white;"></button>
          </div>
          <div class="offcanvas-body" style="background-color: #1b2854;">
            <nav class="nav nav-masthead " style="display: flex;">
              <div style="margin-top: -10px; margin-left: 5px;">
  

           <a style="font-weight: bold; margin-left: 40px;" href="/Inicio" class="Links">Inicio</a>

           <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

           <a style="font-weight: bold; margin-left: 50px;" class="Links" href="/Wo" id="navbarDropdown">Wo</a>

           <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 32px;" class="Links" href="/Turnos" id="navbarDropdown">Turnos</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 29px;" class="Links" href="/TiempoTrabajado" id="navbarDropdown">Trabajo</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 25px;" class="Links" href="/Razones" id="navbarDropdown">Razones</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 24px;" class="Links" href="/Usuarios" id="navbarDropdown">Usuarios</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 17px;" class="Links" href="/Maquinas" id="navbarDropdown">Maquinas</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold;margin-left: 15px;" class="Links" href="/Productos" id="navbarDropdown">Productos</a>

          <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

          <a style="font-weight: bold; margin-left: 8px;" class="Links" href="/Produccion" id="navbarDropdown">Produccion</a>

        </div>
          </div>
        </div>     
    
    <div class="cuerpo">

    <div class="margen" style="display: flex">
    <h1>Work orders</h1>

    @if($BotonVerTodos == "Ver")
    <a href="Wo" class="NuevoTabla">Todos</a>
    @else
    <a href="Wo/create" class="NuevoTabla">Nuevo</a>
    @endif

    </div>

    <form action="{{ route('buscar') }}" method="POST">
      @csrf
      <div class="display">

       <div class="col-md-11">
            <input type="text" class="form-control @error('Buscar') is-invalid @enderror" id="validationCustom01" name="Buscar" value="{{ old('Buscar') }}" required>
            @error('Buscar')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <input type="hidden" value="Wo" name="Tabla" required>
        <input type="hidden" value="{{$Busqueda}}" name="Elegir" required>

        <button class="btn btn-primary" style="margin-left: 2vw; padding-bottom: 4px">Buscar</button>
      </div>
    </style>
    </form>

    <div class="table-responsive">
    <table class="table" style="margin-top: 40px">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Wo</th>
              <th scope="col">Producto</th>
              <th scope="col">Meta</th>
              <th scope="col">Real</th>
              <th scope="col">Tipo</th>
              {{-- @if($Busqueda == 0) --}}
              <th scope="col">Editar</th>
              <th scope="col">Eliminar</th>
              {{-- @endif --}}
              {{-- @if($Busqueda == 1)
              <th scope="col">Seleccionar</th>
              @endif --}}
            </tr>
          </thead>
          <tbody>

            @php
            $counter = ($DatosPaginados->currentPage() - 1) * $DatosPaginados->perPage();
            @endphp

      @if(!empty($DatosPaginados))
      @foreach ($DatosPaginados as $Razon)
    
      {{-- @dd($Maquina) --}}

      <tr>
        <td scope="row">{{ ++$counter }}</td>
        <td>{{ $Razon["Wo"] }}</td>
        <td>{{ $Razon["product"]["Description"] }}</td>
        <td>{{ $Razon["Meta"] }}</td>
        <td>{{ $Razon["total_real"]["TotalProducido"] }}</td>
        <td>{{ $Razon["product"]["Tipo"] }}</td>
        <td><a href="/Wo/{{ $Razon["IdWo"] }}/edit">Editar</a></td>        
        <td><a href="/Wo/{{ $Razon["IdWo"] }}">Borrar</a></td> 

      @endforeach
    </tbody>
      </table>
    </div>

      <div id="pagination-links" class="pagination">
        <!-- Mostrar el texto de la paginación actual -->
        <p style="position: relative; top: 5px; margin-bottom: 30px;">Página {{ $DatosPaginados->currentPage() }} de {{ $DatosPaginados->lastPage() }}</p>
    
        <!-- Botones para avanzar y retroceder -->
        <div>
            @if ($DatosPaginados->currentPage() > 1)
                <a href="{{ route('Wo.index') }}?page={{ $DatosPaginados->currentPage() - 1 }}" class="btn btn-primary" style="margin-left: 7px">Anterior</a>
            @endif
    
            @if ($DatosPaginados->hasMorePages())
                <a href="{{ route('Wo.index') }}?page={{ $DatosPaginados->currentPage() + 1 }}" class="btn btn-primary" style="margin-left: 7px">Siguiente</a>
            @endif
        </div>
    </div>

      @else

      <h1>No hay registros hasta este momento</h1>

      @endif

    </div> <!-- fin del cuerpo con margenes -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>
</html>
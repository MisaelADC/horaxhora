<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="{{ asset('Todo.css') }}" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
  
    <div class="header" style="display: flex;">

      <img src="{{ asset('ADCLogoHeader.png') }}"  style="height: 100px;position: absolute;top: 0px;left: 5px;width: 100px;z-index: 1000;">

      <a style="font-weight: bold;margin-left: 45px;" href="/Desloguearse" class="Links">Salir</a>

      <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>

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
              <div style="margin-top: -15px; margin-left: 5px;">
  
  
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
      </center>
    
    <div class="cuerpo">

    <div class="margen" style="display: flex">
    <h1>Tiempos Muertos</h1>

    <a href="/TimeOut/create" class="NuevoTabla">Nuevo</a>

    </div>

    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Comienzo</th>
              <th scope="col">Final</th>
              <th scope="col">Descripcion</th>
              <th scope="col">Usuario a cargo</th>
              <th scope="col">Editar</th>
              <th scope="col">Borrar</th>
            </tr>
          </thead>
          <tbody>

            @php
            $counter = 0;
            @endphp

      @if(!empty($CTO))
      @foreach ($CTO as $Info)

      <tr>
        <td scope="row">{{ ++$counter }}</td>
        <td>{{ $Info["StartTime"] }}</td>
        <td>{{ $Info["EndTime"] }}</td>
        <td>{{ $Info->downtimeReason->Reason }}</td>
        <td>{{$Info->user->Name}}</td>

        <td><a href="/TimeOut/{{ $Info["IdTimeOut"] }}/edit">Editar</a></td> 
        <td><a href="/TimeOut/{{ $Info["IdTimeOut"] }}">Borrar</a></td>

      </tr>
      @endforeach
    </tbody>
      </table>
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
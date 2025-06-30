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
  
                {{-- <a style="font-weight: bold;margin-left: 45px;" href="/Desloguearse" class="Links">Salir</a>

                <div class="SeparadorLinks"><hr class="LineaSeparadora"></div> --}}
  
             <a style="font-weight: bold; margin-left: 40px;" href="/Inicio" class="Links">Inicio</a>
  
             <div class="SeparadorLinks"><hr class="LineaSeparadora"></div>
  
             <a style="font-weight: bold; margin-left: 38px;" class="Links" href="/Ciclos" id="navbarDropdown">Ciclos</a>
  
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

    <div class="margen">
    <h1>Editar Ciclo</h1>
    </div>

    <form class="row g-3 needs-validation" method="POST" action="/Ciclos/{{$Ciclo->IdCycle}}" enctype="multipart/form-data" novalidate>
      @csrf
      @method("PUT")
  
      <div class="col-md-4">
        <label for="validationCustom01" class="form-label">Duracion</label>
        <input type="text" class="form-control @error('Duracion') is-invalid @enderror" id="validationCustom01" value="{{$Ciclo->duration}}" name="Duracion" readonly>
    </div>

    <div class="col-md-4">
      <label for="validationCustom01" class="form-label">Producto:</label>
      <select class="form-control @error('Producto') is-invalid @enderror" id="Producto" name="Producto">
          @foreach ($Productos as $Pro)
              <option value="{{ $Pro->IdProduct }}" 
                  {{ (old('Producto') == $Pro->IdProduct || $Ciclo->IdProduct == $Pro->IdProduct) ? 'selected' : '' }}>
                  {{ $Pro->ItemCode }}
              </option>
          @endforeach
      </select>
      
      @error('Producto')
          <div class="invalid-feedback">
              {{ $message }}
          </div>
      @enderror
  </div>

  <div class="col-md-4">
    <label for="validationCustom01" class="form-label">Maquina:</label>
    <select class="form-control @error('Maquina') is-invalid @enderror" id="Maquina" name="Maquina">
        @foreach ($Maquinas as $Maq)
            <option value="{{ $Maq->IdMachine }}" 
                {{ (old('Maquina') == $Maq->IdMachine || $Ciclo->IdMachine == $Maq->IdMachine) ? 'selected' : '' }}>
                {{ $Maq->MachineCode }}
            </option>
        @endforeach
    </select>
    
    @error('Producto')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
  
      <center>
          <div class="col-12" style="margin-bottom: 50px">
              <button class="btn btn-primary" type="submit">Editar</button>
          </div>
      </center>
  </form>
    </div> <!-- fin del cuerpo con margenes -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>
</html>
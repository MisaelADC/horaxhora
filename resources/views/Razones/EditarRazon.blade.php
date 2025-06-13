<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Razon</title>
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

    <div class="margen">
    <h1>Editar Razon</h1>
    </div>

    <form class="row g-3 needs-validation" method="POST" action="/Razones/{{$Razon->IdDowntimeReason}}" enctype="multipart/form-data" novalidate>
      @csrf
      @method("PUT")

    <div class="col-md-12">
        <label for="validationCustom01" class="form-label">Razon</label>
        <input type="text" class="form-control" id="validationCustom01" value="{{$Razon->Reason}}"  name="Razon" required>
        @error('Razon')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
    </div>

    <div class="col-md-6">
      <label for="tipo" class="form-label">Tipo</label>
      <select class="form-control @error('Tipo') is-invalid @enderror" id="tipo" name="Tipo" required>
          <option value="Maquina" {{ $Razon->Tipo == 'Maquina' ? 'selected' : '' }}>Máquina</option>
          <option value="Mesa" {{ $Razon->Tipo == 'Mesa' ? 'selected' : '' }}>Mesa</option>
          <option value="Ambos" {{ $Razon->Tipo == 'Ambos' ? 'selected' : '' }}>Ambos</option>
      </select>
      @error('Tipo')
          <div class="invalid-feedback">
              {{ $message }}
          </div>
      @enderror
  </div>

  <div class="col-md-6">
      <label for="sustituto" class="form-label">¿Sustituto?</label>
      <select class="form-control @error('Sustituto') is-invalid @enderror" id="sustituto" name="Sustituto" required>
          <option value="Si" {{ $Razon->replacement == 'Si' ? 'selected' : '' }}>Sí</option>
          <option value="No" {{ $Razon->replacement == 'No' ? 'selected' : '' }}>No</option>
      </select>
      @error('Sustituto')
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
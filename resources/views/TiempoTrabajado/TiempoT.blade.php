<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiempo Trabajado</title>
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
    <h1>Tiempos Trabajados</h1>

    @if($BotonVerTodos == "Ver")
    <a href="/TiempoTrabajado" class="NuevoTabla">Todos</a>

    <form action="/TiempoExcel" method="post" onsubmit="startDownload(event)">
      @csrf
  
      @if(!empty($BFecha))
          <input class="form-control" id="validationCustom01" value="{{$BFecha}}" name="Fecha" hidden>
      @endif
  
      @if(!empty($BUsuario))
          <input class="form-control" id="validationCustom01" value="{{$BUsuario}}" name="Usuario" hidden>
      @endif
  
      <button id="submitButton" style="position: absolute; right: 0; margin-right: 140px; margin-top: 10px;" class="btn btn-success">Excel</button>
  </form>
  
  <iframe id="download-frame" style="display: none;"></iframe>
  

    @else
    <a href="TiempoTrabajado/create" class="NuevoTabla">Nuevo</a>
    @endif
   

    </div>


    <form action="{{ route('buscar') }}" method="POST">
      @csrf
      <div class="display">

        <div class="col-md-6">
          <label for="validationCustom01" class="form-label">Empleado</label>
          <select class="form-control @error('Usuario') is-invalid @enderror" id="Usuario" name="Usuario">
              <option value="">Seleccione un Empleado</option>
              @foreach ($Usuarios as $Usu)
                  <option value="{{ $Usu->IdUser }}" {{ old('Producto') == $Usu->IdUser ? 'selected' : '' }}>
                      {{ $Usu->Name }}
                  </option>
              @endforeach
          </select>  
          @error('Usuario')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
          @enderror
      </div>
      
  
      <div class="col-md-5">
        <label for="validationCustom01" class="form-label">Fecha de comienzo</label>
        <input type="date" class="form-control @error('Fecha') is-invalid @enderror" id="validationCustom01" value="{{ old('Fecha') }}" name="Fecha">
        @error('Fecha')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

        <input type="hidden" value="TiempoT" id="validationCustomUsername" aria-describedby="inputGroupPrepend" method="POST" name="Tabla" accept="image/*"required>

        <button class="btn btn-primary" style="height: 40px; position: relative; top: 30px;margin-left: 2vw;"  style="margin-left: 2vw; padding-bottom: 4px">Buscar</button>
      </div>
    </style>
    </form>

    <div class="table-responsive">
    <table class="table" style="margin-top: 40px">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Inicio</th>
              <th scope="col">Final</th>
              <th scope="col">Empleado</th>
              <th scope="col">Editar</th>
              <th scope="col">Eliminar</th>
            </tr>
          </thead>
          <tbody>

            @php
            $counter = ($DatosPaginados->currentPage() - 1) * $DatosPaginados->perPage();
            @endphp

      @if(!empty($DatosPaginados))
      @foreach ($DatosPaginados as $TiempoT)

      <tr>
        <td scope="row">{{ ++$counter }}</td>
        <td>{{ $TiempoT["HStart"] }}</td>
        <td>{{ $TiempoT["HEnd"] }}</td>
        @if(!empty($TiempoT["user"]["CodeEmp"]))
        <td>{{ $TiempoT["user"]["CodeEmp"] ." ".$TiempoT["user"]["Name"] }}</td>
        @else
        <td>No Disponible</td>
        @endif
        <td><a href="/TiempoTrabajado/{{ $TiempoT["IdTimeWorked"] }}/edit">Editar</a></td>
        <td><a href="/TiempoTrabajado/{{ $TiempoT["IdTimeWorked"] }}">Borrar</a></td> 
      </tr>
      @endforeach
    </tbody>
      </table>
    </div>

      <div id="pagination-links" class="pagination">
        <!-- Mostrar el texto de la paginación actual -->
        <p style="position: relative; top: 5px;">Página {{ $DatosPaginados->currentPage() }} de {{ $DatosPaginados->lastPage() }}</p>
    
        <!-- Botones para avanzar y retroceder -->
        <div>
            @if ($DatosPaginados->currentPage() > 1)
                <a href="{{ route('TiempoTrabajado.index') }}?page={{ $DatosPaginados->currentPage() - 1 }}" class="btn btn-primary" style="margin-left: 7px">Anterior</a>
            @endif
    
            @if ($DatosPaginados->hasMorePages())
                <a href="{{ route('TiempoTrabajado.index') }}?page={{ $DatosPaginados->currentPage() + 1 }}" class="btn btn-primary" style="margin-left: 7px">Siguiente</a>
            @endif
        </div>
    </div>

      @else

      <h1>No hay registros hasta este momento</h1>

      @endif

    </div> <!-- fin del cuerpo con margenes -->

    <script>
      function startDownload(event) {
          // Evitar el comportamiento normal del formulario
          event.preventDefault();
  
          const button = document.getElementById('submitButton');
          const iframe = document.getElementById('download-frame');
          
          // Deshabilitar el botón y mostrar el mensaje de carga
          button.classList.add('disabled');
          button.innerText = 'Generando...';
  
          // Crear un FormData object y enviar el formulario a través del iframe
          const formData = new FormData(event.target);
  
          // Crear un nuevo formulario temporal para la descarga
          const tempForm = document.createElement('form');
          tempForm.method = 'POST';
          tempForm.action = event.target.action;
          tempForm.target = 'download-frame'; // Apunta al iframe
  
          // Añadir el token CSRF manualmente
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = document.querySelector('input[name="_token"]').value;
          tempForm.appendChild(csrfInput);
  
          // Agregar los campos del formulario
          formData.forEach((value, key) => {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = key;
              input.value = value;
              tempForm.appendChild(input);
          });
  
          // Agregar el formulario temporal al DOM
          document.body.appendChild(tempForm);
  
          // Enviar el formulario temporal
          tempForm.submit();
  
          // Opción para recargar la página automáticamente después de un tiempo
          setTimeout(function() {
              window.location.reload(); // Recargar la página
          }, 5000); // Ajusta el tiempo según el tamaño del archivo
  
          // Opción de habilitar el botón después de un tiempo, sin recargar
          /*
          setTimeout(function() {
              button.classList.remove('disabled');
              button.innerText = 'Excel';
          }, 10000); // 10 segundos como ejemplo
          */
  
          // Limpiar el formulario temporal
          document.body.removeChild(tempForm);
      }
  </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>
</html>
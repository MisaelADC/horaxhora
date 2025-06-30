<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obtener todos los detalles</title>
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

    <div class="margen">

    <div class="display">

    <h1>Excel Produccion</h1>

    <a href="Produccion" class="NuevoTabla">Regresar</a>

    </div>
    </div>

    @php
    $count = 0;
    @endphp

    <form class="row g-3 needs-validation" method="POST" action="{{ route('excel.todo') }}" onsubmit="startDownload(event)">
      @csrf
     
      <div class="col-md-6">
          <label for="validationCustom01" class="form-label">Fecha Inicio</label>
          <input type="date" class="form-control @error('Fecha') is-invalid @enderror" id="validationCustom01" value="{{ old('Fecha') }}" name="Fecha">
          @error('Fecha')
              <div class="invalid-feedback">
                  {{ $message }}    
              </div>
          @enderror
      </div>

      <div class="col-md-6">
        <label for="validationCustom01" class="form-label">Fecha Final</label>
        <input type="date" class="form-control @error('FechaF') is-invalid @enderror" id="validationCustom01" value="{{ old('FechaF') }}" name="FechaF">
        @error('FechaF')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="tipo" class="form-label">Tipo</label>
        <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo">
             <option value="">Seleccione...</option>
             <option value="Maquina" {{ old('tipo') == 'Maquina' ? 'selected' : '' }}>Máquina</option>
             <option value="Mesa" {{ old('tipo') == 'Mesa' ? 'selected' : '' }}>Mesa</option>
        </select>
        @error('tipo')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
    

    
    <div class="col-md-6">
        <label for="validationCustom01" class="form-label">Selecciona un Turno:</label>
        <select class="form-control @error('Turno') is-invalid @enderror" id="Turno" name="Turno">
            <option value="">Seleccione un Turno</option>
            @foreach ($Turno as $Tur)
                <option value="{{ $Tur->IdShift }}" {{ old('Turno') == $Tur->IdShift ? 'selected' : '' }}>
                    {{ $Tur->Shift }}
                </option>
            @endforeach
        </select>  
        @error('Turno')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
       
    </div>


      <center>
          <div class="col-12" style="margin-bottom: 50px">
              <button id="submitButton" class="btn btn-success" type="submit">Generar Excel Detallado Todo</button>
          </div>
      </center>

      <div style="height: 30px;whith: 100%"></div>

  </form>

  <iframe id="downloadFrame" style="display:none;"></iframe>

    </div> <!-- fin del cuerpo con margenes -->
    
    <script>
  
        let isGeneratingExcel = false; // Variable de control
        
        function startDownload(event) {
            // Evitar el comportamiento normal del formulario
            event.preventDefault();
    
            const button = document.getElementById('submitButton');
            const iframe = document.getElementById('download-frame');
    
            // Cambiar el estado de la variable antes de comenzar
            isGeneratingExcel = true; 
    
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
    
            // Limpiar el formulario temporal
            document.body.removeChild(tempForm);
            
            // Cambiar el estado de la variable después de completar
            isGeneratingExcel = false; 
        }
    
        // Recargar la página cada 90 segundos si no se está generando el Excel
        setInterval(function() {
            if (!isGeneratingExcel) {
                window.location.reload(); // Recargar la página
            }
        }, 90000); // 90 segundos
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="{{ asset('Todo.js') }}"></script> --}}

</body>
</html>
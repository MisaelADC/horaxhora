<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Produccion</title>
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

        <div class="cuerpo">

          <div class="margen" style="display: flex">
            <h1>Produccion</h1>


      <a href="/Produccion" class="NuevoTabla">Regresar</a>

            <form id="export-form" action="/export-hxh-excel/{{$id}}" method="GET" style="display: none;">
              @csrf
              <!-- Si necesitas pasar otros datos, agréguelos aquí como inputs ocultos -->
          </form>
          
          <iframe id="download-frame" style="display: none;"></iframe>
          
          <form action="/export-hxh-excel/{{$id}}" onsubmit="startDownload(event)" method="GET">
              <button id="submitButton" type="submit" class="btn btn-success" style="position: absolute; right: 0; margin-right: 150px; margin-top: 10px;">
                  Exportar a Excel
              </button>
          </form>
          

            </div>  

          <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Meta Inicial</th>
                        @if($Produccion->wo->product->Tipo == "Maquina")
                        <th scope="col">Producido</th>
                        <th scope="col">Tapetes Malos</th>  
                        @else
                        <th scope="col">Empacado</th>
                        @endif
                        <th scope="col">Max.Posible</th>
                        <th scope="col">Productividad</th>  
                        @if($Produccion->wo->product->Tipo  == "Maquina")
                        <th scope="col">Efectividad</th>  
                        @endif
                    </tr>
                </thead>
                <tbody>
                   
                       
                        <td>{{ $Produccion->Meta }}</td>
                        <td>{{ $Produccion->Real }}</td>
                        @if($Produccion->wo->product->Tipo  == "Maquina")
                        <td>{{ $Produccion->Scrap }}</td>
                        @endif

                        <td>{{$Hxh->MaximoPosible}}</td>
                       
                        @if($Produccion->Meta <= 0)
                        <td>0 %</td>
                        @else
                        <td>{{ number_format($Produccion->Real / $Produccion->Meta * 100, 2) }} %</td>
                        @endif

                        @if($Produccion->wo->product->Tipo  == "Maquina")
                        @if(($Produccion->Real + $Produccion->Scrap) <= 0)
                        <td>0 %</td>
                        @else
                        <td>{{ number_format($Produccion->Real / ($Produccion->Real + $Produccion->Scrap)  * 100, 2) }} %</td>
                        @endif
                        @endif
                        
                        

                    </tr>
                </tbody>
            </table>
          </div>

          <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Máquina/Mesa</th>
                        <th scope="col">Producto</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>{{ $Produccion->Machine->Description }}</td>
                        <td>{{ $Produccion->Wo->Product->Description }}</td>

                    </tr>
                </tbody>
            </table>  
          </div>

          <div class="margen" style="display: flex">
          <h1>Hora por hora</h1>
          </div>
      
          <div class="table-responsive">
          <table class="table">
              <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Comienzo</th>
                    <th scope="col">Final</th>
                    <th scope="col">Meta</th>
                    <th scope="col">Cantidad Producida</th>
                    @if($Produccion->wo->Tipo == "Maquina")
                    <th scope="col">Merma</th>
                    @endif
                    <th scope="col">Editar</th>
                  </tr>
                </thead>
                <tbody>
      
                  @php
                  $counter = 0;
                  @endphp
      
            @if(!empty($Hxh))
            @foreach ($Hxh as $Info)
      
            <tr>
              <td scope="row">{{ ++$counter }}</td>
              <td>{{ $Info["HStart"] }}</td>
              <td>{{ $Info["HEnd"] }}</td>
              <td>{{ round($Info["Segundos"] / $Produccion->Wo->Product->Cycle) }}</td>
              <td>{{ $Info["Real"] }}</td>
              @if($Produccion->wo->Tipo == "Maquina")
              <td>{{ $Info["Scrap"] }}</td>
              @endif
              <td><a href="/HxHREdit/{{ $Info["IdShiftHxh"] }}">Editar</a></td>
              
      
            </tr>
            @endforeach
          </tbody>
            </table>
          </div>
      
            @else
      
            <h1>No hay registros hasta este momento</h1>
      
            @endif


            <div class="margen" style="display: flex">
              <h1>Tiempos Muertos</h1>
          
              <!-- <a href="/TimeOut/create" class="NuevoTabla">Nuevo</a> -->
          
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

                  @if(!empty($Info->downtimeReason->Reason))
                  <td>{{ $Info->downtimeReason->Reason }}</td>
                  @else
                  <td>No Disponible</td>
                  @endif
                  @if(!empty($Info->user->Name))
                  <td>{{$Info->user->Name}}</td>
                  @else
                  <td>No Disponible</td>
                  @endif
          
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
            tempForm.method = 'GET';
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

</body>
</html>
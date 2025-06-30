<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="Todo.css" rel="stylesheet">

</head>

<body>

    <div class="header" style="color: white;">

       <img src="ADCLogoHeader.png" style="height: 100px;position: absolute;top: 0px;left: 5px;width: 100px;z-index: 1000;">

        <div style="display: flex">
            <a style="font-weight: bold;" href="/Desloguearse" class="LinksPrincipal">Salir</a>

            @if (session('OcultarToff') == false)
            
            <a style="font-weight: bold;" href="/VistaTiempoMuerto/{{$Produccion->IdProduction}}" class="LinksPrincipal">TimeOut</a>

            @endif
        </div>
    </div>

    <div class="cuerpo">

        <div class="margen" style="display: flex">

        </div>


        @if (!is_null($Produccion))

            <div class="accordion" id="accordionPanelsStayOpenExample">

                                <div class="table-responsive">
                                    <table class="table" style="font-size: 20px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">Meta</th>
                                                <th scope="col">Progreso</th>
                                                <th scope="col">Tapetes faltantes Wo</th>     
                                                <th scope="col">Turno</th>     
                                                <th scope="col">Wo</th>       
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>{{ $Produccion->Meta }}</td>
                                                <td>{{ number_format($Produccion->Real / $Produccion->Meta * 100, 2) }} %</td>
                                                <td>{{ $Produccion->wo->Meta -  $Produccion->wo->TotalReal->TotalProducido}} </td>
                                                <td>{{$Turno->Shift}}</td>
                                                <td>{{$Produccion->wo->Wo}}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table"  style="font-size: 20px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">Máquina/Mesa</th>
                                                <th scope="col">Producto</th>
                                                <th scope="col">Ciclo</th>
                                                @if($Produccion->wo->Design != 0)
                                                <th scope="col">Diseño</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>{{ $Produccion->Machine }}</td>
                                                <td>{{ $Produccion->Product }}</td>
                                                <td>{{ $Produccion->wo->product->Cycle }} s</td>

                                                @if($Produccion->wo->Design != 0)
                                                <th scope="col"> <img src="{{asset($Produccion["wo"]["design"]["Image"])}}" style="width: 80px" alt=""></th>
                                                @endif

                                            </tr>
                                        </tbody>
                                    </table>
                                

                <div class="accordion-item" style="margin-top: 50px;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                            aria-controls="panelsStayOpen-collapseThree">
                            <h1>Hora por Hora</h1>
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                        <div class="accordion-body">

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Comienzo</th>
                                                <th scope="col">Final</th>
                                                <th scope="col">Cantidad Producida</th>
                                                @if(($Produccion->Wo->Product->Tipo == "Maquina"))
                                                <th scope="col">Merma</th>
                                                @endif
                                                <th scope="col">Meta</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        

                                            @foreach ($Hxh as $Info)
                                                <tr>
                                                    <td>{{ $Info['HStart'] }}</td>
                                                    <td>{{ $Info['HEnd'] }}</td>
                                                    <td>{{ $Info['Real'] }}</td>
                                                    @if(($Produccion->Wo->Product->Tipo == "Maquina"))
                                                    <td>{{ $Info['Scrap'] }}</td>
                                                    @endif
                                                    <td>{{round($Info["Segundos"]/$Produccion->Wo->Product->Cycle, 0) }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="display" style="margin-bottom: 30px">

            @if(($Produccion->Wo->Product->Tipo == "Maquina"))   
            <div style="width: 47.5%; margin-right: 5%;">
            @else
            <div style="width: 100%;">
            @endif
       
  <div class="table-responsive">
      <table class="table">
         <thead>
                <tr>
                  <th style="font-size: 29px" scope="col">Piezas buenas</th> 
              </tr>
          </thead>  
          <tbody>
              <tr>
                  <td style="font-size: 29px">{{ $Produccion->Real }}</td>
              </tr>
          </tbody>
      </table>
      <div>

          <form action="/GestionSuma" method="POST">
            @csrf
            <input class="form-control" name="IdProduccion"
            value="{{ $Produccion->IdProduction }}" hidden>

            <div>
              <input style="height: 64px;font-size: 30px;width: 100%;" type="Number"
              min="0" class="form-control @error('CantidadBuenas') is-invalid @enderror" name="CantidadBuenas" id="validationCustom02"
              value="" value="{{ old('CantidadBuenas') }}" >
              @error('CantidadBuenas')
                  <div style="font-size: 23px;" class="invalid-feedback">
                      {{ $message }}
                  </div>
              @enderror
          </div>

</div>
</div>
        </div>

     @if(($Produccion->Wo->Product->Tipo == "Maquina"))
     <div style="width: 47.5%">


  <div class="table-responsive">
      <table class="table">
          <thead>
              <tr>
                  <tr>
                      <th style="font-size: 29px" scope="col">Piezas malas</th>
                  </tr>
              </thead>
              <tbody>
                  <tr>
                      <td style="font-size: 29px">{{ $Produccion->Scrap }}</td>
              </tr>
          </tbody>
      </table>



      <div>
          {{-- <div class="col-md-4"> --}}
              <div>
                  <input style="height: 64px;font-size: 30px;width: 100%;" type="Number"
                  min="0" class="form-control @error('CantidadMalas') is-invalid @enderror" name="CantidadMalas" id="validationCustom02"
                  value="" value="{{ old('CantidadMalas') }}" >
                  @error('CantidadMalas')
                      <div style="font-size: 23px;" class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
              </div>                            
          </div>
      </div> 
  </div>
  @endif
  <input class="form-control" name="HxhActual"
  value="{{ $Hxh[$HxhActual]['IdShiftHxh'] }}" hidden>
  <input class="form-control" name="QueSumar" value="Merma" hidden>   

 
</div>
</div>

<br>
<button class="btn btn-primary" type="submit"
style="height: 60px;width: 100%; font-size: 30px; position: relative; top: -30px;">Sumar</button>
</form>


     

            @if (session('OcultarToff') == false)
            
            <form id="terminarProduccionForm" action="/TerminarProduccion/{{$Produccion->IdProduction}}" method="GET">
              @csrf
              <button class="TerminarBoton" type="button" id="terminarButton">Terminar</button>
            </form>

            @else

       

            @endif

            <form action="/SalirTrabajo/{{$Produccion->IdProduction}}" method="GET">
                @csrf
            <button class="DejarBoton">Dejar de trabajar</button>
            </form> 

            @else

            <center><h1>No hay Trabajo Asignado para esta maquina</h1></center>

            <form action="/SalirTrabajo/{{$Produccion->IdProduction}}" method="GET">
                @csrf
            <button class="DejarBoton">Dejar de trabajar</button>
            </form> 

        @endif

    </div> <!-- fin del cuerpo con margenes -->


    <script>
      document.getElementById('terminarButton').addEventListener('click', function(event) {
          Swal.fire({
              title: '¿Estás seguro?',
              text: "No podrás revertir esta acción",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Sí, terminar',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
                  // Enviar el formulario si el usuario confirma
                  document.getElementById('terminarProduccionForm').submit();
              }
          })
      });
  </script>

    @if(!is_null($Produccion))
        <script>
        var horaLimite = "{{ $Hxh[$HxhActual]['HEnd'] }}"; 

        // Convertir la horaLimite en horas y minutos
        var partes = horaLimite.split(":");
        var horaLimiteHoras = parseInt(partes[0]);
        var horaLimiteMinutos = parseInt(partes[1]);

        // Sumar 10 minutos a la hora límite
        horaLimiteMinutos += 10; 
        if (horaLimiteMinutos >= 60) {
            horaLimiteHoras += Math.floor(horaLimiteMinutos / 60);
            horaLimiteMinutos = horaLimiteMinutos % 60;
        }

        // Crear un intervalo que revisa cada minuto si la hora actual es mayor o igual a la hora límite ajustada
        var checkTime = setInterval(function() {
            var fechaActual = new Date();
            var horaActual = fechaActual.getHours();
            var minutosActuales = fechaActual.getMinutes();

            // Comprobar si ya hemos pasado la hora límite ajustada
            if (horaActual > horaLimiteHoras || 
                (horaActual === horaLimiteHoras && minutosActuales >= horaLimiteMinutos)) {
                location.reload();  // Recargar la página si ha pasado el tiempo
                clearInterval(checkTime);  // Detener el intervalo una vez recargada la página
            }
        }, 30000);  // Revisar cada 60 segundos (1 minuto)

    </script>
@else
    <script>
      setTimeout(function(){
        location.reload();
      }, 20000);  // Revisar cada 60 segundos (1 minuto)
    </script>
@endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>

</html>

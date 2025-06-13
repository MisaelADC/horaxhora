<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bienvenido</title>
    <link href="Todo.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('Todo.css') }}" rel="stylesheet">
</head>
<body>
    <div class="header" style="display: flex;">
        <div>
          <a style="font-weight: bold;" href="/Desloguearse" class="Links">Salir</a>
           <a style="font-weight: bold;" href="/InicioEmp" class="Links">Inicio</a>
          <a style="font-weight: bold;" class="Links" href="/Produccion" id="navbarDropdown">Produccion</a>
        </div>
        </div>
        
        <div class="cuerpo">
    
        <div class="margen" style="display: flex">
        <h1>Bienvenido</h1> 

        <a href="/Inicio" class="NuevoTabla">Vista Admin</a>

        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Word Order</th>
                  <th scope="col">Can. Optima</th>
                  <th scope="col">Can. Producida</th>
                  <th scope="col">Merma</th>
                  <th scope="col">Material</th>
                  <th scope="col">Lote</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">HxH</th>
                  <th scope="col">Tiempos muertos</th>
                </tr>
              </thead>
              <tbody>
    
                @php
                $counter = 0;
                @endphp
    
          @if(!empty($Produccion))
          @foreach ($Produccion as $Pro)
    
          <tr>
            <td scope="row">{{ ++$counter }}</td>
            <td>{{ $Pro->Date }}</td>
            <td>{{ $Pro->Wo }}</td>
            <td>{{ $Pro->Meta }}</td>
            <td>{{ $Pro->Real }}</td>
            <td>{{ $Pro->Scrap }}</td>
            <td>{{ $Pro->RawMaterial }}</td>
            <td>{{ $Pro->BatchNumber }}</td>
            <td>{{ $Pro->WeightKg }}</td>
    
            <td><a href="/HxH/{{ $Pro->IdProduction }}">HxH</a></td>  
            <td><a href="/CTO/{{ $Pro->IdProduction }}">Tiempo Muerto</a></td>  
          </tr>
          @endforeach
        </tbody>
          </table>

          @else
           <h2>No Hay Trabajo Proximo</h2>
          @endif
        
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('Todo.js') }}"></script>
    
    </body>
    </html>
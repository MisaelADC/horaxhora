<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="{{ asset('Todo.css') }}" rel="stylesheet">

</head>

<body>

    <div class="header" style="display: flex;color: white;" >

        <img src="{{asset("ADCLogoHeader.png")}}" style="height: 100px;position: absolute;top: 0px;left: 5px;width: 100px;z-index: 1000;">

            <div style="display: flex">
                <a style="font-weight: bold; position:relative; left: -1.3vw;" href="/Desloguearse" class="LinksPrincipal">Salir</a>
            </div>
    </div>

    <div class="cuerpo">

    <div style="width: 90%; margin-left: 4.5%">

    @if ($errors->has('ErrorTOFF'))
    <form action="/SalirTiempoMuerto/{{$idProduccion}}" method="GET">
        @csrf
  
        <center><div class="alert alert-danger" style="margin-top: 10vh; whith: 60%">
        {{ $errors->first('ErrorTOFF') }}
        </div></center>
        <button class="ReanudarBoton" style="margin-top: 2vh;">Reanudar Trabajo</button>
    </form>
    @else
    <form action="/SalirTiempoMuerto/{{$idProduccion}}" method="GET">
        @csrf
        <button class="ReanudarBoton" style="margin-top: 25vh;">Reanudar Trabajo</button>
    </form>
    @endif

    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>

</html>

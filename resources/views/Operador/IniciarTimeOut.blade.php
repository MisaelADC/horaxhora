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
                <a style="font-weight: bold; position:relative; left: -1.3vw;" href="/Gestion" class="LinksPrincipal">Atras</a>
            </div>
    </div>

    <div class="cuerpo">

    <div style="width: 90%; margin-left: 4.5%">

        @if (session('OcultarToff') == false)

    <form action="/TiempoMuertoRedireccion/{{$idProduccion}}" method="POST">
        @csrf
        @method("PUT") 

        <div class="mb-3" style="margin-top: 20px">
            <center><label  for="validationCustom01" class="form-label"><b style="font-size: 25px">Usuario</b></label></center>
            <select class="form-control @error('Usuario') is-invalid @enderror" id="Usuario" name="Usuario" style="font-size: 25px">
                <option value="">Seleccione un Empleado</option>
                @foreach ($Usuarios as $Usu)
                    <option value="{{ $Usu->IdUser }}" {{ old('Usuario') == $Usu->IdUser ? 'selected' : '' }}>
                    {{ $Usu->CodeEmp." ".$Usu->Name }}
                    </option>
                @endforeach
            </select>  
            @error('Usuario')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3" style="margin-top: 20px">
            <center><label  for="validationCustom01" class="form-label"><b style="font-size: 25px">Razones</b></label></center>
            <select class="form-control @error('Descripcion') is-invalid @enderror" id="Descripcion" name="Descripcion" style="font-size: 25px">
                <option value="">Seleccione una Razon</option>
                @foreach ($Razones as $Raz)
                    <option value="{{ $Raz->IdDowntimeReason }}" {{ old('Descripcion') == $Raz->IdDowntimeReason ? 'selected' : '' }}>
                        {{ $Raz->Reason }}
                    </option>
                @endforeach
            </select>  
            @error('Descripcion')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="ReanudarBoton" style="margin-right: -.5vw;">Empezar Time Out</button>
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

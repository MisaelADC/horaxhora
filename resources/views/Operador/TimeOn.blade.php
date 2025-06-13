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

    <div class="header" style="display: flex;color: white;" >
        <img src="ADCLogoHeader.png" style="height: 100px;position: absolute;top: 0px;left: 5px;width: 100px;z-index: 1000;">

        <div style="display: flex">
            <a style="font-weight: bold;position:relative; left: -1.3vw;" href="/Desloguearse" class="LinksPrincipal">Salir</a>
        </div>
    </div>

    <div class="cuerpo">
        <div class="margen" style="display: flex">

   <form action="/IniciarConteo" method="POST">
        @csrf

        <div class="col-md-11" style="margin-left: 5vw; margin-top: 7vh;">
            <select class="form-control @error('Maquina') is-invalid @enderror" id="Maquina" name="Maquina" style="font-size: 23px;">
                <option value="" style="font-size: 23px;">Seleccione un Máquina/Mesa</option>
                @foreach ($Maquinas as $Maq)
                    <option value="{{ $Maq->IdMachine }}" {{ old('Producto') == $Maq->IdMachine ? 'selected' : '' }} style="font-size: 23px;">
                        {{ $Maq->MachineCode." ". $Maq->Description  }}
                    </option>
                @endforeach
            </select>  
            @error('Maquina')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

    <button class="EmpezarBoton">Empezar a trabajar</button>
    </form>

        </div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="{{ asset('Todo.css') }}" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <div class="margen" style="display: flex">
    <h1>Diseños</h1>
    <a href="/Productos" class="NuevoTabla">Regresar</a>
    </div>

    <br>
    <center><h2>{{$Producto["Description"]}}</h2></center>


      <div class="accordion accordion-flush" id="accordionFlushExample">

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
               <h2> Agregar diseño </h2>
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              
              <form class="row g-3 needs-validation" style="margin-top: 10px;" method="POST" action="/Diseño" enctype="multipart/form-data" >
                @csrf
                @method("POST")

                <div class="col-md-6" style="margin-bottom: 30px">
                  <label for="validationCustomUsername" class="form-label">Imagen</label>
                  <div class="input-group has-validation">
                    <input type="file" class="form-control"value="URL" id="validationCustomUsername" aria-describedby="inputGroupPrepend" method="POST" name="Imagen" 
                    accept="image/*"required>
                  </div>
                </div>
          

                <div class="col-md-6">
                    <label for="validationCustom01" class="form-label">Nombre del diseño</label>
                    <input type="text" class="form-control @error('Nombre') is-invalid @enderror" id="validationCustom01" value="{{ old('Nombre') }}" name="Nombre" required>
                    @error('Nombre')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <input type="hidden" id="IdProducto" name="IdProducto" value="{{$Producto->IdProduct}}">

                <center>
                    <div class="col-12" style="margin-bottom: 45px;">
                        <button class="btn btn-primary" type="submit">Agregar</button>
                    </div>
                </center>
            </form>

            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="true" aria-controls="flush-collapseTwo">
                    <h2>Diseños Registrados</h2>
                </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExample">
              
              <div class="table-responsive">
                <table class="table" style="margin-top: 40px">
                    <thead>
                        <tr>
                          <th scope="col">Nombre</th>
                          <th scope="col">Imagen</th>
                          <th scope="col">Editar</th>
                          <th scope="col">Eliminar</th>
                        
                        </tr>
                      </thead>
                      <tbody>
            
                  @if(!empty($Diseños))
                  @foreach($Diseños as $D)
                  <tr>
                      <td>{{ $D->Name }}</td>
                      <td> <img src="{{asset($D["Image"])}}" style="width: 80px" alt=""> </td> 
                      <td>
                          <a href="/Diseño/{{ $D->IdDesign }}/edit">Editar</a>
                      </td>
                      <td>
                          <form method="POST" action="/Diseño/{{ $D->IdDesign }}" class="form-eliminar">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Borrar</button>
                          </form>
                      </td>
                  </tr>
              @endforeach
                </tbody>
                  </table>
                </div>
            
                  @else
            
                  <h1>No hay registros hasta este momento</h1>
            
                  @endif

            </div>
          </div>

        </div>
      </div>

   
      <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los formularios con la clase 'form-eliminar'
            const forms = document.querySelectorAll('.form-eliminar');
    
            // Iterar sobre cada formulario
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Detener el envío del formulario
    
                    // Mostrar el cuadro de diálogo de SweetAlert
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor:  '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminarlo',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirma, enviar el formulario
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    </div> <!-- fin del cuerpo con margenes -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>
</html>
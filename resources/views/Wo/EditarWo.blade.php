<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Wo</title>
    <link href="{{ asset('Todo.css') }}" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (debe estar antes de Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

</head>
<body>

  <style>
    .select2-container--bootstrap-5 .select2-selection {
  border: 1px solid #ced4da !important; /* Color de borde Bootstrap */
  border-radius: 5px; /* Bordes redondeados */
  padding-top: 4px;
  height: 38px;
}
  </style>
  
  
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
    <h1>Editar Wo</h1>
    </div>

    <form class="row g-3 needs-validation" method="POST" action="/Wo/{{$Wo->IdWo}}" enctype="multipart/form-data">
      @csrf
      @method("PUT")

    <div class="col-md-6">
        <label for="validationCustom01" class="form-label">Work order</label>
        <input type="text" class="form-control" id="validationCustom01" value="{{$Wo->Wo}}"  name="Wo" required>
        @error('Wo')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
    </div>

    <div class="col-md-6">
      <label for="validationCustom01" class="form-label">Cantida de tapetes</label>
      <input type="number" min="0"  class="form-control" id="validationCustom01" value="{{$Wo->Meta}}"  name="Cantidad" required>
      @error('Cantidad')
      <div class="invalid-feedback">
          {{ $message }}
      </div>
      @enderror
    </div>


    <div class="col-md-6">
      <label for="productSelect" class="form-label">Producto:</label>

      <select id="productSelect" class="form-control @error('Producto') is-invalid @enderror" name="Producto">
          <option value="">Seleccione un producto</option>
          @foreach ($Productos as $Pro)
              <option value="{{ $Pro->IdProduct }}" 
                  {{ (old('Producto') == $Pro->IdProduct || $Wo->IdProduct == $Pro->IdProduct) ? 'selected' : '' }}>
                  {{ $Pro->ItemCode . " " . $Pro->Description }}
              </option>
          @endforeach
      </select>
      @error('Producto')
          <div class="invalid-feedback">
              {{ $message }}
          </div>
      @enderror
  </div>

  <div class="col-md-2">
    <label for="designSelect" class="form-label">Diseño Actual:</label><br>
    @if(isset($Wo["design"]) && !is_null($Wo["design"]) && isset($Wo["design"]["Image"]))
        <img src="{{ asset($Wo['design']['Image']) }}" style="width: 90px" alt="">
    @else
        <b>Sin Diseño</b>
    @endif

</div>

  <div class="col-md-4">
    <label for="designSelect" class="form-label">Diseño:</label>
    <select id="designSelect" class="form-control" name="Diseño"  disabled>
        <option value="0">Ninguno</option>
    </select>
</div>

@if ($errors->has('Diseno'))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->get('Diseno') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  
      <center>
          <div class="col-12" style="margin-bottom: 50px">
              <button class="btn btn-primary" type="submit">Editar</button>
          </div>
      </center>
  </form>
    </div> <!-- fin del cuerpo con margenes -->

    <script>
      $(document).ready(function() {
          // Inicializar Select2 con Bootstrap
          $('#designSelect').select2({
              theme: "bootstrap-5", // Para que use el estilo de Bootstrap
              dropdownParent: $('#designSelect').parent(), // Ajusta el menú desplegable
              templateResult: formatDesignOption,  // Mostrar imágenes en opciones
              templateSelection: formatDesignSelection, // Mostrar imagen al seleccionar
              minimumResultsForSearch: -1  // Desactiva el buscador
          });
    
          // Función para cargar diseños basados en el producto seleccionado
          function loadDesigns(productId) {
              const designSelect = $('#designSelect');
    
              if (productId) {
                  fetch(`/get-designs/${productId}`)
                      .then(response => response.json())
                      .then(data => {
                          // Limpiar el select
                          designSelect.empty();
    
                          // Verificar si hay diseños relacionados
                          if (data.length > 0) {
                              // Si hay diseños, agregar la opción "Seleccionar" y los diseños
                              designSelect.append('<option value="">No cambiar</option>'); // Opción "Seleccionar" con valor vacío
                              designSelect.append('<option value="0">Ninguno</option>');
                              data.forEach(design => {
                                  let option = new Option(design.Name, design.IdDesign, false, false);
                                  $(option).attr('data-image', design.Image);
                                  designSelect.append(option);
                              });
                          } else {
                              // Si no hay diseños, agregar solo la opción "Ninguno"
                              designSelect.append('<option value="0">Ninguno</option>');
                          }
    
                          // Activar select y aplicar Select2 con Bootstrap
                          designSelect.prop('disabled', false).select2({
                              theme: "bootstrap-5",
                              dropdownParent: designSelect.parent(),
                              templateResult: formatDesignOption,
                              templateSelection: formatDesignSelection
                          });
    
                          // Seleccionar el diseño actual de la Wo
                          const currentDesignId = "{{ $Wo->IdDesign }}"; // Obtener el IdDesign de la Wo
                          if (currentDesignId) {
                              designSelect.val(currentDesignId).trigger('change');
                          } else {
                              // Si no hay un diseño seleccionado, seleccionar "Ninguno" o "Seleccionar"
                              if (data.length > 0) {
                                  designSelect.val("").trigger('change'); // Seleccionar "Seleccionar"
                              } else {
                                  designSelect.val("0").trigger('change'); // Seleccionar "Ninguno"
                              }
                          }
                      });
              } else {
                  // Si no hay un producto seleccionado, deshabilitar el select y agregar solo "Ninguno"
                  designSelect.empty()
                      .append('<option value="">Seleccionar</option>')
                      .prop('disabled', true);
              }
          }
    
          // Cargar diseños cuando se selecciona un producto
          $('#productSelect').on('change', function() {
              const productId = this.value;
              loadDesigns(productId);
          });
    
          // Cargar diseños al iniciar la página si ya hay un producto seleccionado
          const initialProductId = $('#productSelect').val();
          if (initialProductId) {
              loadDesigns(initialProductId);
          }
      });
    
      function formatDesignOption(design) {
          if (!design.id) return design.text;
          const imageUrl = $(design.element).data('image');
          return imageUrl ? $(`<span><img src="${imageUrl}" class="img-thumbnail" style="width: 50px; height: 50px; margin-right: 10px;"> ${design.text}</span>`) : design.text;
      }
    
      function formatDesignSelection(design) {
          if (!design.id) return design.text;
          const imageUrl = $(design.element).data('image');
          return imageUrl ? $(`<span><img src="${imageUrl}" class="img-thumbnail" style="width: 30px; height: 30px; margin-right: 5px;"> ${design.text}</span>`) : design.text;
      }
    </script>

    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Todo.js') }}"></script>

</body>
</html>
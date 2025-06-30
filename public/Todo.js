const botones = document.querySelectorAll('button[type="submit"]');
botones.forEach(boton => {
    boton.addEventListener('click', function(event) {
        // Verificar si el formulario es válido antes de mostrar la notificación de carga
        if (!this.form.checkValidity()) {
            return; // Salir si el formulario no es válido
        }

        // Mostrar la notificación de carga
        Swal.fire({
            icon: 'info',
            title: 'Procesando solicitud...',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 30000 // Ejemplo de tiempo de espera para la notificación
        });

        // Verificar la conexión a internet antes de enviar el formulario
        if (!navigator.onLine) {
            Swal.fire({
                icon: 'error',
                title: 'Sin conexión a Internet',
                text: 'No tienes conexión a internet. Por favor, verifica tu conexión.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Reintentar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Recargar la página si se confirma
                }
            });

            event.preventDefault(); // Evitar que se envíe el formulario si no hay conexión
        }
    });
});

<script src="{{ asset('custom/js/telinput.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.querySelector("#phone");
        const country_code = document.querySelector("#country_code");
        const iti = window.intlTelInput(input, {
            initialCountry: "auto",
            separateDialCode: true,
            geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("us"));
            },
            utilsScript: "{{ asset('custom/js/telinput.js') }}",
        });
        if (input.value) {
            const countryData = iti.getSelectedCountryData();
            country_code.value = countryData.dialCode; // Aquí puedes establecer el código de área inicial
        }
        input.addEventListener("countrychange", function() {
            country_code.value = iti.getSelectedCountryData().dialCode;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.href; // Obtiene la URL actual
        const targetUrl = "{{ route('contacts.index', ['type' => 'create']) }}"; // Ruta objetivo
        const baseUrl = "{{ route('contacts.index') }}"; // Ruta base
        document.getElementById('createContact').addEventListener('click', function() {
            if (currentUrl === targetUrl || currentUrl.startsWith(baseUrl)) {
                // Si estamos en la ruta correcta, abrir el modal
                console.log('abre el modal');
                $('#kt_modal_create').modal('show');
                // No es necesario llamar a openModal() ya que Bootstrap lo maneja
            } else {
                // Si no estamos en la ruta correcta, redirigir a la ruta correcta
                window.location.href = targetUrl;
            }
        });
        // Código para abrir el modal si ya estás en la ruta correcta
        setTimeout(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            if (type === 'create') {
                // Abre el modal usando Bootstrap
                $('#kt_modal_create').modal('show');
            }
        }, 1000); // Ajusta el tiempo según sea necesario
    });
</script>

<script>
    $(document).ready(function() {
        // Verifica si la sesión 'first_login' existe
        @if (session('first_login'))
            // Abre el modal
            $('#button_first_login').click();
            console.log('show')
        @endif
        // Manejo del botón de guardar
        $('#kt_first_login_google_face form').on('submit', function(e) {
            console.log('submit');
            e.preventDefault(); // Evita que el formulario se envíe de la manera tradicional
            // Obtén los datos del formulario
            var formData = $(this).serialize(); // Serializa los datos del formulario
            // Envía los datos al servidor
            $.ajax({
                url: '{{ route('save_data_google_facebook') }}', // Cambia esto a la ruta donde deseas enviar los datos
                type: 'POST',
                data: formData + '&_token={{ csrf_token() }}', // Incluye el token CSRF
                success: function(response) {
                    if (response.success) {
                        $('.btn[data-bs-dismiss="modal"]')
                    .click(); // Cierra el modal si es necesario
                    } else {
                        // Manejo de errores si la respuesta no es exitosa
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // Manejo de errores
                }
            });
        });
        // $('.btn[data-bs-dismiss="modal"]').on('click', function() {
        //     // Aquí puedes hacer la llamada AJAX para eliminar la sesión
        //     $.ajax({
        //         url: '{{ route('omit_modal') }}', // Cambia esto a la ruta correcta
        //         type: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}' // Asegúrate de incluir el token CSRF
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 // El modal se cerrará automáticamente, no necesitas hacer nada más
        //             }
        //         },
        //         error: function(xhr) {
        //             console.log(xhr.responseText);
        //         }
        //     });
        // });
        // // Manejo del botón de guardar
        // $('#kt_first_login_google_face form').on('submit', function(e) {
        //     e.preventDefault(); // Evita que el formulario se envíe de la manera tradicional
        //     // Obtén los datos del formulario
        //     var formData = $(this).serialize(); // Serializa los datos del formulario
        //     // Envía los datos al servidor
        //     $.ajax({
        //         url: '{{ route('save_data_google_facebook') }}', // Cambia esto a la ruta donde deseas enviar los datos
        //         type: 'POST',
        //         data: formData + '&_token={{ csrf_token() }}', // Incluye el token CSRF
        //         success: function(response) {
        //             if (response.success) {
        //                 // Aquí puedes agregar lógica para manejar una respuesta exitosa
        //                 $('#kt_first_login_google_face').modal(
        //                     'hide'); // Cierra el modal si es necesario
        //             } else {
        //                 // Manejo de errores si la respuesta no es exitosa
        //                 alert('Error: ' + response.message);
        //             }
        //         },
        //         error: function(xhr) {
        //             console.log(xhr.responseText); // Manejo de errores
        //         }
        //     });
        // });
        // Manejo del cierre del modal al hacer clic fuera del modal
        // $('#kt_first_login_google_face').on('hidden.bs.modal', function() {
        //     // Aquí puedes hacer la llamada AJAX para eliminar la sesión
        //     $.ajax({
        //         url: '{{ route('omit_modal') }}', // Cambia esto a la ruta correcta
        //         type: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}' // Asegúrate de incluir el token CSRF
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 // El modal se ha cerrado y la sesión se ha eliminado
        //             }
        //         },
        //         error: function(xhr) {
        //             console.log(xhr.responseText);
        //         }
        //     });
        // });
    });
</script>


<script>
    // Stepper lement
    var element = document.querySelector("#kt_stepper_example_basic");

    // Initialize Stepper
    var stepper = new KTStepper(element);

    // Handle next step
    stepper.on("kt.stepper.next", function(stepper) {
        stepper.goNext(); // go next step
    });

    // Handle previous step
    stepper.on("kt.stepper.previous", function(stepper) {
        stepper.goPrevious(); // go previous step
    });
</script>

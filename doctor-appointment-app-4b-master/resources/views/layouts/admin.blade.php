
@props([
    'title' => config ('app.name, Laravel'),
    'breadcrumbs' => []])


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/f04a2d4b08.js" crossorigin="anonymous"></script>

         <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- WireUI -->
        <wireui:scripts />

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50"
    @if (session()->has('swal'))
        data-swal="@json(session('swal'))"
    @endif
>
        @include('layouts.includes.admin.navigation')

        @include('layouts.includes.admin.sidebar')

        <div class="p-4 sm:ml-64">
            <div class="mt-14 flex items-center justify-between w-full">
                @include('layouts.includes.admin.breadcrumb')
                {{ $action ?? '' }}
            </div>

    {{$slot}}
</div>

        @stack('modals')

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

        <!-- Mostrar Sweet Alert -->
        @if(@session()->has('swal'))
        <script>
            Swal.fire({!! json_encode(session('swal')) !!});
        </script>
        @endif

        <script>
    //Buscar todos los elementos de una clase
    const forms = document.querySelectorAll('.delete-form'); // Asegúrate que la clase sea '.delate-form' y no '.delete-form'
    
    // El método es forEach (con 'E' mayúscula)
    forms.forEach(form => {
        //Se pone al pendiente
        form.addEventListener('submit', function(e) {
            //Evita que se envíe el formulario inmediatamente
            e.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, bórralo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Usamos form.submit() en lugar de this.submit()
                    // para asegurar que nos referimos al formulario correcto.
                    form.submit();
                }
            });
        });
    });
</script>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AgendAí</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Fonts  -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Carregando todos os assets via Vite --}}
        @vite(['resources/css/app.css', 'resources/css/explore.css', 'resources/css/nightMode.css', 'resources/js/app.js', 'resources/js/dashBoard.js', 'resources/js/searchFlights.js', 'resources/js/formTrip.js' , 'resources/js/hotels.js', 'resources/js/nightMode.js'])
        <script>
        // Aplica night-mode globalmente em todas as telas
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('siteTheme');
            if (theme === 'night') {
                document.documentElement.classList.add('night-mode');
                document.body.classList.add('night-mode');
            } else {
                document.documentElement.classList.remove('night-mode');
                document.body.classList.remove('night-mode');
            }
        });
        </script>
    </head>
    <body>
            <main class="">
                @yield('content')
            </main>
    </body>
</html>

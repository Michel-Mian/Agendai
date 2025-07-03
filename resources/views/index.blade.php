<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AgendAí</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/myTrips.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashBoard.js', 'resources/js/searchFlights.js', 'resources/js/formTrip.js' , 'resources/js/formTrip.js'])
    </head>
    <body>
            <main class="">
                @yield('content')
            </main>
            
            <!--Google Maps API-->
            <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWLH0DB_w7iFWxaPJHOl69rSP6YT3sp80&libraries=places&callback=initMap" async defer>
            </script>
    </body>
    
</html>

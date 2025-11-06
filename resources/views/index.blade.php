<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AgendAí</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<<<<<<< HEAD
=======
        <script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">
            (function () {
                var script = document.createElement("script");
                script.async = 1;
                script.src = 'https://emrldco.com/NDY2NTA3.js?t=466507';
                document.head.appendChild(script);
            })();
        </script>
>>>>>>> d643e774296f46c453f341bc72b8ad752d734306
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/app.css', 'resources/css/explore.css', 'resources/css/nightMode.css', 'resources/js/app.js', 'resources/js/dashBoard.js', 'resources/js/searchFlights.js', 'resources/js/formTrip.js' , 'resources/js/hotels.js', 'resources/js/nightMode.js', 'resources/js/insurance-modal.js'])
        <style>
            /* ===== Loader ===== */
            /* Loader base */
            #loader {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                transition: opacity 0.3s ease;
            }
            /* Loader dark mode */
            .night-mode #loader {
                background: #181f2a;
            }
            #loader.hidden {
                opacity: 0;
                pointer-events: none;
            }
            .loading-container {
                position: relative;
                width: 100%;
                height: 100vh;
                overflow: hidden;
            }
            .circle {
                position: absolute;
                top: 50%; left: 50%;
                width: 200px; height: 200px;
                background: #0088FF;
                border-radius: 50%;
                transform: translate(-50%, -50%);
                box-shadow: 0 0 40px 0 #0088FF33;
            }
            .night-mode .circle {
                background: #2d4a7a;
                box-shadow: 0 0 40px 0 #2d4a7a55;
            }
            .cloud {
                position: absolute;
                background: url('/imgs/loading/clouds.png') no-repeat center/cover;
                opacity: 0.9;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
            }
            .night-mode .cloud {
                filter: brightness(0.7) drop-shadow(0 2px 8px #222);
                opacity: 0.7;
            }
            /* Nuvens mais altas e distribuídas */
            .cloud1 {
                width: 90px; height: 90px;
                top: 32%; left: 44%;
                animation: moveCloud1 14s linear infinite;
            }
            .cloud2 {
                width: 70px; height: 70px;
                top: 28%; left: 60%;
                animation: moveCloud2 16s linear infinite;
                animation-delay: 4s;
            }
            .cloud3 {
                width: 60px; height: 60px;
                top: 48%; left: 35%;
                animation: moveCloud3 13s linear infinite;
                animation-delay: 8s;
            }
            .cloud4 {
                width: 80px; height: 80px;
                top: 46%; left: 68%;
                animation: moveCloud4 15s linear infinite;
                animation-delay: 2s;
            }
            .cloud5 {
                width: 50px; height: 50px;
                top: 42%; left: 53%;
                animation: moveCloud5 12s linear infinite;
                animation-delay: 6s;
            }
            .cloud6 {
                width: 65px; height: 65px;
                top: 30%; left: 57%;
                animation: moveCloud6 17s linear infinite;
                animation-delay: 3s;
            }
            .airplane {
                position: absolute;
                top: 50%; left: 50%;
                width: 200px; height: 200px;
                background: url('/imgs/loading/plane.png') no-repeat center/cover;
                transform: translate(-50%, -50%) rotate(10deg);
                animation: fly 4s ease-in-out infinite;
                z-index: 10;
            }
            .night-mode .airplane {
                filter: brightness(0.8) drop-shadow(0 2px 8px #222);
            }
            /* Animações das nuvens: movimento horizontal suave */
            @keyframes moveCloud1 {
                0%   { left: 44%; }
                50%  { left: 48%; }
                100% { left: 44%; }
            }
            @keyframes moveCloud2 {
                0%   { left: 60%; }
                50%  { left: 64%; }
                100% { left: 60%; }
            }
            @keyframes moveCloud3 {
                0%   { left: 35%; }
                50%  { left: 39%; }
                100% { left: 35%; }
            }
            @keyframes moveCloud4 {
                0%   { left: 68%; }
                50%  { left: 72%; }
                100% { left: 68%; }
            }
            @keyframes moveCloud5 {
                0%   { left: 53%; }
                50%  { left: 57%; }
                100% { left: 53%; }
            }
            @keyframes moveCloud6 {
                0%   { left: 57%; }
                50%  { left: 61%; }
                100% { left: 57%; }
            }
            @keyframes fly {
                0%   { transform: translate(-50%, -50%) rotate(10deg) translateY(0); }
                50%  { transform: translate(-50%, -50%) rotate(12deg) translateY(-8px); }
                100% { transform: translate(-50%, -50%) rotate(10deg) translateY(0); }
            }
            .loading-text {
                position: absolute;
                left: 50%;
                top: 72%; /* Centralizado abaixo do círculo */
                transform: translate(-50%, 0);
                font-size: 1.5rem;
                color: #0088FF;
                font-weight: 600;
                letter-spacing: 1px;
                text-shadow: 0 2px 8px #e0e7ef;
                z-index: 20;
            }
            .night-mode .loading-text {
                color: #7bb6ff;
                text-shadow: 0 2px 8px #222;
            }
        </style>
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
        <!-- Loader -->
        <div id="loader">
            <div class="loading-container">
                <div class="circle"></div>
                <div class="cloud cloud1"></div>
                <div class="cloud cloud2"></div>
                <div class="cloud cloud3"></div>
                <div class="cloud cloud4"></div>
                <div class="cloud cloud5"></div>
                <div class="cloud cloud6"></div>
                <div class="airplane"></div>
                <div class="loading-text">Carregando...</div>
            </div>
        </div>

            @yield('content')
        
        <script>
            // Esconde o loader quando a página termina de carregar
            window.addEventListener("load", function () {
                document.getElementById("loader").classList.add("hidden");
            });

            // Mostra o loader ao navegar por links internos ou enviar forms
            document.addEventListener("DOMContentLoaded", () => {
                const loader = document.getElementById("loader");

                document.querySelectorAll("a").forEach(link => {
                    link.addEventListener("click", e => {
                        // NÃO mostra loader se for .no-loader, âncora interna, ou tab
                        if (
                            link.classList.contains('no-loader') ||
                            (link.hash && link.pathname === window.location.pathname) ||
                            link.getAttribute('href')?.startsWith('#')
                        ) {
                            return;
                        }
                        if (link.href && link.href.startsWith(window.location.origin)) {
                            document.getElementById("loader").classList.remove("hidden");
                        }
                    });
                });

                document.querySelectorAll("form").forEach(form => {
                    form.addEventListener("submit", () => {
<<<<<<< HEAD
                        loader.classList.remove("hidden");
=======
                        // Não mostra loader se o form tiver atributo data-no-loader
                        if (!form.hasAttribute('data-no-loader')) {
                            loader.classList.remove("hidden");
                        }
>>>>>>> d643e774296f46c453f341bc72b8ad752d734306
                    });
                });
            });
        </script>
    </body>
</html>

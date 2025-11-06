<div class="bg-white rounded-xl shadow-lg p-12">
    <div class="flex flex-col items-center justify-center py-12">
        <style>
            /* ===== Loader ===== */
            .loading-container {
                position: relative;
                width: 220px;
                height: 220px;
                margin: 0 auto;
            }

            .circle {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 200px;
                height: 200px;
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

            /* Nuvens acima e abaixo do avião/círculo */
            .cloud1 {
                width: 60px;
                height: 60px;
                top: 35px;
                left: 100%;
                animation: cloudMove 7s linear infinite;
            }

            .cloud2 {
                width: 50px;
                height: 50px;
                top: 60px;
                left: 100%;
                animation: cloudMove 9s linear infinite;
                animation-delay: 2s;
            }

            .cloud3 {
                width: 45px;
                height: 45px;
                top: 120px;
                left: 100%;
                animation: cloudMove 8s linear infinite;
                animation-delay: 4s;
            }

            .cloud4 {
                width: 55px;
                height: 55px;
                top: 170px;
                left: 100%;
                animation: cloudMove 10s linear infinite;
                animation-delay: 1s;
            }

            .cloud5 {
                width: 40px;
                height: 40px;
                top: 80px;
                left: 100%;
                animation: cloudMove 6s linear infinite;
                animation-delay: 3s;
            }

            .cloud6 {
                width: 50px;
                height: 50px;
                top: 150px;
                left: 100%;
                animation: cloudMove 11s linear infinite;
                animation-delay: 5s;
            }

            @keyframes cloudMove {
                0% {
                    left: 100%;
                }

                100% {
                    left: -60px;
                }
            }

            .airplane {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 120px;
                height: 120px;
                background: url('/imgs/loading/plane.png') no-repeat center/cover;
                transform: translate(-50%, -50%) rotate(10deg);
                animation: fly 4s ease-in-out infinite;
                z-index: 10;
            }

            @keyframes fly {
                0% {
                    transform: translate(-50%, -50%) rotate(10deg) translateY(0);
                }

                50% {
                    transform: translate(-50%, -50%) rotate(12deg) translateY(-8px);
                }

                100% {
                    transform: translate(-50%, -50%) rotate(10deg) translateY(0);
                }
            }
        </style>
        
        <div class="loading-container">
            <div class="circle"></div>
            <div class="cloud cloud1"></div>
            <div class="cloud cloud2"></div>
            <div class="cloud cloud3"></div>
            <div class="cloud cloud4"></div>
            <div class="cloud cloud5"></div>
            <div class="cloud cloud6"></div>
            <div class="airplane"></div>
        </div>

        <div class="text-blue-700 font-semibold text-lg mb-2 mt-8">Buscando os melhores veículos...</div>
        <div class="text-gray-600 text-sm text-center max-w-md mb-4">
            Estamos procurando as melhores opções de veículos para sua viagem.
            Isso pode levar alguns segundos.
        </div>
        <div class="mt-4 w-full max-w-md">
            <div class="bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500 animate-pulse" style="width: 60%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-1 text-center">Aguarde enquanto buscamos os veículos...</div>
        </div>
    </div>
</div>

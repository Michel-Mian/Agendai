<header class="bg-white w-full top-0 z-50 h-25 border-b border-gray-200">
    <div class="flex justify-between items-center max-w-8xl mx-auto">
        <div class="">
            <h1 class="text-2xl text-black font-bold tracking-wider mx-5 mt-4 p-0 ">
                {{ $title ?? 'Bem vindo de volta!' }}
            </h1>
            <h2 class="text-base font-thin mt-1 mx-5 text-gray-600">Pronto para planejar sua próxima aventura?</h2>
        </div>
        @if (request()->is('explore'))
            <div class="flex items-center space-x-2 mx-5 mt-4">
                <button class="hidden sm:flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Exportar
                </button>
            </div>
        @else
            <button id="new-trip" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-4 py-2 rounded-lg flex items-center space-x-2 cursor-pointer mx-5 mt-4 hover:from-blue-700 hover:to-blue-600 transition">
                <a href="formTrip">
                    <span class="text-xl">+</span>
                    <span>Nova Viagem</span>
                </a>
            </button>
        @endif
    </div>
</header>
@extends('index')

@section('content')
<style>
    #loader { display: none !important; }
</style>
<div class="flex min-h-screen bg-white">
    @include('components/layout.sidebar')
    
    <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components/layout/header')
        
        <div class="w-full max-w-7xl mx-auto mt-8 px-4 pb-8">
            <!-- Título -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-hotel text-pink-600 mr-3"></i>
                    Pesquisa de Hotéis
                </h1>
                <p class="text-gray-600 mt-2">
                    Encontre a hospedagem perfeita para sua viagem
                </p>
            </div>
            
            <!-- Formulário de Busca -->
            <!-- Formulário de Busca -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form id="hotel-search-form" class="flex flex-col gap-6 w-full">
                    @csrf
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col gap-2">
                            <label for="hotel-query" class="text-base text-gray-700 font-semibold">Destino ou Nome do Hotel</label>
                            <input type="text" id="hotel-query" class="px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-base text-gray-700 placeholder-gray-400 transition" placeholder="Ex: Rio de Janeiro" required>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex flex-col w-full md:w-1/3">
                                <label for="check-in-date" class="text-base text-gray-700 mb-2 font-semibold">Check-in</label>
                                <input type="date" id="check-in-date" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition w-full" required>
                            </div>
                            <div class="flex flex-col w-full md:w-1/3">
                                <label for="check-out-date" class="text-base text-gray-700 mb-2 font-semibold">Check-out</label>
                                <input type="date" id="check-out-date" class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition w-full" required>
                            </div>
                            <button type="submit" class="cursor-pointer bg-pink-600 hover:bg-pink-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition flex items-center gap-2 text-base justify-center w-full md:w-auto" aria-label="Pesquisar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                Pesquisar
                            </button>
                            <div class="flex items-center w-full md:w-auto">
                                @include('components.hotels.advanceFilter')
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            @include('components/hotels/filterHotels')

            <div id="results-summary" class="hidden mb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <span id="results-count" class="text-gray-800 font-medium"></span>
                </div>
            </div>

            <div id="hotel-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            </div>

            <div id="loading-indicator" class="hidden text-center mt-8">
                <div class="inline-flex items-center px-6 py-4 font-semibold leading-6 text-base shadow-md rounded-lg text-gray-700 bg-white transition ease-in-out duration-150 border border-gray-200">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Carregando resultados...
                </div>
            </div>

            <div id="error-message" class="hidden mt-4 bg-red-50 border border-red-300 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-base text-red-800" id="error-text"></p>
                    </div>
                </div>
            </div>

            <div id="pagination" class="flex justify-center mt-8">
                <button id="load-more" class="hidden bg-pink-600 hover:bg-pink-700 text-white font-medium py-3 px-6 rounded-lg shadow-md transition duration-200 flex items-center text-base">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Carregar Mais Resultados
                </button>
            </div>
        </div>
    </div>
    <script>
        window.userTrips = @json($trips ?? []);
    </script>
    <script src="/js/toast.js"></script>
</div>

<div id="trip-selection-modal" class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 md:p-7 w-full max-w-xl border border-gray-200">
        <button id="close-trip-modal-btn" type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600" aria-label="Fechar">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Adicionar hotel a uma viagem</h3>
    <p id="trip-modal-hint" class="text-sm text-gray-600 mb-5">Selecione uma viagem cujo período cubra as datas da sua hospedagem.</p>

        <div class="mb-5">
            <label for="trip-select" class="block text-sm font-medium text-gray-700 mb-2">Escolha uma viagem</label>
            <select id="trip-select" class="h-11 w-full rounded-lg border border-gray-300 bg-white text-gray-800 pl-3 pr-10 focus:border-pink-500 focus:ring-pink-500 transition">
            </select>
            <p id="trip-select-helper" class="mt-2 text-xs text-gray-500">Listamos viagens cujo intervalo de datas abrange o seu check-in e check-out.</p>
        </div>

        <div class="flex justify-end gap-3">
            <button id="cancel-trip-btn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition">
                Cancelar
            </button>
            <button id="add-to-trip-confirm-btn" type="button" class="px-5 py-2 text-sm font-semibold text-white bg-pink-600 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition">
                Adicionar
            </button>
        </div>
    </div>
</div>

@endsection
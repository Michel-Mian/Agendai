@extends('index')

@section('content')
<style>
    #loader { display: none !important; }
</style>
<div class="flex min-h-[30vh] bg-white-100">
    @include('components/layout.sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components/layout/header', ['title' => 'Pesquisa de Hotéis'])
        <div class="w-full max-w-5xl rounded-3xl px-4 sm:px-10 py-8 sm:py-14 mx-auto">
        <h2 class="text-2xl font-bold mb-5 text-black-700">Pesquisa de Hotéis</h2>
        <form id="hotel-search-form" class="flex flex-col gap-8 w-full">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <label for="hotel-query" class="text-lg text-black-600 font-semibold mb-2 mt-1">Destino ou Nome do Hotel</label>
                    <input type="text" id="hotel-query" class="max-w-2xl px-5 py-3 border border-black-200 rounded-xl shadow focus:outline-none focus:ring-2 focus:ring-black-300 text-lg text-black-700 placeholder-black-400 transition" placeholder="Ex: Rio de Janeiro" required>
                </div>
                <div class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="flex flex-col w-full md:w-1/3">
                        <label for="check-in-date" class="text-lg text-black-600 mb-2 font-semibold">Check-in</label>
                        <input type="date" id="check-in-date" class="bg-black-50 border border-black-200 rounded-xl px-5 py-3 text-lg text-black-700 focus:outline-none focus:ring-2 focus:ring-black-300 transition w-full" required>
                    </div>
                    <div class="flex flex-col w-full md:w-1/3">
                        <label for="check-out-date" class="text-lg text-black-600 mb-2 font-semibold">Check-out</label>
                        <input type="date" id="check-out-date" class="bg-black-50 border border-black-200 rounded-xl px-5 py-3 text-lg text-black-700 focus:outline-none focus:ring-2 focus:ring-black-300 transition w-full" required>
                    </div>
                    <button type="submit" class="cursor-pointer bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold px-6 py-3 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition flex items-center gap-2 text-lg h-14 w-full md:w-auto mt-4 md:mt-0" aria-label="Pesquisar">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Pesquisar
                    </button>
                    <div class="h-14 flex items-center w-full md:w-auto mt-4 md:mt-0">
                        @include('components.hotels.advanceFilter')
                    </div>
                </div>
            </div>
        </form>

        @include('components/hotels/filterHotels')

        <div id="results-summary" class="hidden mb-4">
            <div class="bg-black-50 border border-black-200 rounded-md p-3">
                <span id="results-count" class="text-black-800 font-medium"></span>
            </div>
        </div>

        <div id="hotel-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        </div>

        <div id="loading-indicator" class="hidden text-center mt-8">
            <div class="inline-flex items-center px-5 py-3 font-semibold leading-6 text-lg shadow rounded-xl text-black-500 bg-black-50 transition ease-in-out duration-150 border border-black-200">
                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-black-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Carregando resultados...
            </div>
        </div>

        <div id="error-message" class="hidden mt-4 bg-red-50 border border-red-300 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-lg text-red-800" id="error-text"></p>
                </div>
            </div>
        </div>

        <div id="pagination" class="flex justify-center mt-8">
            <button id="load-more" class="hidden bg-gradient-to-r from-black-600 to-black-500 text-white font-medium py-3 px-6 rounded-xl transition duration-200 flex items-center text-lg">
                <i class="fa-solid fa-plus mr-2"></i>
                Carregar Mais Resultados
            </button>
        </div>
    </div>
</div>

<div id="trip-selection-modal" class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden">
    <div class="relative bg-white rounded-lg shadow-xl p-4 w-full max-w-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Adicionar hotel a uma viagem</h3>
        <p class="text-base text-gray-600 mb-3">Selecione a viagem para a qual deseja adicionar este hotel.</p>
        
        <div class="mb-3">
            <label for="trip-select" class="block text-base font-medium text-gray-700 mb-1">Escolha uma viagem:</label>
            <select id="trip-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-black-500 focus:border-black-500 rounded-md">
            </select>
        </div>
        
        <div class="flex justify-end space-x-2">
            <button id="cancel-trip-btn" type="button" class="px-4 py-2 text-base font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                Cancelar
            </button>
            <button id="add-to-trip-confirm-btn" type="button" class="px-4 py-2 text-base font-medium text-white bg-black-600 rounded-md hover:bg-black-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black-500 transition-colors">
                Adicionar
            </button>
        </div>
    </div>
</div>

@endsection
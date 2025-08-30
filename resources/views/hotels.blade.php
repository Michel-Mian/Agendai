@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-100">
        @include('components/layout.sidebar')
        <div class="flex-1 flex flex-col p-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-900">Pesquisa de Hotéis</h2>
            
            <div class="bg-gray-100 py-8 px-4">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                    <form id="hotel-search-form" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="hotel-query" class="block text-sm font-medium text-gray-700 mb-2">
                                    Destino ou Nome do Hotel
                                </label>
                                <input type="text" 
                                    id="hotel-query" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                    placeholder="Ex: Rio de Janeiro" 
                                    required>
                            </div>
                            <div>
                                <label for="check-in-date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Check-in
                                </label>
                                <input type="date" 
                                    id="check-in-date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                    required>
                            </div>
                            <div>
                                <label for="check-out-date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Check-out
                                </label>
                                <input type="date" 
                                    id="check-out-date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-800" 
                                    required>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button type="submit" 
                                    id="search-btn"
                                    class="flex-1 bg-gray-800 hover:bg-white text-white hover:text-blue-900 border-2 hover:border-blue-900 font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                <i class="fa-solid fa-magnifying-glass mr-2"></i>
                                Pesquisar
                            </button>
                            @include('components.hotels.advanceFilter')
                        </div>
                    </form>
                </div>
            </div>

            @include('components/hotels/filterHotels')

            <div id="results-summary" class="hidden mb-4">
                <div class="bg-gray-200 border border-gray-300 rounded-md p-3">
                    <span id="results-count" class="text-gray-800 font-medium"></span>
                </div>
            </div>

            <div id="hotel-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            </div>

            <div id="loading-indicator" class="hidden text-center mt-8">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white transition ease-in-out duration-150 border border-gray-300">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Carregando resultados...
                </div>
            </div>

            <div id="error-message" class="hidden mt-4 bg-red-100 border border-red-300 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800" id="error-text"></p>
                    </div>
                </div>
            </div>

            <div id="pagination" class="flex justify-center mt-8">
                <button id="load-more" 
                        class="hidden bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-6 rounded-md transition duration-200 flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Carregar Mais Resultados
                </button>
            </div>
        </div>
    </div>


    <div id="trip-selection-modal" class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden">
    <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Adicionar hotel a uma viagem</h3>
        <p class="text-sm text-gray-600 mb-4">Selecione a viagem para a qual deseja adicionar este hotel.</p>
        
        <div class="mb-4">
            <label for="trip-select" class="block text-sm font-medium text-gray-700 mb-1">Escolha uma viagem:</label>
            <select id="trip-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                </select>
        </div>
        
        <div class="flex justify-end space-x-2">
            <button id="cancel-trip-btn" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                Cancelar
            </button>
            <button id="add-to-trip-confirm-btn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Adicionar
            </button>
        </div>
    </div>
</div>
<script>
    window.userTrips = @json($trips);
</script>
@endsection
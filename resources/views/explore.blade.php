@extends('index')
@section('content')
<div class="h-screen w-screen overflow-hidden bg-gray-100 flex">
    <!-- Sidebar -->
    @include('components/layout/sidebar')
    
    <!-- Conteúdo principal (header + resto) -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header melhorado -->
        <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Explorar Rio de Janeiro</h1>
                    <p class="text-sm text-gray-500">Descubra os melhores lugares da cidade</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="hidden sm:flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Exportar
                </button>
                <button class="flex items-center px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Salvar
                </button>
            </div>
        </header>

        <!-- Conteúdo abaixo do header -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Map Section melhorado -->
            <div class="flex-1 p-6 overflow-hidden">
                <div class="h-full w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div id="map" class="w-full h-full rounded-xl"></div>
                </div>
            </div>

            <!-- Desktop Side Panel melhorado -->
            <div class="hidden lg:flex w-96 flex-shrink-0 p-6 overflow-hidden">
                <div class="flex flex-col h-full w-full">
                    <!-- Search and Filters melhorados -->
                    <div class="mb-6 flex-shrink-0">
                        <div class="relative mb-4">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                type="text" 
                                id="searchInput"
                                placeholder="Pesquisar lugares..." 
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm transition-all duration-200"
                            />
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            @include('components.explore.filter-modal')
                        </div>
                    </div>

                    <!-- Itinerary Panel melhorado -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 flex-1 flex flex-col overflow-hidden">
                        <div class="p-6 flex-1 flex flex-col overflow-hidden">
                            <!-- Day Tabs melhorados -->
                            <div class="flex border-b mb-4 flex-shrink-0">
                                <button class="day-tab px-4 py-2 font-medium border-b-2 border-blue-500 text-blue-600" data-day="1">Dia 1</button>
                                <button class="day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-day="2">Dia 2</button>
                                <button class="day-tab px-4 py-2 font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-day="3">Dia 3</button>
                            </div>

                            <!-- Itinerary Content -->
                            <div class="flex-1 overflow-y-auto min-h-0">
                                <div id="itinerary-content" class="h-full">
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="font-medium mb-1">Nenhuma atividade ainda</span>
                                        <span class="text-sm text-center">Clique nos marcadores do mapa para adicionar atividades ao seu roteiro</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Suggestions melhoradas -->
                            <div id="suggestions" class="mt-6 pt-6 border-t border-gray-200 flex-shrink-0">
                                <div class="flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <h3 class="font-semibold text-gray-900">Sugestões para Você</h3>
                                </div>
                                <div id="suggestions-list" class="space-y-3 max-h-48 overflow-y-auto">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

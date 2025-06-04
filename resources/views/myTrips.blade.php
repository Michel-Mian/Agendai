@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')
        <div class="max-w-3xl mx-auto w-full py-8">
            <!-- Card de Viagem -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-6">
                    <div class="flex items-center mb-2">
                        <h2 class="text-2xl font-bold mr-3">Rio de Janeiro</h2>
                    </div>
                    <div class="flex items-center text-gray-500 text-sm mb-2">
                        <i class="fa-regular fa-calendar mr-2" style="color:rgb(65, 160, 131);"></i>
                        14/02/2025 - 16/02/2025
                    </div>
                    <p class="mb-4 text-gray-700">Viagem para conhecer as praias e pontos turísticos do Rio de Janeiro.</p>
                    <div class="flex space-x-4 mb-4">
                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">2 dias</span>
                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">4 lugares</span>
                    </div>
                    <div class="flex space-x-3">
                        <button class="flex items-center px-4 py-2 border rounded text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <i class="fa-solid fa-globe mr-2"></i> Ver no Mapa
                        </button>
                        <button class="flex items-center px-4 py-2 border rounded text-gray-700 hover:bg-gray-50 cursor-pointer">
                            Editar Viagem
                        </button>
                        <button class="ml-auto text-green-700 font-semibold flex items-center hover:underline ver-detalhes-btn" data-target="detalhes-1">
                            Ver Detalhes <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
                        </button>
                    </div>
                </div>
                <div id="detalhes-1" class="detalhes-viagem hidden px-6 py-6 bg-gray-50 transition-all duration-300">
    <h3 class="text-xl font-bold mb-6 text-gray-800">Itinerário Detalhado</h3>

    <div class="space-y-6">
        <div>
            <div class="flex items-center mb-4">
                <span class="text-lg font-bold text-gray-800">14 Fevereiro</span>
                <span class="ml-3 text-sm text-gray-500">Dia 1</span>
            </div>
            <div class="ml-2 border-l-2 border-gray-200 pl-6 space-y-4">
                <div class="relative bg-white rounded-lg shadow-sm p-4 flex items-center">
                    <div class="absolute top-1/2 -translate-y-1/2 w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-white text-xs"></i>
                    </div>
                    <div class="flex-1 ml-8">
                        <span class="font-medium text-gray-900">Cristo Redentor</span>
                        <span class="ml-3 bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">Atração</span>
                    </div>
                </div>
                <div class="relative bg-white rounded-lg shadow-sm p-4 flex items-center">
                    <div class="absolute top-1/2 -translate-y-1/2 w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-white text-xs"></i>
                    </div>
                    <div class="flex-1 ml-8">   
                        <span class="font-medium text-gray-900">Praia de Copacabana</span>
                        <span class="ml-3 bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">Atração</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="flex items-center mb-4">
                <span class="text-lg font-bold text-gray-800">15 Fevereiro</span>
                <span class="ml-3 text-sm text-gray-500">Dia 2</span>
            </div>
            <div class="ml-2 border-l-2 border-gray-200 pl-6 space-y-4">
                <div class="relative bg-white rounded-lg shadow-sm p-4 flex items-center">
                    <div class="absolute top-1/2 -translate-y-1/2 w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-white text-xs"></i>
                    </div>
                    <div class="flex-1 ml-8">
                        <span class="font-medium text-gray-900">Pão de Açúcar</span>
                        <span class="ml-3 bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">Atração</span>
                    </div>
                </div>
                <div class="relative bg-white rounded-lg shadow-sm p-4 flex items-center">
                    <div class="absolute top-1/2 -translate-y-1/2 w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-utensils text-white text-xs"></i>
                    </div>
                    <div class="flex-1 ml-8">
                        <span class="font-medium text-gray-900">Restaurante Fogo de Chão</span>
                        <span class="ml-3 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded-full">Restaurante</span>
                    </div>
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
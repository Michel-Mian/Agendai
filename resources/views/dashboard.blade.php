@extends('index')

@section('content')
<div class="flex min-h-screen">
    @include('components.sidebar')
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Profile Section -->
            <div class="text-center mb-12">
                <div class="relative inline-block">
                    <img 
                        src="" 
                        alt="Foto do perfil de João" 
                        class="w-32 h-32 rounded-full object-cover mx-auto shadow-lg border-4 border-white"
                    >
                </div>
                <h1 class="text-3xl font-semibold text-green-600 mt-6">
                    Olá, João.
                </h1>
            </div>

            <!-- Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Visualizar Viagens -->
                <div class="bg-emerald-300 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="mb-6">
                        <!-- Calendar Icon -->
                        <svg class="w-16 h-16 mx-auto text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-8">
                        Visualizar minhas viagens
                    </h3>
                    <a href="" 
                       class="inline-flex items-center bg-white text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-50 transition-colors duration-200">
                        Saiba mais
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 2: Adicionar Roteiro -->
                <div class="bg-emerald-400 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="mb-6">
                        <!-- Globe Icon -->
                        <svg class="w-16 h-16 mx-auto text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-8">
                        Adicionar novo roteiro
                    </h3>
                    <a href="" 
                       class="inline-flex items-center bg-white text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-50 transition-colors duration-200">
                        Saiba mais
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 3: Buscar Voos -->
                <div class="bg-emerald-300 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="mb-6">
                        <!-- Airplane Icon -->
                        <svg class="w-16 h-16 mx-auto text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-8">
                        Buscar melhores voos
                    </h3>
                    <a href="" 
                       class="inline-flex items-center bg-white text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-50 transition-colors duration-200">
                        Saiba mais
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
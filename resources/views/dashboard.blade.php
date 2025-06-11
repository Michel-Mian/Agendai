@extends('index')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')
        <main class="flex-1 p-8">
            <!-- Cards superiores -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">
                <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
                    <div>
                        <div class="text-gray-500 text-sm">Viagens Planejadas</div>
                        <div class="text-3xl font-bold mt-6">12 até agora!</div>
                    </div>
                    <div class="self-end mt-4">
                        <span class="bg-green-100 p-2 rounded-lg">
                            <i class="fa-solid fa-calendar" style="color:rgb(85, 201, 166);"></i>                        
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
                    <div>
                        <div class="text-gray-500 text-sm">Sua moeda preferida</div>
                        <div class="text-3xl font-bold mt-2">Real</div>
                        <div class="text-base mt-1 ">R$4,59 em relação ao dólar americano</div>
                    </div>
                    <div class="self-end mt-4">
                        <span class="bg-blue-100 p-2 rounded-lg">
                            <i class="fa-solid fa-file-invoice-dollar" style="color: #74C0FC;"></i>                       
                        </span>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
                    <div>
                        <div class="text-gray-500 text-sm">Próxima Viagem</div>
                        <div class="text-xl font-bold mt-2">Rio de Janeiro</div>
                        <div class="text-purple-600 text-xs mt-1">Em 15 dias</div>
                    </div>
                    <div class="self-end mt-4">
                        <i class="fa-solid fa-umbrella-beach" style="color: #74C0FC;"></i>
                    </div>
                </div>
            </div>
            <!-- Ações rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="">
                    <div class="bg-white rounded-xl shadow p-6 flex items-center cursor-pointer transition duration-200 hover:shadow-lg">
                        <span class="bg-green-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Criar Novo Roteiro</div>
                            <div class="text-gray-500 text-sm">Planeje sua próxima viagem</div>
                        </div>
                        <span class="ml-auto">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
                <a href="/flights">
                    <div class="bg-white rounded-xl shadow p-6 flex items-center cursor-pointer transition duration-200 hover:shadow-lg">
                        <span class="bg-blue-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6M3 10a7 7 0 1114 0 7 7 0 01-14 0z"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Buscar Voos</div>
                            <div class="text-gray-500 text-sm">Encontre as melhores ofertas</div>
                        </div>
                        <span class="ml-auto">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
                <a href="">
                    <div class="bg-white rounded-xl shadow p-6 flex items-center cursor-pointer transition duration-200 hover:shadow-lg">
                        <span class="bg-purple-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Explorar Destinos</div>
                            <div class="text-gray-500 text-sm">Descubra novos lugares</div>
                        </div>
                        <span class="ml-auto">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
            </div>
            <!-- Suas viagens -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Suas Viagens</h2>
                    <button class="bg-white border rounded-lg px-5 py-2 text-sm font-medium flex items-center gap-2 hover:bg-gray-100 cursor-pointer">
                        <a href="/myTrips">
                            <span class="mr-1">Ver Todas</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow flex">
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-lg">Rio de Janeiro</div>
                                <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">Próxima</span>
                            </div>
                            <div class="text-gray-500 text-sm mt-2">14/02/2024 • 5 dias</div>
                            <div class="flex items-center text-gray-500 text-sm mt-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
                                2 pessoas
                            </div>
                            <a href="/myTrips" class="text-blue-600 font-medium mt-4 inline-flex items-center">Ver Detalhes <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow flex opacity-80">
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-lg">Salvador</div>
                                <span class="bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full">Concluída</span>
                            </div>
                            <div class="text-gray-500 text-sm mt-2">09/01/2024 • 7 dias</div>
                            <div class="flex items-center text-gray-500 text-sm mt-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
                                2 pessoas
                            </div>
                            <a href="/myTrips" class="text-blue-600 font-medium mt-4 inline-flex items-center">Ver Detalhes <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
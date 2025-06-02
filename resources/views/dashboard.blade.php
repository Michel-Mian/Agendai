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
                            <i class="fa-solid fa-calendar" style="color: #63E6BE;"></i>                        
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
                            <i class="fa-solid fa-file-invoice-dollar" style="color: #63E6BE;"></i>                        
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
                        <span class="bg-purple-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.364-7.364l-1.414 1.414M6.05 17.95l-1.414 1.414m12.728 0l-1.414-1.414M6.05 6.05L4.636 4.636"/></svg>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Ações rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6 flex items-center">
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
                <div class="bg-white rounded-xl shadow p-6 flex items-center">
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
                <div class="bg-white rounded-xl shadow p-6 flex items-center">
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
            </div>
            <!-- Suas viagens -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Suas Viagens</h2>
                    <button class="bg-white border rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 hover:bg-gray-100">
                        Ver Todas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Rio de Janeiro -->
                    <div class="bg-white rounded-xl shadow flex">
                        <div class="w-1/3 flex items-center justify-center bg-gray-100 rounded-l-xl">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        </div>
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
                            <a href="#" class="text-green-600 font-medium mt-4 inline-flex items-center">Ver Detalhes <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
                        </div>
                    </div>
                    <!-- Card Salvador -->
                    <div class="bg-white rounded-xl shadow flex opacity-80">
                        <div class="w-1/3 flex items-center justify-center bg-gray-100 rounded-l-xl">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        </div>
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
                            <a href="#" class="text-green-600 font-medium mt-4 inline-flex items-center">Ver Detalhes <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
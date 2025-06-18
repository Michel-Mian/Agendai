@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
            <div class="mx-auto w-full max-w-2xl mt-4 mb-8">
                <!-- Filtro de Pesquisa -->
                <div class="flex justify-center">
                    <form action="" method="GET" class="flex w-full max-w-xl bg-white shadow rounded-2xl px-2 py-2 items-center gap-1">
                        <input type="text" name="search" placeholder="Pesquisar destino ou companhia..." class="flex-1 bg-transparent border-0 focus:ring-0 px-2 py-2 text-sm text-gray-700 placeholder-gray-400 focus:outline-none">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-700 hover:to-blue-600 transition cursor-pointer transition text-white w-10 h-10 flex items-center justify-center rounded-xl shadow-none"aria-label="Pesquisar">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>
        <!-- Lista de Voos -->
        <div class="max-w-4xl mx-auto mb-0 w-full py-8">
            <!-- Card de Voo Melhorado -->
            <div class="bg-white rounded-lg shadow-md px-8 py-6">
                <div class="flex items-center">
                    <!-- Companhia -->
                    <div class="flex items-center w-48">
                        <i class="fa-solid fa-plane-departure text-2xl mr-3"></i>
                        <div>
                            <span class="block text-base font-semibold text-gray-800">LATAM</span>
                            <span class="block text-xs text-gray-500">Econômica</span>
                        </div>
                    </div>
                    <!-- Horário de saída -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">08:30</span>
                        <span class="text-xs text-gray-500">GRU</span>
                        <span class="text-xs text-gray-400">São Paulo</span>
                    </div>
                    <!-- Duração e Direto -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-sm text-gray-500">2h 15m</span>
                        <i class="fa-solid fa-plane text-gray-400 my-1"></i>
                        <span class="text-xs text-gray-500">Direto</span>
                    </div>
                    <!-- Horário de chegada -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">10:45</span>
                        <span class="text-xs text-gray-500">SDU</span>
                        <span class="text-xs text-gray-400">Rio de Janeiro</span>
                    </div>
                    <!-- Preço e Seleção -->
                    <div class="flex flex-col items-end flex-1">
                        <span class="text-blue-600 text-2xl font-bold">R$ 450</span>
                        <span class="text-xs text-gray-500">por pessoa</span>
                        <button class="border border-blue-600 text-blue-600 px-4 py-1 rounded hover:bg-green-50 text-sm font-semibold my-2 cursor-pointer">Mais Detalhes</button>
                    </div>
                </div>
                <!-- Benefícios centralizados -->
                <div class="flex justify-center space-x-2 mt-6 border-t pt-4 border-gray-200">
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-wifi mr-1"></i> Wifi
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-tv mr-1"></i> Entretenimento
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-cookie-bite mr-1"></i> Lanche
                    </span>
                    <span class=" flex items-right text-xs px-2 py-1 text-gray-400">23kg incluído</span>
                </div>
            </div>
            <div class="max-w-4xl mx-auto mb-0 w-full py-8">
            <!-- Card de Voo Melhorado -->
            <div class="bg-white rounded-lg shadow-md px-8 py-6">
                <div class="flex items-center">
                    <!-- Companhia -->
                    <div class="flex items-center w-48">
                        <i class="fa-solid fa-plane-departure text-2xl mr-3"></i>
                        <div>
                            <span class="block text-base font-semibold text-gray-800">GOL</span>
                            <span class="block text-xs text-gray-500">Primeira Classe</span>
                        </div>
                    </div>
                    <!-- Horário de saída -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">13:30</span>
                        <span class="text-xs text-gray-500">GRU</span>
                        <span class="text-xs text-gray-400">São Paulo</span>
                    </div>
                    <!-- Duração e Direto -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-sm text-gray-500">3h 15m</span>
                        <i class="fa-solid fa-plane text-gray-400 my-1"></i>
                        <span class="text-xs text-gray-500">Direto</span>
                    </div>
                    <!-- Horário de chegada -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">16:45</span>
                        <span class="text-xs text-gray-500">SDU</span>
                        <span class="text-xs text-gray-400">Pernambuco</span>
                    </div>
                    <!-- Preço e Seleção -->
                    <div class="flex flex-col items-end flex-1">
                        <span class="text-blue-600 text-2xl font-bold">R$ 950</span>
                        <span class="text-xs text-gray-500">por pessoa</span>
                        <button class="border border-blue-600 text-blue-600 px-4 py-1 rounded hover:bg-green-50 text-sm font-semibold my-2 cursor-pointer">Mais Detalhes</button>
                    </div>
                </div>
                <!-- Benefícios centralizados -->
                <div class="flex justify-center space-x-2 mt-6 border-t pt-4 border-gray-200">
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-wifi mr-1"></i> Wifi
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-tv mr-1"></i> Entretenimento
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-cookie-bite mr-1"></i> Lanche
                    </span>
                    <span class=" flex items-right text-xs px-2 py-1 text-gray-400">30kg incluído</span>
                </div>
            </div>
            <div class="max-w-4xl mx-auto mb-0 w-full py-8">
            <!-- Card de Voo Melhorado -->
            <div class="bg-white rounded-lg shadow-md px-8 py-6">
                <div class="flex items-center">
                    <!-- Companhia -->
                    <div class="flex items-center w-48">
                        <i class="fa-solid fa-plane-departure text-2xl mr-3"></i>
                        <div>
                            <span class="block text-base font-semibold text-gray-800">Azuk</span>
                            <span class="block text-xs text-gray-500">Executiva</span>
                        </div>
                    </div>
                    <!-- Horário de saída -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">08:40</span>
                        <span class="text-xs text-gray-500">GRU</span>
                        <span class="text-xs text-gray-400">Campinas</span>
                    </div>
                    <!-- Duração e Direto -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-sm text-gray-500">2h 20m</span>
                        <i class="fa-solid fa-plane text-gray-400 my-1"></i>
                        <span class="text-xs text-gray-500">Direto</span>
                    </div>
                    <!-- Horário de chegada -->
                    <div class="flex flex-col items-center w-32">
                        <span class="text-2xl font-bold text-black">11:00</span>
                        <span class="text-xs text-gray-500">SDU</span>
                        <span class="text-xs text-gray-400">Maceio</span>
                    </div>
                    <!-- Preço e Seleção -->
                    <div class="flex flex-col items-end flex-1">
                        <span class="text-blue-600 text-2xl font-bold">R$ 947</span>
                        <span class="text-xs text-gray-500">por pessoa</span>
                        <button class="border border-blue-600 text-blue-600 px-4 py-1 rounded hover:bg-green-50 text-sm font-semibold my-2 cursor-pointer">Mais Detalhes</button>
                    </div>
                </div>
                <!-- Benefícios centralizados -->
                <div class="flex justify-center space-x-2 mt-6 border-t pt-4 border-gray-200">
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-wifi mr-1"></i> Wifi
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <i class="fa-solid fa-cookie-bite mr-1"></i> Lanche
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
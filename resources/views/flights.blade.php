@extends('index')

@section('content')
<<<<<<< HEAD
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
            @include('components/layout/header')

            <!-- Filtro de Pesquisa -->
            @include('components/flights/searchFilter')
            
            <!-- Modal de Filtro -->
            @include('components/flights/modalFlights')

            <!-- Lista de Voos -->
            <div class="max-w-4xl mx-auto mb-0 w-full py-8" id="flights-container">
                @if(isset($flights) && count($flights))
                    @foreach($flights as $index => $flight)
                        @include('components.flights.cardFlights', ['flight' => $flight, 'index' => $index, 'user' => $user])
                         <pre>{{ print_r($flight, true) }}</pre> 
                        <!-- Modal de Seleção de Viagem -->
                        @include('components/flights/modalSelectTrip', ['flight' => $flight, 'index' => $index, 'user' => $user])
                        <!-- Modal de Confirmação -->
                        @include('components/flights/modalConfirmation', ['flight' => $flight, 'index' => $index, 'user' => $user])
                    @endforeach

                    <div class="mt-6">
                        {{ $flights->links() }}
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        Nenhum voo encontrado para os filtros selecionados.
                    </div>
                @endif
=======
    <div class="flex min-h-screen bg-white">
        @include('components/layout/sidebar')
        
        <div id="main-content" class="flex-1 flex flex-col px-0">
            @include('components/layout/header')
            
            <div class="w-full max-w-7xl mx-auto mt-8 px-4 pb-8">
                <!-- Título -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-plane text-blue-600 mr-3"></i>
                        Pesquisa de Voos
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Encontre as melhores opções de voos para sua viagem
                    </p>
                </div>

                <!-- Filtro de Pesquisa -->
                @include('components/flights/searchFilter')
                
                <!-- Modal de Filtro -->
                @include('components/flights/modalFlights')

                <!-- Lista de Voos -->
                <div class="mb-0 w-full" id="flights-container">
                <!-- Lista de Voos -->
                <div class="mb-0 w-full" id="flights-container">
                    @if(isset($flights) && count($flights))
                        @foreach($flights as $index => $flight)
                            @include('components.flights.cardFlights', ['flight' => $flight, 'index' => $index, 'user' => $user])
                            {{-- <pre>{{ print_r($flight, true) }}</pre> --}}
                            <!-- Modal de Seleção de Viagem -->
                            @include('components/flights/modalSelectTrip', ['flight' => $flight, 'index' => $index, 'user' => $user])
                            <!-- Modal de Confirmação -->
                            @include('components/flights/modalConfirmation', ['flight' => $flight, 'index' => $index, 'user' => $user])
                        @endforeach

                        <div class="mt-6">
                            {{ $flights->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-lg shadow-md">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg text-gray-500">Nenhum voo encontrado para os filtros selecionados.</p>
                            <p class="text-sm text-gray-400 mt-2">Tente ajustar seus critérios de busca</p>
                        </div>
                    @endif
                </div>
>>>>>>> d643e774296f46c453f341bc72b8ad752d734306
            </div>
        </div>
    </div>
@endsection

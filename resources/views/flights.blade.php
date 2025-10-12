@extends('index')

@section('content')
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
            </div>
        </div>
    </div>
@endsection

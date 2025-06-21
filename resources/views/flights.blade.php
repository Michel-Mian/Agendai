@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')

            <!-- Filtro de Pesquisa -->
            @include('components/flights/searchFilter')

            <!-- Lista de Voos -->
            <div class="max-w-4xl mx-auto mb-0 w-full py-8">
                @if(isset($flights) && count($flights))
                    @foreach($flights as $flight)
                        @include('components.flights.cardFlights', ['flight' => $flight])
                        <!-- <pre>{{ print_r($flight, true) }}</pre> -->
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
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
                @include('components/flights/cardFlights')
                @include('components/flights/cardFlights')
                @include('components/flights/cardFlights')
            </div>
        </div>
    </div>
@endsection
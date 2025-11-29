@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div id="main-content" class="flex-1 flex flex-col px-0">
            @include('components/layout/header')
            <main class="flex-1 p-8">
                <!-- Cards superiores -->
                @include('components/dashboard/superiorCards', ['viagens' => $viagens])

                <!-- Ações rápidas -->
                @include('components/dashboard/actionFast')
                <!-- Suas viagens -->
                @include('components/dashboard/yourTrips', ['viagens' => $viagens])
                <!-- Modal -->
                @include('components/dashboard/modalCurrency')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection 
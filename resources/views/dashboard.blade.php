@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
            <main class="flex-1 p-8">
                <!-- Cards superiores -->
                @include('components/dashboard/superiorCards')

                <!-- Ações rápidas -->
                @include('components/dashboard/actionFast')
                <!-- Suas viagens -->
                @include('components/dashboard/yourTrips')
                <!-- Modal -->
                @include('components/dashboard/modalCurrency')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection
@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
<div class="container mt-5">
    <form action="{{ route('trip.form.finish') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Revisão</h2>
            <p class="mb-3">Resumo da sua viagem:</p>

            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Destino:</strong> {{ $trip['destination'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Quantidade de adultos:</strong> {{ $trip['adults'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Quantidade de crianças:</strong> {{ $trip['children'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Data de ida:</strong> {{ $trip['departure_date'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Data de volta:</strong> {{ $trip['return_date'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Meio de locomoção:</strong> {{ $trip['transportation'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Forma de estadia:</strong> {{ $trip['accommodation'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Preferência:</strong> {{ $trip['preference'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Orçamento total:</strong> {{ $trip['budget'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Seguro viagem:</strong> {{ $trip['insurance_option'] ?? '-' }}</li>
                <li class="list-group-item"><strong>Passagem aérea:</strong> {{ $trip['flight_option'] ?? '-' }}</li>
            </ul>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('trip.form.step5.view') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-success">Explorar</button>
            </div>
        </div>
    </form>
</div>
@endsection
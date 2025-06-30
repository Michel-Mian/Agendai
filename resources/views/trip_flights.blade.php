@extends('index')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
<div class="container mt-5">
    <form action="{{ route('trip.form.step6') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Voos</h2>
            <p class="mb-3">Escolha sua passagem aérea:</p>

            <div class="row g-3">
                @php
                    $flights = [
                        'econ_123' => ['title' => 'Econômico - Voo 123', 'price' => 'R$800'],
                        'econ_456' => ['title' => 'Econômico - Voo 456', 'price' => 'R$950'],
                        'exec_789' => ['title' => 'Executivo - Voo 789', 'price' => 'R$1.600'],
                        'first_999' => ['title' => 'Primeira Classe - Voo 999', 'price' => 'R$3.000'],
                    ];
                @endphp

                @foreach($flights as $key => $info)
                <div class="col-md-3">
                    <label class="w-100">
                        <input type="radio" name="flight_option" value="{{ $key }}" class="btn-check" required>
                        <div class="card text-center border border-info btn btn-outline-info">
                            <img src="/images/flights/{{ $key }}.jpg" class="card-img-top" alt="{{ $info['title'] }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $info['title'] }}</h5>
                                <p class="card-text">{{ $info['price'] }}</p>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('trip.form.step4.view') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-primary">Próxima Página</button>
            </div>
        </div>
    </form>
</div>
@endsection
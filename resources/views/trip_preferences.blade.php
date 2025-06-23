@extends('index')

@section('content')
<div class="container mt-5">
    <form action="{{ route('trip.form.step4') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Preferências</h2>
            <p class="mb-3">Selecione o foco principal da sua viagem:</p>

            <div class="row g-3">
                @php
                    $options = [
                        'adventure' => 'Aventura',
                        'relax' => 'Relaxamento',
                        'culture' => 'Cultura',
                        'romantic' => 'Romântica',
                        'gastronomy' => 'Gastronomia',
                        'shopping' => 'Compras',
                        'nature' => 'Natureza',
                        'sports' => 'Esportes',
                    ];
                @endphp

                @foreach($options as $value => $label)
                <div class="col-md-3">
                    <label class="w-100">
                        <input type="radio" name="preference" value="{{ $value }}" class="btn-check" required>
                        <div class="card text-center border border-primary btn btn-outline-primary">
                            <img src="/images/preferences/{{ $value }}.jpg" class="card-img-top" alt="{{ $label }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $label }}</h5>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('trip.form.step2.view') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-primary">Próxima Página</button>
            </div>
        </div>
    </form>
</div>
@endsection
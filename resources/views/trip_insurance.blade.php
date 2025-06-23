@extends('index')

@section('content')
<div class="container mt-5">
    <form action="{{ route('trip.form.step5') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Seguro Viagem</h2>

            <div class="mb-3">
                <label for="budget" class="form-label">Qual seu orçamento total?</label>
                <select class="form-select" id="budget" name="budget" required>
                    <option disabled selected>Selecione</option>
                    <option value="500">Até R$500</option>
                    <option value="1000">Até R$1.000</option>
                    <option value="2000">Até R$2.000</option>
                    <option value="5000">Até R$5.000</option>
                    <option value="mais">Mais de R$5.000</option>
                </select>
            </div>

            <p class="mb-3">Escolha seu seguro viagem:</p>
            <div class="row g-3">
                @php
                    $insurances = [
                        'basic' => ['title' => 'Básico', 'price' => 'R$50'],
                        'standard' => ['title' => 'Intermediário', 'price' => 'R$100'],
                        'premium' => ['title' => 'Premium', 'price' => 'R$200'],
                        'vip' => ['title' => 'VIP', 'price' => 'R$500'],
                    ];
                @endphp

                @foreach($insurances as $key => $info)
                <div class="col-md-3">
                    <label class="w-100">
                        <input type="radio" name="insurance_option" value="{{ $key }}" class="btn-check" required>
                        <div class="card text-center border border-success btn btn-outline-success">
                            <img src="/images/insurance/{{ $key }}.jpg" class="card-img-top" alt="{{ $info['title'] }}">
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
                <a href="{{ route('trip.form.step3.view') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-primary">Próxima Página</button>
            </div>
        </div>
    </form>
</div>
@endsection
@extends('index')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Revise suas informações</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Destino:</strong> {{ $step1['destino'] }}</li>
        <li class="list-group-item"><strong>Data de ida:</strong> {{ $step1['data_ida'] }}</li>
        <li class="list-group-item"><strong>Data de volta:</strong> {{ $step1['data_volta'] }}</li>
        <li class="list-group-item"><strong>Quantidade de passageiros:</strong> {{ $step1['qtd_passageiros'] }}</li>
        <li class="list-group-item"><strong>Motivo da viagem:</strong> {{ $step1['motivo'] }}</li>
        <li class="list-group-item"><strong>Cancelamento:</strong> {{ $step1['cancelamento'] ?? 'Não' }}</li>
    </ul>

    <form action="{{ route('trip.form.step4.view') }}" method="GET">
        <button type="submit" class="btn btn-primary">Confirmar e Continuar</button>
        <a href="{{ route('trip.form.step1') }}" class="btn btn-secondary ms-2">Voltar e Editar</a>
    </form>
</div>
@endsection

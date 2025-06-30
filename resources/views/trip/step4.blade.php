@extends('index')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">Etapa Final: Buscar Seguro de Viagem</h2>
        <p class="text-muted">Clique no botão abaixo para pesquisar os seguros de viagem com base nas informações fornecidas.</p>
    </div>

    <div class="d-flex justify-content-center">
        <form method="POST" action="{{ route('trip.scrape') }}">
            @csrf
            <button type="submit" class="btn btn-lg btn-success px-5 shadow">
                <i class="bi bi-search-heart"></i> Buscar Seguro Viagem
            </button>
        </form>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('trip.form.step1') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Voltar ao Início
        </a>
    </div>
</div>
@endsection

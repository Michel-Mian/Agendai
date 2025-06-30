@extends('index')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center text-primary fw-bold">Pacotes de Seguro Encontrados</h2>

    @if (!empty($seguros) && count($seguros) > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($seguros as $seguro)
                @php
                    $linhas = explode("\n", trim($seguro));
                    $nome = $linhas[0] ?? 'Nome desconhecido';
                    $preco = $linhas[1] ?? '';
                    $beneficios = $linhas[2] ?? '';
                @endphp

                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-success fw-bold">
                                <i class="bi bi-shield-check"></i> {{ $nome }}
                            </h5>

                            @if ($preco)
                                <p class="card-text">
                                    <i class="bi bi-cash-coin"></i> <strong>Preço:</strong> {{ $preco }}
                                </p>
                            @endif

                            @if ($beneficios)
                                <p class="card-text">
                                    <i class="bi bi-list-check"></i> <strong>Benefícios:</strong> {{ $beneficios }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center mt-5">
            <i class="bi bi-exclamation-circle-fill"></i>
            Nenhum pacote de seguro foi encontrado. Tente novamente mais tarde.
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('trip.form.step1') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left-circle"></i> Voltar ao início
        </a>
    </div>
</div>
@endsection

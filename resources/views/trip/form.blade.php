@extends('index')

@section('content')
<div class="container py-5">
    <h2>Formulário de Raspagem</h2>

    <form action="{{ route('scraping.executar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="motivo">Motivo:</label>
            <select name="motivo" id="motivo" class="form-control">
                <option value="1">Turismo</option>
                <option value="2">Negócios</option>
                <option value="3">Estudos</option>
                <option value="4">Outro</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="destino">Destino:</label>
            <select name="destino" id="destino" class="form-control">
                <option value="1">Europa</option>
                <option value="2">América do Sul</option>
                <!-- Adicione os demais destinos conforme seu script Python -->
            </select>
        </div>

        <div class="mb-3">
            <label for="data_ida">Data de Ida:</label>
            <input type="date" name="data_ida" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data_volta">Data de Volta:</label>
            <input type="date" name="data_volta" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="qtd_passageiros">Quantidade de Passageiros:</label>
            <input type="number" name="qtd_passageiros" min="1" max="3" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Idades:</label>
            <input type="number" name="idade1" placeholder="Passageiro 1" class="form-control mb-2">
            <input type="number" name="idade2" placeholder="Passageiro 2" class="form-control mb-2">
            <input type="number" name="idade3" placeholder="Passageiro 3" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Executar Scraping</button>
    </form>

    @if (!empty($frases))
        <hr>
        <h3 class="mt-4">Resultados:</h3>
        <ul>
            @foreach ($frases as $frase)
                <li>{{ $frase }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection

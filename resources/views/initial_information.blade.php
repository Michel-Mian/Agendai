<form action="{{ route('trip.form.step2') }}" method="POST">
@extends('index')

@section('content')
<div class="container mt-5">
    <form action="{{ route('trip.form.step2') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Informações Iniciais</h2>

            {{-- Destino --}}
            <div class="mb-3">
                <label for="destino" class="form-label">Qual seu destino?</label>
                <input type="text" class="form-control" id="destino" name="destino" required placeholder="Ex: Paris, França">
            </div>

            {{-- Número de Pessoas --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="adultos" class="form-label">Nº de adultos:</label>
                    <select class="form-select" id="adultos" name="adultos" required>
                        <option selected disabled>Selecione</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="criancas" class="form-label">Nº de crianças:</label>
                    <select class="form-select" id="criancas" name="criancas" required>
                        <option selected disabled>Selecione</option>
                        @for($i = 0; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Datas --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="data_ida" class="form-label">Data de ida:</label>
                    <input type="date" class="form-control" id="data_ida" name="data_ida" required>
                </div>

                <div class="col-md-6">
                    <label for="data_volta" class="form-label">Data de volta:</label>
                    <input type="date" class="form-control" id="data_volta" name="data_volta" required>
                </div>
            </div>

            {{-- Botões --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-primary">Próxima Página</button>
            </div>
        </div>
    </form>
</div>
@endsection

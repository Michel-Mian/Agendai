@extends('index')

@section('content')
<div class="container mt-5">
    <form action="{{ route('trip.form.step3') }}" method="POST">
        @csrf
        <div class="card shadow-lg p-4">
            <h2 class="mb-4">Detalhes da Viagem</h2>

            <div class="mb-3">
                <label for="transportation" class="form-label">Qual será o meio de locomoção?</label>
                <select class="form-select" id="transportation" name="transportation" required>
                    <option disabled selected>Selecione</option>
                    <option value="carro">Carro</option>
                    <option value="onibus">Ônibus</option>
                    <option value="aviao">Avião</option>
                    <option value="trem">Trem</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="accommodation" class="form-label">Qual será a forma de estadia?</label>
                <select class="form-select" id="accommodation" name="accommodation" required>
                    <option disabled selected>Selecione</option>
                    <option value="hotel">Hotel</option>
                    <option value="hostel">Hostel</option>
                    <option value="airbnb">Airbnb</option>
                    <option value="pousada">Pousada</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('trip.form.step1') }}" class="btn btn-secondary">Voltar</a>
                <button type="submit" class="btn btn-primary">Próxima Página</button>
            </div>
        </div>
    </form>
</div>
@endsection

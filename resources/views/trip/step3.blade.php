@extends('index')

@section('content')
<h1>Preferências da Viagem - Passo 3</h1>

{{-- Você pode mostrar dados anteriores se quiser --}}
<p><strong>Destino:</strong> {{ $tripData['step1']['destino'] ?? '' }}</p>

<form method="POST" action="{{ route('trip.form.step4') }}">
    @csrf

    <label for="preferencias">Suas preferências ou observações:</label><br>
    <textarea name="preferencias" id="preferencias" rows="5" cols="50">{{ old('preferencias') }}</textarea><br>

    @error('preferencias')
        <small style="color:red;">{{ $message }}</small><br>
    @enderror

    <button type="submit">Próximo</button>
</form>
@endsection

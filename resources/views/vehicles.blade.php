@extends('index')

@section('content')
<!-- CSS do Autocomplete -->
<link rel="stylesheet" href="{{ asset('css/places-autocomplete.css') }}">

<div class="flex min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-white">
    @include('components.layout.sidebar')
    
    <div id="main-content" class="flex-1 flex flex-col px-0">
        @include('components.layout.header')
        
        <div class="w-full max-w-7xl mx-auto mt-8 px-4 pb-8">
            <!-- Título -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-car text-blue-600 mr-3"></i>
                    Aluguel de Veículos
                </h1>
                <p class="text-gray-600 mt-2">
                    Encontre o veículo ideal para sua viagem
                </p>
            </div>
            
            <!-- Formulário de Busca -->
            @include('vehicles.form')
            
            <!-- Alerta de Localização Alternativa -->
            <div id="vehicle-location-alert" class="hidden mb-6">
                @include('vehicles.alert')
            </div>
            
            <!-- Loading -->
            <div id="vehicle-loading" class="hidden">
                @include('vehicles.loading')
            </div>
            
            <!-- Erro -->
            <div id="vehicle-error" class="hidden">
                @include('vehicles.error')
            </div>
            
            <!-- Resultados -->
            <div id="vehicle-results" class="hidden">
                @include('vehicles.results')
            </div>
        </div>
    </div>
</div>

<!-- Modal de Seleção de Viagem -->
@include('vehicles.modal-select-trip')

<script>
    window.APP_ROUTES = {
        searchVehicles: "{{ route('vehicles.search.ajax') }}",
        saveVehicle: "{{ route('vehicles.save') }}",
        getUserTrips: "{{ route('vehicles.user.trips') }}"
    };
    
    window.VIAGEM_DATA = @json($viagem);
</script>

<!-- Scripts do Autocomplete -->
<script src="{{ asset('js/placesAutocomplete.js') }}"></script>
<script src="{{ asset('js/vehicles-autocomplete.js') }}"></script>
<script src="{{ asset('js/vehicles-search.js') }}"></script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places&callback=initVehiclesMap" async defer></script>
@endsection
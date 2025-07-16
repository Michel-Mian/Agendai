@extends('index')
@section('content')
    <div class="flex min-h-screen bg-gray-50">
        @include('components/layout/sidebar')
        <div class="flex-1 flex flex-col">
            @include('components/layout/header')
            <div class="w-full px-4 py-10 md:py-16">
                <!-- Detalhes Gerais da Viagem (sem card) -->
                <div class="mb-8 mx-5 flex flex-col gap-2">
                    <div class="flex items-center mb-2">
                        <a href="{{ route('myTrips') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors text-sm font-medium shadow-sm mr-4">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Voltar
                        </a>
                        <h1 class="text-4xl font-bold text-gray-900">{{ $viagem->destino_viagem }}</h1>
                    </div>
                    <div>
                        <div class="text-gray-700 mb-1">Criada por: <span class="font-semibold">{{ $usuario->name }}</span></div>
                        <div class="text-gray-700 mb-1">Origem: {{ $viagem->origem_viagem }}</div>
                        <div class="text-gray-700 mb-1">Período: {{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') }}</div>
                        <div class="text-gray-700 mb-1">Orçamento: R$ {{ number_format($viagem->orcamento_viagem, 2, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Objetivos e Viajantes lado a lado -->
                <div class="flex flex-col md:flex-row gap-6 mb-8 w-full">
                    <!-- Objetivos -->
                    <div class="flex-1 bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold">Objetivos</h2>
                            <div class="flex items-center">
                                <button type="button" id="open-add-objetivo-modal-btn" class="ml-2 flex items-center px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors text-sm font-medium shadow-sm" title="Adicionar objetivo">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @if($objetivos->count())
                            <ul class="list-disc list-inside space-y-1">
                                @php
                                    $objetivosExibidos = ($objetivos->count() > 5) ? $objetivos->take(3) : $objetivos;
                                @endphp
                                @foreach($objetivosExibidos as $objetivo)
                                    <li class="text-gray-700 duration-150 hover:bg-gray-100 rounded px-2 py-1 flex items-center justify-between group">
                                        <span>{{ $objetivo->nome }}</span>
                                        <form action="{{ route('objetivos.destroy', ['id' => $objetivo->pk_id_objetivo]) }}" method="POST" class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 bg-red-100 hover:bg-red-200 rounded p-1.5 text-xs font-semibold flex items-center" title="Remover objetivo">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                            @if($objetivos->count() > 5)
                                <div class="mt-4">
                                    <button id="open-objetivos-modal-btn" class="w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                        Ver mais
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="text-gray-400">Nenhum objetivo cadastrado.</div>
                        @endif
                    </div>
                    <!-- Viajantes -->
                    <div class="flex-1 bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold">Viajantes</h2>
                            <button type="button" id="open-add-viajante-modal-btn" class="ml-2 flex items-center px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors text-sm font-medium shadow-sm" title="Adicionar viajante">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        @if($viajantes->count())
                            <ul class="space-y-1">
                                @php
                                    $viajantesExibidos = ($viajantes->count() > 5) ? $viajantes->take(3) : $viajantes;
                                @endphp
                                @foreach($viajantesExibidos as $viajante)
                                    <li class="group flex items-center justify-between transition-colors duration-150 hover:bg-gray-100 rounded px-2 py-1">
                                        <div>
                                            <span class="font-semibold">{{ $viajante->nome }}</span>
                                            <span class="text-gray-500">- {{ $viajante->idade }} anos</span>
                                        </div>
                                        <form action="{{ route('viajantes.destroy', ['id' => $viajante->pk_id_viajante]) }}" method="POST" class="ml-2 hidden group-hover:inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 bg-red-100 hover:bg-red-200 rounded px-2 py-1 text-xs font-semibold transition-colors duration-150 flex items-center" title="Remover viajante">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                            @if($viajantes->count() > 5)
                                <div class="mt-4">
                                    <button id="open-viajantes-modal-btn" class="w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                        Ver mais
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="text-gray-400">Nenhum viajante cadastrado.</div>
                        @endif
                    </div>
                </div>

                <!-- Voos -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4">Voos</h2>
                    @if($voos->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1">Aeronave</th>
                                        <th class="px-2 py-1">Data/Hora</th>
                                        <th class="px-2 py-1">Origem</th>
                                        <th class="px-2 py-1">Destino</th>
                                        <th class="px-2 py-1">Companhia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($voos as $voo)
                                        <tr class="border-b">
                                            <td class="px-2 py-1">{{ $voo->desc_aeronave_voo }}</td>
                                            <td class="px-2 py-1">{{ \Carbon\Carbon::parse($voo->data_hora_voo)->format('d/m/Y H:i') }}</td>
                                            <td class="px-2 py-1">{{ $voo->origem_voo }}</td>
                                            <td class="px-2 py-1">{{ $voo->destino_voo }}</td>
                                            <td class="px-2 py-1">{{ $voo->companhia_voo }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-gray-400">Nenhum voo cadastrado.</div>
                    @endif
                </div>

                <!-- Pontos de Interesse -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">Pontos de Interesse</h2>
                        <a href="{{ route('explore.setTrip', ['id' => $viagem->pk_id_viagem]) }}" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors text-sm font-medium shadow-sm">
                            <i class="fa-solid fa-map-location-dot mr-2"></i> Editar no mapa
                        </a>
                    </div>
                    <div>
                        @php
                            $pontosOrdenados = $pontosInteresse->sortBy('data_ponto_interesse');
                        @endphp
                        @if($pontosOrdenados->count())
                            <ul class="space-y-2">
                                @foreach($pontosOrdenados as $ponto)
                                    <li class="transition-colors duration-150 hover:bg-gray-100 rounded px-3 py-2 cursor-pointer flex flex-col" onclick="openPlaceDetailsModal('{{ $ponto->placeid_ponto_interesse }}', true, {{ $ponto->pk_id_ponto_interesse }}, '{{ $ponto->hora_ponto_interesse ? \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') : '' }}')">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-base">{{ $ponto->nome_ponto_interesse }}</span>
                                            <span class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($ponto->data_ponto_interesse)->format('d/m/Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 text-xs">Horário: {{ \Carbon\Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') }}</span>
                                        </div>
                                        @if($ponto->desc_ponto_interesse)
                                            <div class="text-gray-600 text-xs mt-1">{{ $ponto->desc_ponto_interesse }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-gray-400">Nenhum ponto de interesse cadastrado.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Viajantes -->
    @include('components.myTrips.modals.viajantesModal')

    <!-- Modal de Objetivos -->
    @include('components.myTrips.modals.objetivosModal')

    <!-- Modal de Adicionar Objetivo -->
    @include('components.myTrips.modals.addObjetivosModal')

    <!-- Modal de Adicionar Viajante -->
    @include('components.myTrips.modals.addViajantesModal')

    @include('components/explore/detailsModal')
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps_api_key')}}&libraries=places" async defer></script>
    
@endsection
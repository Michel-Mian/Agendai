<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 rounded-lg p-2">
                    <i class="fas fa-plane text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-blue-800">Voos</h2>
                    <p class="text-blue-600 text-sm">{{ $voos->count() }} {{ $voos->count() == 1 ? 'voo' : 'voos' }} cadastrados</p>
                </div>
            </div>
            <a href="{{ route('flights.search') }}" class="bg-white/20 hover:bg-white/30 text-blue-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Adicionar voo
            </a>
        </div>
    </div>
    
    <div class="p-6">
        @if($voos->count())
            <div class="overflow-hidden">
                <div class="space-y-4">
                    @foreach($voos as $voo)
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $voo->desc_aeronave_voo }}</div>
                                            <div class="text-sm text-gray-600">{{ $voo->companhia_voo }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ \Carbon\Carbon::parse($voo->data_hora_partida)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($voo->data_hora_partida)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm text-gray-500">Origem</div>
                                            <div class="font-semibold text-gray-800">{{ $voo->origem_voo }}</div>
                                        </div>
                                        <div class="flex items-center space-x-2 text-blue-500">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <div class="w-8 h-0.5 bg-blue-500"></div>
                                            <i class="fas fa-plane text-sm"></i>
                                            <div class="w-8 h-0.5 bg-blue-500"></div>
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm text-gray-500">Destino</div>
                                            <div class="font-semibold text-gray-800">{{ $voo->destino_voo }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-plane text-blue-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum voo cadastrado</h3>
                <p class="text-gray-500 mb-6">Adicione informações sobre seus voos para manter tudo organizado</p>
                <a href="{{ route('flights.search') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Adicionar primeiro voo
                </a>
            </div>
        @endif
    </div>
</div>
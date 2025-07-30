<div class="space-y-8">
    @include('components.myTrips.finance')

    <!-- Seção de Previsão do Tempo -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-cloud-sun text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-blue-700">Previsão do Tempo</h3>
                        <p class="text-blue-400 text-sm">{{ $viagem->destino_viagem }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-blue-700 text-sm">Período da viagem</div>
                    <div class="text-blue-400 font-medium">{{ \Carbon\Carbon::parse($viagem->data_inicio_viagem)->format('d/m') }} - {{ \Carbon\Carbon::parse($viagem->data_final_viagem)->format('d/m') }}</div>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Clima Atual -->
            <div class="bg-gradient-to-br from-sky-50 to-blue-100 rounded-lg p-6 mb-6 border border-sky-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-6xl">☀️</div>
                        <div>
                            <div class="text-3xl font-bold text-gray-800">28°C</div>
                            <div class="text-gray-600">Ensolarado</div>
                            <div class="text-sm text-gray-500">Sensação térmica: 31°C</div>
                        </div>
                    </div>
                    <div class="text-right space-y-2">
                        <div class="flex items-center space-x-2 text-gray-600">
                            <i class="fas fa-eye text-sm"></i>
                            <span class="text-sm">Visibilidade: 10km</span>
                        </div>
                        <div class="flex items-center space-x-2 text-gray-600">
                            <i class="fas fa-wind text-sm"></i>
                            <span class="text-sm">Vento: 15 km/h</span>
                        </div>
                        <div class="flex items-center space-x-2 text-gray-600">
                            <i class="fas fa-tint text-sm"></i>
                            <span class="text-sm">Umidade: 65%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Previsão dos Próximos Dias -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Hoje</div>
                    <div class="text-2xl mb-2">☀️</div>
                    <div class="text-lg font-bold text-gray-800">28°</div>
                    <div class="text-sm text-gray-500">18°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Amanhã</div>
                    <div class="text-2xl mb-2">⛅</div>
                    <div class="text-lg font-bold text-gray-800">26°</div>
                    <div class="text-sm text-gray-500">19°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Qua</div>
                    <div class="text-2xl mb-2">🌧️</div>
                    <div class="text-lg font-bold text-gray-800">23°</div>
                    <div class="text-sm text-gray-500">16°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Qui</div>
                    <div class="text-2xl mb-2">🌤️</div>
                    <div class="text-lg font-bold text-gray-800">25°</div>
                    <div class="text-sm text-gray-500">17°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Sex</div>
                    <div class="text-2xl mb-2">☀️</div>
                    <div class="text-lg font-bold text-gray-800">29°</div>
                    <div class="text-sm text-gray-500">20°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Sáb</div>
                    <div class="text-2xl mb-2">☀️</div>
                    <div class="text-lg font-bold text-gray-800">31°</div>
                    <div class="text-sm text-gray-500">22°</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-600 mb-2">Dom</div>
                    <div class="text-2xl mb-2">⛅</div>
                    <div class="text-lg font-bold text-gray-800">27°</div>
                    <div class="text-sm text-gray-500">19°</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Notícias da Região -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-newspaper text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-800">Notícias da Região</h3>
                        <p class="text-red-300 text-sm">{{ $viagem->destino_viagem }}</p>
                    </div>
                </div>
                <button class="hover:bg-red-600 text-red-400 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <a href="https://news.google.com/search?q=regiao de {{ $viagem->destino_viagem }}" target="_blank">Ver mais notícias</a>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <!-- Notícia Principal -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                    @foreach ($eventos as $evento)
                        <div class="flex items-start space-x-4 mb-8">
                            <div class="w-24 h-24 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $evento ['thumbnail'] }}" alt="imagem-do-evento">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Eventos</span>
                                    <span class="text-gray-500 text-sm">{{ $evento ['data_formatada'] }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $evento ['title'] }}</h4>
                                <p class="text-gray-600 text-sm mb-3">{{ $evento ['address'][0] }}</p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>Avaliação do local:</span>
                                    <span class="flex items-center space-x-1 text-blue-600">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $evento ['venue']['rating']}}</span>
                                    </span>
                                    <span>adquira seu ingresso em</span>
                                    <span class="flex items-center space-x-1 text-blue-600">
                                        <i class="fas fa-ticket"></i>
                                        <a href="{{ $evento ['ticket_info'][0]['link'] }}" target="_blank">{{ $evento ['ticket_info'][0]['source'] }}</a>
                                    </span>
                                </div>
                            </div>
                        </div>                
                    @endforeach
                </div>
                
                <!-- Outras Notícias -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" id="noticia-cultura">
                        @if(isset($noticias['Cultura']))
                        <div class="flex items-start space-x-3">
                            <div class="w-20 h-16 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $noticias ['Cultura']['thumbnail'] }}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{$noticias['Cultura']['q']}}</span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $noticias['Cultura']['title'] }}</h5>
                                <a href="{{ $noticias['Cultura']['link'] }}" target="_blank" class="text-gray-600 text-sm mb-2">
                                    Ver mais em <span class="text-blue-700">{{ $noticias['Cultura']['source_name'] }}</span>
                                </a>
                                <span class="text-gray-500 text-xs">{{ $noticias['Cultura']['date'] }}</span>
                            </div>
                        </div>
                        @else
                            <p class="text-gray-400">Nenhuma notícia de cultura encontrada.</p>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" id="noticia-saude">
                        @if(isset($noticias['Saúde']))
                        <div class="flex items-start space-x-3">
                            <div class="w-20 h-16 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $noticias ['Saúde']['thumbnail'] }}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{$noticias['Saúde']['q']}}</span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $noticias['Saúde']['title'] }}</h5>
                                <a href="{{ $noticias['Saúde']['link'] }}" target="_blank" class="text-gray-600 text-sm mb-2">
                                    Ver mais em <span class="text-blue-700">{{ $noticias['Saúde']['source_name'] }}</span>
                                </a>
                                <span class="text-gray-500 text-xs">{{ $noticias['Saúde']['date'] }}</span>
                            </div>
                        </div>
                        @else
                            <p class="text-gray-400">Nenhuma notícia de Saúde encontrada.</p>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" id="noticia-local">
                        @if(isset($noticias['Entretenimento']))
                        <div class="flex items-start space-x-3">
                            <div class="w-20 h-16 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $noticias ['Entretenimento']['thumbnail'] }}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{$noticias['Entretenimento']['q']}}</span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $noticias['Entretenimento']['title'] }}</h5>
                                <a href="{{ $noticias['Entretenimento']['link'] }}" target="_blank" class="text-gray-600 text-sm mb-2">
                                    Ver mais em <span class="text-blue-700">{{ $noticias['Entretenimento']['source_name'] }}</span>
                                </a>
                                <span class="text-gray-500 text-xs">{{ $noticias['Entretenimento']['date'] }}</span>
                            </div>
                        </div>
                        @else
                            <p class="text-gray-400">Nenhuma notícia de Entretenimento encontrada.</p>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" id="noticia-local">
                        @if(isset($noticias['Esportes']))
                        <div class="flex items-start space-x-3">
                            <div class="w-20 h-16 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $noticias ['Esportes']['thumbnail'] }}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{$noticias['Esportes']['q']}}</span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $noticias['Esportes']['title'] }}</h5>
                                <a href="{{ $noticias['Esportes']['link'] }}" target="_blank" class="text-gray-600 text-sm mb-2">
                                    Ver mais em <span class="text-blue-700">{{ $noticias['Esportes']['source_name'] }}</span>
                                </a>
                                <span class="text-gray-500 text-xs">{{ $noticias['Esportes']['date'] }}</span>
                            </div>
                        </div>
                        @else
                            <p class="text-gray-400">Nenhuma notícia de Esportes encontrada.</p>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" id="noticia-local">
                        @if(isset($noticias['Local']))
                        <div class="flex items-start space-x-3">
                            <div class="w-20 h-16 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $noticias ['Local']['thumbnail'] }}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{$noticias['Local']['q']}}</span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-1">{{ $noticias['Local']['title'] }}</h5>
                                <a href="{{ $noticias['Local']['link'] }}" target="_blank" class="text-gray-600 text-sm mb-2">
                                    Ver mais em <span class="text-blue-700">{{ $noticias['Local']['source_name'] }}</span>
                                </a>
                                <span class="text-gray-500 text-xs">{{ $noticias['Local']['date'] }}</span>
                            </div>
                        </div>
                        @else
                            <p class="text-gray-400">Nenhuma notícia de Local encontrada.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animações suaves para hover */
    .hover\:shadow-md:hover {
        transition: box-shadow 0.3s ease-in-out;
    }
    
    /* Gradientes personalizados */
    .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }
    
    /* Efeitos de transição */
    .transition-shadow {
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .transition-colors {
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
</style>

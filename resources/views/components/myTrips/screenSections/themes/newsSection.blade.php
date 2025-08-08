<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2">
                        <i class="fas fa-newspaper text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-800">Notícias e eventos da Região</h3>
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
                                        <span>{{ $evento ['venue']['rating'] ?? 'sem avaliação'}}</span>
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
                                <img src="{{ $noticias ['Cultura']['thumbnail'] ?? 'sem imagem'}}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
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
                                <img src="{{ $noticias ['Saúde']['thumbnail'] ?? 'sem imagem'}}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
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
                                <img src="{{ $noticias ['Entretenimento']['thumbnail'] ?? 'sem imagem'}}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
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
                                <img src="{{ $noticias ['Esportes']['thumbnail'] ?? 'sem imagem'}}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
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
                                <img src="{{ $noticias ['Local']['thumbnail'] ?? 'sem imagem'}}" alt="logotipo-notícia" class="w-full h-full object-cover rounded-lg">
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
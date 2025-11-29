@php
    // Garantir que $hotel seja uma collection
    if (isset($hotel)) {
        if (is_object($hotel) && method_exists($hotel, 'count')) {
            // Se já é uma collection
            $hotels = $hotel;
        } elseif (is_object($hotel)) {
            // Se é um objeto único, transformar em collection
            $hotels = collect([$hotel]);
        } elseif (is_array($hotel)) {
            // Se é um array, transformar em collection
            $hotels = collect($hotel);
        } else {
            $hotels = collect();
        }
    } else {
        $hotels = collect();
    }
@endphp

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-hotel text-pink-600"></i>
            <h3 class="text-xl font-semibold text-pink-600">Hospedagem</h3>
        </div>
    <a href="{{ route('hotels.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Adicionar hospedagem
        </a>
    </div>

    @if($hotels && $hotels->count() > 0)
        <div class="space-y-4">
            @foreach($hotels as $hotelItem)
                <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-lg hover:border-gray-300 transition-all duration-300">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Imagem do Hotel -->
                        @if($hotelItem->image_url)
                            <div class="md:w-1/3">
                                <img src="{{ $hotelItem->image_url }}" 
                                        alt="{{ $hotelItem->nome_hotel }}" 
                                        class="w-full h-48 object-cover rounded-lg hover:scale-105 transition-transform duration-300">
                            </div>
                        @endif
                        
                        <!-- Informações do Hotel -->
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-3">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-1 hover:text-pink-600 transition-colors duration-200">{{ $hotelItem->nome_hotel }}</h4>
                                    
                                    <!-- Avaliação com Estrelas -->
                                    @if($hotelItem->avaliacao)
                                        @php $avaliacaoFloat = convertToFloat($hotelItem->avaliacao); @endphp
                                        <div class="flex items-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($avaliacaoFloat))
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @elseif($i == ceil($avaliacaoFloat) && $avaliacaoFloat - floor($avaliacaoFloat) >= 0.5)
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <defs>
                                                            <linearGradient id="half-fill-{{ $loop->index }}">
                                                                <stop offset="50%" stop-color="#FCD34D"/>
                                                                <stop offset="50%" stop-color="#E5E7EB"/>
                                                            </linearGradient>
                                                        </defs>
                                                        <path fill="url(#half-fill-{{ $loop->index }})" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-600">({{ number_format($avaliacaoFloat, 1) }})</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Preço -->
                                @if($hotelItem->preco)
                                    @php $precoFloat = convertToFloat($hotelItem->preco); @endphp
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-pink-600">
                                            R$ {{ number_format($precoFloat, 2, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">por noite</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Datas e Duração -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                @if($hotelItem->data_check_in)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        <div>
                                            <div class="text-xs text-gray-500">Check-in</div>
                                            <div class="font-medium">{{ \Carbon\Carbon::parse($hotelItem->data_check_in)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($hotelItem->data_check_out)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <div>
                                            <div class="text-xs text-gray-500">Check-out</div>
                                            <div class="font-medium">{{ \Carbon\Carbon::parse($hotelItem->data_check_out)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($hotelItem->data_check_in && $hotelItem->data_check_out)
                                    @php
                                        $checkin = \Carbon\Carbon::parse($hotelItem->data_check_in);
                                        $checkout = \Carbon\Carbon::parse($hotelItem->data_check_out);
                                        $noites = $checkin->diffInDays($checkout);
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-xs text-gray-500">Duração</div>
                                            <div class="font-medium">{{ $noites }} {{ $noites == 1 ? 'noite' : 'noites' }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Total Estimado -->
                            @if($hotelItem->preco && $hotelItem->data_check_in && $hotelItem->data_check_out)
                                @php
                                    $precoFloat = convertToFloat($hotelItem->preco);
                                    $total = $precoFloat * $noites;
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-3 mb-3 hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-700">Total estimado da hospedagem:</span>
                                        <span class="text-xl font-bold text-pink-700">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Localização -->
                            @if($hotelItem->latitude && $hotelItem->longitude)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <a href="https://www.google.com/maps?q={{ $hotelItem->latitude }},{{ $hotelItem->longitude }}" 
                                        target="_blank" 
                                        class="text-pink-600 hover:text-pink-800 hover:underline transition-all duration-200">
                                        Ver localização no mapa
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-2 0H9m-2 0H5m2 0v-4a2 2 0 012-2h2a2 2 0 012 2v4"></path>
            </svg>
            <p class="text-lg">Nenhuma hospedagem cadastrada para esta viagem</p>
            <p class="text-sm mt-1 mb-4">Adicione informações do hotel para visualizar os detalhes aqui</p>
            <a href="{{ route('hotels.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Adicionar primeira hospedagem
            </a>
        </div>
    @endif
</div>

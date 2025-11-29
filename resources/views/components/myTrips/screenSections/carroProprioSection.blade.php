@if($viagemCarro)
<!-- Seção de Carro Próprio -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 mb-6">
    <!-- Header da Seção -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-car text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-blue-600">Carro Próprio</h3>
                    <p class="text-blue-600 text-sm">Informações e custos da viagem</p>
                </div>
            </div>
            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                <span class="text-white font-semibold text-lg">
                    R$ {{ number_format($viagemCarro->custo_total, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Mapa da Rota -->
        <div class="mb-6">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-blue-600"></i>
                    Rota da Viagem
                </h4>
                <div class="rounded-lg overflow-hidden border-2 border-gray-300 shadow-md">
                    <div id="carroProprioMap" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Informações do Veículo e Rota -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Informações do Veículo -->
            <div class="bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl p-4 border-2 border-green-300 shadow-md">
                <h4 class="text-md font-bold text-green-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-gas-pump text-green-700"></i>
                    Informações do Veículo
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Autonomia:</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($viagemCarro->autonomia_veiculo_km_l, 2, ',', '.') }} km/L</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Tipo de Combustível:</span>
                        <span class="text-sm font-bold text-gray-900 capitalize">{{ $viagemCarro->tipo_combustivel }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Preço do Combustível:</span>
                        <span class="text-sm font-bold text-gray-900">R$ {{ number_format($viagemCarro->preco_combustivel_litro, 2, ',', '.') }}/L</span>
                    </div>
                </div>
            </div>

            <!-- Informações da Rota -->
            <div class="bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl p-4 border-2 border-blue-300 shadow-md">
                <h4 class="text-md font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-route text-blue-700"></i>
                    Informações da Rota
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Distância Total:</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($viagemCarro->distancia_total_km, 2, ',', '.') }} km</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Duração Estimada:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $viagemCarro->duracao_texto }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-white/60 p-2 rounded">
                        <span class="text-sm font-medium text-gray-800">Combustível Necessário:</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($viagemCarro->combustivel_estimado_litros, 2, ',', '.') }} L</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custos Detalhados -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-calculator text-purple-600"></i>
                Detalhamento de Custos
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Combustível -->
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Combustível</span>
                        <i class="fas fa-gas-pump text-green-500"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-800">
                        R$ {{ number_format($viagemCarro->custo_combustivel_estimado, 2, ',', '.') }}
                    </p>
                </div>

                <!-- Pedágios -->
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Pedágios</span>
                        <i class="fas fa-road text-orange-500"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-800">
                        R$ {{ number_format($viagemCarro->pedagio_estimado, 2, ',', '.') }}
                        @if($viagemCarro->pedagio_oficial)
                            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">✓ Oficial</span>
                        @else
                            <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">≈ Estimado</span>
                        @endif
                    </p>
                </div>

                <!-- Total -->
                <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-lg p-3 shadow-md">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-white/90">Total</span>
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <p class="text-lg font-bold text-white">
                        R$ {{ number_format($viagemCarro->custo_total, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold mb-1">Sobre os cálculos:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>Distância e rota calculadas via Google Maps Routes API</li>
                        <li>Pedágios: valores oficiais quando disponíveis, caso contrário estimados</li>
                        <li>Consumo baseado na autonomia informada do veículo</li>
                        <li>Valores são estimativas e podem variar conforme condições reais</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para renderizar o mapa -->
<script>
    // Armazenar dados da rota para uso posterior
    let carroProprioRotaData = {!! json_encode($viagemCarro->rota_detalhada) !!};
    
    console.log('=== DEBUG MAPA CARRO PRÓPRIO ===');
    console.log('$viagemCarro existe?', {{ $viagemCarro ? 'true' : 'false' }});
    console.log('Dados brutos (carroProprioRotaData):', carroProprioRotaData);
    console.log('Tipo dos dados:', typeof carroProprioRotaData);
    
    // Se for string, fazer parse
    if (typeof carroProprioRotaData === 'string') {
        try {
            console.log('⚠️ Dados são string, fazendo parse...');
            carroProprioRotaData = JSON.parse(carroProprioRotaData);
            console.log('✓ Parse realizado:', carroProprioRotaData);
        } catch (e) {
            console.error('❌ Erro ao fazer parse:', e);
        }
    }
    
    console.log('É array?', Array.isArray(carroProprioRotaData));
    console.log('É objeto?', carroProprioRotaData !== null && typeof carroProprioRotaData === 'object');
    
    if (carroProprioRotaData && typeof carroProprioRotaData === 'object') {
        console.log('Keys do objeto:', Object.keys(carroProprioRotaData));
        console.log('Tem polyline?', 'polyline' in carroProprioRotaData ? 'SIM' : 'NÃO');
        
        if (carroProprioRotaData.polyline) {
            console.log('✓ Polyline encontrada (primeiros 100 chars):', carroProprioRotaData.polyline.substring(0, 100));
        } else {
            console.log('❌ Polyline não encontrada. Conteúdo:', JSON.stringify(carroProprioRotaData).substring(0, 200));
        }
    } else {
        console.log('❌ carroProprioRotaData inválido');
    }
    
    // Função para inicializar o mapa (global para ser acessível pelo callback)
    window.initCarroProprioMap = function() {
        const mapContainer = document.getElementById('carroProprioMap');
        
        if (!mapContainer) {
            console.error('❌ Container do mapa não encontrado');
            return;
        }
        
        if (!carroProprioRotaData || !carroProprioRotaData.polyline) {
            console.error('❌ Dados da rota não disponíveis ou polyline ausente');
            mapContainer.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-500"><i class="fas fa-exclamation-triangle mr-2"></i> Dados da rota não disponíveis</div>';
            return;
        }

        if (typeof google === 'undefined' || !google.maps || !google.maps.geometry) {
            console.warn('⏳ Google Maps API não carregada ainda, tentando novamente em 500ms...');
            setTimeout(initCarroProprioMap, 500);
            return;
        }

        try {
            console.log('🗺️ Criando mapa...');
            
            // Criar mapa
            const map = new google.maps.Map(mapContainer, {
                zoom: 7,
                center: { lat: -23.5505, lng: -46.6333 },
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true
            });

            console.log('📍 Decodificando polyline...');
            // Decodificar polyline
            const path = google.maps.geometry.encoding.decodePath(carroProprioRotaData.polyline);
            console.log('✓ Polyline decodificada:', path.length, 'pontos');
            
            // Desenhar polyline
            const polyline = new google.maps.Polyline({
                path: path,
                strokeColor: '#4F46E5',
                strokeWeight: 5,
                strokeOpacity: 0.8,
                map: map
            });
            console.log('✓ Linha da rota desenhada');

            // Ajustar bounds para mostrar toda a rota
            const bounds = new google.maps.LatLngBounds();
            path.forEach(point => bounds.extend(point));
            map.fitBounds(bounds);
            console.log('✓ Mapa ajustado aos limites da rota');

            // Adicionar marcadores para os destinos
            if (path.length > 0) {
                console.log('📌 Estrutura dos legs:', carroProprioRotaData.legs);
                
                // Marcador de início (verde)
                new google.maps.Marker({
                    position: path[0],
                    map: map,
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                    },
                    label: {
                        text: 'A',
                        color: 'white',
                        fontWeight: 'bold'
                    },
                    title: 'Início da viagem',
                    zIndex: 1000
                });

                // Marcadores intermediários baseados nos legs
                if (carroProprioRotaData.legs && Array.isArray(carroProprioRotaData.legs) && carroProprioRotaData.legs.length > 0) {
                    console.log('📌 Processando', carroProprioRotaData.legs.length, 'legs (trechos)');
                    
                    // Para cada leg, adicionar marcador no final dele
                    carroProprioRotaData.legs.forEach((leg, legIndex) => {
                        console.log(`Leg ${legIndex}:`, {
                            start: leg.startLocation,
                            end: leg.endLocation,
                            distance: leg.distanceMeters
                        });
                        
                        // Usar a localização final de cada leg
                        if (leg.endLocation && leg.endLocation.latLng) {
                            const endPos = {
                                lat: leg.endLocation.latLng.latitude,
                                lng: leg.endLocation.latLng.longitude
                            };
                            
                            // Não adicionar marcador no último leg (será o marcador final vermelho)
                            if (legIndex < carroProprioRotaData.legs.length - 1) {
                                const markerLabel = String.fromCharCode(66 + legIndex); // B, C, D...
                                
                                new google.maps.Marker({
                                    position: endPos,
                                    map: map,
                                    icon: {
                                        url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                                    },
                                    label: {
                                        text: markerLabel,
                                        color: 'white',
                                        fontWeight: 'bold'
                                    },
                                    title: `Destino ${legIndex + 2}`,
                                    zIndex: 999
                                });
                                
                                console.log(`✓ Marcador ${markerLabel} adicionado em`, endPos);
                            }
                        }
                    });
                }

                // Marcador de fim (vermelho)
                const lastLeg = carroProprioRotaData.legs?.[carroProprioRotaData.legs.length - 1];
                let finalPos = path[path.length - 1];
                
                if (lastLeg?.endLocation?.latLng) {
                    finalPos = {
                        lat: lastLeg.endLocation.latLng.latitude,
                        lng: lastLeg.endLocation.latLng.longitude
                    };
                }
                
                const totalLegs = (carroProprioRotaData.legs?.length || 0) + 1;
                const finalLabel = String.fromCharCode(64 + totalLegs); // Se 3 legs, será 'D'
                
                new google.maps.Marker({
                    position: finalPos,
                    map: map,
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                    },
                    label: {
                        text: finalLabel,
                        color: 'white',
                        fontWeight: 'bold'
                    },
                    title: 'Fim da viagem',
                    zIndex: 1000
                });
                
                console.log('✓ Marcadores adicionados ao mapa');
            }

            console.log('✅ Mapa renderizado com SUCESSO!');

        } catch (error) {
            console.error('❌ Erro ao renderizar mapa:', error);
            console.error('Stack trace:', error.stack);
            mapContainer.innerHTML = '<div class="flex items-center justify-center h-full bg-red-50 text-red-600 p-4 text-center"><div><i class="fas fa-exclamation-circle mr-2"></i><br>Erro ao carregar mapa<br><small class="text-xs">' + error.message + '</small></div></div>';
        }
    }

    // Tentar inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM carregado, aguardando Google Maps...');
            // Esperar o Google Maps carregar
            waitForGoogleMaps();
        });
    } else {
        console.log('📄 DOM já estava carregado, aguardando Google Maps...');
        waitForGoogleMaps();
    }
    
    // Função para esperar o Google Maps carregar
    function waitForGoogleMaps() {
        if (typeof google !== 'undefined' && google.maps && google.maps.geometry) {
            console.log('✅ Google Maps já disponível, inicializando mapa...');
            setTimeout(initCarroProprioMap, 100);
        } else {
            console.log('⏳ Aguardando Google Maps carregar...');
            setTimeout(waitForGoogleMaps, 300);
        }
    }
    
    // Garantir que também tente inicializar quando a página estiver totalmente carregada
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (typeof google !== 'undefined' && google.maps && google.maps.geometry) {
                const mapContainer = document.getElementById('carroProprioMap');
                // Só inicializar se ainda não tiver conteúdo (não foi inicializado ainda)
                if (mapContainer && !mapContainer.querySelector('.gm-style')) {
                    console.log('🔄 Tentando inicializar mapa no evento load...');
                    initCarroProprioMap();
                }
            }
        }, 500);
    });
</script>
@endif

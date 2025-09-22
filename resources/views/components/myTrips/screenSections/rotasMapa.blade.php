<div class="w-full bg-white rounded-xl shadow-xl relative flex">
    <!-- Painel lateral de pontos de interesse -->
    @include('components/myTrips/screenSections/themes/pointSection')

    <!-- Container principal do mapa -->
    <div class="flex-1 flex flex-col">
        <!-- Header do mapa -->
        <div class="bg-gradient-to-br from-blue-900 to-blue-800 text-white p-5 text-center">
            <h3 class="m-0 text-xl font-semibold flex items-center justify-center gap-2 mb-3">
                <i class="fas fa-map-marker-alt"></i>
                Mapa de Rotas - {{ $viagem->nome_viagem }}
            </h3>
            
            @if($viagem->destinos && $viagem->destinos->count() > 0)
                <div class="flex justify-center">
                    <select id="destino-selector" class="bg-white text-gray-800 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Selecione um destino</option>
                        @foreach($viagem->destinos as $destino)
                            <option value="{{ $destino->nome_destino }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $destino->nome_destino }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="text-blue-200 text-sm">Nenhum destino cadastrado para esta viagem</p>
            @endif
        </div>

        <!-- Container do mapa -->
        <div class="relative h-[50rem] w-full flex-1">
            <div id="map" class="w-full h-full"></div>
            
            <!-- Loading overlay -->
            <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-95 flex items-center justify-center z-50">
                <div class="text-center text-blue-900">
                    <div class="w-10 h-10 border-4 border-gray-200 border-t-blue-900 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm font-medium">Carregando mapa...</p>
                </div>
            </div>
        </div>

        <!-- Controles do mapa -->
        <div class="absolute top-24 right-4 flex flex-col gap-2 z-40">
            <button type="button" 
                    class="w-10 h-10 bg-white border-0 rounded-lg shadow-lg text-blue-900 hover:bg-blue-900 hover:text-white hover:-translate-y-0.5 hover:shadow-blue-200 hover:shadow-xl active:translate-y-0 transition-all duration-200 flex items-center justify-center" 
                    onclick="centerMap()" 
                    title="Centralizar no destino">
                <i class="fas fa-crosshairs text-base"></i>
            </button>
            <button type="button" 
                    class="w-10 h-10 bg-white border-0 rounded-lg shadow-lg text-blue-900 hover:bg-blue-900 hover:text-white hover:-translate-y-0.5 hover:shadow-blue-200 hover:shadow-xl active:translate-y-0 transition-all duration-200 flex items-center justify-center" 
                    onclick="toggleMapType()" 
                    title="Alternar tipo de mapa">
                <i class="fas fa-layer-group text-base"></i>
            </button>
            <button type="button" 
                    class="w-10 h-10 bg-white border-0 rounded-lg shadow-lg text-blue-900 hover:bg-blue-900 hover:text-white hover:-translate-y-0.5 hover:shadow-blue-200 hover:shadow-xl active:translate-y-0 transition-all duration-200 flex items-center justify-center" 
                    onclick="toggleRoute()" 
                    title="Mostrar/ocultar rota">
                <i class="fas fa-route text-base"></i>
            </button>
        </div>
    </div>
</div>

<script>
    let map;
    let geocoder;
    let currentMapType = 'terrain';
    let pontosInteresseMarkers = [];
    let routePolyline;
    let routeVisible = false;
    let panelExpanded = true; // Variável para controlar estado do painel
    
    const destinos = @json($viagem->destinos);
    const destinoAtual = destinos && destinos.length > 0 ? destinos[0].nome_destino : '';
    const pontosInteresse = @json($viagem->pontosInteresse()->orderBy('data_ponto_interesse')->orderBy('hora_ponto_interesse')->get());
    const googleMapsApiKey = @json(config('services.google_maps_api_key'));

    function focusOnPoint(index, lat, lng) {
        if (map && lat && lng) {
            const location = { lat: parseFloat(lat), lng: parseFloat(lng) };
            map.setCenter(location);
            map.setZoom(16);
            
            if (pontosInteresseMarkers[index] && pontosInteresseMarkers[index].infoWindow) {
                pontosInteresseMarkers.forEach(m => {
                    if (m.infoWindow) {
                        m.infoWindow.close();
                    }
                });
                pontosInteresseMarkers[index].infoWindow.open(map, pontosInteresseMarkers[index].marker);
            }
        }
    }

    function waitForGoogleMaps(callback, maxAttempts = 50) {
        let attempts = 0;
        
        function checkGoogleMaps() {
            attempts++;
            
            if (typeof google !== 'undefined' && 
                google.maps && 
                google.maps.Map && 
                google.maps.Marker &&
                google.maps.Geocoder) {
                callback();
                return;
            }
            
            if (attempts >= maxAttempts) {
                console.error('Google Maps API não pôde ser carregado após múltiplas tentativas');
                handleMapError();
                return;
            }
            
            setTimeout(checkGoogleMaps, 100);
        }
        
        checkGoogleMaps();
    }

    function initMapComponent() {
        try {
            geocoder = new google.maps.Geocoder();
            
            const mapOptions = {
                zoom: 15,
                center: { lat: -23.5505, lng: -46.6333 },
                mapTypeId: google.maps.MapTypeId.TERRAIN,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                zoomControl: false,
                disableDefaultUI: true,
                gestureHandling: 'greedy',
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            trafficLayer = new google.maps.TrafficLayer();
            
            if (pontosInteresse && pontosInteresse.length > 0 && pontosInteresse[0].latitude && pontosInteresse[0].longitude) {
                centerOnFirstPonto();
            } else {
                geocodeDestinationForCenter();
            }
        } catch (error) {
            console.error('Erro ao inicializar o mapa:', error);
            handleMapError();
        }
    }

    function centerOnFirstPonto() {
        const firstPonto = pontosInteresse[0];
        const location = {
            lat: parseFloat(firstPonto.latitude),
            lng: parseFloat(firstPonto.longitude)
        };
        
        map.setCenter(location);
        
        addPontosInteresseToMap();
        
        hideLoading();
    }

    function addPontosInteresseToMap() {
        if (!pontosInteresse || pontosInteresse.length === 0) {
            return;
        }

        pontosInteresse.forEach((ponto, index) => {
            if (ponto.latitude && ponto.longitude) {
                const pontoMarker = new google.maps.Marker({
                    position: { 
                        lat: parseFloat(ponto.latitude), 
                        lng: parseFloat(ponto.longitude) 
                    },
                    map: map,
                    title: ponto.nome_ponto_interesse,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    },
                    animation: google.maps.Animation.DROP
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="p-0 max-w-xs bg-white rounded-lg shadow-lg border-0 overflow-hidden">
                            ${ponto.imagem_ponto_interesse ? `
                                <div class="w-full h-32 bg-gray-200 overflow-hidden">
                                    <img src="${ponto.imagem_ponto_interesse}" 
                                         alt="${ponto.nome_ponto_interesse}"
                                         class="w-full h-full object-cover"
                                         onerror="this.parentElement.style.display='none'">
                                </div>
                            ` : ''}
                            
                            <div class="p-4">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-800 mb-1 leading-tight">
                                            ${ponto.nome_ponto_interesse}
                                        </h4>
                                        ${ponto.desc_ponto_interesse ? `
                                            <p class="text-sm text-gray-600 mb-3 leading-relaxed">
                                                ${ponto.desc_ponto_interesse}
                                            </p>
                                        ` : ''}
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 bg-gray-50 p-2 rounded-md">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-calendar text-blue-500"></i>
                                        <span>${formatDate(ponto.data_ponto_interesse)}</span>
                                    </div>
                                    ${ponto.hora_ponto_interesse ? `
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-clock text-blue-500"></i>
                                            <span>${formatTime(ponto.hora_ponto_interesse)}</span>
                                        </div>
                                    ` : ''}
                                </div>
                                
                                <button onclick="openPlaceDetailsModal('${ponto.placeid_ponto_interesse}', true, ${ponto.pk_id_ponto_interesse}, '${ponto.hora_ponto_interesse ? formatTime(ponto.hora_ponto_interesse) : ''}')" 
                                        class="w-full bg-blue-800 hover:bg-white text-white hover:text-blue-800 font-medium py-2 px-4 rounded-md text-sm transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                    <i class="fas fa-info-circle mr-2"></i>Ver Detalhes
                                </button>
                            </div>
                        </div>
                    `,
                    pixelOffset: new google.maps.Size(0, -10)
                });

                pontoMarker.addListener('click', function() {
                    pontosInteresseMarkers.forEach(m => {
                        if (m.infoWindow) {
                            m.infoWindow.close();
                        }
                    });
                    infoWindow.open(map, pontoMarker);
                });

                pontosInteresseMarkers.push({
                    marker: pontoMarker,
                    infoWindow: infoWindow,
                    data: ponto
                });
            }
        });
    }

    function geocodeDestinationForCenter() {
        const destinoSelecionado = document.getElementById('destino-selector')?.value || destinoAtual;
        
        if (!destinoSelecionado) {
            hideLoading();
            return;
        }

        const timeoutId = setTimeout(() => {
            handleMapError();
        }, 8000);

        geocoder.geocode({ address: destinoSelecionado }, function(results, status) {
            clearTimeout(timeoutId);
            
            if (status === 'OK' && results && results.length > 0) {
                const location = results[0].geometry.location;
                map.setCenter(location);
                
                addPontosInteresseToMap();
                hideLoading();
            } else {
                console.error('Geocodificação falhou:', status);
                handleMapError();
            }
        });
    }

    function toggleRoute() {
        if (routeVisible) {
            if (routePolyline) {
                routePolyline.setMap(null);
                routePolyline = null;
            }
            routeVisible = false;
        } else {
            calculateRouteWithRoutesAPI();
        }
    }

    async function calculateRouteWithRoutesAPI() {
        if (!pontosInteresse || pontosInteresse.length < 2) {
            alert('É necessário pelo menos 2 pontos de interesse para criar uma rota.');
            return;
        }

        const validPoints = pontosInteresse.filter(ponto => 
            ponto.latitude && ponto.longitude
        );

        if (validPoints.length < 2) {
            alert('É necessário pelo menos 2 pontos com coordenadas válidas para criar uma rota.');
            return;
        }

        try {
            const waypoints = validPoints.map(ponto => ({
                location: {
                    latLng: {
                        latitude: parseFloat(ponto.latitude),
                        longitude: parseFloat(ponto.longitude)
                    }
                }
            }));

            const requestBody = {
                origin: waypoints[0],
                destination: waypoints[waypoints.length - 1],
                intermediates: waypoints.slice(1, -1),
                travelMode: "DRIVE",
                routingPreference: "TRAFFIC_AWARE",
                computeAlternativeRoutes: false,
                routeModifiers: {
                    avoidTolls: false,
                    avoidHighways: false,
                    avoidFerries: false
                },
                languageCode: "pt-BR",
                units: "METRIC"
            };

            const response = await fetch(`https://routes.googleapis.com/directions/v2:computeRoutes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Goog-Api-Key': googleMapsApiKey,
                    'X-Goog-FieldMask': 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                const encodedPolyline = route.polyline.encodedPolyline;
                
                const decodedPath = google.maps.geometry.encoding.decodePath(encodedPolyline);
                
                routePolyline = new google.maps.Polyline({
                    path: decodedPath,
                    geodesic: true,
                    strokeColor: '#1E3A8A',
                    strokeOpacity: 0.8,
                    strokeWeight: 4
                });

                routePolyline.setMap(map);
                routeVisible = true;

                const bounds = new google.maps.LatLngBounds();
                decodedPath.forEach(point => bounds.extend(point));
                map.fitBounds(bounds);

                const duration = route.duration;
                const distance = route.distanceMeters;
                
                console.log(`Rota calculada: ${(distance/1000).toFixed(1)}km, ${duration}`);
                
            } else {
                throw new Error('Nenhuma rota encontrada');
            }

        } catch (error) {
            console.error('Erro ao calcular rota com Routes API:', error);
            alert('Não foi possível calcular a rota entre os pontos. Tentando método alternativo...');
            
            calculateAndDisplayRouteFallback();
        }
    }

    function calculateAndDisplayRouteFallback() {
        const validPoints = pontosInteresse.filter(ponto => 
            ponto.latitude && ponto.longitude
        );

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#1E3A8A',
                strokeWeight: 4,
                strokeOpacity: 0.8
            }
        });

        const origin = {
            lat: parseFloat(validPoints[0].latitude),
            lng: parseFloat(validPoints[0].longitude)
        };

        const destination = {
            lat: parseFloat(validPoints[validPoints.length - 1].latitude),
            lng: parseFloat(validPoints[validPoints.length - 1].longitude)
        };

        const waypoints = validPoints.slice(1, -1).map(ponto => ({
            location: {
                lat: parseFloat(ponto.latitude),
                lng: parseFloat(ponto.longitude)
            },
            stopover: true
        }));

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
            optimizeWaypoints: false
        }, (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                directionsRenderer.setMap(map);
                routeVisible = true;
                
                const bounds = new google.maps.LatLngBounds();
                validPoints.forEach(ponto => {
                    bounds.extend({
                        lat: parseFloat(ponto.latitude),
                        lng: parseFloat(ponto.longitude)
                    });
                });
                map.fitBounds(bounds);
            } else {
                console.error('Erro ao calcular rota:', status);
                alert('Não foi possível calcular a rota entre os pontos.');
            }
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    function formatTime(timeString) {
        if (!timeString) return '';
        const time = new Date(`2000-01-01 ${timeString}`);
        return time.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function hideLoading() {
        const loadingElement = document.getElementById('map-loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }

    function centerMap() {
        const destinoSelecionado = document.getElementById('destino-selector')?.value || destinoAtual;
        
        if (destinoSelecionado && geocoder) {
            geocoder.geocode({ address: destinoSelecionado }, function(results, status) {
                if (status === 'OK' && results && results.length > 0) {
                    const location = results[0].geometry.location;
                    map.setCenter(location);
                    map.setZoom(15);
                }
            });
        } else if (pontosInteresse && pontosInteresse.length > 0 && pontosInteresse[0].latitude && pontosInteresse[0].longitude) {
            const location = {
                lat: parseFloat(pontosInteresse[0].latitude),
                lng: parseFloat(pontosInteresse[0].longitude)
            };
            map.setCenter(location);
            map.setZoom(15);
        }
    }

    function toggleMapType() {
        const mapTypes = ['terrain', 'roadmap', 'satellite', 'hybrid'];
        const currentIndex = mapTypes.indexOf(currentMapType);
        const nextIndex = (currentIndex + 1) % mapTypes.length;
        currentMapType = mapTypes[nextIndex];
        
        map.setMapTypeId(google.maps.MapTypeId[currentMapType.toUpperCase()]);
    }

    function handleMapError() {
        hideLoading();
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gray-50 text-gray-500 text-center p-5">
                    <div>
                        <i class="fas fa-exclamation-triangle text-3xl mb-2 text-yellow-500"></i>
                        <p class="font-semibold">Erro ao carregar o mapa</p>
                        <p class="text-sm">Verifique sua conexão ou tente novamente</p>
                    </div>
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        waitForGoogleMaps(initMapComponent);
        
        // Adicionar evento para o dropdown de destinos
        const destinoSelector = document.getElementById('destino-selector');
        if (destinoSelector) {
            destinoSelector.addEventListener('change', function() {
                const destinoSelecionado = this.value;
                if (destinoSelecionado && map && geocoder) {
                    geocoder.geocode({ address: destinoSelecionado }, function(results, status) {
                        if (status === 'OK' && results && results.length > 0) {
                            const location = results[0].geometry.location;
                            map.setCenter(location);
                            map.setZoom(15);
                        }
                    });
                }
            });
        }
    });
</script>


<style>
    @media (max-width: 768px) {
        .h-\[50rem\] { height: 30rem; }
        .top-24 { top: 4.5rem; }
        .w-10 { width: 2.25rem; }
        .h-10 { height: 2.25rem; }
        
        /* Removido CSS que interferia com o painel lateral */
    }
</style>

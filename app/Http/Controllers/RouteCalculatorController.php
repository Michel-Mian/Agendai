<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteCalculatorController extends Controller
{
    /**
     * Calcula a rota usando Google Maps Routes API (v2)
     * Fornece informações de pedágios, distância e duração
     */
    public function calcularRota(Request $request)
    {
        try {
            $request->validate([
                'origem_place_id' => 'required|string',
                'destinos_place_ids' => 'required|array|min:1',
                'destinos_place_ids.*' => 'required|string'
            ]);

            $apiKey = config('services.google_maps_api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'Google Maps API Key não configurada'
                ], 500);
            }

            $origem = $request->origem_place_id;
            $destinos = $request->destinos_place_ids;

            // Construir waypoints (intermediários)
            $intermediates = [];
            if (count($destinos) > 1) {
                // Todos os destinos exceto o último são waypoints intermediários
                for ($i = 0; $i < count($destinos) - 1; $i++) {
                    $intermediates[] = [
                        'placeId' => $destinos[$i]
                    ];
                }
            }

            // Montar corpo da requisição para Routes API v2
            $body = [
                'origin' => [
                    'placeId' => $origem
                ],
                'destination' => [
                    'placeId' => end($destinos)
                ],
                'travelMode' => 'DRIVE',
                'routingPreference' => 'TRAFFIC_AWARE',
                'computeAlternativeRoutes' => false,
                'languageCode' => 'pt-BR',
                'units' => 'METRIC',
                'extraComputations' => ['TOLLS']  // Solicitar cálculo de pedágios
            ];

            // Adicionar waypoints intermediários se houver
            if (!empty($intermediates)) {
                $body['intermediates'] = $intermediates;
            }

            // Fazer requisição para Routes API v2
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.legs.duration,routes.legs.distanceMeters,routes.travelAdvisory.tollInfo,routes.legs.travelAdvisory.tollInfo'
            ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', $body);

            if (!$response->successful()) {
                Log::error('Erro na API do Google Routes', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Verificar se é erro de permissão
                $errorBody = $response->json();
                $errorMessage = 'Erro ao calcular rota';
                
                if (isset($errorBody['error'])) {
                    $error = $errorBody['error'];
                    
                    if (isset($error['status']) && $error['status'] === 'PERMISSION_DENIED') {
                        $errorMessage = 'A Routes API não está habilitada ou não tem permissão. ' .
                                       'Acesse: https://console.cloud.google.com/apis/library/routes-backend.googleapis.com ' .
                                       'e clique em "Ativar". Aguarde 2-3 minutos após ativar.';
                    } elseif (isset($error['message'])) {
                        $errorMessage .= ': ' . $error['message'];
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], $response->status());
            }

            $data = $response->json();

            if (empty($data['routes'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Nenhuma rota encontrada entre os pontos especificados'
                ], 400);
            }

            $route = $data['routes'][0];

            // Extrair informações da rota
            $distanciaMetros = $route['distanceMeters'] ?? 0;
            $distanciaKm = $distanciaMetros / 1000;
            
            // Duração em segundos (formato: "123s")
            $duracaoStr = $route['duration'] ?? '0s';
            $duracaoSegundos = (int) rtrim($duracaoStr, 's');

            // Informações de pedágios
            $pedagioInfo = null;
            $pedagioEstimado = 0;
            $temPedagioOficial = false;
            
            if (isset($route['travelAdvisory']['tollInfo'])) {
                $tollInfo = $route['travelAdvisory']['tollInfo'];
                
                Log::info('TollInfo detectado:', ['tollInfo' => $tollInfo]);
                
                // Verificar se há informação de valor estimado
                if (isset($tollInfo['estimatedPrice'])) {
                    foreach ($tollInfo['estimatedPrice'] as $price) {
                        // Pegar valores em BRL (Real Brasileiro)
                        if (isset($price['currencyCode']) && $price['currencyCode'] === 'BRL') {
                            $pedagioEstimado = floatval($price['units'] ?? 0);
                            if (isset($price['nanos'])) {
                                $pedagioEstimado += floatval($price['nanos']) / 1000000000;
                            }
                            $temPedagioOficial = true;
                            break;
                        }
                    }
                    
                    // Se não encontrou BRL, tentar primeiro item disponível
                    if (!$temPedagioOficial && !empty($tollInfo['estimatedPrice'])) {
                        $price = $tollInfo['estimatedPrice'][0];
                        $pedagioEstimado = floatval($price['units'] ?? 0);
                        if (isset($price['nanos'])) {
                            $pedagioEstimado += floatval($price['nanos']) / 1000000000;
                        }
                        $temPedagioOficial = true;
                        
                        Log::info('Usando preço em outra moeda', [
                            'currency' => $price['currencyCode'] ?? 'UNKNOWN',
                            'value' => $pedagioEstimado
                        ]);
                    }
                }
                
                $pedagioInfo = [
                    'tem_pedagio' => true,
                    'valor_estimado' => round($pedagioEstimado, 2),
                    'tem_preco_oficial' => $temPedagioOficial
                ];
            }
            
            // Se não houver informação de pedágio da API, estimar
            if (!$temPedagioOficial) {
                // Estimar R$ 0,10 por km
                $pedagioEstimado = $distanciaKm * 0.10;
                $pedagioInfo = [
                    'tem_pedagio' => false,
                    'valor_estimado' => round($pedagioEstimado, 2),
                    'tem_preco_oficial' => false,
                    'observacao' => 'Valor estimado (R$ 0,10/km - sem dados oficiais da API)'
                ];
                
                Log::info('Usando estimativa de pedágio', ['valor' => $pedagioEstimado]);
            }

            // Processar legs (segmentos da rota)
            $legs = [];
            if (isset($route['legs'])) {
                foreach ($route['legs'] as $leg) {
                    $legDuracaoStr = $leg['duration'] ?? '0s';
                    $legDuracaoSegundos = (int) rtrim($legDuracaoStr, 's');
                    
                    $legs[] = [
                        'distancia_metros' => $leg['distanceMeters'] ?? 0,
                        'distancia_km' => round(($leg['distanceMeters'] ?? 0) / 1000, 2),
                        'duracao_segundos' => $legDuracaoSegundos,
                        'duracao_texto' => $this->formatarDuracao($legDuracaoSegundos)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'distancia_km' => round($distanciaKm, 2),
                'distancia_metros' => $distanciaMetros,
                'duracao_segundos' => $duracaoSegundos,
                'polyline' => $route['polyline']['encodedPolyline'] ?? '',
                'pedagio' => $pedagioInfo,
                'legs' => $legs
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao calcular rota', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formata duração em segundos para texto legível
     */
    private function formatarDuracao($segundos)
    {
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        
        if ($horas > 0) {
            return sprintf('%dh %dmin', $horas, $minutos);
        }
        
        return sprintf('%dmin', $minutos);
    }
}

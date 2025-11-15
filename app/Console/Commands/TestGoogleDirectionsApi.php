<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class TestGoogleDirectionsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:routes-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se a Google Routes API está funcionando e retorna informações de pedágios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando Google Routes API...');
        $this->newLine();

        $apiKey = config('services.google_maps_api_key');
        
        if (!$apiKey) {
            $this->error('❌ API Key não configurada!');
            $this->warn('Configure GOOGLE_MAPS_API_KEY no arquivo .env');
            return 1;
        }

        $this->info('✅ API Key encontrada: ' . substr($apiKey, 0, 20) . '...');
        $this->newLine();

        // Teste: São Paulo -> Rio de Janeiro (tem vários pedágios)
        $this->info('📍 Testando rota com pedágios: São Paulo, SP → Rio de Janeiro, RJ');
        
        $body = [
            'origin' => [
                'address' => 'São Paulo, SP, Brazil'
            ],
            'destination' => [
                'address' => 'Rio de Janeiro, RJ, Brazil'
            ],
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_AWARE',
            'languageCode' => 'pt-BR',
            'units' => 'METRIC',
            'extraComputations' => ['TOLLS']  // Solicitar cálculo de pedágios
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.legs.duration,routes.legs.distanceMeters,routes.travelAdvisory.tollInfo,routes.legs.travelAdvisory.tollInfo'
            ])->timeout(15)->post('https://routes.googleapis.com/directions/v2:computeRoutes', $body);
            
            if (!$response->successful()) {
                $this->error('❌ Erro na requisição HTTP: ' . $response->status());
                $errorBody = $response->json();
                
                if (isset($errorBody['error'])) {
                    $this->error('Detalhes: ' . json_encode($errorBody['error'], JSON_PRETTY_PRINT));
                }
                
                return 1;
            }

            $data = $response->json();
            
            $this->newLine();
            $this->info('📊 Resposta da API:');
            
            if (empty($data['routes'])) {
                $this->error('❌ Nenhuma rota encontrada');
                return 1;
            }

            $route = $data['routes'][0];
            
            $this->info('✅ SUCESSO! A Routes API está funcionando!');
            $this->newLine();
            
            // Distância
            $distanciaMetros = $route['distanceMeters'] ?? 0;
            $distanciaKm = $distanciaMetros / 1000;
            $this->info('📏 Distância: ' . number_format($distanciaKm, 2, ',', '.') . ' km');
            
            // Duração
            $duracaoStr = $route['duration'] ?? '0s';
            $duracaoSegundos = (int) rtrim($duracaoStr, 's');
            $horas = floor($duracaoSegundos / 3600);
            $minutos = floor(($duracaoSegundos % 3600) / 60);
            $this->info('⏱️  Duração: ' . $horas . 'h ' . $minutos . 'min');
            
            // Pedágios
            $this->newLine();
            if (isset($route['travelAdvisory']['tollInfo'])) {
                $this->info('🛣️  PEDÁGIOS DETECTADOS:');
                $tollInfo = $route['travelAdvisory']['tollInfo'];
                
                // Mostrar estrutura completa para debug
                $this->line('   Estrutura do tollInfo:');
                $this->line('   ' . json_encode($tollInfo, JSON_PRETTY_PRINT));
                
                if (isset($tollInfo['estimatedPrice'])) {
                    $this->newLine();
                    $this->info('   💰 Preços disponíveis:');
                    foreach ($tollInfo['estimatedPrice'] as $index => $price) {
                        $currency = $price['currencyCode'] ?? 'UNKNOWN';
                        $valor = floatval($price['units'] ?? 0);
                        if (isset($price['nanos'])) {
                            $valor += floatval($price['nanos']) / 1000000000;
                        }
                        
                        $this->line('   ' . ($index + 1) . '. ' . $currency . ': ' . number_format($valor, 2, ',', '.'));
                        
                        if ($currency === 'BRL') {
                            $this->info('      ✓ Usando este valor (Real Brasileiro)');
                        }
                    }
                } else {
                    $this->warn('   ⚠️  Pedágios detectados mas sem informação de preço (estimatedPrice vazio)');
                }
            } else {
                $this->warn('⚠️  Sem informação de pedágios da API');
                $this->line('   A rota pode:');
                $this->line('   • Não ter pedágios');
                $this->line('   • A API não retornou dados de pedágio para esta região');
                $this->line('   • O campo routes.travelAdvisory.tollInfo está vazio');
                $estimativa = $distanciaKm * 0.10;
                $this->newLine();
                $this->line('   💡 Estimativa manual: R$ ' . number_format($estimativa, 2, ',', '.') . ' (R$ 0,10/km)');
            }
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Erro na requisição: ' . $e->getMessage());
            $this->newLine();
            $this->warn('🚨 A Routes API não está habilitada!');
            $this->newLine();
            $this->info('📖 Como resolver:');
            $this->line('1. Acesse: https://console.cloud.google.com/apis/library/routes-backend.googleapis.com');
            $this->line('2. Clique em "Ativar"');
            $this->line('3. Aguarde 2-3 minutos');
            $this->line('4. Execute este comando novamente');
            return 1;
        }
    }
}

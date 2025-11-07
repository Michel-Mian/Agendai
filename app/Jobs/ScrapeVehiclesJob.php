<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Throwable;

class ScrapeVehiclesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $params;
    public $timeout = 900; // 15 minutos
    
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    
    public function handle(): void
    {
        $cacheKey = $this->params['cache_key'];
        $lockKey = "vehicles_scraping_lock:{$cacheKey}";
        $lock = Cache::lock($lockKey, 900);
        
        if ($lock->get()) {
            try {
                // Verificar se já existe resultado válido
                $existing = DB::table('veiculos_cache')
                    ->where('cache_key', $cacheKey)
                    ->whereNotNull('result_json')
                    ->where('result_json', '!=', 'null')
                    ->first();
                
                if ($existing) {
                    Log::info('Cache válido já existe para veículos', [
                        'cache_key' => $cacheKey
                    ]);
                    return;
                }
                
                Log::info('ScrapeVehiclesJob handle iniciado', ['cache_key' => $cacheKey, 'params' => $this->params]);

                // Marcar como processando
                DB::table('veiculos_cache')->updateOrInsert(
                    ['cache_key' => $cacheKey],
                    [
                        'result_json' => null,
                        'status' => 'processing',
                        'started_at' => now(),
                        'updated_at' => now()
                    ]
                );
                
                // Executar scraping
                $this->executeScraping($cacheKey);

                Log::info('ScrapeVehiclesJob finalizou executeScraping', ['cache_key' => $cacheKey]);
                
            } catch (\Exception $e) {
                Log::error('Erro no ScrapeVehiclesJob', [
                    'cache_key' => $cacheKey,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Marcar como falho
                DB::table('veiculos_cache')->updateOrInsert(
                    ['cache_key' => $cacheKey],
                    [
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                        'updated_at' => now()
                    ]
                );
                
                throw $e;
                
            } finally {
                $lock->release();
            }
        }
    }
    
    private function executeScraping($cacheKey)
    {
        Log::info('Iniciando scraping de veículos', [
            'cache_key' => $cacheKey,
            'params' => $this->params
        ]);
        
        $request = $this->params['params'];
        
        // Detectar Python
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $pythonCmd = $this->detectPython($isWindows);
        
        if (!$pythonCmd) {
            Log::error('Python não encontrado');
            $this->saveFallbackCache($cacheKey, 'Python não encontrado no sistema');
            throw new \Exception('Python não encontrado');
        }
        
        // Caminho do script
        $scriptPath = base_path('scripts/webscraping/scrapingRentCars.py');
        
        if (!file_exists($scriptPath)) {
            Log::error('Script Python não encontrado', [
                'path' => $scriptPath
            ]);
            $this->saveFallbackCache($cacheKey, 'Script não encontrado');
            throw new \Exception('Script não encontrado');
        }
        
        // Preparar parâmetros
        $localRetirada = $request['local_retirada'];
        $dataRetirada = $request['data_retirada'];
        $horaRetirada = $request['hora_retirada'];
        $dataDevolucao = $request['data_devolucao'];
        $horaDevolucao = $request['hora_devolucao'];
        
        // Montar comando
        $command = [
            $pythonCmd,
            $scriptPath,
            $localRetirada,
            $dataRetirada,
            $horaRetirada,
            $dataDevolucao,
            $horaDevolucao
        ];
        
        Log::info('Executando comando Python', [
            'command' => implode(' ', $command),
            'script_path_exists' => file_exists($scriptPath),
            'python_cmd' => $pythonCmd
        ]);
        
        // Executar com timeout de 15 minutos
        $result = Process::timeout(900)->run($command);

        Log::info('Process execution finished', [
            'successful' => $result->successful(),
            'exit_code' => $result->exitCode(),
            'output_len' => strlen($result->output() ?? ''),
            'error_output_len' => strlen($result->errorOutput() ?? '')
        ]);

        if ($result->successful()) {
            $output = $result->output();
            
            Log::info('Script executado com sucesso', [
                'output_length' => strlen($output),
                'output_preview' => substr($output, 0, 800)
            ]);
            
            if (empty(trim($output))) {
                Log::error('Output vazio do script');
                $this->saveFallbackCache($cacheKey, 'Nenhum resultado retornado');
                return;
            }
            
            try {
                // Parse do JSON
                $dados = json_decode($output, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
                }
                
                // Extrair informações
                $veiculos = $dados['veiculos'] ?? [];
                $alerta = $dados['alerta'] ?? null;
                
                Log::info('Resultados processados', [
                    'total_veiculos' => count($veiculos),
                    'tem_alerta' => !is_null($alerta)
                ]);
                
                // Salvar no cache
                DB::table('veiculos_cache')->updateOrInsert(
                    ['cache_key' => $cacheKey],
                    [
                        'result_json' => json_encode($veiculos),
                        'status' => 'completed',
                        'local_original' => $alerta['local_original'] ?? null,
                        'local_alternativo' => $alerta['local_alternativo'] ?? null,
                        'distancia_km' => $alerta['distancia'] ?? null,
                        'updated_at' => now()
                    ]
                );
                
                Log::info('Cache de veículos salvo com sucesso', [
                    'cache_key' => $cacheKey,
                    'veiculos_count' => count($veiculos)
                ]);
                
            } catch (\Exception $e) {
                Log::error('Erro ao processar output', [
                    'error' => $e->getMessage(),
                    'output_preview' => substr($output, 0, 500)
                ]);
                
                $this->saveFallbackCache($cacheKey, $e->getMessage());
                throw $e;
            }
            
        } else {
            Log::error('Falha na execução do script', [
                'exit_code' => $result->exitCode(),
                'output' => $result->output(),
                'error_output' => $result->errorOutput()
            ]);
            
            $this->saveFallbackCache($cacheKey, 'Falha na execução: ' . $result->errorOutput());
            throw new \Exception('Script falhou: ' . $result->exitCode());
        }
    }
    
    private function detectPython($isWindows)
    {
        $commands = $isWindows ? ['python', 'py'] : ['python3', 'python'];
        
        foreach ($commands as $cmd) {
            $result = Process::run("$cmd --version");
            if ($result->successful()) {
                Log::info('Python detectado', [
                    'command' => $cmd,
                    'version' => trim($result->output())
                ]);
                return $cmd;
            }
        }
        
        return null;
    }
    
    private function saveFallbackCache($cacheKey, $errorMessage)
    {
        try {
            DB::table('veiculos_cache')->updateOrInsert(
                ['cache_key' => $cacheKey],
                [
                    'result_json' => json_encode([]),
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'updated_at' => now()
                ]
            );
            
            Log::info('Cache de fallback salvo', [
                'cache_key' => $cacheKey
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar fallback', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function failed(Throwable $exception): void
    {
        $cacheKey = $this->params['cache_key'];
        
        Log::error('ScrapeVehiclesJob falhou permanentemente', [
            'cache_key' => $cacheKey,
            'error' => $exception->getMessage()
        ]);
        
        DB::table('veiculos_cache')->updateOrInsert(
            ['cache_key' => $cacheKey],
            [
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'result_json' => json_encode([]),
                'updated_at' => now()
            ]
        );
    }
}

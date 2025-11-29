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

class ScrapeInsuranceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;
    public $timeout = 600;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function handle(): void
    {
        $cacheKey = $this->params['cache_key'];
        $lockKey = "scraping_lock:{$cacheKey}";
        $lock = Cache::lock($lockKey, 360); // Aumentar timeout

        if ($lock->get()) {
            try {
                // Verificar se já existe resultado válido
                $existing = DB::table('seguros_cache')
                    ->where('cache_key', $cacheKey)
                    ->whereNotNull('result_json')
                    ->where('result_json', '!=', 'null')
                    ->first();
                    
                if ($existing) {
                    Log::info('Cache válido já existe, pulando execução');
                    return;
                }

                // Marcar como processando
                DB::table('seguros_cache')->updateOrInsert(
                    ['cache_key' => $cacheKey],
                    [
                        'result_json' => null, 
                        'status' => 'processing', 
                        'updated_at' => now(),
                        'started_at' => now() // Adicionar controle de tempo
                    ]
                );

                $this->executeScraping($cacheKey);
                
            } catch (\Exception $e) {
                Log::error('Erro no ScrapeInsuranceJob', [
                    'cache_key' => $cacheKey,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Limpar cache corrompido
                DB::table('seguros_cache')->where('cache_key', $cacheKey)->delete();
                
            } finally {
                $lock->release();
            }
        }
    }

    private function executeScraping($cacheKey)
    {
        Log::info('Iniciando execução de scraping', ['cache_key' => $cacheKey]);

        $request = $this->params['params'];
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $pythonCmd = $this->detectPython($isWindows);

        if (!$pythonCmd) {
            Log::error('Python não encontrado no sistema');
            $this->saveFallbackCache($cacheKey);
            // Lança uma exceção para que o job falhe imediatamente
            throw new \Exception('Comando Python não pôde ser encontrado no sistema.');
        }

        $scriptPath = base_path('scripts/webscraping/scrapingSP.py');
        if (!file_exists($scriptPath)) {
            Log::error('Script Python não encontrado', ['path' => $scriptPath]);
            $this->saveFallbackCache($cacheKey);
            throw new \Exception('O script de scraping não foi encontrado no caminho esperado: ' . $scriptPath);
        }

        $destino = $request['destino'] ?? 2;
        $data_ida = $request['data_ida'] ?? date('Y-m-d');
        $data_volta = $request['data_volta'] ?? date('Y-m-d', strtotime('+7 days'));

        $mapDestinoTexto = [
            1 => 'América do Norte',
            2 => 'Europa',
            4 => 'América do Sul', 
            5 => 'África',
            6 => 'Ásia',
            7 => 'Oceania',
            11 => 'Oriente Médio',
            12 => 'Argentina',      
            13 => 'Internacional',  
            14 => 'América Central'
        ];
        $destinoTexto = $mapDestinoTexto[$destino] ?? 'Europa';

        // Monta o comando como um array para segurança
        $command = [
            $pythonCmd,
            $scriptPath,
            $destinoTexto,
            $data_ida,
            $data_volta,
            "Matheus", // Exemplo de nome
            "matheus@email.com", // Exemplo de email
            "11999999999" // Exemplo de telefone
        ];

        Log::info('Executando comando Python com o facade Process', ['command' => implode(' ', $command)]);

        // Executa o processo com um timeout de 5 minutos (300 segundos)
        $result = Process::timeout(300)->run($command);

        // Verifica se o processo foi executado com sucesso
        if ($result->successful()) {
            $output = $result->output();

            Log::info('Processo Python finalizado com sucesso', [
                'exit_code' => $result->exitCode(),
                'output_length' => strlen($output),
                'has_delimiter' => strpos($output, '=====') !== false
            ]);

            if (empty(trim($output)) || strpos($output, '=====') === false) {
                Log::error('Script Python retornou um output vazio ou inválido', [
                    'output' => substr($output, 0, 500),
                    'error_output' => $result->errorOutput()
                ]);
                $this->saveFallbackCache($cacheKey);
                return;
            }

            try {
                $parsed = app('App\Http\Controllers\TripController')->parseOutput($output);

                Log::info('Resultado final do ScrapeInsuranceJob', [
                    'cache_key' => $cacheKey,
                    'total_seguros' => count($parsed)
                ]);

                DB::table('seguros_cache')->updateOrInsert(
                    ['cache_key' => $cacheKey],
                    [
                        'result_json' => json_encode($parsed),
                        'status' => 'completed',
                        'updated_at' => now()
                    ]
                );

                Log::info('Cache de seguros salvo com sucesso', [
                    'cache_key' => $cacheKey,
                    'seguros_count' => count($parsed)
                ]);

            } catch (\Exception $e) {
                Log::error('Erro ao processar o output do Python', [
                    'error' => $e->getMessage(),
                    'output_preview' => substr($output, 0, 200)
                ]);
                $this->saveFallbackCache($cacheKey);
                // Lança a exceção para que o job seja marcado como falho
                throw $e;
            }

        } else {
            // O processo falhou
            Log::error('Falha na execução do script Python', [
                'exit_code' => $result->exitCode(),
                'output' => $result->output(),
                'error_output' => $result->errorOutput() // Captura a saída de erro (stderr)
            ]);

            $this->saveFallbackCache($cacheKey);
            
            // Lança uma exceção para que o Laravel saiba que o job falhou
            // Isso fará com que o job seja reenfileirado ou movido para a tabela 'failed_jobs'
            throw new \Exception('O script de scraping falhou. Exit Code: ' . $result->exitCode() . ' - Erro: ' . $result->errorOutput());
        }
    }

    public function failed(Throwable $exception): void
    {
        $cacheKey = $this->params['cache_key'];

        // Loga o erro real que causou a falha
        Log::error('ScrapeInsuranceJob falhou permanentemente', [
            'cache_key' => $cacheKey,
            'error' => $exception->getMessage()
        ]);

        // Atualiza a tabela de cache para indicar a falha
        DB::table('seguros_cache')->updateOrInsert(
            ['cache_key' => $cacheKey],
            [
                'status' => 'failed', // Novo status
                'result_json' => json_encode(['error' => $exception->getMessage()]),
                'updated_at' => now()
            ]
        );
    }

    private function detectPython($isWindows)
    {
        $pythonCommands = $isWindows ? ['python', 'py', 'python3'] : ['python3', 'python'];
        
        foreach ($pythonCommands as $cmd) {
            $testCmd = $isWindows ? "where $cmd 2>nul" : "command -v $cmd 2>/dev/null";
            exec($testCmd, $output, $returnCode);
            
            if ($returnCode === 0) {
                // Testar se realmente executa
                $testPython = $isWindows ? "$cmd --version 2>nul" : "$cmd --version 2>/dev/null";
                exec($testPython, $versionOutput, $versionCode);
                
                if ($versionCode === 0) {
                    Log::info("Python encontrado e testado: $cmd");
                    return $cmd;
                }
            }
        }
        
        return null;
    }

    private function saveFallbackCache($cacheKey)
    {
        try {
            DB::table('seguros_cache')->updateOrInsert(
                ['cache_key' => $cacheKey],
                [
                    'result_json' => json_encode([]),
                    'updated_at' => now()
                ]
            );
            Log::info('Cache de fallback salvo para evitar loops');
        } catch (\Exception $cacheError) {
            Log::error('Erro crítico ao salvar cache de fallback', ['error' => $cacheError->getMessage()]);
        }
    }
}
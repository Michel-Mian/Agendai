<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculos;
use App\Models\Viagens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VehiclesController extends Controller
{
    /**
     * Exibe a tela de busca de veículos
     */
    public function index(Request $request)
    {
        $tripId = session('trip_id');
        $viagem = null;
        
        if ($tripId) {
            $viagem = Viagens::with(['destinos', 'veiculos'])->find($tripId);
        }
        
        return view('vehicles', [
            'title' => 'Aluguel de Veículos',
            'viagem' => $viagem
        ]);
    }

    /**
     * Display the vehicle search form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('vehicles');
    }
    
    /**
     * AJAX: Inicia busca de veículos (similar ao sistema de seguros)
     */
    public function searchVehiclesAjax(Request $request)
    {
        try {
            Log::info('searchVehiclesAjax iniciado', [
                'request_data' => $request->all()
            ]);
            
            // Validação
            $validated = $request->validate([
                'local_retirada' => 'required|string',
                'data_retirada' => 'required|date',
                'hora_retirada' => 'required|string|size:5', // HH:MM
                'data_devolucao' => 'required|date|after_or_equal:data_retirada',
                'hora_devolucao' => 'required|string|size:5'
            ]);
            
            // Gerar cache key
            $cacheKey = $this->generateCacheKey($validated);
            
            Log::info('Cache key gerado', ['cache_key' => $cacheKey]);
            
            // Verificar cache existente
            try {
                Log::info('Tentando buscar registro de cache no BD', ['cache_key' => $cacheKey]);
                $cacheRow = DB::table('veiculos_cache')->where('cache_key', $cacheKey)->first();
                Log::info('Consulta de cache realizada', ['cache_key' => $cacheKey, 'found' => (bool) $cacheRow]);
            } catch (\Exception $e) {
                Log::error('Erro ao consultar veiculos_cache', ['error' => $e->getMessage(), 'cache_key' => $cacheKey]);
                // Continuar para tentar disparar o job mesmo se o BD estiver temporariamente indisponível
                $cacheRow = null;
            }
            
            // SE CACHE COMPLETO EXISTE
            if ($cacheRow && $cacheRow->status === 'completed' && !is_null($cacheRow->result_json)) {
                Log::info('🔍 Cache completo encontrado', [
                    'cache_key' => $cacheKey,
                    'status' => $cacheRow->status,
                    'result_json_length' => strlen($cacheRow->result_json)
                ]);
                
                try {
                    $veiculos = json_decode($cacheRow->result_json, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('❌ Erro ao decodificar JSON do cache', [
                            'cache_key' => $cacheKey,
                            'json_error' => json_last_error_msg()
                        ]);
                        $veiculos = [];
                    }
                    
                    if (!is_array($veiculos)) {
                        Log::warning('⚠️ Veículos não é array, convertendo para array vazio', [
                            'cache_key' => $cacheKey,
                            'type' => gettype($veiculos)
                        ]);
                        $veiculos = [];
                    }
                    
                    $response = [
                        'status' => 'concluido',
                        'veiculos' => $veiculos,
                        'alerta' => $cacheRow->local_alternativo ? [
                            'local_original' => $cacheRow->local_original,
                            'local_alternativo' => $cacheRow->local_alternativo,
                            'distancia' => $cacheRow->distancia_km
                        ] : null
                    ];
                    
                    Log::info('✅ Retornando cache válido', [
                        'cache_key' => $cacheKey,
                        'veiculos_count' => count($veiculos),
                        'response_status' => $response['status'],
                        'has_alerta' => !is_null($response['alerta'])
                    ]);
                    
                    return response()->json($response);
                } catch (\Exception $e) {
                    Log::error('❌ Erro ao processar cache completo', [
                        'cache_key' => $cacheKey,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Cache corrompido, deletar e reprocessar
                    DB::table('veiculos_cache')->where('cache_key', $cacheKey)->delete();
                    Log::info('🗑️ Cache corrompido deletado, reprocessando...', ['cache_key' => $cacheKey]);
                }
            }
            
            // SE ESTÁ PROCESSANDO
            if ($cacheRow && $cacheRow->status === 'processing') {
                $startedAt = Carbon::parse($cacheRow->started_at ?? $cacheRow->updated_at);
                $elapsedMinutes = $startedAt->diffInMinutes(now());
                
                Log::info('⏳ Cache em processamento', [
                    'cache_key' => $cacheKey,
                    'started_at' => $startedAt,
                    'elapsed_minutes' => $elapsedMinutes
                ]);
                
                // Verificar timeout (15 minutos)
                if ($elapsedMinutes > 15) {
                    Log::warning('⏰ Processo travado detectado (timeout 15min)', [
                        'cache_key' => $cacheKey,
                        'started_at' => $startedAt,
                        'elapsed_minutes' => $elapsedMinutes
                    ]);
                    
                    // Limpar e reiniciar
                    DB::table('veiculos_cache')->where('cache_key', $cacheKey)->delete();
                    Log::info('🗑️ Cache travado deletado', ['cache_key' => $cacheKey]);
                } else {
                    // Ainda processando validamente
                    Log::info('⏳ Retornando status carregando', [
                        'cache_key' => $cacheKey,
                        'elapsed_minutes' => $elapsedMinutes
                    ]);
                    
                    return response()->json([
                        'status' => 'carregando',
                        'veiculos' => [],
                        'message' => 'Buscando veículos disponíveis...'
                    ]);
                }
            }
            
            // SE FALHOU
            if ($cacheRow && $cacheRow->status === 'failed') {
                Log::info('Cache com status failed encontrado', [
                    'cache_key' => $cacheKey,
                    'error_message' => $cacheRow->error_message
                ]);
                
                return response()->json([
                    'error' => 'A busca por veículos falhou',
                    'message' => $cacheRow->error_message,
                    'status' => 'failed'
                ], 500);
            }
            
            // INICIAR NOVO PROCESSO
            Log::info('Nenhum cache válido encontrado, iniciando novo processo', [
                'cache_key' => $cacheKey,
                'cache_exists' => (bool) $cacheRow,
                'cache_status' => $cacheRow->status ?? 'null'
            ]);
            
            $lockKey = "vehicles_scraping_lock:{$cacheKey}";
            $lock = null;
            $lockSupported = true;
            try {
                $lock = Cache::lock($lockKey, 900); // 15 minutos
                Log::info('Lock criado com sucesso', ['lock_key' => $lockKey]);
            } catch (\Throwable $e) {
                // Alguns drivers de cache (ex: database) não suportam locks
                Log::warning('Cache::lock não suportado pelo driver atual', ['error' => $e->getMessage()]);
                $lockSupported = false;
            }

            Log::info('Tentando adquirir lock para scraping de veículos', [
                'cache_key' => $cacheKey,
                'lock_key' => $lockKey,
                'lock_supported' => $lockSupported
            ]);

            if ($lockSupported && $lock && $lock->get()) {
                try {
                    Log::info('Lock adquirido com sucesso', ['cache_key' => $cacheKey]);

                    // Criar registro de cache em processing
                    $ok = DB::table('veiculos_cache')->updateOrInsert(
                        ['cache_key' => $cacheKey],
                        [
                            'result_json' => null,
                            'status' => 'processing',
                            'started_at' => now(),
                            'updated_at' => now()
                        ]
                    );

                    Log::info('Registro de cache atualizado para processing', [
                        'cache_key' => $cacheKey,
                        'update_ok' => (bool) $ok
                    ]);

                    // Disparar job assíncrono
                    Log::info('Dispatching ScrapeVehiclesJob', ['cache_key' => $cacheKey, 'params' => $validated]);
                    \App\Jobs\ScrapeVehiclesJob::dispatch([
                        'cache_key' => $cacheKey,
                        'params' => $validated
                    ]);

                    Log::info('Job de scraping de veículos disparado', [
                        'cache_key' => $cacheKey
                    ]);

                } catch (\Exception $e) {
                    Log::error('Erro ao tentar disparar job de scraping', ['cache_key' => $cacheKey, 'error' => $e->getMessage()]);
                    throw $e;
                } finally {
                    try {
                        $lock->release();
                        Log::info('Lock liberado', ['cache_key' => $cacheKey]);
                    } catch (\Exception $e) {
                        Log::warning('Falha ao liberar lock', ['cache_key' => $cacheKey, 'error' => $e->getMessage()]);
                    }
                }
            } else {
                if (! $lockSupported) {
                    Log::warning('Lock não suportado; prosseguindo sem lock', ['cache_key' => $cacheKey]);
                } else {
                    Log::warning('Não foi possível adquirir lock para scraping (outro processo pode estar rodando)', [
                        'cache_key' => $cacheKey,
                        'lock_key' => $lockKey
                    ]);
                    // Mesmo que não tenha conseguido lock, prosseguimos para tentar enfileirar
                }

                try {
                    Log::info('Preparando registro de cache em processing (sem lock)', ['cache_key' => $cacheKey]);
                    DB::table('veiculos_cache')->updateOrInsert(
                        ['cache_key' => $cacheKey],
                        [
                            'result_json' => null,
                            'status' => 'processing',
                            'started_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Erro ao criar registro processing sem lock', ['error' => $e->getMessage(), 'cache_key' => $cacheKey]);
                }

                // Disparar job mesmo sem lock
                try {
                    Log::info('Dispatching ScrapeVehiclesJob (sem lock)', ['cache_key' => $cacheKey, 'params' => $validated]);
                    \App\Jobs\ScrapeVehiclesJob::dispatch([
                        'cache_key' => $cacheKey,
                        'params' => $validated
                    ]);
                    Log::info('Job de scraping de veículos disparado (sem lock)', ['cache_key' => $cacheKey]);
                } catch (\Exception $e) {
                    Log::error('Erro ao dispatchar job sem lock', ['error' => $e->getMessage(), 'cache_key' => $cacheKey]);
                }
            }
            
            return response()->json([
                'veiculos' => [],
                'status' => 'carregando'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Dados inválidos',
                'details' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro crítico em searchVehiclesAjax', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Gera chave de cache única baseada nos parâmetros
     */
    private function generateCacheKey(array $params)
    {
        $hashData = implode('|', [
            $params['local_retirada'],
            $params['data_retirada'],
            $params['hora_retirada'],
            $params['data_devolucao'],
            $params['hora_devolucao']
        ]);
        
        return 'vehicles_' . md5($hashData);
    }
    
    /**
     * AJAX: Salva veículo selecionado para a viagem
     */
    public function saveVehicleForTrip(Request $request)
    {
        try {
            $validated = $request->validate([
                'fk_id_viagem' => 'required|integer|exists:viagens,pk_id_viagem',
                'veiculo_data' => 'required|array'
            ]);
            
            $viagemId = $validated['fk_id_viagem'];
            $veiculoData = $validated['veiculo_data'];
            
            // Verificar permissão
            $viagem = Viagens::findOrFail($viagemId);
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }
            
            // Desmarcar veículos anteriores
            Veiculos::where('fk_id_viagem', $viagemId)
                ->update(['is_selected' => false]);
            
            // Criar/atualizar veículo selecionado
            $veiculo = Veiculos::create([
                'fk_id_viagem' => $viagemId,
                'nome_veiculo' => $veiculoData['nome'] ?? 'N/A',
                'categoria' => $veiculoData['categoria'] ?? null,
                'imagem_url' => $veiculoData['imagem'] ?? null,
                'passageiros' => $veiculoData['configuracoes']['passageiros'] ?? null,
                'malas' => $veiculoData['configuracoes']['malas'] ?? null,
                'ar_condicionado' => $veiculoData['configuracoes']['ar_condicionado'] ?? false,
                'cambio' => $veiculoData['configuracoes']['cambio'] ?? null,
                'quilometragem' => $veiculoData['configuracoes']['quilometragem'] ?? null,
                'diferenciais' => json_encode($veiculoData['diferenciais'] ?? []),
                'tags' => json_encode($veiculoData['tags'] ?? []),
                'endereco_retirada' => $veiculoData['local_retirada']['endereco'] ?? null,
                'tipo_local' => $veiculoData['local_retirada']['tipo'] ?? null,
                'nome_local' => $veiculoData['local_retirada']['nome'] ?? null,
                'locadora_nome' => $veiculoData['locadora']['nome'] ?? null,
                'locadora_logo' => $veiculoData['locadora']['logo'] ?? null,
                'avaliacao_locadora' => $veiculoData['locadora']['avaliacao'] ?? null,
                'preco_total' => $veiculoData['preco']['total'] ?? null,
                'preco_diaria' => $veiculoData['preco']['diaria'] ?? null,
                'link_reserva' => $veiculoData['link_continuar'] ?? null,
                'is_selected' => true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Veículo salvo com sucesso',
                'veiculo' => $veiculo
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao salvar veículo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar veículo'
            ], 500);
        }
    }
    
    /**
     * Retorna veículos da viagem
     */
    public function getVehiclesByTrip($trip_id)
    {
        try {
            $viagem = Viagens::findOrFail($trip_id);
            
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }
            
            $veiculos = Veiculos::where('fk_id_viagem', $trip_id)->get();
            
            return response()->json([
                'success' => true,
                'veiculos' => $veiculos
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar veículos', [
                'error' => $e->getMessage(),
                'trip_id' => $trip_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar veículos'
            ], 500);
        }
    }

    /**
     * Remove veículo da viagem
     */
    public function destroy($id)
    {
        try {
            $veiculo = Veiculos::findOrFail($id);
            
            // Verificar se o veículo pertence a uma viagem do usuário logado
            $viagem = Viagens::findOrFail($veiculo->fk_id_viagem);
            
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }
            
            $veiculo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Veículo removido com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao excluir veículo', [
                'error' => $e->getMessage(),
                'vehicle_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover veículo'
            ], 500);
        }
    }

    /**
     * Retorna viagens do usuário
     */
    public function getUserTrips()
    {
        try {
            $viagens = Viagens::where('fk_id_usuario', auth()->id())
                ->orderBy('data_inicio_viagem', 'desc')
                ->get(['pk_id_viagem', 'nome_viagem', 'data_inicio_viagem', 'data_final_viagem']);
            
            return response()->json([
                'success' => true,
                'viagens' => $viagens
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao buscar viagens do usuário', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar viagens'
            ], 500);
        }
    }
}

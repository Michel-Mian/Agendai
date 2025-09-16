<?php

namespace App\Http\Controllers;

use App\Models\PontoInteresse;
use App\Models\Hotel;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Viagens;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        // Para desenvolvimento - remove em produção
        //session(['trip_id' => 1]);
        
        $dataInicio = null;
        $dataFim = null;
        $filtrosObjetivo = null;
        $nomeObjetivo = null;
        $destino = null;
        $origem = null;
        
        if (session()->has('trip_id')) {
            $tripId = session('trip_id');
            $viagem = Viagens::findOrFail($tripId);
            $dataInicio = $viagem?->data_inicio_viagem;
            $dataFim = $viagem?->data_final_viagem;
            $destino = $viagem?->destino_viagem;
            $origem = $viagem?->origem_viagem;
        }
        
        // Verificar se há filtros de objetivo na URL
        if ($request->has('filters')) {
            try {
                $filtrosObjetivo = json_decode(urldecode($request->get('filters')), true);
                $nomeObjetivo = $request->get('objective', 'Filtros por Objetivo');
                
                Log::info('Filtros de objetivo recebidos:', [
                    'filtros_raw' => $request->get('filters'),
                    'filtros_decoded' => $filtrosObjetivo,
                    'nome_objetivo' => $nomeObjetivo
                ]);
            } catch (\Exception $e) {
                Log::warning('Erro ao decodificar filtros de objetivo:', ['error' => $e->getMessage()]);
            }
        }
        
        Log::info('Página explore carregada', [
            'trip_id' => session('trip_id'),
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'destino' => $destino,
            'origem' => $origem,
            'filtros_objetivo' => $filtrosObjetivo,
            'nome_objetivo' => $nomeObjetivo
        ]);
        
        return view('explore', [
            'title' => 'Explorar',
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
            'hasTrip' => session()->has('trip_id'),
            'destino' => $destino,
            'origem' => $origem,
            'filtrosObjetivo' => $filtrosObjetivo,
            'nomeObjetivo' => $nomeObjetivo,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        Log::info('=== ADICIONANDO PONTO DE INTERESSE ===');
        Log::info('Dados recebidos:', $request->all());
        
        $tripId = session('trip_id');
        
        if (!$tripId) {
            Log::error('Trip ID não encontrado na sessão');
            return response()->json(['error' => 'Nenhuma viagem ativa'], 400);
        }

        $validator = Validator::make($request->all(), [
            'nome_ponto_interesse' => 'required|string|max:100',
            'placeid_ponto_interesse' => 'required|string|max:100',
            'desc_ponto_interesse' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'categoria' => 'nullable|string|max:100',
            'hora_ponto_interesse' => 'nullable|string',
            'data_ponto_interesse' => 'nullable|date',
            'data_check_in' => 'nullable|date',
            'data_check_out' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::error('Erro de validação:', $validator->errors()->toArray());
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $validated = $validator->validated();
            
            // Se a categoria for 'lodging', buscar dados na SerpAPI (Google Places) e salvar em hotels
            if (isset($validated['categoria']) && strtolower($validated['categoria']) === 'lodging') {
                $apiKey = env('SERPAPI_KEY');
                $latitude = $validated['latitude'];
                $longitude = $validated['longitude'];
                $nomeHotel = $validated['nome_ponto_interesse'];
                $dataCheckIn = $validated['data_check_in'] ?? null;
                $dataCheckOut = $validated['data_check_out'] ?? null;

                // Buscar detalhes do hotel na SerpAPI (Google Hotels)
                $params = [
                    'engine' => 'google_hotels',
                    'q' => $nomeHotel,
                    'check_in_date' => $dataCheckIn,
                    'check_out_date' => $dataCheckOut,
                    'api_key' => $apiKey,
                    'hl' => 'pt-br',
                    'gl' => 'br',
                    'currency' => $user->currency,
                ];
                $response = Http::get('https://serpapi.com/search', $params);
                $json = $response->json();
                $hotelData = null;
                // 1. Se vier como array de hotéis (busca)
                if ($response->successful() && isset($json['properties']) && is_array($json['properties']) && count($json['properties']) > 0) {
                    // Busca o hotel mais próximo pelo nome (pode melhorar usando latitude/longitude)
                    $hotelData = collect($json['properties'])->first(function($h) use ($nomeHotel) {
                        return isset($h['name']) && stripos($h['name'], $nomeHotel) !== false;
                    }) ?? $json['properties'][0];
                }
                // 2. Se vier como objeto único (detalhe do hotel)
                elseif ($response->successful() && (isset($json['type']) && $json['type'] === 'hotel')) {
                    $hotelData = $json;
                }
                // 3. Fallback para hotels_results (antigo)
                elseif ($response->successful() && isset($json['hotels_results'][0])) {
                    $hotelData = $json['hotels_results'][0];
                }
                else {
                    Log::error('Erro ao buscar dados do hotel na SerpAPI', [
                        'params' => $params,
                        'response_status' => $response->status(),
                        'response_body' => $json
                    ]);
                    return response()->json([
                        'error' => 'Não foi possível obter dados do hotel na SerpAPI.',
                        'serpapi_response' => $json
                    ], 500);
                }


                // Extrair preço do hotel de diferentes formatos possíveis e converter para float
                $preco = null;
                // 1. Tenta extracted_price (preferencial, já é numérico)
                if (isset($hotelData['extracted_price']) && is_numeric($hotelData['extracted_price'])) {
                    $preco = floatval($hotelData['extracted_price']);
                }
                // 2. Tenta rate_per_night/extracted_lowest
                elseif (isset($hotelData['rate_per_night']['extracted_lowest']) && is_numeric($hotelData['rate_per_night']['extracted_lowest'])) {
                    $preco = floatval($hotelData['rate_per_night']['extracted_lowest']);
                }
                // 3. Tenta price (string com símbolo)
                elseif (isset($hotelData['price'])) {
                    $precoStr = $hotelData['price'];
                    $precoStr = preg_replace('/[^\d,\.]/', '', $precoStr);
                    $precoStr = str_replace(',', '.', $precoStr);
                    if (is_numeric($precoStr)) {
                        $preco = floatval($precoStr);
                    }
                }
                // 4. Tenta outros campos possíveis (lowest, etc)
                elseif (isset($hotelData['rate_per_night']['lowest']) && is_numeric($hotelData['rate_per_night']['lowest'])) {
                    $preco = floatval($hotelData['rate_per_night']['lowest']);
                } elseif (isset($hotelData['total_rate']['lowest']) && is_numeric($hotelData['total_rate']['lowest'])) {
                    $preco = floatval($hotelData['total_rate']['lowest']);
                }
                // 5. Tenta featured_prices/prices/ads arrays
                elseif (isset($hotelData['featured_prices']) && is_array($hotelData['featured_prices'])) {
                    foreach ($hotelData['featured_prices'] as $fp) {
                        if (isset($fp['rate_per_night']['extracted_lowest']) && is_numeric($fp['rate_per_night']['extracted_lowest'])) {
                            $preco = floatval($fp['rate_per_night']['extracted_lowest']);
                            break;
                        } elseif (isset($fp['rate_per_night']['lowest']) && is_numeric($fp['rate_per_night']['lowest'])) {
                            $preco = floatval($fp['rate_per_night']['lowest']);
                            break;
                        } elseif (isset($fp['extracted_price']) && is_numeric($fp['extracted_price'])) {
                            $preco = floatval($fp['extracted_price']);
                            break;
                        } elseif (isset($fp['price'])) {
                            $precoStr = preg_replace('/[^\d,\.]/', '', $fp['price']);
                            $precoStr = str_replace(',', '.', $precoStr);
                            if (is_numeric($precoStr)) {
                                $preco = floatval($precoStr);
                                break;
                            }
                        }
                    }
                }
                elseif (isset($hotelData['prices']) && is_array($hotelData['prices'])) {
                    foreach ($hotelData['prices'] as $fp) {
                        if (isset($fp['rate_per_night']['extracted_lowest']) && is_numeric($fp['rate_per_night']['extracted_lowest'])) {
                            $preco = floatval($fp['rate_per_night']['extracted_lowest']);
                            break;
                        } elseif (isset($fp['rate_per_night']['lowest']) && is_numeric($fp['rate_per_night']['lowest'])) {
                            $preco = floatval($fp['rate_per_night']['lowest']);
                            break;
                        } elseif (isset($fp['extracted_price']) && is_numeric($fp['extracted_price'])) {
                            $preco = floatval($fp['extracted_price']);
                            break;
                        } elseif (isset($fp['price'])) {
                            $precoStr = preg_replace('/[^\d,\.]/', '', $fp['price']);
                            $precoStr = str_replace(',', '.', $precoStr);
                            if (is_numeric($precoStr)) {
                                $preco = floatval($precoStr);
                                break;
                            }
                        }
                    }
                }
                elseif (isset($hotelData['ads']) && is_array($hotelData['ads'])) {
                    foreach ($hotelData['ads'] as $ad) {
                        if (isset($ad['extracted_price']) && is_numeric($ad['extracted_price'])) {
                            $preco = floatval($ad['extracted_price']);
                            break;
                        } elseif (isset($ad['price'])) {
                            $precoStr = preg_replace('/[^\d,\.]/', '', $ad['price']);
                            $precoStr = str_replace(',', '.', $precoStr);
                            if (is_numeric($precoStr)) {
                                $preco = floatval($precoStr);
                                break;
                            }
                        }
                    }
                }
                // Se não encontrar, define como null e loga o JSON para análise
                if ($preco === null) {
                    Log::warning('Preço do hotel não encontrado em nenhum campo esperado', [
                        'hotelData' => $hotelData,
                        'json' => $json
                    ]);
                }

                Log::info('Dados do hotel extraídos da SerpAPI:', $hotelData);
                $hotel = Hotel::create([
                    'nome_hotel' => $hotelData['name'] ?? $nomeHotel,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'avaliacao' => $hotelData['overall_rating'] ?? $hotelData['rating'] ?? null,
                    'data_check_in' => $dataCheckIn,
                    'data_check_out' => $dataCheckOut,
                    'preco' => $preco,
                    'image_url' => $hotelData['thumbnail'] ?? ($hotelData['images'][0]['thumbnail'] ?? null),
                    'fk_id_viagem' => $tripId,
                ]);
                Log::info('Hotel salvo com sucesso:', ['id' => $hotel->pk_id_hotel]);
                return response()->json([
                    'success' => true,
                    'message' => 'Hotel adicionado com sucesso!',
                    'hotel' => $hotel
                ]);
            } else {
                // Salvar ponto de interesse comum
                $ponto = PontoInteresse::create([
                    'nome_ponto_interesse' => $validated['nome_ponto_interesse'],
                    'placeid_ponto_interesse' => $validated['placeid_ponto_interesse'],
                    'desc_ponto_interesse' => $validated['desc_ponto_interesse'] ?? null,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'categoria' => $validated['categoria'] ?? null,
                    'hora_ponto_interesse' => $validated['hora_ponto_interesse'] ?? null,
                    'data_ponto_interesse' => $validated['data_ponto_interesse'] ?? null,
                    'fk_id_viagem' => $tripId,
                ]);
                Log::info('Ponto de interesse salvo com sucesso:', ['id' => $ponto->pk_id_ponto_interesse]);
                return response()->json([
                    'success' => true,
                    'message' => 'Ponto de interesse adicionado com sucesso!',
                    'ponto' => $ponto
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao salvar ponto:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function destroy($id)
    {
        Log::info('=== REMOVENDO PONTO DE INTERESSE ===');
        Log::info('ID para deletar:', ['id' => $id]);
        
        try {
            $ponto = PontoInteresse::findOrFail($id);
            
            // Verifica se o ponto pertence à viagem da sessão atual
            $tripId = session('trip_id');
            if ($ponto->fk_id_viagem != $tripId) {
                Log::warning('Tentativa de deletar ponto de outra viagem:', [
                    'ponto_viagem' => $ponto->fk_id_viagem,
                    'session_viagem' => $tripId
                ]);
                return response()->json(['error' => 'Não autorizado'], 403);
            }
            
            $ponto->delete();
            
            Log::info('Ponto deletado com sucesso:', ['id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ponto removido com sucesso!'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Ponto não encontrado:', ['id' => $id]);
            return response()->json(['error' => 'Ponto não encontrado'], 404);
            
        } catch (\Exception $e) {
            Log::error('Erro ao deletar ponto:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    public function show()
    {
        $tripId = session('trip_id');
        if (!$tripId) {
            return response()->json([]);
        }
        $pontos = PontoInteresse::where('fk_id_viagem', $tripId)->get();
        return response()->json($pontos);
    }

    public function updateHorario(Request $request, $id)
    {
        // Log para debug - primeiro log para ver se chega aqui
        \Log::info('=== UPDATE HORARIO CONTROLLER EXECUTADO ===');
        \Log::info('ID recebido:', [$id]);
        
        try {
            // Validação sem regex primeiro para testar
            $validated = $request->validate([
                'novo_horario' => 'required|string|size:5',
            ], [
                'novo_horario.required' => 'O horário é obrigatório',
                'novo_horario.size' => 'O horário deve ter 5 caracteres (HH:MM)'
            ]);
            
            // Validação manual do formato
            $horario = $validated['novo_horario'];
            if (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $horario)) {
                \Log::error('Formato de horário inválido:', [$horario]);
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de horário inválido. Use HH:MM'
                ], 422);
            }

            \Log::info('Validação passou:', $validated);

            $ponto = \App\Models\PontoInteresse::findOrFail($id);
            \Log::info('Ponto encontrado:', ['id' => $ponto->id, 'nome' => $ponto->nome_ponto_interesse]);
            
            $ponto->hora_ponto_interesse = $validated['novo_horario'];
            $ponto->save();

            \Log::info('Horário atualizado com sucesso para:', [$validated['novo_horario']]);

            // Retornar JSON para requisições AJAX
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                \Log::info('Retornando resposta JSON');
                return response()->json([
                    'success' => true,
                    'message' => 'Horário do ponto de interesse alterado com sucesso!',
                    'novo_horario' => $validated['novo_horario']
                ]);
            }

            \Log::info('Retornando redirect');
            return redirect()->back()->with('success', 'Horário do ponto de interesse alterado com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error details:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'error' => 'Dados de validação inválidos',
                    'validation_errors' => $e->errors(),
                    'input_received' => $request->all()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            \Log::error('General error in updateHorario:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro interno: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Erro ao alterar horário.');
        }
    }

    
    public function setTripIdAndRedirect($id, Request $request)
    {
        session(['trip_id' => $id]);
        
        // Verificar se há parâmetros de filtro para preservar
        $queryParams = [];
        if ($request->has('filters')) {
            $queryParams['filters'] = $request->get('filters');
        }
        if ($request->has('objective')) {
            $queryParams['objective'] = $request->get('objective');
        }
        
        Log::info('Redirecionando para explore com trip_id definido:', [
            'trip_id' => $id,
            'query_params' => $queryParams
        ]);
        
        // Redirecionar preservando os parâmetros de filtro
        if (!empty($queryParams)) {
            return redirect()->route('explore', $queryParams);
        }
        
        return redirect()->route('explore');
    }
}

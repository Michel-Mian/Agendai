<?php

namespace App\Http\Controllers;

use App\Models\PontoInteresse;
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
        ]);

        if ($validator->fails()) {
            Log::error('Erro de validação:', $validator->errors()->toArray());
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $validated = $validator->validated();
            
            $ponto = PontoInteresse::create([
                'nome_ponto_interesse' => $validated['nome_ponto_interesse'],
                'placeid_ponto_interesse' => $validated['placeid_ponto_interesse'],
                'desc_ponto_interesse' => $validated['desc_ponto_interesse'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'categoria' => $validated['categoria'] ?? null,
                'hora_ponto_interesse' => $validated['hora_ponto_interesse'] ?? null,
                'data_ponto_interesse' => $validated['data_ponto_interesse'] ?? now()->toDateString(),
                'fk_id_viagem' => $tripId,
            ]);
            
            Log::info('Ponto salvo com sucesso:', ['id' => $ponto->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ponto adicionado com sucesso!',
                'ponto' => $ponto
            ]);
            
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

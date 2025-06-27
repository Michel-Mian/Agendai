<?php

namespace App\Http\Controllers;

use App\Models\PontoInteresse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExploreController extends Controller
{
    public function index()
    {
        session(['trip_id' => 1]);
        if (session()->has('trip_id')) {
            $tripId = session('trip_id');
            $viagem = \App\Models\Viagens::find($tripId);
            $dataInicio = $viagem ? $viagem->data_inicio_viagem : null;
            $dataFim = $viagem ? $viagem->data_final_viagem : null;
        } else {
            $dataInicio = null;
            $dataFim = null;
        }
        Log::info('Página explore carregada', ['trip_id' => session('trip_id')]);
        return view('explore', [
            'title' => 'Explorar',
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
        ]);
    }

    public function store(Request $request)
    {
        // Log TUDO que chega aqui
        Log::info('=== MÉTODO STORE EXECUTADO ===');
        Log::info('Request Method: ' . $request->method());
        Log::info('Request URL: ' . $request->url());
        Log::info('Request Full URL: ' . $request->fullUrl());
        Log::info('Request Headers: ', $request->headers->all());
        Log::info('Request All Data: ', $request->all());
        Log::info('Request JSON: ', $request->json()->all() ?? []);
        Log::info('Request Input: ', $request->input());
        Log::info('Session Data: ', session()->all());
        
        $tripId = session('trip_id');
        Log::info('Trip ID: ' . $tripId);
        
        if (!$tripId) {
            Log::error('Trip ID não encontrado na sessão');
            return response()->json(['error' => 'Nenhuma viagem ativa'], 400);
        }

        $data = $request->all();
        Log::info('Dados para validação: ', $data);

        $validator = Validator::make($data, [
            'nome_ponto_interesse' => 'required|string|max:100',
            'desc_ponto_interesse' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'categoria' => 'nullable|string|max:100',
            'hora_ponto_interesse' => 'nullable|string',
            'data_ponto_interesse' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::error('Erro de validação: ', $validator->errors()->toArray());
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        Log::info('Dados validados: ', $validated);

        try {
            $ponto = new PontoInteresse();
            $ponto->nome_ponto_interesse = $validated['nome_ponto_interesse'];
            $ponto->desc_ponto_interesse = $validated['desc_ponto_interesse'] ?? null;
            $ponto->latitude = $validated['latitude'];
            $ponto->longitude = $validated['longitude'];
            $ponto->categoria = $validated['categoria'] ?? null;
            $ponto->hora_ponto_interesse = $validated['hora_ponto_interesse'] ?? null;
            $ponto->data_ponto_interesse = $validated['data_ponto_interesse'] ?? now()->toDateString();
            $ponto->fk_id_viagem = $tripId;
            
            Log::info('Tentando salvar ponto: ', $ponto->toArray());
            $ponto->save();
            Log::info('Ponto salvo com sucesso! ID: ' . $ponto->id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Ponto adicionado com sucesso!',
                'ponto' => $ponto
            ]);
            
        } catch (\Exception $e) {
            Log::error('ERRO AO SALVAR: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $tripId = session('trip_id');
        Log::info('Buscando pontos para trip_id: ' . $tripId);
        
        if (!$tripId) {
            return response()->json([]);
        }
        
        $pontos = PontoInteresse::where('fk_id_viagem', $tripId)->get();
        Log::info('Pontos encontrados: ' . $pontos->count());
        
        return response()->json($pontos);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Log::info('=== MÉTODO DESTROY EXECUTADO ===');
        Log::info('ID para deletar: ' . $id);
        
        try {
            $ponto = PontoInteresse::findOrFail($id);
            Log::info('Ponto encontrado: ', $ponto->toArray());
            
            $ponto->delete();
            Log::info('Ponto deletado com sucesso');
            
            return response()->json([
                'success' => true,
                'message' => 'Ponto removido com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao deletar ponto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao remover ponto'], 500);
        }
    }

    // Método de teste para verificar se as rotas funcionam
    public function test(Request $request)
    {
        Log::info('=== MÉTODO TEST EXECUTADO ===');
        Log::info('Dados recebidos: ', $request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Rota de teste funcionando!',
            'data' => $request->all()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PontoInteresse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PontosInteresseController extends Controller
{
    /**
     * Atualiza o status de completado de um ponto de interesse
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleCompleted(Request $request, $id): JsonResponse
    {
        try {
            // Validação dos dados recebidos
            $validator = Validator::make($request->all(), [
                'is_completed' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Busca o ponto de interesse
            $pontoInteresse = PontoInteresse::find($id);

            if (!$pontoInteresse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ponto de interesse não encontrado'
                ], 404);
            }

            // Verifica se o usuário autenticado tem acesso a este ponto de interesse
            // através da viagem associada
            $user = $request->user();
            if ($pontoInteresse->viagem->fk_id_usuario !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para atualizar este ponto de interesse'
                ], 403);
            }

            // Atualiza o campo is_completed
            $pontoInteresse->is_completed = $request->is_completed;
            $pontoInteresse->save();

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'data' => [
                    'id' => $pontoInteresse->pk_id_ponto_interesse,
                    'nome' => $pontoInteresse->nome_ponto_interesse,
                    'is_completed' => $pontoInteresse->is_completed
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status do ponto de interesse',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ViagemCarro;
use App\Models\Viagens;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ViagemCarroController extends Controller
{
    /**
     * Retorna todas as viagens de carro do usuário autenticado
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Obtém o usuário autenticado
            $user = $request->user();
            
            // Busca todas as viagens de carro que pertencem ao usuário
            // através da relação viagem -> user
            $viagensCarro = ViagemCarro::with(['viagem' => function($query) {
                $query->select('pk_id_viagem', 'nome_viagem', 'fk_id_usuario', 'origem_viagem', 'data_inicio_viagem', 'data_final_viagem');
            }])
            ->whereHas('viagem', function($query) use ($user) {
                $query->where('fk_id_usuario', $user->id);
            })
            ->get();
            
            // Formata os dados para retornar
            $data = $viagensCarro->map(function($viagemCarro) {
                return [
                    'id' => $viagemCarro->pk_id_viagem_carro,
                    'viagem' => [
                        'id' => $viagemCarro->viagem->pk_id_viagem,
                        'nome' => $viagemCarro->viagem->nome_viagem,
                        'origem' => $viagemCarro->viagem->origem_viagem,
                        'data_inicio' => $viagemCarro->viagem->data_inicio_viagem,
                        'data_final' => $viagemCarro->viagem->data_final_viagem,
                    ],
                    'autonomia_veiculo_km_l' => $viagemCarro->autonomia_veiculo_km_l,
                    'tipo_combustivel' => $viagemCarro->tipo_combustivel,
                    'preco_combustivel_litro' => $viagemCarro->preco_combustivel_litro,
                    'distancia_total_km' => $viagemCarro->distancia_total_km,
                    'pedagio_estimado' => $viagemCarro->pedagio_estimado,
                    'pedagio_oficial' => $viagemCarro->pedagio_oficial,
                    'combustivel_estimado_litros' => $viagemCarro->combustivel_estimado_litros,
                    'custo_combustivel_estimado' => $viagemCarro->custo_combustivel_estimado,
                    'duracao_segundos' => $viagemCarro->duracao_segundos,
                    'duracao_texto' => $viagemCarro->duracao_texto,
                    'custo_total' => $viagemCarro->custo_total,
                    'rota_detalhada' => $viagemCarro->rota_detalhada,
                    'created_at' => $viagemCarro->created_at,
                    'updated_at' => $viagemCarro->updated_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $data->count(),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar viagens de carro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna a viagem de carro de uma viagem específica
     * 
     * @param int $id ID da viagem
     * @return JsonResponse
     */
    public function getByViagem(int $id): JsonResponse
    {
        try {
            // Busca a viagem
            $viagem = Viagens::find($id);
            
            if (!$viagem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Viagem não encontrada',
                ], 404);
            }
            
            // Busca a viagem de carro relacionada a esta viagem
            $viagemCarro = ViagemCarro::with(['viagem' => function($query) {
                $query->select('pk_id_viagem', 'nome_viagem', 'fk_id_usuario', 'origem_viagem', 'data_inicio_viagem', 'data_final_viagem');
            }])
            ->where('fk_id_viagem', $id)
            ->first();
            
            if (!$viagemCarro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Viagem de carro não encontrada para esta viagem',
                ], 404);
            }
            
            // Formata os dados para retornar
            $data = [
                'id' => $viagemCarro->pk_id_viagem_carro,
                'viagem' => [
                    'id' => $viagemCarro->viagem->pk_id_viagem,
                    'nome' => $viagemCarro->viagem->nome_viagem,
                    'origem' => $viagemCarro->viagem->origem_viagem,
                    'data_inicio' => $viagemCarro->viagem->data_inicio_viagem,
                    'data_final' => $viagemCarro->viagem->data_final_viagem,
                ],
                'autonomia_veiculo_km_l' => $viagemCarro->autonomia_veiculo_km_l,
                'tipo_combustivel' => $viagemCarro->tipo_combustivel,
                'preco_combustivel_litro' => $viagemCarro->preco_combustivel_litro,
                'distancia_total_km' => $viagemCarro->distancia_total_km,
                'pedagio_estimado' => $viagemCarro->pedagio_estimado,
                'pedagio_oficial' => $viagemCarro->pedagio_oficial,
                'combustivel_estimado_litros' => $viagemCarro->combustivel_estimado_litros,
                'custo_combustivel_estimado' => $viagemCarro->custo_combustivel_estimado,
                'duracao_segundos' => $viagemCarro->duracao_segundos,
                'duracao_texto' => $viagemCarro->duracao_texto,
                'custo_total' => $viagemCarro->custo_total,
                'rota_detalhada' => $viagemCarro->rota_detalhada,
                'created_at' => $viagemCarro->created_at,
                'updated_at' => $viagemCarro->updated_at,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar viagem de carro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

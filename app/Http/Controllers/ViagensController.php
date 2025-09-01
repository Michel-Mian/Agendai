<?php
namespace App\Http\Controllers;
use App\Models\Viagens;
use App\Models\User;
use App\Models\Viajantes;
use App\Models\Objetivos;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\PontoInteresse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
Carbon::setLocale('pt_BR');


class ViagensController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();
        $viagens = Viagens::with('viajantes')->where('fk_id_usuario', $user->id)->get();
        return view('viagens/myTrips', [
            'title' => 'Minhas Viagens',
            'viagens' => $viagens
        ]);
    }
    public function show($id, Request $request)
    {
        $pontos = PontoInteresse::where('fk_id_viagem', $id)
            ->orderBy('data_ponto_interesse')
            ->orderBy('hora_ponto_interesse')
            ->get();
        $viagem = Viagens::with([
            'viajantes',
            'voos',
            'objetivos',
            'user',
            'hotel'
        ])->findOrFail($id);

        // Busca notícias da SerpAPI
        $destino = $viagem->destino_viagem;
        $apiKey = env('SERPAPI_KEY');
        $categorias = [
            'Cultura' => "Cultura em $destino",
            'Saúde' => "Saúde em $destino",
            'Entretenimento' => "Entretenimento em $destino",
            'Esportes' => "Jogos de esporte em $destino",
            'Local' => "Notícias locais na região de $destino"
        ];
        $noticias = [];
        foreach ($categorias as $tipo => $query) {
            $params = [
                'engine' => 'google_news',
                'q' => $query,
                'hl' => 'pt-br',
                'api_key' => $apiKey,
            ];
            $url = 'https://serpapi.com/search.json?' . http_build_query($params);
            $response = @file_get_contents($url);
            $data = json_decode($response, true);

            $noticia = $data['news_results'][0] ?? null;
            if ($noticia) {
                // Formata a data, se existir
                if (isset($noticia['date'])) {
                    $dataLimpa = preg_replace('/, \+\d{4} UTC$/', '', $noticia['date']);
                    $carbonDate = Carbon::createFromFormat('m/d/Y, h:i A', trim($dataLimpa));
                    $noticia['date'] = $carbonDate->format('d/m/Y H:i');
                }
                $noticia['source_name'] = $noticia['source']['name'] ?? 'Fonte desconhecida';
                $noticia['source_icon'] = $noticia['source']['icon'] ?? '';
                $noticia['q'] = $query;
                unset($noticia['source']);
                $noticias[$tipo] = $noticia;
            }
        }

        // Busca eventos na SerpAPI
        $eventos = [];

        $params = [
            'engine' => 'google_events',
            'q' => 'Events in ' . $destino,
            'start' => 0,
            'api_key' => $apiKey
        ];

        $url = 'https://serpapi.com/search.json?' . http_build_query($params);
        $response = @file_get_contents($url);

        if ($response !== false) {
            $dados = json_decode($response, true);
            $eventosBrutos = $dados['events_results'] ?? [];

            // Formata a data dos eventos
            foreach ($eventosBrutos as $evento) {
                if (isset($evento['date']['start_date'])) {
                    try {
                        $dataFormatada = Carbon::parse($evento['date']['start_date'])->format('d/m/Y');
                        $evento['data_formatada'] = $dataFormatada;
                    } catch (\Exception $e) {
                        $evento['data_formatada'] = 'Data inválida';
                    }
                } else {
                    $evento['data_formatada'] = 'Data não informada';
                }

                $eventos[] = $evento;
            }

        }

        //Busca o clima na Open-Meteo
        $destino = $viagem->destino_viagem;
        $coordenadas = $this->getLatLngFromAddress($destino);
        $data_inicio = Carbon::parse($viagem->data_inicio_viagem)->format('Y-m-d');
        $data_fim = Carbon::parse($viagem->data_final_viagem)->format('Y-m-d');
        $hoje = Carbon::today();
        $diasDiferenca = $hoje->diffInDays($data_inicio, false);

        $climas = [];
        if ($diasDiferenca < 7) {
            if ($coordenadas) {
                $latitude = $coordenadas['lat'];
                $longitude = $coordenadas['lng'];
                $weatherUrl = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,wind_speed_10m_max,precipitation_sum,rain_sum,precipitation_probability_max&start_date={$data_inicio}&end_date={$data_fim}";
                $weatherResponse = file_get_contents($weatherUrl);
                $clima = json_decode($weatherResponse, true);

                $climas[] = $clima;
            } else {
                $clima = null;
            }
        } else {
            $clima = null;
        }


        return view('viagens/detailsTrip', [
            'title' => 'Detalhes da Viagem',
            'viagem' => $viagem,
            'viajantes' => $viagem->viajantes,
            'pontosInteresse' => $viagem->pontosInteresse,
            'voos' => $viagem->voos->filter(function($voo) {
                return is_object($voo) && $voo !== false;
            }),
            'objetivos' => $viagem->objetivos,
            'usuario' => $viagem->user,
            'hotel' => $viagem->hotel,
            'noticias' => $noticias,
            'eventos' => $eventos,
            'clima' => $clima,
            'seguros' => $viagem->seguros,
        ]);
    }


    public function destroyObjetivo($id)
    {
        $objetivo = \App\Models\Objetivos::findOrFail($id);
        $viagemId = $objetivo->fk_id_viagem;
        $objetivo->delete();
        return redirect()->route('viagens', ['id' => $viagemId])->with('success', 'Objetivo removido com sucesso!');
    }

    // Adiciona um novo objetivo à viagem
    public function addObjetivo(Request $request)
    {
        $request->validate([
            'nome_objetivo' => 'required|string|max:100',
            'viagem_id' => 'required|integer|exists:viagens,pk_id_viagem',
        ]);

        $objetivo = new \App\Models\Objetivos();
        $objetivo->nome = $request->input('nome_objetivo');
        $objetivo->fk_id_viagem = $request->input('viagem_id');
        $objetivo->save();

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'objetivo' => [
                    'id' => $objetivo->pk_id_objetivo,
                    'nome' => $objetivo->nome
                ]
            ]);
        }

        // Redireciona de volta para a página da viagem
        return redirect()->route('viagens', ['id' => $objetivo->fk_id_viagem])->with('success', 'Objetivo adicionado com sucesso!');
    }

    public function destroy($id): RedirectResponse
    {
        $viajante = Viajantes::findOrFail($id);
        $viagemId = $viajante->fk_id_viagem;
        $viajante->delete();
        return redirect()->route('viagens', ['id' => $viagemId]);
    }

    // Adiciona um novo viajante à viagem
    public function addViajante(\Illuminate\Http\Request $request)
    {
        $rules = [
            'nome_viajante' => 'required|string|max:100',
            'idade_viajante' => 'required|integer|min:0|max:127',
            'viagem_id' => 'required|integer|exists:viagens,pk_id_viagem',
        ];

        // Adiciona a regra de validação para 'responsavel_legal' (que é o ID do responsável)
        // apenas se a idade do novo viajante for menor que 18
        if ($request->input('idade_viajante') < 18) {
            // Garante que o responsável legal é obrigatório e existe na tabela de viajantes
            $rules['responsavel_legal'] = 'required|integer|exists:viajantes,pk_id_viajante';
        }

        $validated = $request->validate($rules);

        $viajante = new Viajantes();
        $viajante->nome = $validated['nome_viajante'];
        $viajante->idade = $validated['idade_viajante'];
        $viajante->fk_id_viagem = $validated['viagem_id'];

        // Se o viajante for menor de 18, atribui o ID do responsável legal
        if ($viajante->idade < 18) {
            $viajante->responsavel_viajante_id = $validated['responsavel_legal']; // O campo no BD é responsavel_viajante_id
        }

        $viajante->save();

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'viajante' => [
                    'id' => $viajante->pk_id_viajante,
                    'nome' => $viajante->nome,
                    'idade' => $viajante->idade,
                    'responsavel_viajante_id' => $viajante->responsavel_viajante_id ?? null, // Inclui o responsável no retorno AJAX, se existir
                ]
            ]);
        }

        // Redireciona de volta para a página da viagem
        return redirect()->route('viagens', ['id' => $viajante->fk_id_viagem])->with('success', 'Viajante adicionado com sucesso!');
    }

    private function getLatLngFromAddress($address)
    {
        $apiKey = env('GOOGLE_GEOCODING_KEY');
        $addressEncoded = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$addressEncoded}&key={$apiKey}";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ];
        }
        return null;
    }

    /**
     * Atualiza campos específicos de uma viagem
     */
    public function updateViagem(Request $request, $id)
    {
        try {
            $viagem = Viagens::findOrFail($id);
            
            // Verificar se o usuário tem permissão para editar
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para editar esta viagem.'
                ], 403);
            }

            // Regras de validação
            $rules = [];
            $messages = [];

            if ($request->has('destino_viagem')) {
                $rules['destino_viagem'] = 'required|string|max:255';
                $messages['destino_viagem.required'] = 'O destino é obrigatório.';
            }

            if ($request->has('origem_viagem')) {
                $rules['origem_viagem'] = 'required|string|max:255';
                $messages['origem_viagem.required'] = 'A origem é obrigatória.';
            }

            if ($request->has('data_inicio_viagem')) {
                $rules['data_inicio_viagem'] = 'required|date';
                $messages['data_inicio_viagem.required'] = 'A data de início é obrigatória.';
            }

            if ($request->has('data_final_viagem')) {
                $rules['data_final_viagem'] = 'required|date|after_or_equal:data_inicio_viagem';
                $messages['data_final_viagem.required'] = 'A data final é obrigatória.';
                $messages['data_final_viagem.after_or_equal'] = 'A data final deve ser posterior ou igual à data de início.';
            }

            if ($request->has('orcamento_viagem')) {
                $rules['orcamento_viagem'] = 'required|numeric|min:0';
                $messages['orcamento_viagem.required'] = 'O orçamento é obrigatório.';
                $messages['orcamento_viagem.min'] = 'O orçamento deve ser maior que zero.';
            }

            // Validar dados
            $validator = \Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Atualizar apenas os campos enviados
            $updateData = $request->only([
                'destino_viagem', 
                'origem_viagem', 
                'data_inicio_viagem', 
                'data_final_viagem', 
                'orcamento_viagem'
            ]);

            $viagem->update($updateData);

            Log::info('Viagem atualizada com sucesso', [
                'viagem_id' => $id,
                'user_id' => auth()->id(),
                'updated_fields' => array_keys($updateData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Viagem atualizada com sucesso!',
                'data' => $updateData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Viagem não encontrada.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar viagem', [
                'viagem_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Remove uma viagem e todos os dados relacionados
     */
    public function destroyViagem($id)
    {
        try {
            $viagem = Viagens::findOrFail($id);
            
            // Verificar se o usuário tem permissão para excluir
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir esta viagem.'
                ], 403);
            }

            // Iniciar transação para garantir integridade dos dados
            \DB::beginTransaction();

            try {
                // Excluir registros relacionados
                PontoInteresse::where('fk_id_viagem', $id)->delete();
                Hotel::where('fk_id_viagem', $id)->delete();
                $viagem->voos()->delete();
                $viagem->viajantes()->delete();
                $viagem->objetivos()->delete();
                
                // Excluir seguros relacionados se existir relacionamento
                if (method_exists($viagem, 'seguros')) {
                    $viagem->seguros()->delete();
                }
                
                // Excluir a viagem
                $viagem->delete();

                \DB::commit();

                \Log::info('Viagem excluída com sucesso', [
                    'viagem_id' => $id,
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Viagem excluída com sucesso!'
                ]);

            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Viagem não encontrada.'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Erro ao excluir viagem', [
                'viagem_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }
}
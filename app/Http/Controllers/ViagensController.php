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
        try {
            Log::info('Acessando detalhes da viagem', ['id' => $id, 'user_id' => auth()->id()]);
            
            // Buscar viagem com relacionamentos básicos
            $viagem = Viagens::with([
                'viajantes',
                'voos',
                'objetivos', 
                'user',
                'hotel',
                'seguros',
                'pontosInteresse' => function($query) {
                    $query->orderBy('data_ponto_interesse', 'asc')
                          ->orderBy('hora_ponto_interesse', 'asc');
                }
            ])->findOrFail($id);
            
            // Verificar permissões
            if ($viagem->fk_id_usuario !== auth()->id()) {
                Log::warning('Tentativa de acesso negada', ['viagem_id' => $id, 'user_id' => auth()->id()]);
                abort(403, 'Acesso negado');
            }

            // Buscar usuário
            $usuario = $viagem->user;
            
            // Dados básicos para a view
            $pontosOrdenados = $viagem->pontosInteresse;
            $viajantes = $viagem->viajantes;
            $objetivos = $viagem->objetivos;
            $voos = $viagem->voos->filter(function($voo) {
                return is_object($voo) && $voo !== false;
            });

            // Corrigir: Garantir que $hotel seja passado corretamente
            $hotel = $viagem->hotel;

            // Adicionar seguros
            $seguros = $viagem->seguros;

            // Inicializar eventos/notícias vazios (serão carregados via AJAX)
            $eventos = collect();

            // Estatísticas básicas 
            $estatisticas = [
                'total_viajantes' => $viajantes->count(),
                'total_pontos' => $pontosOrdenados->count(), 
                'total_objetivos' => $objetivos->count(),
                'orcamento_liquido' => $viagem->orcamento_viagem - $voos->sum('preco_voo'),
                'dias_viagem' => Carbon::parse($viagem->data_inicio_viagem)->diffInDays(Carbon::parse($viagem->data_final_viagem)) + 1
            ];

            Log::info('Dados carregados com sucesso', [
                'viagem_id' => $id,
                'pontos_count' => $pontosOrdenados->count(),
                'viajantes_count' => $viajantes->count(),
                'seguros_count' => $seguros->count(),
                'hotel_count' => $hotel ? $hotel->count() : 0
            ]);

            return view('viagens.detailsTrip', compact(
                'viagem', 
                'usuario', 
                'pontosOrdenados',
                'viajantes',
                'objetivos', 
                'voos',
                'hotel',
                'seguros',
                'eventos',
                'estatisticas'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar detalhes da viagem', [
                'viagem_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('myTrips')->with('error', 'Erro ao carregar detalhes da viagem: ' . $e->getMessage());
        }
    }

    /**
     * Calcula estatísticas básicas da viagem
     */
    private function calculateBasicStats($viagem)
    {
        $totalViajantes = $viagem->viajantes->count();
        $totalPontos = $viagem->pontosInteresse->count();
        $totalObjetivos = $viagem->objetivos->count();
        
        // Calcular orçamento líquido
        $gastoVoos = $viagem->voos->sum('preco_voo');
        $gastoHotel = $viagem->hotel ? $viagem->hotel->preco : 0;
        $orcamentoLiquido = $viagem->orcamento_viagem - $gastoVoos - $gastoHotel;
        
        // Calcular dias até a viagem
        $dataInicio = Carbon::parse($viagem->data_inicio_viagem);
        $diasRestantes = now()->diffInDays($dataInicio, false);
        
        return [
            'total_viajantes' => $totalViajantes,
            'total_pontos' => $totalPontos,
            'total_objetivos' => $totalObjetivos,
            'orcamento_liquido' => $orcamentoLiquido,
            'dias_restantes' => $diasRestantes,
            'dias_viagem' => $dataInicio->diffInDays(Carbon::parse($viagem->data_final_viagem)) + 1
        ];
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

    /**
     * Carrega dados de clima via AJAX
     */
    public function getWeatherData($id)
    {
        try {
            $viagem = Viagens::select('destino_viagem', 'fk_id_usuario')->findOrFail($id);
            
            // Verificar permissões
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }
            
            // Cache por 3 horas
            $cacheKey = "weather_data_{$id}";
            $weatherData = \Cache::remember($cacheKey, 10800, function() use ($viagem) {
                return $this->fetchWeatherFromAPI($viagem->destino_viagem);
            });

            return response()->json([
                'success' => true,
                'data' => $weatherData
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar dados do clima', ['viagem_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar clima'], 500);
        }
    }

    /**
     * Carrega notícias via AJAX
     */
    public function getNewsData($id)
    {
        try {
            $viagem = Viagens::select('destino_viagem', 'fk_id_usuario')->findOrFail($id);
            
            // Verificar permissões
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }
            
            // Cache por 6 horas
            $cacheKey = "news_data_{$id}";
            $newsData = \Cache::remember($cacheKey, 21600, function() use ($viagem) {
                return $this->fetchNewsFromAPI($viagem->destino_viagem);
            });

            return response()->json([
                'success' => true,
                'data' => $newsData
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar notícias', ['viagem_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar notícias'], 500);
        }
    }

    /**
     * Busca dados do clima de forma otimizada
     */
    private function fetchWeatherFromAPI($destino)
    {
        try {
            // Primeiro buscar coordenadas (com timeout menor)
            $geocodeUrl = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($destino) . "&limit=1&appid=" . env('OPENWEATHER_API_KEY', 'aa4dfd898f8821ac74f8c5dec5a7d2b4');
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 3, // Timeout agressivo de 3 segundos
                    'ignore_errors' => true
                ]
            ]);
            
            $geocodeResponse = @file_get_contents($geocodeUrl, false, $context);
            
            if (!$geocodeResponse) {
                throw new \Exception('Falha na geocodificação');
            }
            
            $geocodeData = json_decode($geocodeResponse, true);
            
            if (empty($geocodeData)) {
                throw new \Exception('Local não encontrado');
            }
            
            $latitude = $geocodeData[0]['lat'];
            $longitude = $geocodeData[0]['lon'];
            
            // Buscar dados do clima
            $weatherUrl = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,weathercode&timezone=America/Sao_Paulo&forecast_days=7";
            
            $weatherResponse = @file_get_contents($weatherUrl, false, $context);
            
            if (!$weatherResponse) {
                throw new \Exception('Falha ao buscar clima');
            }
            
            return json_decode($weatherResponse, true);
            
        } catch (\Exception $e) {
            // Retornar dados mockados em caso de erro
            return [
                'daily' => [
                    'time' => array_fill(0, 7, date('Y-m-d')),
                    'temperature_2m_max' => array_fill(0, 7, 25),
                    'temperature_2m_min' => array_fill(0, 7, 18),
                    'weathercode' => array_fill(0, 7, 0)
                ]
            ];
        }
    }

    /**
     * Busca notícias de forma otimizada
     */
    private function fetchNewsFromAPI($destino)
    {
        try {
            $apiKey = env('SERPAPI_KEY');
            if (!$apiKey) {
                throw new \Exception('API key não configurada');
            }

            $queries = [
                "turismo em $destino",
                "pontos turísticos $destino",
                "viagem $destino dicas"
            ];

            $allNews = [];
            
            foreach (array_slice($queries, 0, 2) as $query) { // Reduzir para 2 queries apenas
                try {
                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 4, // Timeout de 4 segundos
                            'ignore_errors' => true
                        ]
                    ]);
                    
                    $url = "https://serpapi.com/search.json?engine=google_news&q=" . urlencode($query) . "&api_key=$apiKey&num=3";
                    $response = @file_get_contents($url, false, $context);
                    
                    if ($response) {
                        $data = json_decode($response, true);
                        if (isset($data['news_results'])) {
                            $allNews = array_merge($allNews, array_slice($data['news_results'], 0, 2));
                        }
                    }
                } catch (\Exception $e) {
                    continue; // Continuar mesmo se uma query falhar
                }
            }

            return array_slice($allNews, 0, 6); // Máximo 6 notícias
            
        } catch (\Exception $e) {
            return []; // Retornar array vazio em caso de erro
        }
    }
}

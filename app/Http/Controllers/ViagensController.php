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
use Illuminate\Support\Facades\Http;
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
            $apiKey = config('services.openweather_api_key'); // Usando config() como boa prática
            if (!$apiKey) {
                throw new \Exception('Chave de API do OpenWeather não configurada.');
            }

            // Contexto para ignorar verificação SSL, se necessário
            $context = stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]);

            // 1. Geocodificação para obter coordenadas
            $geocodeUrl = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($destino) . "&limit=1&appid=" . $apiKey;
            $geocodeResponse = @file_get_contents($geocodeUrl, false, $context);
            if ($geocodeResponse === FALSE) {
                throw new \Exception('Não foi possível obter as coordenadas para o destino.');
            }
            $geocodeData = json_decode($geocodeResponse, true);
            if (empty($geocodeData)) {
                throw new \Exception('Destino não encontrado na API de geocodificação.');
            }
            $latitude = $geocodeData[0]['lat'];
            $longitude = $geocodeData[0]['lon'];

            // 2. Obter dados do clima usando as coordenadas
            $weatherUrl = "https://api.openweathermap.org/data/2.5/onecall?lat={$latitude}&lon={$longitude}&exclude=current,minutely,hourly,alerts&appid={$apiKey}&units=metric&lang=pt_br";
            $weatherResponse = @file_get_contents($weatherUrl, false, $context);
            if ($weatherResponse === FALSE) {
                throw new \Exception('Não foi possível obter os dados do clima.');
            }
            $weatherData = json_decode($weatherResponse, true);

            return $weatherData;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do clima (lógica antiga): ' . $e->getMessage());
            // Retorna nulo ou um array vazio para que a UI possa tratar o erro
            return null;
        }
    }

    /**
     * Busca notícias de forma otimizada
     */
    private function fetchNewsFromAPI($destino)
    {
        try {
            $apiKey = config('services.serp_api_key'); // Usando config() como boa prática
            if (!$apiKey) {
                throw new \Exception('Chave da SerpApi não configurada.');
            }

            $queries = [
                "turismo em $destino",
                "pontos turísticos $destino",
                "viagem $destino dicas"
            ];
            $allNews = [];

            // Contexto para ignorar verificação SSL
            $context = stream_context_create([
                'http' => ['timeout' => 4], // Timeout de 4 segundos
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]);

            foreach (array_slice($queries, 0, 2) as $query) {
                $url = "https://serpapi.com/search.json?engine=google_news&q=" . urlencode($query) . "&api_key=" . $apiKey . "&num=3";
                $response = @file_get_contents($url, false, $context);

                \Illuminate\Support\Facades\Log::info('Resposta crua da SerpApi:', ['response' => $response]);
                $data = json_decode($response, true);
                \Illuminate\Support\Facades\Log::info('Dados da SerpApi decodificados:', $data ?? ['error' => 'Falha ao decodificar JSON']);

                if ($response !== false) {
                    if (isset($data['news_results'])) {
                        $allNews = array_merge($allNews, array_slice($data['news_results'], 0, 2));
                    }
                }
            }
            return array_slice($allNews, 0, 6);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícias via SerpAPI (lógica antiga): ' . $e->getMessage());
            return []; // Retorna um array vazio em caso de erro
        }
    }

    public function showApi($id) {
        try{
            $viagem = Viagens::with([
                'viajantes', 'voos', 'objetivos', 'user', 'hotel', 'pontosInteresse', 'seguros'
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

            $viagemMobile = [
                'id' => $viagem->pk_id_viagem, 
                'destino' => $viagem->destino_viagem ?? '-',
                'pontosInteresse' => $viagem->pontosInteresse,
                'user' => $viagem->user,
                'seguros' => $viagem->seguros,
                'noticias' => $noticias,
                'eventos' => $eventos,
                'clima' => $clima,
                'viajantes' => $viagem->viajantes,
                'objetivos' => $viagem->objetivos,
                'voos' => $viagem->voos,
                'hotel' => $viagem->hotel,
                'orcamento' => $viagem->orcamento_viagem,
                'data_inicio' => $viagem->data_inicio_viagem,
                'data_fim' => $viagem->data_final_viagem,
                'created_at' => $viagem->created_at,
                'updated_at' => $viagem->updated_at,
            ];

            return response()->json($viagemMobile);

            }
            catch(\Exception $e){
                \Log::error('Erro ao buscar detalhes da viagem', ['erro' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Erro interno', 'error' => $e->getMessage()], 500);
            }
        }
}

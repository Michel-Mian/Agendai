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
use App\Models\Destinos;
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
                'veiculos',
                'destinos' => function($query) {
                    $query->orderBy('ordem_destino', 'asc');
                },
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
            $destinos = $viagem->destinos;
            $pontosOrdenados = $viagem->pontosInteresse;
            $viajantes = $viagem->viajantes;
            $objetivos = $viagem->objetivos;
            $voos = $viagem->voos->filter(function($voo) {
                return is_object($voo) && $voo !== false;
            });

            // Garantir que $hotel seja passado corretamente
            $hotel = $viagem->hotel;

            // Adicionar seguros
            $seguros = $viagem->seguros;

            // Adicionar veículos
            $veiculos = $viagem->veiculos;

            // Inicializar eventos/notícias vazios (serão carregados via AJAX)
            $eventos = collect();

            // Estatísticas básicas 
            $estatisticas = [
                'total_viajantes' => $viajantes->count(),
                'total_pontos' => $pontosOrdenados->count(), 
                'total_objetivos' => $objetivos->count(),
                'total_destinos' => $destinos->count(),
                'orcamento_liquido' => $viagem->orcamento_viagem - ($voos->sum('preco_voo') * $viajantes->count()) - (($seguros ?? collect())->sum(function($seguro) use ($viajantes) { 
                    return ($seguro->preco_pix ?? $seguro->preco_cartao ?? 0) * $viajantes->count(); 
                })) - ($hotel ? $hotel->sum(function($h) { 
                    $checkin = Carbon::parse($h->data_check_in);
                    $checkout = Carbon::parse($h->data_check_out);
                    $noites = $checkin->diffInDays($checkout);
                    return $h->preco * $noites;
                }) : 0),
                'dias_viagem' => Carbon::parse($viagem->data_inicio_viagem)->diffInDays(Carbon::parse($viagem->data_final_viagem)) + 1
            ];

            Log::info('Dados carregados com sucesso', [
                'viagem_id' => $id,
                'destinos_count' => $destinos->count(),
                'pontos_count' => $pontosOrdenados->count(),
                'viajantes_count' => $viajantes->count(),
                'seguros_count' => $seguros->count(),
                'veiculos_count' => $veiculos->count(),
                'hotel_count' => $hotel ? $hotel->count() : 0
            ]);

            return view('viagens.detailsTrip', compact(
                'viagem', 
                'usuario',
                'destinos',
                'pontosOrdenados',
                'viajantes',
                'objetivos', 
                'voos',
                'hotel',
                'seguros',
                'veiculos',
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
            'observacoes' => 'nullable|string|max:1000',
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
        $viajante->observacoes = $validated['observacoes'] ?? null;

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

    /**
     * Atualiza um viajante (nome e idade) via requisição assíncrona
     */
    public function updateViajante(Request $request, $id)
    {
        try {
            $viajante = Viajantes::findOrFail($id);

            // Verifica permissão: apenas o dono da viagem pode editar
            if ($viajante->fk_id_viagem) {
                $viagem = Viagens::find($viajante->fk_id_viagem);
                if ($viagem && $viagem->fk_id_usuario !== auth()->id()) {
                    return response()->json(['success' => false, 'message' => 'Você não tem permissão para editar este viajante.'], 403);
                }
            }


            $rules = [
                'nome' => 'required|string|max:100',
                'idade' => 'required|integer|min:0|max:127',
            ];

            // Se a idade enviada for menor que 18, o responsável é obrigatório
            if ($request->has('idade') && intval($request->input('idade')) < 18) {
                $rules['responsavel_viajante_id'] = 'required|integer|exists:viajantes,pk_id_viajante';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            $viajante->nome = $request->input('nome');
            $viajante->idade = $request->input('idade');

            // Se menor de 18, atribui o responsável (verifica pertence à mesma viagem e não é ele mesmo)
            if (intval($viajante->idade) < 18) {
                $respId = $request->input('responsavel_viajante_id');
                // verificar existência e pertença à mesma viagem
                $resp = Viajantes::find($respId);
                if (!$resp) {
                    return response()->json(['success' => false, 'message' => 'Responsável não encontrado.'], 422);
                }
                if ($resp->pk_id_viajante == $viajante->pk_id_viajante) {
                    return response()->json(['success' => false, 'message' => 'O responsável não pode ser o próprio viajante.'], 422);
                }
                if ($viajante->fk_id_viagem && $resp->fk_id_viagem != $viajante->fk_id_viagem) {
                    return response()->json(['success' => false, 'message' => 'O responsável deve pertencer à mesma viagem.'], 422);
                }
                $viajante->responsavel_viajante_id = $respId;
            } else {
                // Maior ou igual a 18: remove responsável
                $viajante->responsavel_viajante_id = null;
            }

            $viajante->save();

            return response()->json([
                'success' => true,
                'message' => 'Viajante atualizado com sucesso.',
                'viajante' => [
                    'id' => $viajante->pk_id_viajante,
                    'nome' => $viajante->nome,
                    'idade' => $viajante->idade,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Viajante não encontrado.'], 404);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar viajante', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erro interno. Tente novamente.'], 500);
        }
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

            if ($request->has('nome_viagem')) {
                $rules['nome_viagem'] = 'required|string|max:255';
                $messages['nome_viagem.required'] = 'O nome da viagem é obrigatório.';
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
                'nome_viagem', 
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

    public function getWeatherDataForDestination(Viagens $viagem, Destinos $destino)
    {
        if ($viagem->fk_id_usuario !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
        }

        try {
            $dataInicio = Carbon::parse($destino->data_chegada_destino)->startOfDay();
            $dataFim = Carbon::parse($destino->data_partida_destino)->endOfDay();
            $hoje = Carbon::today();

            if ($hoje->lt($dataInicio)) {
                // Antes do destino, não mostra clima
                return response()->json(['success' => true, 'data' => null, 'message' => 'A previsão do tempo estará disponível a partir do início deste destino.']);
            }
            if ($hoje->gt($dataFim)) {
                // Após o destino, não mostra clima
                return response()->json(['success' => true, 'data' => null, 'message' => 'A previsão do tempo não está mais disponível para este destino.']);
            }

            // Durante o destino, mostra clima normalmente
            $cacheKey = "weather_data_destino_{$destino->pk_id_destino}_{$dataInicio->format('Ymd')}_{$dataFim->format('Ymd')}";
            $weatherData = \Cache::remember($cacheKey, 7200, function () use ($destino, $dataInicio, $dataFim) {
                return $this->fetchWeatherFromAPIWithDates($destino->nome_destino, $dataInicio, $dataFim);
            });

            if (!$weatherData) {
                return response()->json(['success' => false, 'message' => 'Não foi possível obter os dados do clima.'], 500);
            }

            return response()->json(['success' => true, 'data' => $weatherData]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar clima para o destino', ['destino_id' => $destino->pk_id_destino, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar dados do clima.'], 500);
        }
    }

    // Busca clima para um destino entre datas específicas
    private function fetchWeatherFromAPIWithDates($destino, $dataInicio, $dataFim)
    {
        $coordinates = $this->getLatLngFromAddress($destino);
        if (!$coordinates) {
            Log::warning('Não foi possível obter coordenadas para o destino de clima.', ['destino' => $destino]);
            return null;
        }

        $lat = $coordinates['lat'];
        $lon = $coordinates['lng'];
        $start = Carbon::parse($dataInicio)->format('Y-m-d');
        $end = Carbon::parse($dataFim)->format('Y-m-d');
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,uv_index_max&timezone=auto&start_date={$start}&end_date={$end}";
        try {
            $response = \Http::timeout(10)->get($url);
            if ($response->successful()) {
                return $response->json();
            }
            Log::error('Falha na API Open-Meteo', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do clima na Open-Meteo', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getNewsDataForDestination(Viagens $viagem, Destinos $destino)
    {
        if ($viagem->fk_id_usuario !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
        }

        try {
            $cacheKey = "news_data_destino_{$destino->pk_id_destino}";
            $newsData = \Cache::remember($cacheKey, 21600, function () use ($destino) { // Cache por 6 horas
                return $this->fetchNewsFromAPI($destino->nome_destino);
            });

            return response()->json(['success' => true, 'data' => $newsData]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar notícias para o destino', ['destino_id' => $destino->pk_id_destino, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar notícias.'], 500);
        }
    }
     
    private function fetchWeatherFromAPI($destino)
    {
        $coordinates = $this->getLatLngFromAddress($destino);
        if (!$coordinates) {
            Log::warning('Não foi possível obter coordenadas para o destino de clima.', ['destino' => $destino]);
            return null;
        }

        $lat = $coordinates['lat'];
        $lon = $coordinates['lng'];
        
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,uv_index_max&timezone=auto";
        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json();
            }
            Log::error('Falha na API Open-Meteo', ['status' => $response->status(), 'body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do clima na Open-Meteo', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function fetchNewsFromAPI($destino)
    {
        $apiKey = config('services.serp_api_key');
        if (!$apiKey) {
            Log::error('Chave da SerpAPI não configurada.');
            return [];
        }

        $query = "notícias turismo em " . $destino;
        $url = "https://serpapi.com/search.json";

        try {
            $response = Http::get($url, [
                'engine' => 'google_news',
                'q' => $query,
                'api_key' => $apiKey,
                'num' => 7, // Pede 7 resultados
            ]);

            if ($response->successful() && isset($response->json()['news_results'])) {
                return $response->json()['news_results'];
            }
            
            Log::warning('Não foi possível buscar notícias na SerpAPI', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Erro ao chamar a SerpAPI', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getNewsData($id)
    {
        try {
            $viagem = Viagens::with('destinos')->findOrFail($id);
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }
            // Usa o primeiro destino como padrão, se existir
            $primeiroDestino = $viagem->destinos->first()->nome_destino ?? 'mundo';
            $newsData = $this->fetchNewsFromAPI($primeiroDestino);
            return response()->json(['success' => true, 'data' => $newsData]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar notícias (método antigo)', ['viagem_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar notícias'], 500);
        }
    }

    public function getWeatherData($id)
    {
        try {
            $viagem = Viagens::with('destinos')->findOrFail($id);
             if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }
            // Usa o primeiro destino como padrão
            $primeiroDestino = $viagem->destinos->first()->nome_destino ?? 'São Paulo';
            $weatherData = $this->fetchWeatherFromAPI($primeiroDestino);
            return response()->json(['success' => true, 'data' => $weatherData]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar clima (método antigo)', ['viagem_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao carregar clima'], 500);
        }
    }

    public function showApi($id) {
        try{
            $viagem = Viagens::with([
                'viajantes', 
                'voos', 
                'objetivos', 
                'user', 
                'hotel', 
                'pontosInteresse' => function($query) {
                    $query->orderBy('data_ponto_interesse', 'asc')
                          ->orderBy('hora_ponto_interesse', 'asc');
                },
                'seguros', 
                'destinos' => function($query) {
                    $query->orderBy('ordem_destino', 'asc');
                },
                'seguroSelecionado'
            ])->findOrFail($id);

            // TEMPORÁRIO: Verificação de permissão desabilitada para teste
            // Descomente as linhas abaixo quando configurar autenticação
            /*
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }
            */

            // Busca notícias da SerpAPI - usando o primeiro destino ou destino principal
            $primeiroDestino = $viagem->destinos->first();
            $nomeDestino = $primeiroDestino ? $primeiroDestino->nome_destino : 'Brasil';
            $apiKey = env('SERPAPI_KEY');
            $categorias = [
                'Cultura' => "Cultura em $nomeDestino",
                'Saúde' => "Saúde em $nomeDestino",
                'Entretenimento' => "Entretenimento em $nomeDestino",
                'Esportes' => "Jogos de esporte em $nomeDestino",
                'Local' => "Notícias locais na região de $nomeDestino"
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
                'q' => 'Events in ' . $nomeDestino,
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
            $coordenadas = $this->getLatLngFromAddress($nomeDestino);
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
                'nome_da_viagem' => $viagem->nome_viagem ?? '-',
                'origem_viagem' => $viagem->origem_viagem,
                'destinos' => $viagem->destinos,
                'pontosInteresse' => $viagem->pontosInteresse,
                'user' => $viagem->user,
                'seguros' => $viagem->seguros,
                'seguro_selecionado' => $viagem->seguroSelecionado,
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
                'estatisticas' => [
                    'total_viajantes' => $viagem->viajantes->count(),
                    'total_pontos' => $viagem->pontosInteresse->count(),
                    'total_objetivos' => $viagem->objetivos->count(),
                    'total_destinos' => $viagem->destinos->count(),
                    'total_voos' => $viagem->voos->count(),
                    'total_seguros' => $viagem->seguros->count(),
                    'total_hoteis' => $viagem->hotel->count(),
                    'orcamento_liquido' => $viagem->orcamento_viagem - $viagem->voos->sum('preco_voo'),
                    'dias_viagem' => Carbon::parse($viagem->data_inicio_viagem)->diffInDays(Carbon::parse($viagem->data_final_viagem)) + 1
                ]
            ];

            return response()->json($viagemMobile);

        } catch(\Exception $e){
            \Log::error('Erro ao buscar detalhes da viagem via API', [
                'viagem_id' => $id,
                'user_id' => auth()->check() ? auth()->id() : 'não autenticado',
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erro interno', 'error' => $e->getMessage()], 500);
        }
    }
    public function getEventsDataForDestination(Viagens $viagem, Destinos $destino)
    {
        try {
            // Parâmetros principais
            $city = $destino->nome_destino;
            $startDate = Carbon::parse($destino->data_chegada_destino)->format('Y-m-d');
            $endDate = Carbon::parse($destino->data_partida_destino)->format('Y-m-d');

            $serpApiKey = env('SERPAPI_KEY');
            if (!$serpApiKey) {
                return response()->json(['success' => false, 'message' => 'Chave SerpAPI não configurada.'], 500);
            }

            // Monta query para buscar eventos na cidade e período
            $params = [
                'engine' => 'google_events',
                'q' => "eventos em $city",
                'hl' => 'pt-br',
                'gl' => 'br',
                'api_key' => $serpApiKey,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
            $url = 'https://serpapi.com/search.json?' . http_build_query($params);

            $response = Http::timeout(15)->get($url);
            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Erro ao buscar eventos na SerpAPI.'], 500);
            }
            $json = $response->json();
            $eventos = [];
            if (isset($json['events_results'])) {
                foreach ($json['events_results'] as $event) {
                    $eventos[] = [
                        'title' => $event['title'] ?? null,
                        'date' => $event['date']['start_date'] ?? ($event['date'] ?? null),
                        'address' => $event['address'] ?? null,
                        'description' => $event['description'] ?? null,
                        'link' => $event['link'] ?? null,
                        'thumbnail' => $event['thumbnail'] ?? null,
                        'source' => $event['source'] ?? null
                    ];
                }
            }
            return response()->json(['success' => true, 'data' => $eventos]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar eventos SerpAPI', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao buscar eventos.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Airport;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $insurances = [];
    return view('formTrip', ['title' => 'Criar Viagem', 'insurances' => $insurances]);
    }

    public function store(Request $request)
    {

        
        // Validação dos dados
        $validatedData = $request->validate([
            'nome_viagem' => 'required|string|max:100',
            'origem' => 'required|string|max:255',
            'destinos' => 'required|array|min:1',
            'destinos.*' => 'required|string|max:255',
            'destino_data_inicio' => 'required|array|min:1',
            'destino_data_inicio.*' => 'required|date',
            'destino_data_fim' => 'required|array|min:1',
            'destino_data_fim.*' => 'required|date',
            'num_pessoas' => 'required|integer|min:1|max:8',
            'orcamento' => 'nullable|numeric|min:0',
            'idades' => 'nullable|array',
            'idades.*' => 'nullable|integer|min:1|max:120',
            'preferences' => 'nullable',
            // Campos de carro próprio
            'meio_locomocao' => 'nullable|string|max:50',
            'autonomia_veiculo' => 'nullable|numeric|min:0',
            'tipo_combustivel' => 'nullable|string|max:50',
            'preco_combustivel' => 'nullable|numeric|min:0',
            'distancia_total_km' => 'nullable|numeric|min:0',
            'combustivel_litros' => 'nullable|numeric|min:0',
            'custo_combustivel' => 'nullable|numeric|min:0',
            'pedagio_estimado' => 'nullable|numeric|min:0',
            'rota_detalhada' => 'nullable|string',
            // Novos campos para viajantes e seguros
            'viajantesData' => 'nullable|string',
            'segurosViajantesData' => 'nullable|string',
            'seguroSelecionadoData' => 'nullable|string',
        ]);



        // Verificar autenticação
        if (!auth()->check()) {
            \Log::error('Usuário não autenticado');
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        try {
            // Extrair dados validados
            $destinos = $validatedData['destinos'];
            $datasInicio = $validatedData['destino_data_inicio'];
            $datasFim = $validatedData['destino_data_fim'];
            
            // Validar datas
            for ($i = 0; $i < count($destinos); $i++) {
                if (isset($datasInicio[$i]) && isset($datasFim[$i])) {
                    if (strtotime($datasFim[$i]) < strtotime($datasInicio[$i])) {
                        return back()->withErrors([
                            "destino_data_fim.$i" => "A data de fim do destino " . ($i + 1) . " não pode ser anterior à data de início."
                        ])->withInput();
                    }
                }
            }

            // Calcular período da viagem
            $dataInicioViagem = $datasInicio[0];
            $dataFimViagem = end($datasFim);



            // 1. Criar viagem principal
            $usaCarroProprio = $request->input('meio_locomocao') === 'carro_proprio';
            
            $viagemData = [
                'nome_viagem' => $validatedData['nome_viagem'],
                'origem_viagem' => $validatedData['origem'],
                'data_inicio_viagem' => $dataInicioViagem,
                'data_final_viagem' => $dataFimViagem,
                'orcamento_viagem' => $validatedData['orcamento'] ?? 0,
                'fk_id_usuario' => auth()->id(),
            ];

            $viagem = \App\Models\Viagens::create($viagemData);

            // Criar registro de viagem de carro se aplicável
            if ($usaCarroProprio) {
                \App\Models\ViagemCarro::create([
                    'fk_id_viagem' => $viagem->pk_id_viagem,
                    'autonomia_veiculo_km_l' => $validatedData['autonomia_veiculo'] ?? null,
                    'tipo_combustivel' => $validatedData['tipo_combustivel'] ?? null,
                    'preco_combustivel_litro' => $validatedData['preco_combustivel'] ?? null,
                    'distancia_total_km' => $validatedData['distancia_total_km'] ?? null,
                    'pedagio_estimado' => $validatedData['pedagio_estimado'] ?? null,
                    'pedagio_oficial' => $request->input('pedagio_oficial', false),
                    'combustivel_estimado_litros' => $validatedData['combustivel_litros'] ?? null,
                    'custo_combustivel_estimado' => $validatedData['custo_combustivel'] ?? null,
                    'duracao_segundos' => $request->input('duracao_segundos'),
                    'rota_detalhada' => $validatedData['rota_detalhada'] ?? null,
                ]);
            }



            // 2. Criar destinos
            foreach ($destinos as $index => $nomeDestino) {
                if (!empty($nomeDestino) && isset($datasInicio[$index]) && isset($datasFim[$index])) {
                    \App\Models\Destinos::create([
                        'fk_id_viagem' => $viagem->pk_id_viagem,
                        'nome_destino' => $nomeDestino,
                        'data_chegada_destino' => $datasInicio[$index],
                        'data_partida_destino' => $datasFim[$index],
                        'ordem_destino' => $index + 1,
                    ]);
                }
            }


            // 3. Criar voo se houver dados
            if ($request->filled('selected_flight_data')) {
                $flightData = json_decode($request->input('selected_flight_data'), true);
                if ($flightData && isset($flightData['flights']) && count($flightData['flights']) > 0) {
                    $primeiroVoo = $flightData['flights'][0];
                    $ultimoVoo = end($flightData['flights']);
                    
                    // Log dos dados do voo para debug
                    $vooData = [
                        'fk_id_viagem' => $viagem->pk_id_viagem,
                        'desc_aeronave_voo' => !empty($primeiroVoo['airplane']) ? $primeiroVoo['airplane'] : 'Não especificado',
                        'origem_voo' => !empty($primeiroVoo['departure_airport']['id']) ? $primeiroVoo['departure_airport']['id'] : 'N/A',
                        'origem_nome_voo' => !empty($primeiroVoo['departure_airport']['name']) ? $primeiroVoo['departure_airport']['name'] : 'Aeroporto de origem',
                        'destino_voo' => !empty($ultimoVoo['arrival_airport']['id']) ? $ultimoVoo['arrival_airport']['id'] : 'N/A',
                        'destino_nome_voo' => !empty($ultimoVoo['arrival_airport']['name']) ? $ultimoVoo['arrival_airport']['name'] : 'Aeroporto de destino',
                        'data_hora_partida' => isset($primeiroVoo['departure_airport']['time']) 
                            ? date('Y-m-d H:i:s', strtotime($primeiroVoo['departure_airport']['time']))
                            : $dataInicioViagem . ' 00:00:00',
                        'data_hora_chegada' => isset($ultimoVoo['arrival_airport']['time'])
                            ? date('Y-m-d H:i:s', strtotime($ultimoVoo['arrival_airport']['time']))
                            : $dataFimViagem . ' 23:59:59',
                        'companhia_voo' => !empty($primeiroVoo['airline']) ? $primeiroVoo['airline'] : 'Companhia não especificada',
                        'classe_voo' => !empty($primeiroVoo['travel_class']) ? $primeiroVoo['travel_class'] : 'Economy',
                        'conexao_voo' => isset($flightData['layovers'][0]['id']) ? $flightData['layovers'][0]['id'] : null,
                        'conexao_nome_voo' => isset($flightData['layovers'][0]['name']) ? $flightData['layovers'][0]['name'] : null,
                        'preco_voo' => isset($flightData['price']) ? (float)$flightData['price'] : 0.00,
                        'numero_voo' => isset($primeiroVoo['flight_number']) ? $primeiroVoo['flight_number'] : null,
                    ];
                    

                    
                    \App\Models\Voos::create($vooData);

                }
            }

            // 4. Processar dados dos viajantes (novo sistema)
            $viajantesData = [];
            if (!empty($validatedData['viajantesData'])) {
                try {
                    $viajantesData = json_decode($validatedData['viajantesData'], true);
                    \Log::info('Dados dos viajantes recebidos:', $viajantesData);
                } catch (\Exception $e) {
                    \Log::error('Erro ao decodificar dados dos viajantes:', ['error' => $e->getMessage()]);
                }
            }

            // 5. Criar viajantes (novo sistema com nomes personalizados)
            $viajantesIds = [];
            if (!empty($viajantesData)) {
                foreach ($viajantesData as $viaganteInfo) {
                    // Usar nome personalizado se existir, senão usar o nome padrão
                    $nomeViajante = !empty($viaganteInfo['nome_personalizado']) 
                        ? $viaganteInfo['nome_personalizado'] 
                        : ($viaganteInfo['nome'] ?? "Viajante {$viaganteInfo['index']}");
                    
                    $viajante = \App\Models\Viajantes::create([
                        'fk_id_viagem' => $viagem->pk_id_viagem,
                        'nome' => $nomeViajante,
                        'idade' => $viaganteInfo['idade'] ?? 25,
                        'observacoes' => null, // Removemos a lógica de colocar nome nas observações
                    ]);
                    $viajantesIds[$viaganteInfo['index']] = $viajante->pk_id_viajante;
                }
            } else {
                // Fallback para sistema antigo
                if (isset($validatedData['idades']) && is_array($validatedData['idades'])) {
                    foreach ($validatedData['idades'] as $index => $idade) {
                        if (!empty($idade)) {
                            $viajante = \App\Models\Viajantes::create([
                                'fk_id_viagem' => $viagem->pk_id_viagem,
                                'nome' => 'Viajante ' . ($index + 1),
                                'idade' => (int)$idade,
                            ]);
                            $viajantesIds[$index] = $viajante->pk_id_viajante;
                        }
                    }
                }
            }

            // 5.1. Processar seguros por viajante (novo sistema)
            $segurosViajantesData = [];
            if (!empty($validatedData['segurosViajantesData'])) {
                try {
                    $segurosViajantesData = json_decode($validatedData['segurosViajantesData'], true);
                    \Log::info('Dados dos seguros por viajante recebidos:', $segurosViajantesData);
                } catch (\Exception $e) {
                    \Log::error('Erro ao decodificar dados dos seguros:', ['error' => $e->getMessage()]);
                }
            }

            // 5.2. Criar seguros para cada viajante
            $seguroId = null; // Para compatibilidade
            if (!empty($segurosViajantesData)) {
                $extractNumeric = function($value) {
                    if (!$value) return null;
                    $cleaned = preg_replace('/[^\d,.]/', '', $value);
                    return is_numeric(str_replace(',', '.', $cleaned)) ? (float) str_replace(',', '.', $cleaned) : null;
                };

                foreach ($segurosViajantesData as $seguroInfo) {
                    $viaganteIndex = $seguroInfo['viajante_index'];
                    $viaganteId = $viajantesIds[$viaganteIndex] ?? null;
                    $seguroData = $seguroInfo['insurance_data'];

                    if ($viaganteId && $seguroData) {
                        $seguro = \App\Models\Seguros::create([
                            'fk_id_viagem' => $viagem->pk_id_viagem,
                            'fk_id_viajante' => $viaganteId,
                            'seguradora' => $seguroData['seguradora'] ?? 'N/A',
                            'plano' => $seguroData['plano'] ?? 'N/A',
                            'detalhes_etarios' => $seguroData['detalhes_etarios'] ?? null,
                            'link' => $seguroData['link'] ?? null,
                            'cobertura_medica' => $seguroData['coberturas']['medica'] ?? null,
                            'cobertura_bagagem' => $seguroData['coberturas']['bagagem'] ?? null,
                            'preco_pix' => $extractNumeric($seguroData['precos']['pix'] ?? null),
                            'preco_cartao' => $extractNumeric($seguroData['precos']['cartao'] ?? null),
                            'parcelamento_cartao' => $seguroData['precos']['parcelas'] ?? null,
                            'is_selected' => true,
                        ]);
                        
                        // Salvar o primeiro seguro para compatibilidade
                        if ($seguroId === null) {
                            $seguroId = $seguro->pk_id_seguro;
                        }
                    }
                }
            } else {
                // Fallback para sistema antigo de seguro único
                if ($request->filled('seguroSelecionadoData')) {
                    $seguroData = json_decode($request->seguroSelecionadoData, true);
                    if ($seguroData) {
                        $extractNumeric = function ($price) {
                            if (!$price) return null;
                            $cleaned = preg_replace('/[^\d,]/', '', $price);
                            return is_numeric(str_replace(',', '.', $cleaned)) ? (float) str_replace(',', '.', $cleaned) : null;
                        };

                        $seguro = \App\Models\Seguros::create([
                            'fk_id_viagem' => $viagem->pk_id_viagem,
                            'seguradora' => $seguroData['seguradora'] ?? 'N/A',
                            'plano' => $seguroData['plano'] ?? 'N/A',
                            'detalhes_etarios' => $seguroData['detalhes_etarios'] ?? null,
                            'link' => $seguroData['link'] ?? null,
                            'cobertura_medica' => $seguroData['coberturas']['medica'] ?? null,
                            'cobertura_bagagem' => $seguroData['coberturas']['bagagem'] ?? null,
                            'preco_pix' => $extractNumeric($seguroData['precos']['pix'] ?? null),
                            'preco_cartao' => $extractNumeric($seguroData['precos']['cartao'] ?? null),
                            'parcelamento_cartao' => $seguroData['precos']['parcelas'] ?? null,
                            'is_selected' => true,
                        ]);
                        $seguroId = $seguro->pk_id_seguro;
                    }
                }
            }

            // 6. Criar objetivos/preferências
            if (!empty($validatedData['preferences'])) {
                $preferencesString = is_array($validatedData['preferences']) 
                    ? $validatedData['preferences'][0] 
                    : $validatedData['preferences'];
                    
                $prefs = explode(',', $preferencesString);
                foreach ($prefs as $pref) {
                    $prefTrimmed = trim($pref);
                    if ($prefTrimmed !== '') {
                        \App\Models\Objetivos::create([
                            'fk_id_viagem' => $viagem->pk_id_viagem,
                            'nome' => $prefTrimmed,
                        ]);
                    }
                }

            }

            // 7. Se o formulário traz dados do veículo selecionado (fluxo de criação), persistir veículo
            if ($request->filled('selected_car_data')) {
                try {
                    $veiculoData = json_decode($request->input('selected_car_data'), true);
                    if (is_array($veiculoData)) {
                        \App\Models\Veiculos::create([
                            'fk_id_viagem' => $viagem->pk_id_viagem,
                            'nome_veiculo' => $veiculoData['nome'] ?? ($veiculoData['nome_veiculo'] ?? 'N/A'),
                            'categoria' => $veiculoData['categoria'] ?? null,
                            'imagem_url' => $veiculoData['imagem'] ?? ($veiculoData['imagem_url'] ?? null),
                            'passageiros' => $veiculoData['configuracoes']['passageiros'] ?? ($veiculoData['passageiros'] ?? null),
                            'malas' => $veiculoData['configuracoes']['malas'] ?? ($veiculoData['malas'] ?? null),
                            'ar_condicionado' => $veiculoData['configuracoes']['ar_condicionado'] ?? ($veiculoData['ar_condicionado'] ?? false),
                            'cambio' => $veiculoData['configuracoes']['cambio'] ?? ($veiculoData['cambio'] ?? null),
                            'quilometragem' => $veiculoData['configuracoes']['quilometragem'] ?? ($veiculoData['quilometragem'] ?? null),
                            'diferenciais' => json_encode($veiculoData['diferenciais'] ?? []),
                            'tags' => json_encode($veiculoData['tags'] ?? []),
                            'endereco_retirada' => $veiculoData['local_retirada']['endereco'] ?? ($veiculoData['endereco_retirada'] ?? null),
                            'tipo_local' => $veiculoData['local_retirada']['tipo'] ?? ($veiculoData['tipo_local'] ?? null),
                            'nome_local' => $veiculoData['local_retirada']['nome'] ?? ($veiculoData['nome_local'] ?? null),
                            'locadora_nome' => $veiculoData['locadora']['nome'] ?? ($veiculoData['locadora_nome'] ?? null),
                            'locadora_logo' => $veiculoData['locadora']['logo'] ?? ($veiculoData['locadora_logo'] ?? null),
                            'avaliacao_locadora' => $veiculoData['locadora']['avaliacao'] ?? ($veiculoData['avaliacao_locadora'] ?? null),
                            'preco_total' => $veiculoData['preco']['total'] ?? ($veiculoData['preco_total'] ?? null),
                            'preco_diaria' => $veiculoData['preco']['diaria'] ?? ($veiculoData['preco_diaria'] ?? null),
                            'link_reserva' => $veiculoData['link_continuar'] ?? ($veiculoData['link_reserva'] ?? null),
                            'is_selected' => true,
                            'observacoes' => $request->input('veiculo_observacoes') ?? null,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Erro ao persistir veiculo durante criação da viagem', ['error' => $e->getMessage()]);
                }
            }

            // 7. Atualizar viagem com seguro se houver (manter compatibilidade)
            // Nota: Com o novo sistema, seguros são vinculados aos viajantes individuais
            // mas mantemos esta linha para compatibilidade com visualizações existentes
            if ($seguroId) {
                // $viagem->update(['fk_id_seguro_selecionado' => $seguroId]);
                // Campo removido do model Viagens - seguros agora são por viajante
            }

            // 8. Salvar na sessão
            session(['trip_id' => $viagem->pk_id_viagem]);

            return redirect()->route('explore')->with('success', 'Viagem criada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar viagem', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['error' => 'Erro ao criar viagem: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function searchAjax(Request $request)
    {
        $request->validate([
            'dep_iata' => 'required|string',
            'arr_iata' => 'required|string',
            'date_departure' => 'required|date',
            'date_return' => 'required|date',
        ]);

        $user = auth()->user();

        $apiKey = env('SERPAPI_KEY');
        $params = [
            'engine'      => 'google_flights',
            'departure_id'=> $request->dep_iata,
            'arrival_id'  => $request->arr_iata,
            'outbound_date' => $request->date_departure,
            'return_date' => $request->date_return,
            'max_price' => $request->price,
            'type' => $request->type_trip,
            'sort_by' => $request->sort_by,
            'currency'    => $user->currency ?? 'BRL',
            'hl' => 'pt-br',
            'travel_class' => $request->class,
            'api_key'     => $apiKey,
            'stops' => $request->stops,
            'exclude_airlines' => is_array($request->exclude_airlines) ? implode(',', $request->exclude_airlines) : $request->exclude_airlines,
            
        ];
        //dd($params);
        $response = Http::get('https://serpapi.com/search', $params);
        //dd($response->json());
        $json = $response->json();

        $flights = [];
        if (isset($json['best_flights'])) {
            $flights = array_merge($flights, $json['best_flights']);
        }
        if (isset($json['other_flights'])) {
            $flights = array_merge($flights, $json['other_flights']);
        }

        return response()->json(['flights' => $flights]);
    }

    public function cardFlightAjax(Request $request)
    {
        $flight = json_decode($request->input('flight'), true); // <-- decodifica para array
        $index = $request->input('index', 0);

        $html = view('components.flights.cardFlights', [
            'flight' => $flight,
            'index' => $index,
            'user' => auth()->user()
        ])->render();

        return response()->json(['html' => $html]);
    }

    // Novo método auxiliar para filtrar blocos relevantes dos dados do seguro
    private function filtrarBlocosDados($dados)
    {
        $linhas = is_array($dados) ? $dados : [$dados];
        $blocos = [];
        foreach ($linhas as $linha) {
            $linha_lower = mb_strtolower($linha);
            if (
                str_contains($linha_lower, 'despesas médico') ||
                str_contains($linha_lower, 'despesas médicas') ||
                str_contains($linha_lower, 'despesa médica hospitalar') ||
                str_contains($linha_lower, 'dmh') ||
                str_contains($linha_lower, 'bagagem') ||
                str_contains($linha_lower, 'cancelamento') ||
                str_contains($linha_lower, 'odontológicas') || str_contains($linha_lower, 'odontológica') ||
                str_contains($linha_lower, 'medicamentos') ||
                str_contains($linha_lower, 'eletrônicos') ||
                str_contains($linha_lower, 'mochila') || str_contains($linha_lower, 'mão protegida') ||
                str_contains($linha_lower, 'atraso de embarque') ||
                str_contains($linha_lower, 'pet') ||
                str_contains($linha_lower, 'sala vip') ||
                str_contains($linha_lower, 'telemedicina') ||
                str_contains($linha_lower, 'preço pix') ||
                (str_contains($linha_lower, 'pix') && str_contains($linha_lower, 'r$')) ||
                (str_contains($linha_lower, 'cartão') && str_contains($linha_lower, 'r$')) ||
                (str_contains($linha_lower, 'em até') && str_contains($linha_lower, 'x') && str_contains($linha_lower, 'r$')) ||
                str_contains($linha_lower, 'x de r$') ||
                preg_match('/\d+x.*(sem juros|no cartão)/i', $linha) ||
                str_contains($linha_lower, 'total à vista') ||
                (str_contains($linha_lower, 'r$'))
            ) {
                $blocos[] = $linha;
            }
        }
        return $blocos;
    }

    // Retorna os motivos e destinos válidos para o select do frontend
    public function getInsuranceOptions()
    {
        $motivos = [
            ['value' => '1', 'text' => 'LAZER/NEGÓCIO'],
            ['value' => '2', 'text' => 'MULTI-VIAGENS'],
            ['value' => '3', 'text' => 'ANUAL'],
            ['value' => '4', 'text' => 'ESTUDANTE'],
        ];
        $destinos = [
            ['value' => '5', 'text' => 'África'],
            ['value' => '1', 'text' => 'América Do Norte'],
            ['value' => '4', 'text' => 'América Do Sul'],
            ['value' => '6', 'text' => 'Ásia'],
            ['value' => '3', 'text' => 'Caribe-México'],
            ['value' => '2', 'text' => 'Europa'],
            ['value' => '7', 'text' => 'Oceânia'],
            ['value' => '11', 'text' => 'Oriente Médio'],
        ];
        return response()->json(['motivos' => $motivos, 'destinos' => $destinos]);
    }

}
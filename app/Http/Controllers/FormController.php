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

        // dd($request->all());

        // 1. Salve a viagem
        $viagem = new \App\Models\Viagens();
        $viagem->destino_viagem = $request->searchInput;
        $viagem->origem_viagem = $request->origem;
        $viagem->data_inicio_viagem = $request->date_departure;
        $viagem->data_final_viagem = $request->date_return;
        $viagem->orcamento_viagem = $request->orcamento;
        $viagem->fk_id_usuario = auth()->id();

        $viagem->save();

        // 2. Salve o voo (se houver)
        if ($request->filled('selected_flight_index')) {
            // Busque os voos novamente ou recupere os dados do voo selecionado
            $flightData = json_decode($request->input('selected_flight_data', '{}'), true);
            //dd($flightData);
            $voo = new \App\Models\Voos();
            $primeiroTrecho = $flightData['flights'][0] ?? [];
            $conexao = isset($flightData['flights'][1]) ? $flightData['flights'][1] : null;
            
            $voo->desc_aeronave_voo = $primeiroTrecho['airplane'] ?? '';
            $voo->origem_voo = $primeiroTrecho['departure_airport']['id'] ?? '';
            $voo->origem_nome_voo = $primeiroTrecho['departure_airport']['name'] ?? '';
            $voo->destino_voo = $conexao['arrival_airport']['id'] ?? '';
            $voo->destino_nome_voo = $conexao['arrival_airport']['name'] ?? '';
            $voo->conexao_voo = $conexao['departure_airport']['id'] ?? '';
            $voo->conexao_nome_voo = $conexao['departure_airport']['name'] ?? '';
            $voo->data_hora_partida = !empty($primeiroTrecho['departure_airport']['time'])
                ? date('Y-m-d H:i:s', strtotime($primeiroTrecho['departure_airport']['time']))
                : now();
            $voo->data_hora_chegada = !empty($primeiroTrecho['arrival_airport']['time'])
                ? date('Y-m-d H:i:s', strtotime($primeiroTrecho['arrival_airport']['time']))
                : now();
            $voo->companhia_voo = $primeiroTrecho['airline'] ?? '';
            $voo->fk_id_viagem = $viagem->pk_id_viagem;
            $voo->save();
        }

        $seguroId = null;
        if ($request->filled('seguroSelecionadoData')) {
            try {
                // Decodifica a string JSON enviada pelo formulário
                $data = json_decode($request->seguroSelecionadoData, true);

                // Função para extrair valor numérico de strings de preço
                $extractNumeric = function ($price) {
                    if (!$price) return null;
                    $cleaned = preg_replace('/[^\d,]/', '', $price); // Remove tudo exceto dígitos e vírgula
                    $cleaned = str_replace(',', '.', $cleaned); // Troca vírgula por ponto
                    return is_numeric($cleaned) ? (float) $cleaned : null;
                };

                $seguro = \App\Models\Seguros::create([
                    'fk_id_viagem' => $viagem->pk_id_viagem, 
                    'seguradora' => $data['seguradora'] ?? 'N/A',
                    'plano' => $data['plano'] ?? 'N/A',
                    'detalhes_etarios' => $data['detalhes_etarios'] ?? null,
                    'link' => $data['link'] ?? null,
                    'cobertura_medica' => $data['coberturas']['medica'] ?? null,
                    'cobertura_bagagem' => $data['coberturas']['bagagem'] ?? null,
                    'preco_pix' => $extractNumeric($data['precos']['pix'] ?? null),
                    'preco_cartao' => $extractNumeric($data['precos']['cartao'] ?? null),
                    'parcelamento_cartao' => $data['precos']['parcelas'] ?? null,
                    'is_selected' => true,
                ]);

                $seguroId = $seguro->pk_id_seguro;

            } catch (\Exception $e) {
                // Logar o erro é uma boa prática
                \Log::error('Falha ao salvar seguro no FormController@store', [
                    'error' => $e->getMessage(), 
                    'data' => $request->seguroSelecionadoData
                ]);
                // Opcional: retornar com um erro
                // return back()->withErrors(['seguro' => 'Ocorreu um erro ao salvar o seguro selecionado.']);
            }
        }

        // 4. Salve as idades dos viajantes
        if ($request->has('idades') && is_array($request->idades)) {
            foreach ($request->idades as $idade) {
                $viajante = new \App\Models\Viajantes();
                $viajante->idade = $idade;
                $viajante->fk_id_viagem = $viagem->pk_id_viagem;
                $viajante->save();
            }
        }

        // 5. Salve as preferências da viagem (vários objetivos para uma viagem)
        if ($request->filled('preferences')) {
            $prefs = explode(',', $request->preferences[0]);
            foreach ($prefs as $pref) {
                if (trim($pref) !== '') {
                    $objetivo = new \App\Models\Objetivos();
                    $objetivo->nome = trim($pref); // Adicionado trim() por segurança
                    $objetivo->fk_id_viagem = $viagem->pk_id_viagem;
                    $objetivo->save();
                }
            }
        }

        // 6. Salve o ID da viagem na sessão
        session(['trip_id' => $viagem->pk_id_viagem]);

        // 7. Atualize o seguro selecionado na viagem
        if ($seguroId) {
            $viagem->fk_id_seguro_selecionado = $seguroId;
            $viagem->save();
        }

        // 8. Redirecione ou retorne sucesso
        return redirect()->route('explore')->with('success', 'Viagem salva com sucesso!');
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
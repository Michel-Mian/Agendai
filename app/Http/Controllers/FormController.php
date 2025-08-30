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

        //dd($request->all());

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
            $voo = new \App\Models\Voos();
            $primeiroTrecho = $flightData['flights'][0] ?? [];

            $voo->desc_aeronave_voo = $primeiroTrecho['airplane'] ?? '';
            $voo->origem_voo = $primeiroTrecho['departure_airport']['id'] ?? '';
            $voo->destino_voo = $primeiroTrecho['arrival_airport']['id'] ?? '';
            $voo->data_hora_partida = !empty($primeiroTrecho['departure_airport']['time'])
                ? date('Y-m-d H:i:s', strtotime($primeiroTrecho['departure_airport']['time']))
                : now();
            $voo->data_hora_chegada = !empty($primeiroTrecho['arrival_airport']['time'])
                ? date('Y-m-d H:i:s', strtotime($primeiroTrecho['arrival_airport']['time']))
                : now();
            $voo->companhia_voo = $primeiroTrecho['airline'] ?? '';
            $voo->fk_id_viagem = $viagem->pk_id_viagem;
            $voo->preco_voo = $flightData['price'] ?? '';
            $voo->save();

            // Verificação do preço do voo em relação ao orçamento
            $orcamento = floatval($viagem->orcamento_viagem);
            $precoVoo = floatval($voo->preco_voo);
            if ($orcamento > 0 && $precoVoo > 0 && $precoVoo > 0.6 * $orcamento) {
                session()->flash('warning', 'Atenção: o preço do voo selecionado é maior que 60% do orçamento da viagem!');
            }
        }

        // 3. Salve o seguro (se houver)
        $seguroId = null;
        if ($request->has('seguroSelecionado')) {
            $seguroSelecionado = $request->seguroSelecionado;
            if (is_string($seguroSelecionado)) {
                $seguroSelecionado = json_decode($seguroSelecionado, true);
            }
            $seguro = new \App\Models\Seguros();
            $seguro->site = $seguroSelecionado['site'] ?? '';
            $seguro->preco = $seguroSelecionado['preco'] ?? '';
            $seguro->preco_pix = $seguroSelecionado['preco_pix'] ?? '';
            $seguro->preco_cartao = $seguroSelecionado['preco_cartao'] ?? '';
            $seguro->parcelas = $seguroSelecionado['parcelas'] ?? '';
            $seguro->dados = isset($seguroSelecionado['dados'])
                ? (is_array($seguroSelecionado['dados']) ? json_encode($seguroSelecionado['dados']) : json_encode([$seguroSelecionado['dados']]))
                : json_encode([]);
            $seguro->link = $seguroSelecionado['link'] ?? '';
            $seguro->cobertura_medica = $seguroSelecionado['cobertura_medica'] ?? '';
            $seguro->cobertura_bagagem = $seguroSelecionado['cobertura_bagagem'] ?? '';
            $seguro->cobertura_cancelamento = $seguroSelecionado['cobertura_cancelamento'] ?? '';
            $seguro->cobertura_odonto = $seguroSelecionado['cobertura_odonto'] ?? '';
            $seguro->cobertura_medicamentos = $seguroSelecionado['cobertura_medicamentos'] ?? '';
            $seguro->cobertura_eletronicos = $seguroSelecionado['cobertura_eletronicos'] ?? '';
            $seguro->cobertura_mochila_mao = $seguroSelecionado['cobertura_mochila_mao'] ?? '';
            $seguro->cobertura_atraso_embarque = $seguroSelecionado['cobertura_atraso_embarque'] ?? '';
            $seguro->cobertura_pet = $seguroSelecionado['cobertura_pet'] ?? '';
            $seguro->cobertura_sala_vip = $seguroSelecionado['cobertura_sala_vip'] ?? '';
            $seguro->cobertura_telemedicina = $seguroSelecionado['cobertura_telemedicina'] ?? false;
            $seguro->fk_id_viagem = $viagem->pk_id_viagem;
            $seguro->is_selected = true;
            $seguro->save();
            $seguroId = $seguro->pk_id_seguro;
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
        if ($request->has('preferences')) {
            $prefs = explode(',', $request->preferences[0]);
            foreach ($prefs as $pref) {
                if (trim($pref) !== '') {
                    $objetivo = new \App\Models\Objetivos();
                    $objetivo->nome = $pref;
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

}

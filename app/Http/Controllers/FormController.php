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
        return view('formTrip', ['title' => 'Criar Viagem']);
    }

    public function store(Request $request)
    {

        //dd($request->all());

        // 1. Salve a viagem
        $viagem = new \App\Models\Viagens();
        $viagem->destino_viagem = $request->searchInput;
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
            $voo->save();
        }

        // 3. Salve o seguro (se houver)
        if ($request->has('seguroSelecionado')) {
            $seguro = new \App\Models\Seguros();
            $seguro->site = $request->seguroSelecionado['site'] ?? '';
            $seguro->preco = $request->seguroSelecionado['preco'] ?? '';
            $seguro->dados = json_encode($request->seguroSelecionado['dados'] ?? []);
            $seguro->link = $request->seguroSelecionado['link'] ?? '';
            $seguro->fk_id_viagem = $viagem->pk_id_viagem;
            $seguro->save();
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

        // 7. Redirecione ou retorne sucesso
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

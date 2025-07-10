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

        dd($request->all());
        
        // Validate the request data
        $request->validate([
            'dep_iata' => 'required',
            'arr_iata' => 'required',
            'date_departure' => 'required',
            'date_return' => 'required',
            'selected_flight_index' => 'required|integer',
        ]);

        // Busque novamente os voos na SerpApi com os mesmos parâmetros
        $params = [
            'engine'      => 'google_flights',
            'departure_id'=> $request->dep_iata,
            'arrival_id'  => $request->arr_iata,
            'outbound_date' => $request->date_departure,
            'return_date' => $request->date_return,
            'currency'    => auth()->user()->currency ?? 'EUR',
            'api_key'     => env('SERPAPI_KEY'),
        ];
        $response = Http::get('https://serpapi.com/search', $params);
        $json = $response->json();

        $flights = [];
        if (isset($json['best_flights'])) {
            $flights = array_merge($flights, $json['best_flights']);
        }
        if (isset($json['other_flights'])) {
            $flights = array_merge($flights, $json['other_flights']);
        }

        $selectedIndex = $request->selected_flight_index;
        $selectedFlight = $flights[$selectedIndex] ?? null;

        if ($selectedFlight) {
            // Salve os dados essenciais do voo no banco
            // Exemplo: tabela 'flights' relacionada à viagem
            $vooModel = new \App\Models\Voos();
            $vooModel->user_id = auth()->id();
            $vooModel->flight_number = $selectedFlight['flights'][0]['flight_number'] ?? null;
            $vooModel->airline = $selectedFlight['flights'][0]['airline'] ?? null;
            $vooModel->departure_airport = $selectedFlight['flights'][0]['departure_airport']['id'] ?? null;
            $vooModel->arrival_airport = $selectedFlight['flights'][count($selectedFlight['flights'])-1]['arrival_airport']['id'] ?? null;
            $vooModel->departure_time = $selectedFlight['flights'][0]['departure_airport']['time'] ?? null;
            $vooModel->arrival_time = $selectedFlight['flights'][count($selectedFlight['flights'])-1]['arrival_airport']['time'] ?? null;
            $vooModel->price = $selectedFlight['price'] ?? null;
            $vooModel->save();
        }

        // Salve também os outros dados da viagem normalmente

        return redirect()->route('dashboard')->with('success', 'Viagem salva com sucesso!');
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

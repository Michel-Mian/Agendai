<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Airport;
use App\Models\Viagens;
use App\Models\Flight; // Certifique-se de ter o modelo Flight importado

class FlightsController extends Controller
{
    public function search(Request $request)
    {
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
            'max_duration' => $this->convertionHoursToMinutes($request->max_duration),
            'outbound_times' => $this->timesToHoursString($request->departure_time),
            'return_times' => $this->timesToHoursString($request->arrival_time),
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

        // Paginação
        $perPage = 6;
        $page = $request->input('page', 1);
        $paginator = $this->paginateArray(
            $flights,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

       $airlines = [
            // América Latina
            'LATAM'                 => 'LA',
            'Gol'                   => 'G3',
            'Azul'                  => 'AD',
            'Avianca'               => 'AV',
            'Aeromexico'            => 'AM',
            'Copa Airlines'         => 'CM',
            'Sky Airline'           => 'H2',
            'Jetsmart'              => 'JA',
            
            // América do Norte
            'American Airlines'     => 'AA',
            'AMERICAN'              => 'AA',
            'Delta'                 => 'DL',
            'United Airlines'       => 'UA',
            'United'                => 'UA',
            'Air Canada'            => 'AC',
            'WestJet'               => 'WS',
            'Alaska Airlines'       => 'AS',
            'JetBlue'               => 'B6',
            'Southwest Airlines'    => 'WN',
            'Spirit Airlines'       => 'NK',
            'Frontier Airlines'     => 'F9',

            // Europa
            'Lufthansa'             => 'LH',
            'British Airways'       => 'BA',
            'Air France'            => 'AF',
            'KLM'                   => 'KL',
            'Iberia'                => 'IB',
            'Ryanair'               => 'FR',
            'EasyJet'               => 'U2',
            'Swiss International'   => 'LX',
            'Turkish Airlines'      => 'TK',
            'TAP Air Portugal'      => 'TP',
            'Alitalia'              => 'AZ',
            'Norwegian Air Shuttle' => 'DY',
            'Wizz Air'              => 'W6',
            'Brussels Airlines'     => 'SN',
            'Aer Lingus'            => 'EI',

            // Ásia
            'Emirates'              => 'EK',
            'Qatar Airways'         => 'QR',
            'Etihad Airways'        => 'EY',
            'Singapore Airlines'    => 'SQ',
            'Cathay Pacific'        => 'CX',
            'ANA (All Nippon Airways)' => 'NH',
            'Japan Airlines'        => 'JL',
            'Thai Airways'          => 'TG',
            'Malaysia Airlines'     => 'MH',
            'Korean Air'            => 'KE',
            'Asiana Airlines'       => 'OZ',
            'IndiGo'                => '6E',
            'Air India'             => 'AI',
            'Air China'             => 'CA',
            'China Southern'        => 'CZ',
            'China Eastern'         => 'MU',

            // Oceania
            'Qantas'                => 'QF',
            'Air New Zealand'       => 'NZ',

            // África
            'Ethiopian Airlines'    => 'ET',
            'South African Airways' => 'SA',
            'EgyptAir'              => 'MS',
            'Royal Air Maroc'       => 'AT',
        ];

        $viagens = Viagens::where('fk_id_usuario', auth()->id())->get();

        return view('flights', [
            'flights' => $paginator,
            'title' => 'Voos',
            'airlines' => $airlines,
            'user' => $user,
            'viagens' => $viagens,
        ]);
    }

    private function paginateArray(array $items, $perPage = 6, $page = null, $options = [])
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $offset = ($page - 1) * $perPage;
        $itemsForCurrentPage = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($items),
            $perPage,
            $page,
            $options
        );
    }

    // Função auxiliar para converter horários para string de horas separadas por vírgula
    private function timesToHoursString($times)
    {
        if (is_null($times) || $times === '') {
            return null;
        }
        if (is_array($times)) {
            // Remove vazios
            $times = array_filter($times, function($t) {
                return $t !== null && $t !== '';
            });
            $hours = array_map(function($t) {
                return intval(explode(':', $t)[0]);
            }, $times);
            if (count($hours) === 2 || count($hours) === 4) {
                return implode(',', $hours);
            }
        }
        return null;
    }

    private function convertionHoursToMinutes($hours)
    {
        if ($hours) {
            return intval($hours) * 60;
        }
        return null;
    }

    public function autocompleteAirports(Request $request)
    {
        $term = $request->get('q');

    $results = Airport::where('name', 'like', "%{$term}%")
        ->orWhere('city', 'like', "%{$term}%")
        ->orWhere('iata_code', 'like', "%{$term}%")
        ->limit(10)
        ->get(['name', 'city', 'iata_code']);

    return response()->json($results);
    }

    public function saveFlights(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'flight_data' => 'required',
            'viagem_id' => 'required|exists:viagens,pk_id_viagem',
        ]);
        $flightData = json_decode($request->flight_data, true);

        // Se vier como array de voos, pegue o primeiro
        $flight = isset($flightData['flights'][0]) ? $flightData['flights'][0] : $flightData;


        \DB::table('voos')->insert([
            'desc_aeronave_voo' => $flight['airplane'] ?? 'Desconhecido',
            'data_hora_partida' => $flight['departure_airport']['time'] ?? now(),
            'data_hora_chegada' => $flight['arrival_airport']['time'] ?? now(),
            'origem_voo'        => $flight['departure_airport']['id'] ?? '',
            'destino_voo'       => $flight['arrival_airport']['id'] ?? '',
            'companhia_voo'     => $flight['airline'] ?? '',
            'preco_voo'         => $flightData['price'],
            'fk_id_viagem'      => $request->viagem_id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Voo salvo com sucesso!');
    }
}

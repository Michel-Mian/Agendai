<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class FlightsController extends Controller
{
    public function search(Request $request)
    {
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
            'currency'    => 'BRL',
            'hl' => 'pt-br',
            'travel_class' => $request->class,
            'api_key'     => $apiKey,
            
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

        return view('flights', ['flights' => $paginator]);
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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FlightsController extends Controller
{
    public function search(Request $request)
    {
        $apiKey = env('FLIGHT_API_KEY');
        $params = [
            'access_key' => $apiKey,
            'dep_iata'   => $request->dep_iata,
            'arr_iata'   => $request->arr_iata,
            'flight_date'=> $request->date_departure,
        ];

        //dd($params);
        $response = Http::get('http://api.aviationstack.com/v1/flights', $params);

        $flights = $response->json()['data'] ?? [];

        return view('flights', compact('flights'));
    }
}

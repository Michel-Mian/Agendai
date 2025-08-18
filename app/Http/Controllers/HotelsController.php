<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Viagens;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel; 

class HotelsController extends Controller
{
    public function index()
    {

        $user = auth()->user();
        $viagens = Viagens::with('viajantes')->where('fk_id_usuario', $user->id)->get();
        return view('hotels', [
            'title' => 'Minhas Viagens',
            'trips' => $viagens
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'next_page_token' => 'nullable|string',
        ]);

        $apiKey = config('services.serp_api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is not configured.'], 500);
        }

        try {
            $params = [
                'engine' => 'google_hotels',
                'q' => $request->input('q'),
                'check_in_date' => $request->input('check_in_date'),
                'check_out_date' => $request->input('check_out_date'),
                'api_key' => $apiKey,
                'hl' => 'pt-br',
                'gl' => 'br',
                'currency' => 'BRL',
            ];

            if ($request->input('next_page_token')) {
                $params['next_page_token'] = $request->input('next_page_token');
            }

            Log::info('SerpApi request params', $params);

            $response = Http::timeout(30)->get('https://serpapi.com/search.json', $params);

            if ($response->failed()) {
                Log::error('SerpApi request failed', [
                    'status' => $response->status(), 
                    'body' => $response->body(),
                    'params' => $params
                ]);
                
                return response()->json([
                    'error' => 'Failed to fetch data from SerpApi. Status: ' . $response->status()
                ], 502);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                Log::error('SerpApi returned error', ['error' => $data['error']]);
                return response()->json(['error' => $data['error']], 400);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('SerpApi request exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addToTrip(Request $request, $id)
    {
        $viagem = Viagens::find($id);

        if (!$viagem) {
            return response()->json(['message' => 'Viagem não encontrada.'], 404);
        }
        
        $request->validate([
            'name' => 'required|string',
            'link' => 'nullable|url',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'overall_rating' => 'nullable|numeric',
            'rate_per_night.lowest' => 'nullable|string',
            'extracted_price' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'thumbnail' => 'nullable|url',
            'gps_coordinates.latitude' => 'nullable|string',
            'gps_coordinates.longitude' => 'nullable|string',
        ]);

        $preco = $request->input('rate_per_night.lowest') ??
                 $request->input('extracted_price') ??
                 $request->input('price') ??
                 null;

        if (is_string($preco)) {
            $preco = preg_replace('/[^\d,\.]/', '', $preco); // Remove R$ e espaços
            $preco = str_replace(',', '.', $preco); // Troca vírgula por ponto
            $preco = (float) $preco;
        }
        $hotelData['preco'] = $preco;

        $hotelData = [
            'nome_hotel' => $request->input('name'),
            'latitude' => $request->input('gps_coordinates.latitude'),
            'longitude' => $request->input('gps_coordinates.longitude'),
            'avaliacao' => $request->input('overall_rating'),
            'preco' => $preco,
            'data_check_in' => $request->input('check_in_date'),
            'data_check_out' => $request->input('check_out_date'),
            'image_url' => $request->input('thumbnail'),
            'fk_id_viagem' => $id,
        ];

        try {
            Hotel::create($hotelData);

            Log::info('Hotel adicionado à viagem', [
                'viagem_id' => $id,
                'hotel_data' => $hotelData
            ]);
            
            return response()->json([
                'message' => 'Hotel adicionado à viagem com sucesso.',
                'hotel' => $hotelData
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar hotel', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'hotel_data' => $hotelData
            ]);

            return response()->json([
                'message' => 'Erro interno ao adicionar o hotel.'
            ], 500);
        }
    }
}

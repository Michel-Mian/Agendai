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
        $user = auth()->user();
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
                'currency' => $user->currency,
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
            'link' => 'nullable|string',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'overall_rating' => 'nullable|numeric',
            // Aceitar número ou string (SerpAPI varia) – retiramos tipo para não invalidar números
            'rate_per_night.lowest' => 'nullable',
            'extracted_price' => 'nullable',
            'price' => 'nullable',
            'thumbnail' => 'nullable|string',
            'gps_coordinates.latitude' => 'nullable|string',
            'gps_coordinates.longitude' => 'nullable|string',
        ]);

        // Sanitizar URLs: adicionar https:// se ausente
        $link = $request->input('link');
        if ($link && !preg_match('#^https?://#i', $link)) {
            $link = 'https://' . ltrim($link, '/');
        }
        $thumb = $request->input('thumbnail');
        if ($thumb && !preg_match('#^https?://#i', $thumb)) {
            $thumb = 'https://' . ltrim($thumb, '/');
        }

        $rawRate = $request->input('rate_per_night.lowest');
        $rawExtracted = $request->input('extracted_price');
        $rawPrice = $request->input('price');

        $preco = $this->normalizePrice([$rawRate, $rawExtracted, $rawPrice]);

        $hotelData = [
            'nome_hotel' => $request->input('name'),
            'latitude' => $request->input('gps_coordinates.latitude'),
            'longitude' => $request->input('gps_coordinates.longitude'),
            'avaliacao' => $request->input('overall_rating'),
            'preco' => $preco,
            'data_check_in' => $request->input('check_in_date'),
            'data_check_out' => $request->input('check_out_date'),
            'image_url' => $thumb,
            'fk_id_viagem' => $id,
        ];

        try {
            Hotel::create($hotelData);

            Log::info('Hotel adicionado à viagem', [
                'viagem_id' => $id,
                'hotel_data' => $hotelData
            ]);
            
            return response()->json([
                'success' => true,
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
                'success' => false,
                'message' => 'Erro interno ao adicionar o hotel.'
            ], 500);
        }
    }

    /**
     * Normaliza uma lista de possíveis campos de preço, retornando float ou null.
     */
    private function normalizePrice(array $candidates): ?float
    {
        foreach ($candidates as $val) {
            if ($val === null || $val === '') continue;
            $num = $this->parseBrazilianPrice($val);
            if ($num !== null) return $num;
        }
        return null;
    }

    /**
     * Converte string monetária variada em float (pt-BR ou en-US misto).
     */
    private function parseBrazilianPrice($value): ?float
    {
        if (is_numeric($value)) return (float)$value;
        if (!is_string($value)) return null;
        $trim = trim($value);
        if ($trim === '') return null;
        // Remove moeda e espaços (permite dígitos, vírgula e ponto)
        $clean = preg_replace('/[^0-9,\.]/', '', $trim);
        if ($clean === '' ) return null;
        // BR: 2.641,50
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d{1,2}$/', $clean)) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
            return (float)$clean;
        }
        // BR simples: 2641,50
        if (preg_match('/^\d+,\d{1,2}$/', $clean)) {
            $clean = str_replace(',', '.', $clean);
            return (float)$clean;
        }
        // EN: 2641.50 ou 2641
        if (preg_match('/^\d+(\.\d+)?$/', $clean)) {
            return (float)$clean;
        }
        // BR milhar sem decimais: 2.641
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
            $clean = str_replace('.', '', $clean);
            return (float)$clean;
        }
        return null;
    }
}

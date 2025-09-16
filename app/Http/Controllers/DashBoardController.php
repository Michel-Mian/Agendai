<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Viagens;
use App\Models\Viajantes;
use Carbon\Carbon;

/**
 * Controller responsible for rendering the user dashboard with currency rates and history.
 */
class DashBoardController extends Controller
{
    
    /**
     * Displays the dashboard view with user data, currency info, and historical exchange rates.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        $user = auth()->user();

        //viagens
        $viagens = Viagens::where('fk_id_usuario', $user->id)
            ->orderBy('data_inicio_viagem', 'asc')
            ->get();
        
        $viajantes = Viajantes::where('fk_id_viagem', $user->id)->get();

        // Fetch available currencies from the API
        $response = Http::get('https://economia.awesomeapi.com.br/json/available/uniq');
        $currencies = [];
        if ($response->successful()) {
            $currencies = $response->json();
        }
        
        // Busca cotação da moeda preferida do usuário
        $token = env('AWESOME_API_TOKEN');
        $currency = $user->currency ?? 'BRL';

        // List of supported currencies
        $supported = [
            'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AZN', 'BAM',
            'BBD', 'BDT', 'BGN', 'BHD', 'BIF', 'BND', 'BOB', 'BRL', 'BRLT', 'BSD',
            'BTC', 'BWP', 'BYN', 'BZD', 'CAD', 'CHF', 'CHFRTS', 'CLP', 'CNH', 'CNY',
            'COP', 'CRC', 'CUP', 'CVE', 'CZK', 'DJF', 'DKK', 'DOGE', 'DOP', 'DZD',
            'EGP', 'ETB', 'ETH', 'EUR', 'FJD', 'GBP', 'GEL', 'GHS', 'GMD', 'GNF',
            'GTQ', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'IQD',
            'IRR', 'ISK', 'JMD', 'JOD', 'JPY', 'JPYRTS', 'KES', 'KGS', 'KHR', 'KMF',
            'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LSL', 'LTC', 'LYD',
            'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR',
            'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NGNI', 'NGNPARALLEL', 'NIO',
            'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN',
            'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RUBTOD', 'RUBTOM', 'RWF', 'SAR',
            'SCR', 'SDG', 'SDR', 'SEK', 'SGD', 'SOS', 'STD', 'SVC', 'SYP', 'SZL',
            'THB', 'TJS', 'TMT', 'TND', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX',
            'USD', 'USDT', 'UYU', 'UZS', 'VEF', 'VND', 'VUV', 'XAF', 'XAGG', 'XBR',
            'XCD', 'XOF', 'XPF', 'XRP', 'YER', 'ZAR', 'ZMK', 'ZWL', 'XAU', 'BRLPTAX',
            'XAG', 'BRETT', 'SOL', 'BNB'
        ];
        // Check if the currency is supported and fetch its exchange rate
        if (!in_array($currency, $supported)) {
            $cotacao = null;
        } elseif ($currency === 'USD') {
            $cotacao = 1;
        } else {
            $apiResponse = Http::get("https://economia.awesomeapi.com.br/json/last/{$currency}-USD?token={$token}");
            $cotacao = null;
            if ($apiResponse->successful()) {
                $json = $apiResponse->json();
                if (isset($json[$currency . 'USD']['bid'])) {
                    $cotacao = $json[$currency . 'USD']['bid'];
                }
            }
        }

        // Fetch 60-day exchange rate history
        $response = Http::get("https://economia.awesomeapi.com.br/json/daily/{$currency}-USD/60");
        $historico = $response->successful() ? $response->json() : [];

        // Prepare data for the chart
        $labels = [];
        $data = [];
        foreach ($historico as $item) {
            $labels[] = date('d/m/Y', $item['timestamp']);
            $data[] = (float) $item['bid'];
        }
        $viagensFlutter = $viagens->toArray();
        // Se a requisição for JSON (ex: chamada do app Flutter), retorna os dados em JSON
        if (request()->wantsJson()) {
            // Formata as viagens para o Flutter
            $viagensFlutter = $viagens->map(function($viagem) {
                $qtdDias = null;
                if ($viagem->data_inicio_viagem && $viagem->data_final_viagem) {
                    $qtdDias = \Carbon\Carbon::parse($viagem->data_inicio_viagem)
                        ->diffInDays(\Carbon\Carbon::parse($viagem->data_final_viagem));
                }
                $pessoas = $viagem->viajantes()->count();
                return [
                    'id' => $viagem->pk_id_viagem,
                    'destino' => $viagem->destino ?? ($viagem->destino_viagem ?? null),
                    'dataInicio' => $viagem->data_inicio_viagem,
                    'dataFim' => $viagem->data_final_viagem ?? null,
                    'dias' => $qtdDias,
                    'pessoas' => $pessoas,
                ];
            });
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'currency' => $user->currency ?? 'BRL',
                ],
                'viagensFlutter' => $viagensFlutter,
                'cotacao' => $cotacao,
                'currencies' => $currencies,
                'historico' => $historico,
                'labels' => $labels,
                'data' => $data,
            ]);
        }
        // Render the dashboard view with all data
        $dados = [
            'user' => $user,
            'viagens' => $viagens,
            'viagensFlutter' => $viagensFlutter,
            'currencies' => $currencies,
            'cotacao' => $cotacao,
            'historico' => $historico,
            'labels' => $labels,
            'data' => $data,
            'title' => 'Dashboard',
        ];
        if (request()->wantsJson()) {
            return response()->json($dados);
        }
        return view('dashboard', $dados);
    }

    /**
     * Returns summarized historical exchange rate data via AJAX for dynamic chart rendering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historicoAjax(Request $request)
    {
        $user = auth()->user();
        $dias = $request->query('dias', 60);
        $currency = $user->currency ?? 'BRL';
        // Fetch historical data
        $response = Http::get("https://economia.awesomeapi.com.br/json/daily/{$currency}-USD/{$dias}");
        $historico = $response->successful() ? $response->json() : [];
        $historico = array_reverse($historico);

        // Group data into 6 time periods
        $periods = 6;
        $total = count($historico);
        $groupSize = max(1, (int) ceil($total / $periods));

        $labels = [];
        $data = [];
        $temp = [];
        $tempDates = [];

        foreach ($historico as $i => $item) {
            $temp[] = (float) $item['bid'];
            $tempDates[] = date('d/m/Y', $item['timestamp']);

            if (count($temp) === $groupSize || $i === array_key_last($historico)) {
                $data[] = array_sum($temp) / count($temp);
                $labels[] = end($tempDates);
                $temp = [];
                $tempDates = [];
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
    
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashBoardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $response = Http::get('https://economia.awesomeapi.com.br/json/available/uniq');
        $currencies = [];
        if ($response->successful()) {
            $currencies = $response->json();
        }
        
        // Busca cotação da moeda preferida do usuário
        $token = env('AWESOME_API_TOKEN');
        $currency = $user->currency ?? 'BRL';
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
        if (!in_array($currency, $supported)) {
            $cotacao = null;
        } elseif ($currency === 'USD') {
            $cotacao = 1;
        } else {
            $apiResponse = Http::get("https://economia.awesomeapi.com.br/json/last/{$currency}-USD?token={$token}");
            $cotacao = null;
            if ($apiResponse->successful()) {
                $json = $apiResponse->json();
                if (isset($json[$currency.'USD']['bid'])) {
                    $cotacao = $json[$currency.'USD']['bid'];
                }
            }
        }

        $response = Http::get("https://economia.awesomeapi.com.br/json/daily/{$currency}-USD/60");
        $historico = $response->successful() ? $response->json() : [];

        // Exemplo
        $labels = [];
        $data = [];
        foreach ($historico as $item) {
            $labels[] = date('d/m/Y', $item['timestamp']);
            $data[] = (float) $item['bid'];
        }
        return view('dashboard', [
            'user' => $user,
            'currencies' => $currencies,
            'cotacao' => $cotacao,
            'historico' => $historico,
            'labels' => $labels,
            'data' => $data,
            'title' => 'Dashboard',
        ]);
    }

    public function historicoAjax(Request $request)
    {
        $user = auth()->user();
        $dias = $request->query('dias', 60);
        $currency = $user->currency ?? 'BRL';

        $response = Http::get("https://economia.awesomeapi.com.br/json/daily/{$currency}-USD/{$dias}");
        $historico = $response->successful() ? $response->json() : [];

        $historico = array_reverse($historico);

        $periodos = 6;

        $total = count($historico);
        $group = max(1, (int) ceil($total / $periodos));

        $labels = [];
        $data = [];
        $temp = [];
        $tempDates = [];

        foreach ($historico as $i => $item) {
            $temp[] = (float) $item['bid'];
            $tempDates[] = date('d/m/Y', $item['timestamp']);

            if (count($temp) === $group || $i === array_key_last($historico)) {
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

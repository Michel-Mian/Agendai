<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function showForm()
    {
        return view('trip.form'); // resources/views/trip/form.blade.php
    }

    public function scrapingAjax(Request $request)
    {
        $request->validate([
            'motivo' => 'required|in:1,2,3,4',
            'destino' => 'required|in:1,2,3,4,5,6,7,11',
            'data_ida' => 'required|date',
            'data_volta' => 'required|date|after_or_equal:data_ida',
            'qtd_passageiros' => 'required|integer|min:1|max:8',
            'idades' => 'required|array',
        ]);

        $qtd = (int) $request->qtd_passageiros;
        $idades = $request->idades;
        for ($i = count($idades); $i < 8; $i++) {
            $idades[] = '0';
        }

        $command = escapeshellcmd('python "' . base_path('scripts/webscraping/scrapingESV.py') . '" ' .
            $request->motivo . ' ' .
            $request->destino . ' ' .
            escapeshellarg($request->data_ida) . ' ' .
            escapeshellarg($request->data_volta) . ' ' .
            $qtd . ' ' .
            implode(' ', $idades)
        );

        $output = shell_exec($command);
        $linhas = explode("\n", trim($output));

        $frases = [];
        $seguroAtual = [];

        foreach ($linhas as $linha) {
            if (trim($linha) === '=====') {
                if (!empty($seguroAtual)) {
                    $frases[] = $seguroAtual;
                    $seguroAtual = [];
                }
            } else {
                $seguroAtual[] = $linha;
            }
        }

        if (!empty($seguroAtual)) {
            $frases[] = $seguroAtual;
        }

        $result = [];
        foreach ($frases as $seguro) {
            $link = '';
            if (filter_var(end($seguro), FILTER_VALIDATE_URL)) {
                $link = array_pop($seguro);
            }
            $result[] = [
                'site' => array_shift($seguro),
                'dados' => $seguro,
                'link' => $link,
            ];
        }

        return response()->json(['frases' => $result]);
    }
}





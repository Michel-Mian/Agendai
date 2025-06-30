<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    // Exibe o formulário para o usuário preencher
    public function mostrarFormulario()
    {
        return view('trip.form'); // Blade: resources/views/form.blade.php
    }

    // Executa o scraping com os dados do formulário
    public function executarScraping(Request $request)
    {
        // Validação básica dos campos
        $request->validate([
            'motivo' => 'required',
            'destino' => 'required',
            'data_ida' => 'required|date',
            'data_volta' => 'required|date|after_or_equal:data_ida',
            'qtd_passageiros' => 'required|integer|min:1|max:3',
        ]);

        // Monta o comando Python com os argumentos
        $command = escapeshellcmd('python "' . base_path('scripts/webscraping/scraping.py') . '" ' .
            $request->motivo . ' ' .
            $request->destino . ' ' .
            escapeshellarg($request->data_ida) . ' ' .
            escapeshellarg($request->data_volta) . ' ' .
            $request->qtd_passageiros . ' ' .
            ($request->idade1 ?? '0') . ' ' .
            ($request->idade2 ?? '0') . ' ' .
            ($request->idade3 ?? '0')
        );

        // Executa o script e coleta a saída
        $output = shell_exec($command);

        // Divide a saída em linhas
        $frases = explode("\n", trim($output));

        // Retorna a mesma view com os resultados
        return view('trip.form', compact('frases'));
    }
}





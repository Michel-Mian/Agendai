<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function showForm()
    {
        return view('trip.form');
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

        $nome = escapeshellarg("Matheus");
        $email = escapeshellarg("matheus@email.com");
        $celular = escapeshellarg("11999999999");

        $mapDestinoTexto = [
            1 => 'América do Norte',
            2 => 'Europa',
            3 => 'Caribe / México',
            4 => 'América do Sul',
            5 => 'África',
            6 => 'Ásia',
            7 => 'Oceania',
            11 => 'Oriente Médio'
        ];
        $destinoTexto = $mapDestinoTexto[$request->destino] ?? 'Europa';

        $categoriaFixa = 17;

        $pax_0_64 = 0;
        $pax_65_70 = 0;
        $pax_71_80 = 0;
        $pax_81_85 = 0;

        foreach ($idades as $idade) {
            if ($idade <= 64) $pax_0_64++;
            elseif ($idade <= 70) $pax_65_70++;
            elseif ($idade <= 80) $pax_71_80++;
            elseif ($idade <= 85) $pax_81_85++;
        }

        $mapDestinoAV = [
            1 => 3,
            2 => 4,
            3 => 9,
            4 => 1,
            5 => 7,
            6 => 7,
            7 => 7,
            11 => 9
        ];
        $destinoAV = $mapDestinoAV[$request->destino] ?? 4;

        $mapDestinoASV = [
            1 => 1,
            2 => 5,
            3 => 1,
            4 => 10,
            5 => 4,
            6 => 6,
            7 => 7,
            11 => 8,
        ];
        $destinoASV = $mapDestinoASV[$request->destino] ?? 5;

        $python = '"C:\\Users\\matheus henrique\\AppData\\Local\\Programs\\Python\\Python313\\python.exe"';

        $cmds = [
            'ESV' => $python . ' "' . base_path('scripts/webscraping/scrapingESV.py') . '" ' .
                $request->motivo . ' ' . $request->destino . ' ' .
                escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' ' .
                $qtd . ' ' . implode(' ', $idades),

            'SP' => $python . ' "' . base_path('scripts/webscraping/scrapingSP.py') . '" ' .
                escapeshellarg($destinoTexto) . ' ' .
                escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' ' .
                $nome . ' ' . $email . ' ' . $celular,

            'ASV' => $python . ' "' . base_path('scripts/webscraping/scrapingASV.py') . '" ' .
                $categoriaFixa . ' ' . $destinoASV . ' ' .
                escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' ' .
                $nome . ' ' . $email . ' ' . $celular . ' ' .
                $pax_0_64 . ' ' . $pax_65_70 . ' ' . $pax_71_80 . ' ' . $pax_81_85,

            'AV' => $python . ' "' . base_path('scripts/webscraping/scrapingAV.py') . '" ' .
                $destinoAV . ' ' . escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' ' .
                $nome . ' ' . $email . ' ' . $celular . ' ' . implode(',', $idades),
        ];

        $pipes = [];
        $processes = [];
        $outputs = [];

        foreach ($cmds as $key => $cmd) {
            $descriptorspec = [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            $process = proc_open($cmd, $descriptorspec, $pipes[$key]);
            if (is_resource($process)) {
                stream_set_blocking($pipes[$key][1], false);
                $processes[$key] = $process;
            }
        }

        $finished = [];

        while (count($finished) < count($cmds)) {
            foreach ($pipes as $key => $pipe) {
                if (isset($finished[$key])) continue;

                $chunk = fread($pipe[1], 8192);
                if ($chunk !== false && strlen($chunk) > 0) {
                    if (!isset($outputs[$key])) $outputs[$key] = '';
                    $outputs[$key] .= $chunk;
                }

                if (feof($pipe[1])) {
                    fclose($pipe[1]);
                    proc_close($processes[$key]);
                    $finished[$key] = true;
                }
            }

            usleep(10000); // 10ms
        }

        // === Aqui vem a estrutura do primeiro script: processa e agrupa os resultados ===
        $result = [];

        foreach ($outputs as $out) {
            $linhas = explode("\n", trim($out));
            $frases = [];
            $seguroAtual = [];

            foreach ($linhas as $linha) {
                if (trim($linha) === '=====') {
                    if (!empty($seguroAtual)) {
                        $result[] = $this->formatSeguroResult($seguroAtual);
                        $seguroAtual = [];
                    }
                } else {
                    $seguroAtual[] = $linha;
                }
            }

            if (!empty($seguroAtual)) {
                $result[] = $this->formatSeguroResult($seguroAtual);
            }
        }

        return response()->json(['frases' => $result]);
    }

    private function formatSeguroResult(array $seguro)
    {
        $link = '';
        $lastLine = end($seguro);
        if ($lastLine && is_string($lastLine) && str_starts_with($lastLine, 'http')) {
            $link = array_pop($seguro);
        }

        $site = array_shift($seguro);

        return [
            'site' => $site,
            'dados' => $seguro,
            'link' => $link,
        ];
    }
}

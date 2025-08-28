<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrapeInsuranceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;

    /**
     * Create a new job instance.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $request = $this->params['params'];
            $cacheKey = $this->params['cache_key'];

            // Monte os comandos Python igual ao TripController
            $motivo = $request['motivo'] ?? 1;
            $destino = $request['destino'] ?? 2;
            $data_ida = $request['data_ida'] ?? date('Y-m-d');
            $data_volta = $request['data_volta'] ?? date('Y-m-d', strtotime('+7 days'));
            $qtd = $request['qtd_passageiros'] ?? 1;
            $idades = $request['idades'] ?? [30];

            for ($i = count($idades); $i < 8; $i++) {
                $idades[] = '0';
            }

            $nome = escapeshellarg("Matheus");
            $email = escapeshellarg("matheus@email.com");
            $celular = escapeshellarg("11999999999");

            $mapDestinoTexto = [
                1 => 'América do Norte', 2 => 'Europa', 3 => 'Caribe / México', 4 => 'América do Sul',
                5 => 'África', 6 => 'Ásia', 7 => 'Oceania', 11 => 'Oriente Médio'
            ];
            $destinoTexto = $mapDestinoTexto[$destino] ?? 'Europa';
            $categoriaFixa = 17;
            $pax_0_64 = $pax_65_70 = $pax_71_80 = $pax_81_85 = 0;
            foreach ($idades as $idade) {
                if ($idade <= 64) $pax_0_64++;
                elseif ($idade <= 70) $pax_65_70++;
                elseif ($idade <= 80) $pax_71_80++;
                elseif ($idade <= 85) $pax_81_85++;
            }
            $mapDestinoAV = [1 => 3, 2 => 4, 3 => 9, 4 => 1, 5 => 7, 6 => 7, 7 => 7, 11 => 9];
            $destinoAV = $mapDestinoAV[$destino] ?? 4;
            $python = 'python';
            $mapDestinoASV = [1 => 1, 2 => 5, 3 => 1, 4 => 10, 5 => 4, 6 => 6, 7 => 7, 11 => 8];
            $destinoASV = $mapDestinoASV[$destino] ?? 5;

            $cmds = [
                'ESV' => $python . ' "' . base_path('scripts/webscraping/scrapingESV.py') . '" '
                    . (int)$motivo . ' ' . (int)$destino . ' '
                    . escapeshellarg($data_ida) . ' ' . escapeshellarg($data_volta) . ' '
                    . $qtd . ' ' . implode(' ', $idades),
                'SP' => $python . ' "' . base_path('scripts/webscraping/scrapingSP.py') . '" '
                    . escapeshellarg($destinoTexto) . ' ' . escapeshellarg($data_ida) . ' '
                    . escapeshellarg($data_volta) . ' ' . $nome . ' ' . $email . ' ' . $celular,
                'ASV' => $python . ' "' . base_path('scripts/webscraping/scrapingASV.py') . '" '
                    . $categoriaFixa . ' ' . $destinoASV . ' '
                    . escapeshellarg($data_ida) . ' ' . escapeshellarg($data_volta) . ' '
                    . $nome . ' ' . $email . ' ' . $celular . ' '
                    . $pax_0_64 . ' ' . $pax_65_70 . ' ' . $pax_71_80 . ' ' . $pax_81_85,
                'AV' => $python . ' "' . base_path('scripts/webscraping/scrapingAV.py') . '" '
                    . $destinoAV . ' ' . escapeshellarg($data_ida) . ' '
                    . escapeshellarg($data_volta) . ' ' . $nome . ' ' . $email . ' ' . $celular . ' '
                    . implode(',', $idades),
            ];

            $pipes = [];
            $processes = [];
            $outputs = [];
            $startTime = microtime(true);
            $timeoutSeconds = 30; // Pode ser maior no Job

            foreach ($cmds as $key => $cmd) {
                $descriptorspec = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
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
                if ((microtime(true) - $startTime) > $timeoutSeconds) {
                    foreach ($processes as $key => $process) {
                        if (!isset($finished[$key]) && is_resource($process)) {
                            fclose($pipes[$key][1]);
                            proc_terminate($process);
                            $finished[$key] = true;
                        }
                    }
                    break;
                }
                usleep(5000);
            }

            // Parse output igual ao controller
            $frases = [];
            foreach ($outputs as $out) {
                $frases = array_merge($frases, app('App\Http\Controllers\TripController')->parseOutput($out));
            }

            // Log resultado para debug
            Log::info('ScrapeInsuranceJob result', ['cache_key' => $cacheKey, 'frases' => $frases]);

            // Log resultado para debug
            \Log::info('ScrapeInsuranceJob salvando no cache', ['cache_key' => $cacheKey, 'frases' => $frases]);
            // Salva no banco (tabela seguros_cache)
            DB::table('seguros_cache')->updateOrInsert(
                ['cache_key' => $cacheKey],
                [
                    'result_json' => json_encode($frases),
                    'updated_at' => now()
                ]
            );
        } catch (\Throwable $e) {
            \Log::error('ScrapeInsuranceJob failed', ['error' => $e->getMessage()]);
        }
    }
}

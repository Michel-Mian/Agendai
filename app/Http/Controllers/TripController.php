<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Seguros;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TripController extends Controller
{
    // Exibe o formulário de pesquisa de seguros
    public function showForm()
    {
    $insurances = Seguros::latest()->get();
    return view('formTrip', ['insurances' => $insurances]);
    }

    // Roda os scripts de scraping em paralelo e retorna os seguros padronizados
    public function scrapingAjax(Request $request)
    {
        $cacheKey = 'seguros:' . md5(json_encode($request->all()));
        $cacheRow = \DB::table('seguros_cache')->where('cache_key', $cacheKey)->first();

        // Log para debug
        \Log::info('scrapingAjax cacheRow', ['cacheKey' => $cacheKey, 'cacheRow' => $cacheRow]);

        if ($cacheRow && $cacheRow->result_json) {
            $frases = json_decode($cacheRow->result_json, true);
            // Log para debug
            \Log::info('scrapingAjax retornando frases do cache', ['frases' => $frases]);
            return response()->json(['frases' => $frases]);
        } else {
            // Dispara o Job para rodar scraping em background
            \App\Jobs\ScrapeInsuranceJob::dispatch([
                'cache_key' => $cacheKey,
                'params' => $request->all()
            ]);
            // Log para debug
            \Log::info('scrapingAjax disparou Job', ['cacheKey' => $cacheKey]);
            return response()->json(['frases' => [], 'status' => 'carregando']);
        }

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

        // Dados fixos para nome, email e celular
        $nome = escapeshellarg("Matheus");
        $email = escapeshellarg("matheus@email.com");
        $celular = escapeshellarg("11999999999");

        // Mapeamentos de destino
        $mapDestinoTexto = [
            1 => 'América do Norte', 2 => 'Europa', 3 => 'Caribe / México', 4 => 'América do Sul',
            5 => 'África', 6 => 'Ásia', 7 => 'Oceania', 11 => 'Oriente Médio'
        ];
        $destinoTexto = $mapDestinoTexto[$request->destino] ?? 'Europa';
        $categoriaFixa = 17;
        $pax_0_64 = $pax_65_70 = $pax_71_80 = $pax_81_85 = 0;
        foreach ($idades as $idade) {
            if ($idade <= 64) $pax_0_64++;
            elseif ($idade <= 70) $pax_65_70++;
            elseif ($idade <= 80) $pax_71_80++;
            elseif ($idade <= 85) $pax_81_85++;
        }
        $mapDestinoAV = [1 => 3, 2 => 4, 3 => 9, 4 => 1, 5 => 7, 6 => 7, 7 => 7, 11 => 9];
        $destinoAV = $mapDestinoAV[$request->destino] ?? 4;
        $python = 'python';
        $mapDestinoASV = [1 => 1, 2 => 5, 3 => 1, 4 => 10, 5 => 4, 6 => 6, 7 => 7, 11 => 8];
        $destinoASV = $mapDestinoASV[$request->destino] ?? 5;

        $cmds = [
            'ESV' => $python . ' "' . base_path('scripts/webscraping/scrapingESV.py') . '" '
                . (int)$request->motivo . ' ' . (int)$request->destino . ' '
                . escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' '
                . $qtd . ' ' . implode(' ', $idades),
            'SP' => $python . ' "' . base_path('scripts/webscraping/scrapingSP.py') . '" '
                . escapeshellarg($destinoTexto) . ' ' . escapeshellarg($request->data_ida) . ' '
                . escapeshellarg($request->data_volta) . ' ' . $nome . ' ' . $email . ' ' . $celular,
            'ASV' => $python . ' "' . base_path('scripts/webscraping/scrapingASV.py') . '" '
                . $categoriaFixa . ' ' . $destinoASV . ' '
                . escapeshellarg($request->data_ida) . ' ' . escapeshellarg($request->data_volta) . ' '
                . $nome . ' ' . $email . ' ' . $celular . ' '
                . $pax_0_64 . ' ' . $pax_65_70 . ' ' . $pax_71_80 . ' ' . $pax_81_85,
            'AV' => $python . ' "' . base_path('scripts/webscraping/scrapingAV.py') . '" '
                . $destinoAV . ' ' . escapeshellarg($request->data_ida) . ' '
                . escapeshellarg($request->data_volta) . ' ' . $nome . ' ' . $email . ' ' . $celular . ' '
                . implode(',', $idades),
        ];

        $pipes = [];
        $processes = [];
        $outputs = [];
        $startTime = microtime(true);
        $timeoutSeconds = 4; // timeout agressivo

        // Inicia todos os processos em paralelo
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
            // Se passou do timeout, encerra todos os processos restantes
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
            usleep(5000); // menor sleep para resposta mais rápida
        }

        // Padroniza a saída dos scripts
        $frases = [];
        foreach ($outputs as $out) {
            $frases = array_merge($frases, $this->parseOutput($out));
        }

        // Salva no cache por 10 minutos
        Redis::setex($cacheKey, 600, json_encode($frases));

        return response()->json(['frases' => $frases]);
    }

    // Salva o seguro selecionado no banco de dados
    public function salvarSeguro(Request $request)
    {
        $data = $request->all();
        // Garante que 'dados' seja sempre um array antes de salvar
        if (is_string($data['dados'])) {
            $data['dados'] = json_decode($data['dados'], true);
        }
        if (!is_array($data['dados'])) {
            return response()->json(['erro' => 'Dados inválidos'], 400);
        }
        $cobertura_medica = null;
        $cobertura_bagagem = null;
        $cobertura_cancelamento = null;
        $preco_pix = null;
        $preco_cartao = null;
        $parcelas = null;
        $preco = null;
        $cobertura_odonto = null;
        $cobertura_medicamentos = null;
        $cobertura_eletronicos = null;
        $cobertura_mochila_mao = null;
        $cobertura_atraso_embarque = null;
        $cobertura_pet = null;
        $cobertura_sala_vip = null;
        $cobertura_telemedicina = false;
        $esperando_valor_para = null;
        $previousLine = '';
        foreach ($data['dados'] as $linha) {
            $linha_lower = mb_strtolower($linha);
            if (
                str_contains($linha_lower, 'despesas médico') ||
                str_contains($linha_lower, 'despesas médicas') ||
                str_contains($linha_lower, 'despesa médica hospitalar') ||
                str_contains($linha_lower, 'dmh')
            ) {
                if (preg_match('/us[d\$]\s*[\d\.,]+/i', $linha)) {
                    $cobertura_medica = $this->extrairValorNumerico($linha);
                } else {
                    $esperando_valor_para = 'cobertura_medica';
                }
            }
            if (str_contains($linha_lower, 'bagagem')) {
                preg_match_all('/(us[d\$])\s*[\d\.,]+/i', $linha, $matches);
                if (!empty($matches[0])) {
                    $ultimo_valor = end($matches[0]);
                    $cobertura_bagagem = $this->extrairValorNumerico($ultimo_valor);
                } else {
                    $esperando_valor_para = 'cobertura_bagagem';
                }
            }
            if (str_contains($linha_lower, 'cancelamento')) {
                if (preg_match('/us[d\$]\s*[\d\.,]+/i', $linha)) {
                    $cobertura_cancelamento = $this->extrairValorNumerico($linha);
                } else {
                    $esperando_valor_para = 'cobertura_cancelamento';
                }
            }
            if ($esperando_valor_para && preg_match('/us[d\$]\s*[\d\.,]+/i', $linha, $match)) {
                $valor = $this->extrairValorNumerico($match[0]);
                if ($esperando_valor_para === 'cobertura_medica' && !$cobertura_medica) {
                    $cobertura_medica = $valor;
                } elseif ($esperando_valor_para === 'cobertura_bagagem' && !$cobertura_bagagem) {
                    $cobertura_bagagem = $valor;
                } elseif ($esperando_valor_para === 'cobertura_cancelamento' && !$cobertura_cancelamento) {
                    $cobertura_cancelamento = $valor;
                }
                $esperando_valor_para = null;
            }
            if (str_contains($linha_lower, 'odontológicas') || str_contains($linha_lower, 'odontológica')) {
                $cobertura_odonto = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'medicamentos')) {
                $cobertura_medicamentos = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'eletrônicos')) {
                $cobertura_eletronicos = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'mochila') || str_contains($linha_lower, 'mão protegida')) {
                $cobertura_mochila_mao = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'atraso de embarque')) {
                $cobertura_atraso_embarque = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'pet')) {
                $cobertura_pet = $this->extrairValorNumerico($linha);
            }
            if (str_contains($linha_lower, 'sala vip')) {
                $cobertura_sala_vip = 'incluído';
            }
            if (str_contains($linha_lower, 'telemedicina')) {
                $cobertura_telemedicina = true;
            }
            if (str_contains($linha_lower, 'preço pix') || (str_contains($linha_lower, 'pix') && str_contains($linha_lower, 'r$'))) {
                $preco_pix = $this->extrairValorNumerico($linha);
            }
            if (
                (str_contains($linha_lower, 'cartão') && str_contains($linha_lower, 'r$')) ||
                (str_contains($linha_lower, 'em até') && str_contains($linha_lower, 'x') && str_contains($linha_lower, 'r$'))
            ) {
                $preco_cartao = $this->extrairValorNumerico($linha);
            }
            if (
                str_contains($linha_lower, 'x de r$') ||
                preg_match('/\d+x.*(sem juros|no cartão)/i', $linha)
            ) {
                $linha_limpa = trim(preg_replace('/^ou\s+/i', '', $linha));
                $parcelas = $linha_limpa;
            }
            if (str_contains($linha_lower, 'total à vista')) {
                $preco = $this->extrairValorNumerico($linha);
            }
            if ($preco === null && str_contains($linha_lower, 'r$')) {
                $preco = $this->extrairValorNumerico($linha);
            }
            $previousLine = $linha;
        }
        $titulo_bruto = $data['dados'][0] ?? 'Título não informado';
        $titulo_limpo = trim($titulo_bruto);
        $titulo_limpo = str_replace(['\"', "\'", '"', "'"], '', $titulo_limpo);

        $tripId = $request->input('trip_id') ?? session('trip_id');
        // Desmarca todos os outros seguros antes de adicionar o novo
        \App\Models\Seguros::where('fk_id_viagem', $tripId)->update(['is_selected' => false]);
        $seguro = new \App\Models\Seguros();
        $seguro->site = $data['site'];
        $seguro->dados = json_encode($data['dados']); // Sempre salva como JSON
        $seguro->link = !empty($data['link']) ? $data['link'] : null;
        $seguro->preco = $preco;
        $seguro->preco_pix = $preco_pix;
        $seguro->preco_cartao = $preco_cartao;
        $seguro->parcelas = $parcelas;
        $seguro->cobertura_medica = $cobertura_medica;
        $seguro->cobertura_bagagem = $cobertura_bagagem;
        $seguro->cobertura_cancelamento = $cobertura_cancelamento;
        $seguro->cobertura_odonto = $cobertura_odonto;
        $seguro->cobertura_medicamentos = $cobertura_medicamentos;
        $seguro->cobertura_eletronicos = $cobertura_eletronicos;
        $seguro->cobertura_mochila_mao = $cobertura_mochila_mao;
        $seguro->cobertura_atraso_embarque = $cobertura_atraso_embarque;
        $seguro->cobertura_pet = $cobertura_pet;
        $seguro->cobertura_sala_vip = $cobertura_sala_vip;
        $seguro->cobertura_telemedicina = $cobertura_telemedicina;
        $seguro->fk_id_viagem = $tripId;
        $seguro->is_selected = true;
        $seguro->save();

        // Retorna o ID do seguro salvo
        return response()->json(['mensagem' => 'Seguro salvo com sucesso!', 'seguro_id' => $seguro->pk_id_seguro]);
    }

    // AJAX: Retorna todos os seguros da viagem
    public function getInsurancesAjax(Request $request)
    {
        // Tenta pegar o trip_id da requisição, senão pega da sessão
        $tripId = $request->input('trip_id') ?? session('trip_id');
        if (!$tripId) {
            // Retorna vazio se não houver trip_id
            return response()->json(['seguros' => []]);
        }
        // Busca todos os seguros da viagem
        $seguros = \App\Models\Seguros::where('fk_id_viagem', $tripId)->get();
        return response()->json(['seguros' => $seguros]);
    }

    // AJAX: Troca o seguro selecionado da viagem
    public function updateInsuranceAjax(Request $request)
    {
        $tripId = $request->input('trip_id') ?? session('trip_id');
        $seguroId = $request->input('seguro_id');
        if (!$tripId || !$seguroId) {
            return response()->json(['success' => false, 'mensagem' => 'Dados inválidos']);
        }
        // Desmarca todos os seguros
        \App\Models\Seguros::where('fk_id_viagem', $tripId)->update(['is_selected' => false]);
        // Marca o novo seguro
        $seguro = \App\Models\Seguros::where('pk_id_seguro', $seguroId)->where('fk_id_viagem', $tripId)->first();
        if ($seguro) {
            $seguro->is_selected = true;
            $seguro->save();
            return response()->json(['success' => true, 'mensagem' => 'Seguro alterado com sucesso!']);
        }
        return response()->json(['success' => false, 'mensagem' => 'Seguro não encontrado']);
    }

    // Funções auxiliares para parsear e formatar saída dos scripts
    public function parseOutput($output)
    {
        $linhas = explode("\n", trim($output));
        $frases = [];
        $seguroAtual = [];
        foreach ($linhas as $linha) {
            if (trim($linha) === '=====') {
                if (!empty($seguroAtual)) {
                    $frases[] = $this->formatSeguro($seguroAtual);
                    $seguroAtual = [];
                }
            } else {
                $seguroAtual[] = $linha;
            }
        }
        if (!empty($seguroAtual)) {
            $frases[] = $this->formatSeguro($seguroAtual);
        }
        return $frases;
    }

    public function formatSeguro(array $seguro)
    {
        $link = '';
        $lastLine = end($seguro);
        if ($lastLine && is_string($lastLine) && str_starts_with($lastLine, 'http')) {
            $link = array_pop($seguro);
        }
        $site = array_shift($seguro);
        if (stripos($site, 'seguropromo') !== false || stripos($site, 'affinityseguro') !== false) {
            $dadosFormatados = [];
            foreach ($seguro as $linha) {
                $linha = mb_convert_encoding($linha, 'UTF-8', 'auto');
                $linha = trim($linha);
                $partes = preg_split('/[:\-\s]{2,}/', $linha);
                foreach ($partes as $parte) {
                    $parte = trim($parte);
                    if (!empty($parte)) {
                        $dadosFormatados[] = $parte;
                    }
                }
            }
            $seguro = $dadosFormatados;
        }
        return [
            'site' => $site,
            'dados' => $seguro,
            'link' => $link,
        ];
    }

    private function extrairValorNumerico($texto)
    {
        if (preg_match('/((US\$|USD|R\$|€|U\$)?\s*)?([\d\.,]+)/i', $texto, $matches)) {
            $moeda = strtoupper($matches[2] ?? '');
            $valor = str_replace(['.', ','], ['', '.'], $matches[3]);
            $valorFormatado = number_format((float) $valor, 2, '.', '');
            switch ($moeda) {
                case 'USD': case 'U$': $moeda = 'US$'; break;
                case 'R$': $moeda = 'R$'; break;
                case '€': $moeda = '€'; break;
                case 'US$': break;
                default: $moeda = 'R$'; break;
            }
            return trim($moeda . ' ' . $valorFormatado);
        }
        return null;
    }
}





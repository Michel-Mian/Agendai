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

    public function salvarSeguro(Request $request) 
{
    $data = $request->all();

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

        // === Cobertura médica ===
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

        // === Cobertura bagagem ===
        if (str_contains($linha_lower, 'bagagem')) {
            preg_match_all('/(us[d\$])\s*[\d\.,]+/i', $linha, $matches);
            if (!empty($matches[0])) {
                $ultimo_valor = end($matches[0]);
                $cobertura_bagagem = $this->extrairValorNumerico($ultimo_valor);
            } else {
                $esperando_valor_para = 'cobertura_bagagem';
            }
        }

        // === Cancelamento ===
        if (str_contains($linha_lower, 'cancelamento')) {
            if (preg_match('/us[d\$]\s*[\d\.,]+/i', $linha)) {
                $cobertura_cancelamento = $this->extrairValorNumerico($linha);
            } else {
                $esperando_valor_para = 'cobertura_cancelamento';
            }
        }

        // === Valor na linha seguinte ===
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

        // === Preço PIX ===
        if (str_contains($linha_lower, 'preço pix') || (str_contains($linha_lower, 'pix') && str_contains($linha_lower, 'r$'))) {
            $preco_pix = $this->extrairValorNumerico($linha);
        }

        // === Preço Cartão ===
        if (
            (str_contains($linha_lower, 'cartão') && str_contains($linha_lower, 'r$')) ||
            (str_contains($linha_lower, 'em até') && str_contains($linha_lower, 'x') && str_contains($linha_lower, 'r$'))
        ) {
            $preco_cartao = $this->extrairValorNumerico($linha);
        }

        // === Parcelas ===
        if (
            str_contains($linha_lower, 'x de r$') ||
            preg_match('/\d+x.*(sem juros|no cartão)/i', $linha)
        ) {
            $linha_limpa = trim(preg_replace('/^ou\s+/i', '', $linha));
            $parcelas = $linha_limpa;
        }

        // === Preço Total ===
        if (str_contains($linha_lower, 'total à vista')) {
            $preco = $this->extrairValorNumerico($linha);
        }

        if ($preco === null && str_contains($linha_lower, 'r$')) {
            $preco = $this->extrairValorNumerico($linha);
        }

        $previousLine = $linha;
    }

    // === Limpa o título (remove aspas e espaços extras) ===
    $titulo_bruto = $data['dados'][0] ?? 'Título não informado';
    $titulo_limpo = trim($titulo_bruto);
    $titulo_limpo = str_replace(['\\"', "\\'", '"', "'"], '', $titulo_limpo);

    $seguro = new \App\Models\Seguros();
    $seguro->site = $data['site'];
    $seguro->titulo = $titulo_limpo;
    $seguro->link = !empty($data['link']) ? $data['link'] : null;
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
    $seguro->preco_pix = $preco_pix;
    $seguro->preco_cartao = $preco_cartao;
    $seguro->parcelas = $parcelas;
    $seguro->preco = $preco;
    $seguro->save();

    return response()->json(['mensagem' => 'Seguro salvo com sucesso!']);
}


private function extrairValorComMoeda($texto)
{
    preg_match('/(usd|us\$|r\$)?\s*([\d\.]+(?:,\d{2})?)/i', $texto, $matches);
    if (isset($matches[2])) {
        $moeda = strtoupper($matches[1] ?? '');
        $numero = str_replace(['.', ','], ['', '.'], $matches[2]);
        return trim($moeda . ' ' . number_format((float) $numero, 2, '.', ''));
    }
    return null;
}

private function extrairValorNumerico($texto)
{
    if (preg_match('/((US\$|USD|R\$|€|U\$)?\s*)?([\d\.,]+)/i', $texto, $matches)) {
        $moeda = strtoupper($matches[2] ?? '');
        $valor = str_replace(['.', ','], ['', '.'], $matches[3]);

        // Garante duas casas decimais
        $valorFormatado = number_format((float) $valor, 2, '.', '');

        // Corrige o símbolo da moeda
        switch ($moeda) {
            case 'USD':
            case 'U$':
                $moeda = 'US$';
                break;
            case 'R$':
                $moeda = 'R$';
                break;
            case '€':
                $moeda = '€';
                break;
            case 'US$':
                // Já está certo
                break;
            default:
                $moeda = 'R$'; // padrão
                break;
        }

        return trim($moeda . ' ' . $valorFormatado);
    }

    return null;
}

}
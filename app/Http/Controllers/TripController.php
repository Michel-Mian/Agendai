<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seguros;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    // Exibe o formulário de pesquisa de seguros
    public function showForm()
    {
        $insurances = Seguros::latest()->get();
        return view('formTrip', ['insurances' => $insurances]);
    }

    // Roda os scripts de scraping em paralelo e retorna os seguros padronizados
    // Em app/Http/Controllers/TripController.php

    public function scrapingAjax(Request $request)
    {
        try {
            \Log::info('scrapingAjax iniciado', [
                'method' => $request->method(),
                'all_data' => $request->all(),
            ]);

            $request->validate([
                'destino' => ['required', 'integer', Rule::in([1, 2, 4, 5, 6, 7, 11, 12, 13, 14])],
                'data_ida' => 'required|date|after_or_equal:today',
                'data_volta' => 'required|date|after:data_ida',
                'idades' => 'nullable|array',
                'idades.*' => 'integer|min:0|max:120'
            ]);

            // Usar apenas o destino e datas para cache inicial
            $baseCacheKey = 'seguros:' . md5(json_encode([
                'destino' => (int) $request->input('destino'),
                'data_ida' => Carbon::parse($request->input('data_ida'))->toDateString(),
                'data_volta' => Carbon::parse($request->input('data_volta'))->toDateString()
            ]));

            $cacheRow = \DB::table('seguros_cache')->where('cache_key', $baseCacheKey)->first();
            $idades = $request->input('idades', []);

            // Se temos cache válido
            if ($cacheRow && $cacheRow->status === 'completed' && !is_null($cacheRow->result_json)) {
                $seguros = json_decode($cacheRow->result_json, true) ?? [];
                
                // Se não foram fornecidas idades específicas, retorna todos os seguros
                if (empty($idades)) {
                    return response()->json([
                        'frases' => $seguros,
                        'status' => 'concluido'
                    ]);
                }
                
                // Filtrar por idade se foram fornecidas
                $allResults = [];
                foreach ($idades as $idade) {
                    $segurosParaIdade = array_filter($seguros, function($seguro) use ($idade) {
                        return $this->seguroAtendeFaixaEtaria($seguro, $idade);
                    });

                    if (!empty($segurosParaIdade)) {
                        $allResults[$idade] = array_values($segurosParaIdade);
                    }
                }

                if (!empty($allResults)) {
                    return response()->json([
                        'frases' => $allResults,
                        'status' => 'concluido'
                    ]);
                }
            }

            // Se não temos cache ou está vazio/expirado
            if (!$cacheRow || $cacheRow->status === 'failed' || 
                ($cacheRow->status === 'processing' && Carbon::parse($cacheRow->started_at)->diffInMinutes(now()) > 10)) {

                $lockKey = "scraping_lock:{$baseCacheKey}";
                $lock = Cache::lock($lockKey, 360);

                if ($lock->get()) {
                    try {
                        \DB::table('seguros_cache')->updateOrInsert(
                            ['cache_key' => $baseCacheKey],
                            [
                                'result_json' => null,
                                'status' => 'processing',
                                'started_at' => now(),
                                'updated_at' => now()
                            ]
                        );

                        // Dispatch job com parâmetros básicos
                        \App\Jobs\ScrapeInsuranceJob::dispatch([
                            'cache_key' => $baseCacheKey,
                            'params' => [
                                'destino' => (int) $request->input('destino'),
                                'data_ida' => Carbon::parse($request->input('data_ida'))->toDateString(),
                                'data_volta' => Carbon::parse($request->input('data_volta'))->toDateString()
                            ]
                        ]);

                    } finally {
                        $lock->release();
                    }
                }
            }

            return response()->json([
                'frases' => [],
                'status' => 'carregando'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro crítico no scrapingAjax', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Erro interno do servidor', 'message' => $e->getMessage()], 500);
        }
    }

    private function seguroAtendeFaixaEtaria($seguro, $idade)
    {
        if (empty($seguro['detalhes_etarios'])) {
            return true; // Se não tem restrição, aceita qualquer idade
        }

        $detalhes = mb_strtolower($seguro['detalhes_etarios']);

        // Padrões comuns de faixa etária
        $patterns = [
            '/até (\d+)/i' => function($matches) use ($idade) {
                return $idade <= (int)$matches[1];
            },
            '/(\d+)\s*(?:a|até)\s*(\d+)/i' => function($matches) use ($idade) {
                return $idade >= (int)$matches[1] && $idade <= (int)$matches[2];
            },
            '/acima de (\d+)/i' => function($matches) use ($idade) {
                return $idade > (int)$matches[1];
            },
            '/maior que (\d+)/i' => function($matches) use ($idade) {
                return $idade > (int)$matches[1];
            },
            '/menor que (\d+)/i' => function($matches) use ($idade) {
                return $idade < (int)$matches[1];
            },
            '/(\d+)\s*anos/i' => function($matches) use ($idade) {
                return $idade == (int)$matches[1];
            }
        ];

        foreach ($patterns as $pattern => $validator) {
            if (preg_match($pattern, $detalhes, $matches)) {
                return $validator($matches);
            }
        }

        // Se não encontrou nenhum padrão conhecido, aceita o seguro
        return true;
    }

    private function filtrarSegurosPorIdade($seguros, $idade)
    {
        return array_filter($seguros, function($seguro) use ($idade) {
            if (empty($seguro['detalhes_etarios'])) {
                return true; // Se não tem restrição de idade, permite para todos
            }

            $detalhes = strtolower($seguro['detalhes_etarios']);
            
            // Verifica diferentes formatos de faixa etária
            if (preg_match('/até (\d+)/', $detalhes, $matches)) {
                return $idade <= (int)$matches[1];
            }
            
            if (preg_match('/(\d+) a (\d+)/', $detalhes, $matches)) {
                return $idade >= (int)$matches[1] && $idade <= (int)$matches[2];
            }
            
            if (preg_match('/maior que (\d+)/', $detalhes, $matches)) {
                return $idade > (int)$matches[1];
            }
            
            if (preg_match('/menor que (\d+)/', $detalhes, $matches)) {
                return $idade < (int)$matches[1];
            }
            
            // Se não conseguiu identificar o formato, retorna true para incluir o seguro
            return true;
        });
    }

    // AJAX: Retorna todos os seguros da viagem
    public function getInsurancesAjax(Request $request)
    {
        try {
            // Tenta pegar o trip_id da requisição, senão pega da sessão
            $tripId = $request->input('trip_id') ?? session('trip_id');
            if (!$tripId) {
                // Retorna vazio se não houver trip_id
                return response()->json(['seguros' => []]);
            }
            // Busca todos os seguros da viagem, ordenados por criação (mais recentes primeiro)
            $seguros = \App\Models\Seguros::where('fk_id_viagem', $tripId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Remove duplicados por site+nome (mantém o primeiro de cada combinação)
            $unicos = [];
            $segurosFiltrados = [];
            foreach ($seguros as $seguro) {
                $dados = $seguro->dados;
                if (is_string($dados)) {
                    try { $dados = json_decode($dados, true); } catch (\Exception $e) {}
                }
                $nome = $seguro->site . '|' . (is_array($dados) && isset($dados[0]) ? $dados[0] : '');
                if (!isset($unicos[$nome])) {
                    $unicos[$nome] = true;
                    $blocos = $this->filtrarBlocosDados($dados);
                    $seguro->dados_filtrados = $blocos;
                    $segurosFiltrados[] = $seguro;
                }
            }
            return response()->json(['seguros' => $segurosFiltrados]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar seguros', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['seguros' => []], 500);
        }
    }

    // AJAX: Troca o seguro selecionado da viagem
    public function updateInsuranceAjax(Request $request)
    {
        try {
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

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar seguro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'mensagem' => 'Erro interno'], 500);
        }
    }


    // Funções auxiliares para parsear e formatar saída dos scripts
    public function parseOutput($output)
    {
        if (empty(trim($output))) {
            Log::warning('Output vazio recebido para parsing');
            return [];
        }
        
        // Garantir encoding UTF-8
        $output = mb_convert_encoding($output, 'UTF-8', 'auto');
        
        $blocosDeSeguro = explode('=====', trim($output));
        
        $frasesFinais = [];

        foreach ($blocosDeSeguro as $bloco) {
            $blocoLimpo = trim($bloco);
            if (empty($blocoLimpo)) {
                continue;
            }
            
            $linhasDoSeguro = explode("\n", $blocoLimpo);
            
            $linhasValidas = array_filter($linhasDoSeguro, function($linha) {
                return !empty(trim($linha));
            });

            if (!empty($linhasValidas)) {
                $frasesFinais[] = $this->formatSeguro($linhasValidas);
            }
        }

        return array_filter($frasesFinais);
    }

    public function formatSeguro(array $linhasDoSeguro)
    {
        // 1. Encontra e extrai o link primeiro, removendo-o do array principal.
        $link = '';
        $dadosSemLink = [];
        foreach ($linhasDoSeguro as $linha) {
            $linhaLimpa = trim($linha);
            if (str_starts_with($linhaLimpa, 'Link: https://')) {
                $link = str_replace('Link: ', '', $linhaLimpa);
            } else {
                $dadosSemLink[] = $linhaLimpa; // Adiciona apenas as outras linhas
            }
        }

        // 2. Agora, trabalha com os dados já sem o link.
        $site = array_shift($dadosSemLink); // A primeira linha é sempre o site.

        $structuredData = [
            'site' => $site,
            'seguradora' => '',
            'plano' => '',
            'coberturas' => ['medica' => 'N/A', 'bagagem' => 'N/A'],
            'precos' => ['pix' => 'N/A', 'cartao' => 'N/A', 'parcelas' => ''],
            'detalhes_etarios' => '',
            'link' => $link // O link já foi extraído
        ];

        // 3. Processa as linhas restantes
        foreach ($dadosSemLink as $linha) {
            if (str_starts_with($linha, 'Seguradora: ')) {
                $structuredData['seguradora'] = str_replace('Seguradora: ', '', $linha);
            } elseif (str_starts_with($linha, 'Plano: ')) {
                $structuredData['plano'] = str_replace('Plano: ', '', $linha);
            } elseif (
                str_starts_with($linha, 'Detalhes etários: ') ||
                str_starts_with($linha, 'Faixa etária:') ||
                stripos($linha, 'faixa etária') !== false ||
                stripos($linha, 'faixa etaria') !== false ||
                stripos($linha, 'detalhes etari') !== false ||
                stripos($linha, 'idade') !== false
            ) {
                // Extrair o conteúdo após ":" se houver, senão usar a linha inteira.
                $parts = preg_split('/:\s*/', $linha, 2);
                $valor = isset($parts[1]) ? trim($parts[1]) : trim($linha);
                // Normalizar espaços e pipes (manter o texto original para exibição,
                // mas garantir que o frontend consiga parsear faixas de idade)
                $structuredData['detalhes_etarios'] = $valor;
             } elseif (str_starts_with($linha, 'Despesa médica hospitalar: ')) {
                $structuredData['coberturas']['medica'] = str_replace('Despesa médica hospitalar: ', '', $linha);
            } elseif (str_starts_with($linha, 'Seguro bagagem: ')) {
                $structuredData['coberturas']['bagagem'] = str_replace('Seguro bagagem: ', '', $linha);
            } elseif (str_starts_with($linha, 'Preço PIX: ')) {
                $structuredData['precos']['pix'] = str_replace('Preço PIX: ', '', $linha);
            } elseif (str_starts_with($linha, 'Preço Cartão: ')) {
                $structuredData['precos']['cartao'] = str_replace('Preço Cartão: ', '', $linha);
            } elseif (str_starts_with($linha, 'Parcelamento Cartão: ')) {
                $structuredData['precos']['parcelas'] = str_replace('Parcelamento Cartão: ', '', $linha);
            }
        }

        return $structuredData;
    }

    private function formatPriceForDatabase($priceString)
    {
        if (is_null($priceString)) {
            return null;
        }

        // 1. Remove tudo que não for dígito, vírgula ou ponto, similar à sua regex.
        $numericPart = preg_replace('/[^\d,.]/', '', $priceString);
        
        // 2. Remove o separador de milhar (ponto) e troca a vírgula decimal por ponto.
        //    Isso lida corretamente com formatos como "1.234,56".
        $cleaned = str_replace('.', '', $numericPart);
        $decimal = str_replace(',', '.', $cleaned);

        // 3. Retorna o valor como float, pronto para o banco de dados.
        return (float) $decimal;
    }

    private function extrairValorNumerico($texto)
    {
        if (preg_match('/((US\$|USD|R\$|€|U\$)?\s*)?([d\.,]+)/i', $texto, $matches)) {
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

    // Novo método auxiliar para filtrar blocos relevantes dos dados do seguro
    private function filtrarBlocosDados($dados)
    {
        $linhas = is_array($dados) ? $dados : [$dados];
        $blocos = [];
        foreach ($linhas as $linha) {
            $linha_lower = mb_strtolower($linha);
            if (
                str_contains($linha_lower, 'despesas médico') ||
                str_contains($linha_lower, 'despesas médicas') ||
                str_contains($linha_lower, 'despesa médica hospitalar') ||
                str_contains($linha_lower, 'dmh') ||
                str_contains($linha_lower, 'bagagem') ||
                str_contains($linha_lower, 'cancelamento') ||
                str_contains($linha_lower, 'odontológicas') || str_contains($linha_lower, 'odontológica') ||
                str_contains($linha_lower, 'medicamentos') ||
                str_contains($linha_lower, 'eletrônicos') ||
                str_contains($linha_lower, 'mochila') || str_contains($linha_lower, 'mão protegida') ||
                str_contains($linha_lower, 'atraso de embarque') ||
                str_contains($linha_lower, 'pet') ||
                str_contains($linha_lower, 'sala vip') ||
                str_contains($linha_lower, 'telemedicina') ||
                str_contains($linha_lower, 'preço pix') ||
                (str_contains($linha_lower, 'pix') && str_contains($linha_lower, 'r$')) ||
                (str_contains($linha_lower, 'cartão') && str_contains($linha_lower, 'r$')) ||
                (str_contains($linha_lower, 'em até') && str_contains($linha_lower, 'x') && str_contains($linha_lower, 'r$')) ||
                str_contains($linha_lower, 'x de r$') ||
                preg_match('/\d+x.*(sem juros|no cartão)/i', $linha) ||
                str_contains($linha_lower, 'total à vista') ||
                (str_contains($linha_lower, 'r$'))
            ) {
                $blocos[] = $linha;
            }
        }
        return $blocos;
    }

    protected function parseScrapingOutput($output, $site)
    {
        \Log::debug('parseScrapingOutput - input', ['site' => $site, 'output' => $output]);
        // Adicione logs ao processar cada seguro
        $seguros = [];
        foreach ($output as $line) {
            // ...parse...
            \Log::debug('parseScrapingOutput - linha', ['line' => $line]);
            // ...monta seguro...
        }
        \Log::debug('parseScrapingOutput - seguros montados', $seguros);
        return $seguros;
    }

    private function getDestinoFallback($destino)
    {
        $fallbacks = [
            1 => 2,  // América do Norte -> Europa
            3 => 1,  // Caribe/México -> América do Norte  
            4 => 2,  // América do Sul -> Europa
            5 => 2,  // África -> Europa
            6 => 2,  // Ásia -> Europa
            7 => 2,  // Oceania -> Europa
            11 => 2, // Oriente Médio -> Europa
        ];
        
        return $fallbacks[$destino] ?? null;
    }

    private function isDestinoProblematico($destino, $idades)
    {
        $idade_maxima = max($idades);
        
        // Destinos que têm mais restrições para idades elevadas
        $destinos_restritivos = [5, 6, 7, 11]; // África, Ásia, Oceania, Oriente Médio
        
        return in_array($destino, $destinos_restritivos) && $idade_maxima > 70;
    }

    // Função para debug do scraping
    public function debugScraping(Request $request)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $pythonCmd = $isWindows ? 'python' : 'python3';
        
        $cmd = "$pythonCmd --version";
        $output = shell_exec($cmd);
        
        $scriptPath = base_path('scripts/webscraping/scrapingSP.py');
        
        return response()->json([
            'python_version' => $output,
            'script_exists' => file_exists($scriptPath),
            'script_path' => $scriptPath,
            'os' => PHP_OS,
            'is_windows' => $isWindows,
            'cache_table' => \DB::table('seguros_cache')->count()
        ]);
    }

    public function saveInsuranceForTraveler(Request $request)
    {
        try {
            $validated = $request->validate([
                'fk_id_viagem'   => 'required|integer|exists:viagens,pk_id_viagem',
                'fk_id_viajante' => 'required|integer|exists:viajantes,pk_id_viajante',
                'seguro_data'    => 'required|array',
                'seguro_data.seguradora'         => 'required|string|max:255',
                'seguro_data.plano'              => 'required|string|max:255',
                'seguro_data.link'               => 'nullable|url',
                'seguro_data.detalhes_etarios'   => 'nullable|string',
                'seguro_data.coberturas.medica'  => 'nullable|string',
                'seguro_data.coberturas.bagagem' => 'nullable|string',
                'seguro_data.precos.pix'         => 'nullable|string',
                'seguro_data.precos.cartao'      => 'nullable|string',
                'seguro_data.precos.parcelas'    => 'nullable|string',
            ]);

            // Validação de permissão: Apenas o dono da viagem pode adicionar seguros
            $viagem = \App\Models\Viagens::findOrFail($validated['fk_id_viagem']);
            if ($viagem->fk_id_usuario !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
            }

            $seguroData = $validated['seguro_data'];

            // `updateOrCreate` é perfeito aqui: ele atualiza o seguro se o viajante já tiver um,
            // ou cria um novo registro caso contrário. Isso evita duplicatas.
            $seguro = Seguros::updateOrCreate(
                [
                    // Condições para encontrar o registro
                    'fk_id_viagem' => $validated['fk_id_viagem'],
                    'fk_id_viajante' => $validated['fk_id_viajante'],
                ],
                [
                    // Dados para atualizar ou criar
                    'seguradora' => $seguroData['seguradora'],
                    'plano' => $seguroData['plano'],
                    'link' => $seguroData['link'] ?? null,
                    'detalhes_etarios' => $seguroData['detalhes_etarios'] ?? null,
                    'cobertura_medica' => $seguroData['coberturas']['medica'] ?? null,
                    'cobertura_bagagem' => $seguroData['coberturas']['bagagem'] ?? null,
                    'preco_pix' => $this->formatPriceForDatabase($seguroData['precos']['pix'] ?? null),
                    'preco_cartao' => $this->formatPriceForDatabase($seguroData['precos']['cartao'] ?? null),
                    'parcelamento_cartao' => $seguroData['precos']['parcelas'] ?? null,
                    'is_selected' => true,
                ]
            );

            return response()->json([
                'success' => true, 
                'message' => 'Seguro salvo com sucesso!',
                'seguro' => $seguro // Retornamos o seguro salvo para a UI se atualizar
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar seguro para viajante', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erro interno do servidor.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Viagens;
use App\Models\AiChatMessage;

class AiChatController extends Controller
{
    /**
     * Processar mensagem do chat e retornar resposta da IA
     */
    public function sendMessage(Request $request)
    {
        try {
            Log::info('Recebendo mensagem do chat IA', [
                'message' => $request->input('message'),
                'trip_id' => $request->input('trip_id')
            ]);

            $request->validate([
                'message' => 'required|string|max:1000',
                'trip_id' => 'required|integer|exists:viagens,pk_id_viagem'
            ]);

            $message = $request->input('message');
            $tripId = $request->input('trip_id');

            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado'
                ], 401);
            }

            $userId = $user->id;

            // Buscar informações da viagem
            $viagem = Viagens::with(['destinos', 'viajantes', 'objetivos', 'hotel', 'veiculos', 'viagemCarro', 'seguros', 'user', 'veiculoSelecionado'])
                ->findOrFail($tripId);

            // Preparar contexto da viagem para a IA
            $context = $this->prepareContext($viagem);

            // Buscar histórico de mensagens (últimas 10)
            $chatHistory = AiChatMessage::where('viagem_id', $tripId)
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->values();

            Log::info('Contexto preparado', [
                'context' => $context,
                'history_count' => $chatHistory->count()
            ]);

            // Limitar total de mensagens por usuário/viagem (máximo 100)
            $totalMessages = AiChatMessage::where('viagem_id', $tripId)
                ->where('user_id', $userId)
                ->count();

            if ($totalMessages >= 100) {
                // Deletar as 20 mensagens mais antigas
                $oldestMessages = AiChatMessage::where('viagem_id', $tripId)
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'asc')
                    ->limit(20)
                    ->pluck('id');
                
                AiChatMessage::whereIn('id', $oldestMessages)->delete();
                Log::info('Mensagens antigas removidas para manter limite', ['deleted' => 20]);
            }

            // Salvar mensagem do usuário
            AiChatMessage::create([
                'viagem_id' => $tripId,
                'user_id' => $userId,
                'role' => 'user',
                'content' => $message
            ]);

            // Chamar API do Google Gemini com histórico
            $aiResponse = $this->callGeminiApi($message, $context, $chatHistory);

            // Salvar resposta da IA
            AiChatMessage::create([
                'viagem_id' => $tripId,
                'user_id' => $userId,
                'role' => 'assistant',
                'content' => $aiResponse
            ]);

            Log::info('Resposta da IA recebida', ['response_length' => strlen($aiResponse)]);

            return response()->json([
                'success' => true,
                'message' => $aiResponse,
                'timestamp' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar mensagem do chat IA: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, houve um erro ao processar sua mensagem. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Stream de resposta da IA usando Server-Sent Events
     */
    public function streamMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'trip_id' => 'required|integer|exists:viagens,pk_id_viagem'
            ]);

            $message = $request->input('message');
            $tripId = $request->input('trip_id');

            // Buscar informações da viagem
            $viagem = Viagens::with(['destinos', 'viajantes', 'objetivos', 'hotel', 'veiculos', 'viagemCarro', 'seguros', 'user', 'veiculoSelecionado'])
                ->findOrFail($tripId);

            // Preparar contexto da viagem para a IA
            $context = $this->prepareContext($viagem);

            // Retornar resposta como Server-Sent Events
            return response()->stream(function () use ($message, $context) {
                $this->streamGeminiResponse($message, $context);
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao fazer streaming da mensagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a IA.'
            ], 500);
        }
    }

    /**
     * Preparar contexto da viagem para a IA
     */
    private function prepareContext($viagem)
    {
        // hospedagens (hotels)
        $hotels = $viagem->hotel ? $viagem->hotel->map(function ($h) {
            return [
                'nome' => $h->nome_hotel,
                'checkin' => $h->data_check_in ?? null,
                'checkout' => $h->data_check_out ?? null,
                'preco' => $h->preco ?? null,
                'avaliacao' => $h->avaliacao ?? null,
                'image_url' => $h->image_url ?? null,
            ];
        })->toArray() : [];

        // veículos e veículo selecionado
        $veiculos = $viagem->veiculos ? $viagem->veiculos->map(function ($v) {
            return [
                'nome' => $v->nome_veiculo,
                'categoria' => $v->categoria ?? null,
                'preco_total' => $v->preco_total ?? null,
                'is_selected' => (bool)($v->is_selected ?? false)
            ];
        })->toArray() : [];

        $veiculoSelecionado = $viagem->veiculoSelecionado ? [
            'nome' => $viagem->veiculoSelecionado->nome_veiculo,
            'categoria' => $viagem->veiculoSelecionado->categoria ?? null,
            'preco_total' => $viagem->veiculoSelecionado->preco_total ?? null,
        ] : null;

        $viagemCarro = $viagem->viagemCarro ? [
            'autonomia_km_l' => $viagem->viagemCarro->autonomia_veiculo_km_l ?? null,
            'tipo_combustivel' => $viagem->viagemCarro->tipo_combustivel ?? null,
            'preco_combustivel_l' => $viagem->viagemCarro->preco_combustivel_litro ?? null,
            'distancia_total_km' => $viagem->viagemCarro->distancia_total_km ?? null,
            'custo_combustivel_estimado' => $viagem->viagemCarro->custo_combustivel_estimado ?? null,
            'pedagio_estimado' => $viagem->viagemCarro->pedagio_estimado ?? null,
            'duracao_texto' => $viagem->viagemCarro->getDuracaoTextoAttribute() ?? null,
        ] : null;

        // seguros vinculados à viagem
        $seguros = $viagem->seguros ? $viagem->seguros->map(function ($s) {
            return [
                'nome' => $s->nome_seguro ?? null,
                'valor' => $s->preco ?? null,
                'is_selected' => (bool)($s->is_selected ?? false)
            ];
        })->toArray() : [];

        // moeda do usuário dono da viagem, fallback BRL
        $moeda = 'BRL';
        try {
            if ($viagem->user && isset($viagem->user->currency)) {
                $moeda = $viagem->user->currency ?: 'BRL';
            }
        } catch (\Exception $e) {
            // ignore and fallback
        }

        // detectar meio de locomoção preferencial com heurísticas
        $meioDetectado = 'outro';
        // se existe campo salvo diretamente na viagem, use como prioridade
        if (!empty($viagem->meio_locomocao)) {
            $raw = mb_strtolower($viagem->meio_locomocao);
            if (str_contains($raw, 'carro') && str_contains($raw, 'pr')) {
                $meioDetectado = 'carro_proprio';
            } elseif (str_contains($raw, 'carro') || str_contains($raw, 'alug')) {
                $meioDetectado = 'carro_alugado';
            } elseif (str_contains($raw, 'ônibus') || str_contains($raw, 'onibus') || str_contains($raw, 'bus')) {
                $meioDetectado = 'onibus';
            } elseif (str_contains($raw, 'avi') || str_contains($raw, 'aeron')) {
                $meioDetectado = 'aviao';
            } else {
                $meioDetectado = $viagem->meio_locomocao;
            }
        } else {
            // inferir a partir de registros relacionados
            if (!empty($viagem->viagemCarro)) {
                $meioDetectado = 'carro_proprio';
            } elseif (!empty($viagem->veiculo_selecionado) || (!empty($viagem->veiculos) && count($viagem->veiculos) > 0)) {
                $meioDetectado = 'carro_alugado';
            } elseif (!empty($viagem->voos) && count($viagem->voos) > 0) {
                $meioDetectado = 'aviao';
            } else {
                $meioDetectado = 'outro';
            }
        }

        return [
            'nome_viagem' => $viagem->nome_viagem,
            'origem' => $viagem->origem_viagem,
            'data_inicio' => $viagem->data_inicio_viagem,
            'data_fim' => $viagem->data_final_viagem,
            'orcamento' => $viagem->orcamento_viagem,
            'num_destinos' => $viagem->destinos ? $viagem->destinos->count() : 0,
            'num_viajantes' => $viagem->viajantes ? $viagem->viajantes->count() : 0,
            'destinos' => $viagem->destinos ? $viagem->destinos->pluck('nome_destino')->toArray() : [],
            'objetivos' => $viagem->objetivos ? $viagem->objetivos->pluck('nome_objetivo')->toArray() : [],
            'hospedagens' => $hotels,
            'hotel_count' => count($hotels),
            'veiculos' => $veiculos,
            'veiculo_selecionado' => $veiculoSelecionado,
            'viagem_carro' => $viagemCarro,
            'seguros' => $seguros,
            'moeda' => $moeda,
            'meio_locomocao_detectado' => $meioDetectado
        ];
    }

    /**
     * Chamar API do Google Gemini (modo simples, sem streaming)
     */
    private function callGeminiApi($userMessage, $context, $chatHistory = null)
    {
        $apiKey = config('services.google_gemini.api_key');
        
        Log::info('Verificando chave API', ['has_key' => !empty($apiKey)]);
        
        if (empty($apiKey)) {
            Log::warning('Chave da API do Google Gemini não configurada');
            return $this->generateSimulatedResponse($userMessage, $context);
        }

        try {
            $prompt = $this->buildPrompt($userMessage, $context, $chatHistory);
            
            Log::info('Chamando API do Gemini', ['prompt_length' => strlen($prompt)]);
            
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]
            );

            Log::info('Resposta da API Gemini', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Dados da resposta', ['data_keys' => array_keys($data)]);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
                
                Log::warning('Formato de resposta inesperado', ['data' => $data]);
            }

            Log::error('Erro na resposta da API Gemini', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return $this->generateSimulatedResponse($userMessage, $context);

        } catch (\Exception $e) {
            Log::error('Exceção ao chamar API Gemini', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->generateSimulatedResponse($userMessage, $context);
        }
    }

    /**
     * Stream de resposta do Google Gemini usando SSE
     */
    private function streamGeminiResponse($userMessage, $context)
    {
        $apiKey = config('services.google_gemini.api_key');
        
        if (empty($apiKey)) {
            $this->sendSSE('error', 'API key não configurada');
            return;
        }

        try {
            $prompt = $this->buildPrompt($userMessage, $context);
            
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:streamGenerateContent?key={$apiKey}&alt=sse",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]
            );

            if ($response->successful()) {
                // Processar response stream
                $body = $response->body();
                $lines = explode("\n", $body);
                
                foreach ($lines as $line) {
                    if (strpos($line, 'data: ') === 0) {
                        $jsonData = substr($line, 6);
                        $data = json_decode($jsonData, true);
                        
                        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];
                            $this->sendSSE('message', $text);
                        }
                    }
                }
                
                $this->sendSSE('done', '');
            } else {
                $this->sendSSE('error', 'Erro ao conectar com a IA');
            }

        } catch (\Exception $e) {
            Log::error('Erro no streaming Gemini: ' . $e->getMessage());
            $this->sendSSE('error', 'Erro ao processar resposta');
        }
    }

    /**
     * Construir prompt com contexto da viagem
     */
    private function buildPrompt($userMessage, $context, $chatHistory = null)
    {
        $destinosText = !empty($context['destinos']) ? implode(', ', $context['destinos']) : 'nenhum destino cadastrado';
        $objetivosText = !empty($context['objetivos']) ? implode(', ', $context['objetivos']) : 'nenhum objetivo cadastrado';
        
        $dias = \Carbon\Carbon::parse($context['data_inicio'])->diffInDays(\Carbon\Carbon::parse($context['data_fim'])) + 1;
        $orcamento = number_format($context['orcamento'], 2, ',', '.');

        // Montar histórico de conversa se existir
        $historyText = '';
        $hasHistory = $chatHistory && $chatHistory->count() > 0;
        
        if ($hasHistory) {
            $historyText = "\n\nHistórico da conversa anterior:\n";
            foreach ($chatHistory as $msg) {
                $role = $msg->role === 'user' ? 'Usuário' : 'Assistente';
                $historyText .= "{$role}: {$msg->content}\n";
            }
            $historyText .= "\n";
        }

        // Instrução específica sobre saudações baseada no histórico
        $greetingInstruction = $hasHistory 
            ? "IMPORTANTE: Esta é uma conversa em andamento. NÃO comece com saudações (olá, oi, bem-vindo, etc). Continue respondendo diretamente a pergunta do usuário de forma natural, como se fosse parte de um diálogo contínuo."
            : "Esta é a primeira mensagem. Você pode começar com uma saudação breve se apropriado.";

        // resumo de hospedagens
        $hospedagensText = 'nenhuma hospedagem cadastrada';
        if (!empty($context['hospedagens'])) {
            $names = array_map(function ($h) { return $h['nome'] ?? null; }, $context['hospedagens']);
            $names = array_filter($names);
            $hospedagensText = count($names) ? implode(', ', array_slice($names, 0, 6)) : $hospedagensText;
        }

        // meio de locomoção principal
        $meioLocomocao = 'não definido';
        if (!empty($context['veiculo_selecionado'])) {
            $meioLocomocao = $context['veiculo_selecionado']['nome'];
        } elseif (!empty($context['veiculos'])) {
            $first = $context['veiculos'][0] ?? null;
            if ($first && !empty($first['nome'])) {
                $meioLocomocao = $first['nome'];
            }
        }

        // resumo da viagem de carro (se existir)
        $viagemCarroResumo = '';
        if (!empty($context['viagem_carro'])) {
            $vc = $context['viagem_carro'];
            $viagemCarroResumo = "Distância aproximada: " . ($vc['distancia_total_km'] ?? '-') . " km; Custo estimado combustível: " . ($vc['custo_combustivel_estimado'] ?? '-') . "; Pedágios: " . ($vc['pedagio_estimado'] ?? '-') . "; Autonomia: " . ($vc['autonomia_km_l'] ?? '-') . " km/l.";
        }

        // human readable do meio detectado
        $meioDetectText = 'não identificado';
        if (!empty($context['meio_locomocao_detectado'])) {
            switch ($context['meio_locomocao_detectado']) {
                case 'carro_proprio':
                    $meioDetectText = 'Carro próprio';
                    break;
                case 'carro_alugado':
                    $meioDetectText = 'Carro alugado';
                    break;
                case 'aviao':
                    $meioDetectText = 'Avião';
                    break;
                case 'onibus':
                    $meioDetectText = 'Ônibus';
                    break;
                default:
                    $meioDetectText = $context['meio_locomocao_detectado'];
            }
        }

        return <<<PROMPT
Você é um assistente de viagens especializado e amigável. Você está ajudando um usuário a planejar a viagem "{$context['nome_viagem']} (esse é apenas o nome da viagem e pode não refletir todos os detalhes específicos).".

Informações da viagem:
- Origem: {$context['origem']}
- Data de início: {$context['data_inicio']}
- Data de término: {$context['data_fim']}
- Duração: {$dias} dias
- Orçamento: R$ {$orcamento}
- Número de viajantes: {$context['num_viajantes']}
- Destinos: {$destinosText}
- Objetivos da viagem: {$objetivosText}
- Hospedagens registradas: {$hospedagensText}
- Meio de locomoção principal: {$meioLocomocao}
- Resumo deslocamento/viagem de carro: {$viagemCarroResumo}
- Meio de locomoção detectado pelo sistema: {$meioDetectText}
- Moeda para transcrever: {$context['moeda']}
{$historyText}
{$greetingInstruction}

Regras importantes:
1. Seja conciso e objetivo (máximo 1-2 parágrafos)
2. Use informações da viagem para personalizar suas respostas
3. Seja específico sobre os destinos mencionados
4. Dê dicas práticas e acionáveis
5. Use emojis ocasionalmente para tornar a conversa mais amigável
6. Responda em português do Brasil
7. Se não souber algo específico, seja honesto mas tente ajudar de outra forma
8. Dê nomes de lugares, restaurantes ou atrações quando possível, pelo menos 5 se o usuário não especificar a quantidade
9. Use o histórico da conversa para manter contexto e referências às mensagens anteriores
10. Se for uma lista, ajuste a formatação para facilitar a leitura
11. Se já existir uma resposta anterior similar, evite repetir a mesma informação
12. Se for referência a algum lugar, restaurante ou item que possa incluir no google maps, deixe o lugar entre [[nome do lugar]], mesmo se estiver em negrito

Pergunta do usuário: {$userMessage}

Responda de forma útil e personalizada:
PROMPT;
    }

    /**
     * Enviar evento Server-Sent Event
     */
    private function sendSSE($event, $data)
    {
        echo "event: {$event}\n";
        echo "data: " . json_encode(['content' => $data]) . "\n\n";
        
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    /**
     * Gerar resposta simulada (fallback quando API não está disponível)
     */
    private function generateSimulatedResponse($message, $context)
    {
        $messageLower = strtolower($message);

        // Respostas baseadas em palavras-chave
        if (strpos($messageLower, 'destino') !== false || strpos($messageLower, 'lugar') !== false || strpos($messageLower, 'turístico') !== false) {
            if (count($context['destinos']) > 0) {
                return "Vejo que você está visitando " . implode(', ', $context['destinos']) . ". Esses são destinos incríveis! 🌍 Posso sugerir pontos turísticos específicos, restaurantes locais ou atividades culturais. O que você gostaria de saber?";
            }
            return "Para a sua viagem '{$context['nome_viagem']}', recomendo pesquisar atrações turísticas principais, museus, parques e pontos históricos. Qual tipo de atração te interessa mais? 🎯";
        }

        if (strpos($messageLower, 'orçamento') !== false || strpos($messageLower, 'dinheiro') !== false || strpos($messageLower, 'gastar') !== false) {
            $orcamento = number_format($context['orcamento'], 2, ',', '.');
            return "Seu orçamento atual é de R$ {$orcamento}. 💰 Posso ajudá-lo a otimizá-lo! Geralmente, para viagens, recomendo dividir: 40% hospedagem, 30% alimentação, 20% passeios e 10% emergências. Deseja dicas de como economizar?";
        }

        if (strpos($messageLower, 'restaurante') !== false || strpos($messageLower, 'comida') !== false || strpos($messageLower, 'comer') !== false) {
            return "Para encontrar ótimos restaurantes, recomendo pesquisar por culinária local autêntica. 🍽️ Experimente pratos típicos da região! Você prefere restaurantes mais sofisticados ou opções mais econômicas e autênticas?";
        }

        if (strpos($messageLower, 'roteiro') !== false || strpos($messageLower, 'itinerário') !== false || strpos($messageLower, 'dia') !== false) {
            $dias = \Carbon\Carbon::parse($context['data_inicio'])->diffInDays(\Carbon\Carbon::parse($context['data_fim'])) + 1;
            return "Sua viagem tem {$dias} dias. 📅 Para um roteiro ideal, sugiro: manhãs para principais atrações, almoços em restaurantes locais, tardes para passeios culturais e noites para experiências gastronômicas. Quer que eu detalhe algum dia específico?";
        }

        if (strpos($messageLower, 'hotel') !== false || strpos($messageLower, 'hospedagem') !== false || strpos($messageLower, 'ficar') !== false) {
            return "Para hospedagem, recomendo procurar locais bem avaliados e próximos às principais atrações. 🏨 Considere fatores como: localização, avaliações, café da manhã incluso e facilidade de transporte. Qual é sua prioridade?";
        }

        if (strpos($messageLower, 'transporte') !== false || strpos($messageLower, 'carro') !== false || strpos($messageLower, 'uber') !== false) {
            return "Para se locomover, avalie: transporte público (mais econômico), aluguel de carro (mais liberdade) ou apps de transporte (mais conforto). 🚗 A escolha depende do seu destino e orçamento. Qual opção te interessa mais?";
        }

        // Resposta padrão inteligente
        $responses = [
            "Interessante! 🤔 Para a viagem '{$context['nome_viagem']}', posso ajudar com sugestões personalizadas. Pode me dar mais detalhes sobre o que você procura?",
            "Entendo sua pergunta! Com base na sua viagem para {$context['origem']}, posso fornecer recomendações específicas. O que mais te interessa saber? ✨",
            "Ótima pergunta! Estou aqui para tornar sua viagem '{$context['nome_viagem']}' inesquecível. Como posso ajudar mais especificamente? 🎒",
            "Perfeito! Tenho muitas dicas para sua viagem. Você gostaria de saber sobre atrações, alimentação, transporte ou hospedagem? 🗺️"
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Obter histórico do chat
     */
    public function getHistory(Request $request)
    {
        try {
            $request->validate([
                'trip_id' => 'required|integer|exists:viagens,pk_id_viagem'
            ]);

            $tripId = $request->input('trip_id');
            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'messages' => []
                ]);
            }

            $userId = $user->id;

            // Buscar últimas 50 mensagens
            $messages = AiChatMessage::where('viagem_id', $tripId)
                ->where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get()
                ->map(function ($message) {
                    return [
                        'role' => $message->role,
                        'content' => $message->content,
                        'timestamp' => $message->created_at->format('H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar histórico do chat: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'messages' => []
            ]);
        }
    }

    /**
     * Salvar ponto de interesse sugerido pela IA na viagem
     */
    public function savePlaceToTrip(Request $request)
    {
        try {
            $request->validate([
                'place_id' => 'required|string|max:100',
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|string|max:512',
                'trip_id' => 'required|integer|exists:viagens,pk_id_viagem'
            ]);

            $tripId = $request->input('trip_id');
            $userId = $request->user() ? $request->user()->id : null;

            // Salvar ponto de interesse (modelo: PontoInteresse)
            $ponto = new \App\Models\PontoInteresse();
            $ponto->viagem_id = $tripId;
            $ponto->nome_ponto = $request->input('name');
            $ponto->endereco_ponto = $request->input('address');
            $ponto->google_place_id = $request->input('place_id');
            $ponto->foto_url = $request->input('photo');
            if ($userId) {
                $ponto->user_id = $userId;
            }
            $ponto->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar ponto de interesse IA: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao salvar ponto de interesse.'], 500);
        }
    }
}

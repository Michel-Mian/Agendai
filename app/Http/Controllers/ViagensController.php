<?php
namespace App\Http\Controllers;
use App\Models\Viagens;
use App\Models\User;
use App\Models\Viajantes;
use App\Models\Objetivos;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Carbon\CarbonInterface;
Carbon::setLocale('pt_BR'); // ou 'pt' se o sistema estiver em português

class ViagensController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $viagens = Viagens::with('viajantes')->where('fk_id_usuario', $user->id)->get();
        return view('viagens/myTrips', [
            'title' => 'Minhas Viagens',
            'viagens' => $viagens
        ]);
    }
    public function show($id, Request $request)
    {
        $viagem = Viagens::with([
            'viajantes',
            'pontosInteresse',
            'voos',
            'objetivos',
            'user'
        ])->findOrFail($id);

        // Busca notícias da SerpAPI
        $destino = $viagem->destino_viagem;
        $apiKey = env('SERPAPI_KEY');
        $categorias = [
            'Cultura' => "Cultura em $destino",
            'Saúde'   => "Saúde em $destino",
            'Entretenimento'   => "Entretenimento em $destino",
            'Esportes' => "Jogos de esporte em $destino",
            'Local'   => "Notícias locais na região de $destino"
        ];
        $noticias = [];
        foreach ($categorias as $tipo => $query) {
            $params = [
                'engine' => 'google_news',
                'q' => $query,
                'hl' => 'pt-br',
                'api_key' => $apiKey,
            ];
            $url = 'https://serpapi.com/search.json?' . http_build_query($params);
            $response = @file_get_contents($url);
            $data = json_decode($response, true);

            $noticia = $data['news_results'][0] ?? null;
            if ($noticia) {
                // Formata a data, se existir
                if (isset($noticia['date'])) {
                    $dataLimpa = preg_replace('/, \+\d{4} UTC$/', '', $noticia['date']);
                    $carbonDate = Carbon::createFromFormat('m/d/Y, h:i A', trim($dataLimpa));
                    $noticia['date'] = $carbonDate->format('d/m/Y H:i');
                }
                $noticia['source_name'] = $noticia['source']['name'] ?? 'Fonte desconhecida';
                $noticia['source_icon'] = $noticia['source']['icon'] ?? '';
                $noticia['q'] = $query;
                unset($noticia['source']);
                $noticias[$tipo] = $noticia;
            }
        }

        // Busca eventos na SerpAPI
        $eventos = [];

        $params = [
            'engine' => 'google_events',
            'q' => 'Events in ' . $destino,
            'start' => 0,
            'api_key' => $apiKey
        ];

        $url = 'https://serpapi.com/search.json?' . http_build_query($params);
        $response = @file_get_contents($url);

        if ($response !== false) {
            $dados = json_decode($response, true);
            $eventosBrutos = $dados['events_results'] ?? [];

            // Formata a data dos eventos
            foreach ($eventosBrutos as $evento) {
                if (isset($evento['date']['start_date'])) {
                    try {
                        $dataFormatada = Carbon::parse($evento['date']['start_date'])->format('d/m/Y');
                        $evento['data_formatada'] = $dataFormatada;
                    } catch (\Exception $e) {
                        $evento['data_formatada'] = 'Data inválida';
                    }
                } else {
                    $evento['data_formatada'] = 'Data não informada';
                }

                $eventos[] = $evento;
            }

        } else {
            Log::error("Erro ao acessar SerpAPI");
        }

        return view('viagens/detailsTrip', [
            'title' => 'Detalhes da Viagem',
            'viagem' => $viagem,
            'viajantes' => $viagem->viajantes,
            'pontosInteresse' => $viagem->pontosInteresse,
            'voos' => $viagem->voos,
            'objetivos' => $viagem->objetivos,
            'usuario' => $viagem->user,
            'noticias' => $noticias,
            'eventos' => $eventos
        ]);
    }


    public function destroyObjetivo($id)
    {
        $objetivo = \App\Models\Objetivos::findOrFail($id);
        $viagemId = $objetivo->fk_id_viagem;
        $objetivo->delete();
        return redirect()->route('viagens', ['id' => $viagemId])->with('success', 'Objetivo removido com sucesso!');
    }

    // Adiciona um novo objetivo à viagem
    public function addObjetivo(Request $request)
    {
        $request->validate([
            'nome_objetivo' => 'required|string|max:100',
            'viagem_id' => 'required|integer|exists:viagens,pk_id_viagem',
        ]);

        $objetivo = new \App\Models\Objetivos();
        $objetivo->nome = $request->input('nome_objetivo');
        $objetivo->fk_id_viagem = $request->input('viagem_id');
        $objetivo->save();

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'objetivo' => [
                    'id' => $objetivo->pk_id_objetivo,
                    'nome' => $objetivo->nome
                ]
            ]);
        }

        // Redireciona de volta para a página da viagem
        return redirect()->route('viagens', ['id' => $objetivo->fk_id_viagem])->with('success', 'Objetivo adicionado com sucesso!');
    }

    public function destroy($id): RedirectResponse
    {
        $viajante = Viajantes::findOrFail($id);
        $viagemId = $viajante->fk_id_viagem;
        $viajante->delete();
        return redirect()->route('viagens', ['id' => $viagemId])
            ->with('success', 'Viajante removido com sucesso!');
    }

    // Adiciona um novo viajante à viagem
    public function addViajante(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'nome_viajante' => 'required|string|max:100',
            'idade_viajante' => 'required|integer|min:0',
            'viagem_id' => 'required|integer|exists:viagens,pk_id_viagem',
        ]);

        $viajante = new Viajantes();
        $viajante->nome = $validated['nome_viajante'];
        $viajante->idade = $validated['idade_viajante'];
        $viajante->fk_id_viagem = $validated['viagem_id'];
        $viajante->save();

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'viajante' => [
                    'id' => $viajante->pk_id_viajante,
                    'nome' => $viajante->nome,
                    'idade' => $viajante->idade
                ]
            ]);
        }

        // Redireciona de volta para a página da viagem
        return redirect()->route('viagens', ['id' => $viajante->fk_id_viagem])->with('success', 'Viajante adicionado com sucesso!');
    }
}

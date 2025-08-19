<?php
namespace App\Http\Controllers;
use App\Models\Viagens;
use App\Models\User;
use App\Models\Viajantes;
use App\Models\Objetivos;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\PontoInteresse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
Carbon::setLocale('pt_BR');


class ViagensController extends Controller
{
    public function dashboardData()
    {
        $user = Auth::user();

        // Quantidade de viagens do usuário
        $totalViagens = Viagens::where('fk_id_usuario', $user->id)->count();

        $hoje = \Carbon\Carbon::today(); // só a data
        $proximaViagem = Viagens::where('fk_id_usuario', $user->id)
            ->whereDate('data_inicio_viagem', '>=', $hoje)
            ->orderBy('data_inicio_viagem', 'asc')
            ->first();;




        return view('dashboard', [
            'user' => $user,
            'totalViagens' => $totalViagens,
            'proximaViagem' => $proximaViagem,
        ]);

    }

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
        $pontos = PontoInteresse::where('fk_id_viagem', $id)
            ->orderBy('data_ponto_interesse')
            ->orderBy('hora_ponto_interesse')
            ->get();
        $viagem = Viagens::with([
            'viajantes',
            'voos',
            'objetivos',
            'user',
            'hotel'
        ])->findOrFail($id);

        // Busca notícias da SerpAPI
        $destino = $viagem->destino_viagem;
        $apiKey = env('SERPAPI_KEY');
        $categorias = [
            'Cultura' => "Cultura em $destino",
            'Saúde' => "Saúde em $destino",
            'Entretenimento' => "Entretenimento em $destino",
            'Esportes' => "Jogos de esporte em $destino",
            'Local' => "Notícias locais na região de $destino"
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

        //Busca o clima na Open-Meteo
        $destino = $viagem->destino_viagem;
        $coordenadas = $this->getLatLngFromAddress($destino);
        $data_inicio = Carbon::parse($viagem->data_inicio_viagem)->format('Y-m-d');
        $data_fim = Carbon::parse($viagem->data_final_viagem)->format('Y-m-d');
        $hoje = Carbon::today();
        $diasDiferenca = $hoje->diffInDays($data_inicio, false);

        $climas = [];
        if ($diasDiferenca < 7) {
            if ($coordenadas) {
                $latitude = $coordenadas['lat'];
                $longitude = $coordenadas['lng'];
                $weatherUrl = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,wind_speed_10m_max,precipitation_sum,rain_sum,precipitation_probability_max&start_date={$data_inicio}&end_date={$data_fim}";
                $weatherResponse = file_get_contents($weatherUrl);
                $clima = json_decode($weatherResponse, true);

                $climas[] = $clima;
            } else {
                $clima = null;
            }
        } else {
            $clima = null;
        }


        return view('viagens/detailsTrip', [
            'title' => 'Detalhes da Viagem',
            'viagem' => $viagem,
            'viajantes' => $viagem->viajantes,
            'pontosInteresse' => $viagem->pontosInteresse,
            'voos' => $viagem->voos->filter(function($voo) {
                return is_object($voo) && $voo !== false;
            }),
            'objetivos' => $viagem->objetivos,
            'usuario' => $viagem->user,
            'hotel' => $viagem->hotel,
            'noticias' => $noticias,
            'eventos' => $eventos,
            'clima' => $clima
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
        return redirect()->route('viagens', ['id' => $viagemId]);
    }

    // Adiciona um novo viajante à viagem
    public function addViajante(\Illuminate\Http\Request $request)
    {
        $rules = [
            'nome_viajante' => 'required|string|max:100',
            'idade_viajante' => 'required|integer|min:0|max:127',
            'viagem_id' => 'required|integer|exists:viagens,pk_id_viagem',
        ];

        // Adiciona a regra de validação para 'responsavel_legal' (que é o ID do responsável)
        // apenas se a idade do novo viajante for menor que 18
        if ($request->input('idade_viajante') < 18) {
            // Garante que o responsável legal é obrigatório e existe na tabela de viajantes
            $rules['responsavel_legal'] = 'required|integer|exists:viajantes,pk_id_viajante';
        }

        $validated = $request->validate($rules);

        $viajante = new Viajantes();
        $viajante->nome = $validated['nome_viajante'];
        $viajante->idade = $validated['idade_viajante'];
        $viajante->fk_id_viagem = $validated['viagem_id'];

        // Se o viajante for menor de 18, atribui o ID do responsável legal
        if ($viajante->idade < 18) {
            $viajante->responsavel_viajante_id = $validated['responsavel_legal']; // O campo no BD é responsavel_viajante_id
        }

        $viajante->save();

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'viajante' => [
                    'id' => $viajante->pk_id_viajante,
                    'nome' => $viajante->nome,
                    'idade' => $viajante->idade,
                    'responsavel_viajante_id' => $viajante->responsavel_viajante_id ?? null, // Inclui o responsável no retorno AJAX, se existir
                ]
            ]);
        }

        // Redireciona de volta para a página da viagem
        return redirect()->route('viagens', ['id' => $viajante->fk_id_viagem])->with('success', 'Viajante adicionado com sucesso!');
    }

    private function getLatLngFromAddress($address)
    {
        $apiKey = env('GOOGLE_GEOCODING_KEY');
        $addressEncoded = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$addressEncoded}&key={$apiKey}";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ];
        }
        return null;
    }
}
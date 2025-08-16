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
    public function show($id)
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

        return view('viagens/detailsTrip', [
            'title' => 'Detalhes da Viagem',
            'viagem' => $viagem,
            'viajantes' => $viagem->viajantes,
            'pontosInteresse' => $pontos,
            'voos' => $viagem->voos,
            'objetivos' => $viagem->objetivos,
            'usuario' => $viagem->user,
            'hotel' => $viagem->hotel
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
}
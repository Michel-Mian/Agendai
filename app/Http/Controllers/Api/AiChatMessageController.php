<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiChatMessageController extends Controller
{
    /**
     * Lista mensagens de uma viagem (histórico da conversa)
     */
    public function index(Request $request, $viagem_id)
    {
        $perPage = (int) $request->get('per_page', 50);

        $messages = AiChatMessage::with(['user'])
            ->where('viagem_id', $viagem_id)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $messages
        ], 200);
    }

    /**
     * Mostra uma mensagem específica
     */
    public function show($id)
    {
        $message = AiChatMessage::with('user')->find($id);

        if (! $message) {
            return response()->json(['success' => false, 'message' => 'Mensagem não encontrada'], 404);
        }

        return response()->json(['success' => true, 'data' => $message], 200);
    }

    /**
     * Armazena uma nova mensagem (usuário ou assistant)
     */
    public function store(Request $request, $viagem_id)
    {
        $validator = Validator::make(array_merge($request->all(), ['viagem_id' => $viagem_id]), [
            'content' => 'required|string',
            'role' => 'required|in:user,assistant',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        $message = AiChatMessage::create([
            'viagem_id' => $viagem_id,
            'user_id' => $user ? $user->id : null,
            'role' => $request->input('role'),
            'content' => $request->input('content'),
        ]);

        return response()->json(['success' => true, 'data' => $message], 201);
    }

    /**
     * Remove uma mensagem criada pelo usuário (ou por admin)
     */
    public function destroy(Request $request, $id)
    {
        $message = AiChatMessage::find($id);

        if (! $message) {
            return response()->json(['success' => false, 'message' => 'Mensagem não encontrada'], 404);
        }

        $user = $request->user();

        // Permite deleção apenas pelo dono da mensagem
        if (! $user || $message->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'Mensagem deletada'], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiChatProxyController extends Controller
{
    /**
     * Proxy para o controller web `AiChatController::sendMessage`.
     * Reaproveita a lógica existente e retorna exatamente o JSON gerado.
     */
    public function send(Request $request)
    {
        // Log incoming payload to help debugging mobile requests
        Log::info('API AI request received', [
            'payload' => $request->all(),
            'raw' => $request->getContent(),
            'headers' => collect($request->headers->all())->map(function ($v) { return $v[0]; })->toArray()
        ]);

        // Normalize common alternate keys from mobile clients
        if ((! $request->has('message') || is_null($request->input('message'))) && $request->has('content')) {
            $request->merge(['message' => $request->input('content')]);
        }
        if ((! $request->has('message') || is_null($request->input('message'))) && $request->has('text')) {
            $request->merge(['message' => $request->input('text')]);
        }
        if ((! $request->has('trip_id') || is_null($request->input('trip_id'))) && $request->has('tripId')) {
            $request->merge(['trip_id' => $request->input('tripId')]);
        }
        // aceitar 'viagem_id' (português) e 'viagemId' também
        if ((! $request->has('trip_id') || is_null($request->input('trip_id'))) && $request->has('viagem_id')) {
            $request->merge(['trip_id' => $request->input('viagem_id')]);
        }
        if ((! $request->has('trip_id') || is_null($request->input('trip_id'))) && $request->has('viagemId')) {
            $request->merge(['trip_id' => $request->input('viagemId')]);
        }

        // Validate presence before forwarding to web controller to avoid opaque validation exceptions
        if (! $request->has('message') || is_null($request->input('message')) || ! $request->has('trip_id') || is_null($request->input('trip_id'))) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => 'message is required',
                    'trip_id' => 'trip_id is required'
                ]
            ], 422);
        }

        // Reuse existing controller logic
        $webController = app(\App\Http\Controllers\AiChatController::class);
        return $webController->sendMessage($request);
    }
}

<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\ViagensController;

// Rotas de autenticação (não protegidas)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/debug', [AuthController::class, 'debug']); // Debug endpoint
    
    // Rotas protegidas por autenticação
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/validate', [AuthController::class, 'validate']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Rota protegida para obter dados do usuário autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/dashboard', [DashBoardController::class, 'dashboard']);
// TEMPORÁRIO: Rota sem autenticação para teste - remova quando configurar auth
Route::get('/viagens/{id}', [ViagensController::class, 'showApi']);
// Lista todos os seguros da viagem
Route::get('/viagens/{id}/seguros', [ViagensController::class, 'segurosByViagem']);
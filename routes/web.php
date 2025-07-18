<?php

use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\FlightsController;
use Illuminate\Support\Facades\Route;
use App\http\controllers\UserController;
use App\http\controllers\ExploreController;
use App\http\controllers\ViagensController;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


Route::get('/', function () {
    return view('home');
});

// ------------ Rotas Auth --------------------
Route::get('/login', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'create']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [UserController::class, 'getForgot'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [UserController::class, 'getReset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware('guest')->name('password.update');
Route::post('/change-password', [UserController::class, 'changePassword'])->middleware('auth')->name('password.change');

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', [DashBoardController::class, 'dashboard'])->name('dashboard');
    Route::get('/myTrips', [ViagensController::class, 'index'])->name('myTrips');
    Route::get('/viagens/{id}', [ViagensController::class, 'show'])->name('viagens');
    Route::delete('/viajantes/{id}', [ViajantesController::class, 'destroy'])->name('viajantes.destroy');
    Route::delete('/objetivos/{id}', [ViagensController::class, 'destroyObjetivo'])->name('objetivos.destroy');
    Route::post('/objetivos', [ViagensController::class, 'addObjetivo'])->name('objetivos.store');
    Route::post('/viajantes', [ViagensController::class, 'addViajante'])->name('viajantes.store');
    Route::get('/myProfile/{id}/edit', [UserController::class, 'editProfile']);
    Route::put('/myProfile/{id}/edit', [UserController::class, 'updateProfile']);
    Route::get('/flights', [FlightsController::class, 'search'])->name('flights.search');
    Route::get('/config/{id}/edit', [UserController::class, 'editConfig']);
    Route::put('/config/{id}/edit', [UserController::class, 'updateConfig']);
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
    Route::get('/explore/set-trip/{id}', [ExploreController::class, 'setTripIdAndRedirect'])->name('explore.setTrip');
    Route::post('/explore', [ExploreController::class, 'store'])->name('explore.store');
    Route::delete('/explore/{id}', [ExploreController::class, 'destroy'])->name('explore.destroy');
    Route::get('/explore/itinerary', [ExploreController::class, 'show'])->name('explore.itinerary');
    Route::post('/explore/ponto-interesse/{id}/horario', [ExploreController::class, 'updateHorario'])->name('explore.updateHorario');   
    Route::put('/user/{id}/profile', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::put('/user/{id}/preferences', [UserController::class, 'updatePreferences'])->name('user.updatePreferences');
    Route::get('/dashboard/historico', [DashBoardController::class, 'historicoAjax'])->name('dashboard.historico');
});


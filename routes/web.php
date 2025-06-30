<?php

use App\Http\Controllers\DashBoardController;
use Illuminate\Support\Facades\Route;
use App\http\controllers\UserController;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\TripController;

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
        // Step 1 - Mostrar formulário (GET)
    Route::get('/trip/create', [TripController::class, 'showStep1'])->name('trip.form.step1');
    // Step 1 - Receber dados (POST)
    Route::post('/trip/step2', [TripController::class, 'handleStep1'])->name('trip.form.step2');
    // Step 2 - Mostrar formulário (GET)
    Route::get('/trip/step2', [TripController::class, 'showStep2'])->name('trip.form.step2.view');
    // Step 2 - Receber dados (POST)
    Route::post('/trip/step3', [TripController::class, 'handleStep2'])->name('trip.form.step3');
    // Step 3 - Mostrar formulário (GET)
    Route::get('/trip/step3', [TripController::class, 'showStep3'])->name('trip.form.step3.view');
    // Step 3 - Receber dados (POST)
    Route::post('/trip/step4', [TripController::class, 'handleStep3'])->name('trip.form.step4');
    // Step 4 - Mostrar formulário (GET)
    //Route::get('/trip/step4', [TripController::class, 'showStep4'])->name('trip.form.step4.view');
    // E assim por diante para as outras etapas...
    Route::get('/formulario', [TripController::class, 'mostrarFormulario'])->name('formulario.mostrar');
    Route::post('/scraping-executar', [TripController::class, 'executarScraping'])->name('scraping.executar');
    Route::post('/trip/scrape-insurance', [TripController::class, 'scrape'])->name('trip.scrape');
    // Outras rotas protegidas...
    Route::get('/dashboard', [DashBoardController::class, 'dashboard'])->name('dashboard');
    // ...
    Route::get('/myTrips', function() {
        return view('myTrips', ['title' => 'Minhas Viagens']);
    });
    Route::get('/myProfile/{id}/edit', [UserController::class, 'editProfile']);
    Route::put('/myProfile/{id}/edit', [UserController::class, 'updateProfile']);
    Route::get('/flights', function(){
        return view('flights', ['title' => 'Voos']);
    });
    Route::get('/config/{id}/edit', [UserController::class, 'editConfig']);
    Route::put('/config/{id}/edit', [UserController::class, 'updateConfig']);
    Route::put('/user/{id}/profile', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::put('/user/{id}/preferences', [UserController::class, 'updatePreferences'])->name('user.updatePreferences');
    Route::get('/dashboard/historico', [DashBoardController::class, 'historicoAjax'])->name('dashboard.historico');
});


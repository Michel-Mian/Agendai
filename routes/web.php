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
    Route::get('/trip/create', [TripController::class, 'showStep1'])->name('trip.form.step1');
    Route::post('/trip/step2', [TripController::class, 'handleStep1'])->name('trip.form.step2');
    Route::get('/trip/details', [TripController::class, 'showStep2'])->name('trip.form.step2.view');
    Route::post('/trip/preferences', [TripController::class, 'handleStep2'])->name('trip.form.step3');
    Route::get('/trip/preferences', [TripController::class, 'showStep3'])->name('trip.form.step3.view');
    Route::post('/trip/insurance', [TripController::class, 'handleStep3'])->name('trip.form.step4');
    Route::get('/trip/insurance', [TripController::class, 'showStep4'])->name('trip.form.step4.view');
    Route::post('/trip/flights', [TripController::class, 'handleStep4'])->name('trip.form.step5');
    Route::get('/trip/flights', [TripController::class, 'showStep5'])->name('trip.form.step5.view');
    Route::post('/trip/review', [TripController::class, 'handleStep5'])->name('trip.form.step6');
    Route::get('/trip/review', [TripController::class, 'showStep6'])->name('trip.form.step6.view');
    Route::post('/trip/finish', [TripController::class, 'finish'])->name('trip.form.finish');
    Route::get('/dashboard', [DashBoardController::class, 'dashboard'])->name('dashboard');
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


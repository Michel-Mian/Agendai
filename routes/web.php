<?php

use Illuminate\Support\Facades\Route;
use App\http\controllers\UserController;
use Illuminate\Http\Request;


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

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', function () {
        return view('dashboard', ['title' => 'Dashboard']);
    });
    Route::get('/myTrips', function() {
        return view('myTrips', ['title' => 'Minhas Viagens']);
    });
});


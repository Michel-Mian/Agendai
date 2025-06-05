<?php

use Illuminate\Support\Facades\Route;
use App\http\controllers\UserController;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'create']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', function () {
        return view('dashboard', ['title' => 'Dashboard']);
    });
    Route::get('/myTrips', function() {
        return view('myTrips', ['title' => 'Minhas Viagens']);
    });
    Route::get('/myProfile/{id}/edit', [UserController::class, 'edit']);
    Route::put('/myProfile/{id}/edit', [UserController::class, 'update']);
});


<?php

use Illuminate\Support\Facades\Route;
use App\http\controllers\UserController;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'create']);
Route::post('/register', [UserController::class, 'store']);

Route::get('/dashboard', function () {
    return view('dashboard');
});
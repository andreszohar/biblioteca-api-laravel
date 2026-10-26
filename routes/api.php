<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutorController;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\PrestamoController;
use Illuminate\Support\Facades\Route;

// Ruta pública: máximo 5 intentos de login por minuto
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Rutas protegidas: exigen un token válido de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Libros: index, show, store, update, destroy
    Route::apiResource('libros', LibroController::class)
        ->parameters(['libros' => 'libro']);

    // Préstamos
    Route::get('/prestamos', [PrestamoController::class, 'index']);
    Route::post('/prestamos', [PrestamoController::class, 'store']);
    Route::put('/prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver']);

    // Autores
    Route::get('/autores', [AutorController::class, 'index']);
    Route::delete('/autores/{autor}', [AutorController::class, 'destroy']);
});
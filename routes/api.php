<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ProfileController;

// Ritorna l’utente autenticato (se usi Sanctum SPA o token)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotte protette da login Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // CRUD spese
    Route::get('/expenses', [ExpenseController::class, 'index']);        // lista
    Route::post('/expenses', [ExpenseController::class, 'store']);       // crea
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);    // dettaglio
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);  // aggiorna
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']); // elimina

    // Rotta extra per aggiornare solo lo status del partecipante loggato
    Route::patch('/expenses/{id}/status', [ExpenseController::class, 'updateStatus']);

    // Profilo utente
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
});



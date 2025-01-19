<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BungalowsController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\AllMigrationsController;

Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

// Authentification
Route::post('login', [AuthController::class, 'login']);
Route::post('allMigrationsHotel', [AllMigrationsController::class, 'allMigrationsHotel']);
Route::post('allMigrationsUsers', [AllMigrationsController::class, 'allMigrationsUsers']);
Route::post('allMigrationsAgences', [AllMigrationsController::class, 'allMigrationsAgences']);
Route::post('allMigrationsClients', [AllMigrationsController::class, 'allMigrationsClients']);
Route::post('allMigrationsBungalows', [AllMigrationsController::class, 'allMigrationsBungalows']);
Route::post('allMigrationsReservations', [AllMigrationsController::class, 'allMigrationsReservations']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('bungalows')->group(function () {
        Route::get('/', [BungalowsController::class, 'getAllBungalows']);
        Route::get('/{id}', [BungalowsController::class, 'getOneBungalow']);
    });

    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationsController::class, 'getAllReservations']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CampaignController, DonationController};
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Public API-like routes (for simplicity in demo)
Route::get('/health', fn() => response('OK', 200));

// Ensure Sanctum CSRF cookie route exists for SPA auth
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('sanctum.csrf-cookie');

// SPA auth endpoints must use session (web middleware). Keep /api/* paths for the SPA
Route::middleware(['web'])->prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});


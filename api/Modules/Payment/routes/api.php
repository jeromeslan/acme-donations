<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

// Routes spécifiques au module Payment
Route::post('/payments/process', [PaymentController::class, 'process'])->middleware('auth:sanctum');
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

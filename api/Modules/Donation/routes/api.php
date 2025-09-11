<?php

use Illuminate\Support\Facades\Route;
use Modules\Donation\Http\Controllers\DonationController;

// Routes spécifiques au module Donation
Route::post('/donations', [DonationController::class, 'store'])->middleware('auth:sanctum');
Route::get('/my-donations', [DonationController::class, 'myDonations'])->middleware('auth:sanctum');
Route::get('/donations/{donation}/receipt', [DonationController::class, 'receipt'])->middleware('auth:sanctum');

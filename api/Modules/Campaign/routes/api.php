<?php

use Illuminate\Support\Facades\Route;
use Modules\Campaign\Http\Controllers\CampaignController;

// Routes spécifiques au module Campaign
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/featured', [CampaignController::class, 'featured']);
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);
Route::post('/campaigns', [CampaignController::class, 'store'])->middleware('auth:sanctum');
Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->middleware('auth:sanctum');

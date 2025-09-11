<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;

// Routes spécifiques au module Admin (protégées par middleware admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/campaigns', [AdminController::class, 'campaigns']);
    Route::post('/admin/campaigns/{campaign}/approve', [AdminController::class, 'approveCampaign']);
    Route::post('/admin/campaigns/{campaign}/reject', [AdminController::class, 'rejectCampaign']);
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::get('/admin/health', [AdminController::class, 'health']);
});

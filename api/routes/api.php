<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, CampaignController, DonationController, AdminController};

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// NOTE: login/logout/me are mounted under web middleware in routes/web.php to ensure session is present

// Categories
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index']);
Route::get('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'show']);
Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->middleware('auth:sanctum');
Route::put('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->middleware('auth:sanctum');

// Campaigns
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/featured', [CampaignController::class, 'featured']);
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);
Route::post('/campaigns', [CampaignController::class, 'store'])->middleware('auth:sanctum');
Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->middleware('auth:sanctum');

// Donations
Route::post('/campaigns/{campaign}/donations', [DonationController::class, 'store']);
Route::get('/me/donations', [DonationController::class, 'myDonations'])->middleware('auth:sanctum');
Route::get('/donations/{donation}/receipt', [DonationController::class, 'receipt'])->middleware('auth:sanctum');

// Public stats endpoint
Route::get('/stats', [AdminController::class, 'publicStats']);

// Admin routes
Route::get('/admin/kpis', [AdminController::class, 'kpis'])->middleware('auth:sanctum');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('auth:sanctum');
Route::get('/admin/campaigns/pending', [AdminController::class, 'pendingCampaigns'])->middleware('auth:sanctum');
Route::post('/admin/campaigns/{id}/approve', [AdminController::class, 'approveCampaign'])->middleware('auth:sanctum');
Route::post('/admin/campaigns/{id}/reject', [AdminController::class, 'rejectCampaign'])->middleware('auth:sanctum');


// DEBUG (temporary): counts and sample campaigns
Route::get('/debug/counts', function () {
    return response()->json([
        'users' => \App\Models\User::count(),
        'categories' => \App\Models\Category::count(),
        'campaigns' => \App\Models\Campaign::count(),
        'donations' => \App\Models\Donation::count(),
    ]);
});

Route::get('/debug/campaigns', function () {
    return \App\Models\Campaign::query()->with('category')->orderByDesc('id')->limit(5)->get();
});



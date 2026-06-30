<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstateController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NyumbaFind API Routes
|--------------------------------------------------------------------------
|
| Prefix: /api
| Auth: Laravel Sanctum (Bearer token)
|
*/

// ─── Public routes (no auth required) ─────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('send-otp',    [AuthController::class, 'sendOtp']);
    Route::post('verify-otp',  [AuthController::class, 'verifyOtp']);
});

// Public listing browse
Route::get('estates',                  [EstateController::class, 'index']);
Route::get('estates/{slug}',           [EstateController::class, 'show']);
Route::get('listings',                 [ListingController::class, 'index']);
Route::get('listings/{id}',            [ListingController::class, 'show']);
Route::get('search',                   [SearchController::class, 'search']);

// ─── Authenticated routes ──────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('me',               [AuthController::class, 'me']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
    });

    // Listings — landlord/caretaker
    Route::post('listings',                        [ListingController::class, 'store']);
    Route::put('listings/{id}',                    [ListingController::class, 'update']);
    Route::patch('listings/{id}/status',           [ListingController::class, 'updateStatus']);
    Route::delete('listings/{id}',                 [ListingController::class, 'destroy']);
    Route::get('my-listings',                      [ListingController::class, 'myListings']);

    // Inquiries — tenant
    Route::post('listings/{id}/inquire',           [InquiryController::class, 'store']);
    Route::patch('inquiries/{id}/whatsapp-opened', [InquiryController::class, 'markWhatsappOpened']);
    Route::get('my-inquiries',                     [InquiryController::class, 'myInquiries']);

    // Reports
    Route::post('listings/{id}/report',            [ReportController::class, 'store']);

    // Reviews
    Route::post('listings/{id}/reviews',           [ReviewController::class, 'store']);
    Route::put('listings/{id}/reviews',            [ReviewController::class, 'update']);

    // ─── Admin routes ──────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('stats',                        [AdminController::class, 'stats']);
        Route::get('listings/pending',             [AdminController::class, 'pendingListings']); // must be before {id}
        Route::get('listings',                     [AdminController::class, 'allListings']);
        Route::post('listings/{id}/approve',       [AdminController::class, 'approve']);
        Route::post('listings/{id}/reject',        [AdminController::class, 'reject']);
        Route::post('listings/{id}/suspend',       [AdminController::class, 'suspend']);
        Route::post('listings/{id}/feature',       [AdminController::class, 'feature']);
        Route::get('reports',                      [AdminController::class, 'reports']);
        Route::post('reports/{id}/resolve',        [AdminController::class, 'resolveReport']);
        Route::get('users',                        [AdminController::class, 'users']);
    });
});

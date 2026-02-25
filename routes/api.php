<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\Public;
use Illuminate\Support\Facades\Route;

// Health check

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});


// Admin
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/programs', [Admin\ProgramController::class, 'index']);
    Route::post('/programs', [Admin\ProgramController::class, 'store']);
    Route::put('/programs/{program}', [Admin\ProgramController::class, 'update']);
    Route::post('/programs/{program}/pdf', [Admin\ProgramController::class, 'uploadPdf']);
    Route::get('/orders', [Admin\OrderController::class, 'index']);
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show']);
    Route::get('/emails', [Admin\EmailController::class, 'index']);
    Route::get('/settings', [Admin\ClinicSettingsController::class, 'index']);
    Route::put('/settings', [Admin\ClinicSettingsController::class, 'update']);
});


// Auth
Route::post('/auth/login', [Admin\AuthController::class, 'login']);
Route::post('/auth/logout', [Admin\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/me', [Admin\AuthController::class, 'me'])->middleware('auth:sanctum');

// Public
Route::middleware('resolve.clinic')->group(function () {
    Route::get('/programs', [Public\ProgramController::class, 'index']);
    Route::post('/orders', [Public\OrderController::class, 'store']);
    Route::post('/orders/{order}/callback', [Public\OrderController::class, 'paymentCallback']);
});


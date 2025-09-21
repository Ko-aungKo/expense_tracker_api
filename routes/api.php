<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Dashboard routes
Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/monthly/{year}', [DashboardController::class, 'getMonthlyStats'])
        ->where('year', '[0-9]{4}');
});

// Resource routes
Route::apiResource('expenses', ExpenseController::class);
Route::apiResource('categories', CategoryController::class);

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Get authenticated user (for future auth implementation)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

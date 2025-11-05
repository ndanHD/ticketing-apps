<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('jwt')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\UserController::class);
        Route::apiResource('ticket-types', \App\Http\Controllers\TicketTypeController::class);
        Route::apiResource('slas', \App\Http\Controllers\SlaController::class);
    });

    // User routes
    Route::middleware('role:user')->group(function () {
        Route::apiResource('tickets', \App\Http\Controllers\TicketController::class)->except(['destroy']);
        Route::post('tickets/{ticket}/comments', [\App\Http\Controllers\TicketCommentController::class, 'store']);
        Route::post('tickets/{ticket}/ratings', [\App\Http\Controllers\TicketRatingController::class, 'store']);
    });

    // Handler routes
    Route::middleware('role:handler')->group(function () {
        Route::patch('tickets/{ticket}/status', [\App\Http\Controllers\TicketController::class, 'updateStatus']);
        Route::get('handler/tickets', [\App\Http\Controllers\TicketController::class, 'handlerTickets']);
    });
});

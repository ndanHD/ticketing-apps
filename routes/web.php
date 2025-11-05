<?php

use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HandlerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketUserController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\Auth\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes
Route::middleware('guest')->group(function() {
    Route::get('login', [WebAuthController::class, 'showLogin'])->name('login.show');
    Route::post('login', [WebAuthController::class, 'login'])->name('login.post');
    Route::get('register', [WebAuthController::class, 'showRegister'])->name('register.show');
    Route::post('register', [WebAuthController::class, 'register'])->name('register.post');
});

Route::post('logout', [WebAuthController::class, 'logout'])->name('logout.post');

// User area - protected by user role
Route::middleware(['require.login', 'role:user'])->group(function() {
    Route::get('dashboard', [UserController::class, 'index'])->name('user.dashboard');
    
    Route::prefix('tickets')->controller(TicketUserController::class)->group(function () {
        Route::get('/', 'index')->name('user.tickets.index');
        Route::get('/create', 'create')->name('user.tickets.create');
        Route::post('/', 'store')->name('user.tickets.store');
        Route::get('/{ticket}', 'show')->name('user.tickets.show');
        Route::post('/{ticket}/comments', 'addComment')->name('user.tickets.comments.store');
        Route::post('/{ticket}/rating', 'submitRating')->name('user.tickets.rating.submit');
    });
});

// Handler area - protected by handler role
Route::middleware(['require.login', 'role:handler'])->prefix('handler')->group(function() {
    Route::get('dashboard', [HandlerController::class, 'dashboard'])->name('handler.dashboard');
    Route::get('tickets', [HandlerController::class, 'index'])->name('handler.tickets.index');
    Route::get('tickets/{ticket}', [HandlerController::class, 'show'])->name('handler.tickets.show');
    Route::post('tickets/close', [HandlerController::class, 'closeTicket'])->name('handler.tickets.close');
    Route::post('tickets/{ticket}/comments', [HandlerController::class, 'addComment'])->name('handler.tickets.comments.store');
});

// Admin area - protected by admin role
Route::middleware(['require.login', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // User management - only superadmin can access these routes (still under /admin prefix)
    Route::middleware('role:superadmin')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('users', 'usersIndex')->name('admin.users.index');
            Route::get('users/create', 'usersCreate')->name('admin.users.create');
            Route::post('users', 'usersStore')->name('admin.users.store');
            Route::get('users/{user}/edit', 'usersEdit')->name('admin.users.edit');
            Route::put('users/{user}', 'usersUpdate')->name('admin.users.update');
            Route::delete('users/{user}', 'usersDestroy')->name('admin.users.destroy');
        });
    });
    
    Route::resource('slas', SlaController::class)->except(['show'])->names([
        'index' => 'admin.slas.index',
        'create' => 'admin.slas.create',
        'store' => 'admin.slas.store',
        'edit' => 'admin.slas.edit',
        'update' => 'admin.slas.update',
        'destroy' => 'admin.slas.destroy'
    ]);

    Route::resource('ticket-types', TicketTypeController::class)->except(['show'])->names([
        'index' => 'admin.ticket-types.index',
        'create' => 'admin.ticket-types.create',
        'store' => 'admin.ticket-types.store',
        'edit' => 'admin.ticket-types.edit',
        'update' => 'admin.ticket-types.update',
        'destroy' => 'admin.ticket-types.destroy'
    ]);
    
    Route::prefix('tickets')->controller(TicketController::class)->group(function () {
        Route::get('/', 'index')->name('admin.tickets.index');
        Route::get('/create', 'create')->name('admin.tickets.create');
        Route::post('/', 'store')->name('admin.tickets.store');
        Route::get('/{ticket}', 'show')->name('admin.tickets.show');
        Route::post('/{ticket}/comments', 'addComment')->name('admin.tickets.comments.store');
        Route::post('/{ticket}/assign', 'assign')->name('admin.tickets.assign');
        Route::get('/{ticket}/edit', 'edit')->name('admin.tickets.edit');
        Route::put('/{ticket}', 'update')->name('admin.tickets.update');
        Route::delete('/{ticket}', 'destroy')->name('admin.tickets.destroy');
    });
});




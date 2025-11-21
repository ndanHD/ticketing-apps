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
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('auth.login');
// })->name('login');

// // Public homepage about the product
Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/password/change', [WebAuthController::class, 'showChangePassword'])->name('password.change')->middleware('auth');
Route::post('/password/change', [WebAuthController::class, 'changePassword'])->name('password.change.submit')->middleware('auth');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [WebAuthController::class, 'showLogin'])->name('login.show');
    Route::post('login', [WebAuthController::class, 'login'])->name('login.post');
    Route::get('register', [WebAuthController::class, 'showRegister'])->name('register.show');
    Route::post('register', [WebAuthController::class, 'register'])->name('register.post');
});

Route::middleware('require.login')->group(function () {
    Route::post('logout', [WebAuthController::class, 'logout'])->name('logout.post');
});

// User area - protected by user role
Route::middleware(['require.login', 'must_change_password', 'role:user'])->group(function () {
    Route::get('dashboard', [UserController::class, 'index'])->name('user.dashboard');

    Route::prefix('tickets')->controller(TicketUserController::class)->group(function () {
        Route::get('/', 'index')->name('user.tickets.index');
        Route::get('/create', 'create')->name('user.tickets.create');
        Route::post('/', 'store')->name('user.tickets.store');
        Route::get('/{ticket}', 'show')->name('user.tickets.show');
        Route::post('/{ticket}/comments', 'addComment')->name('user.tickets.comments.store');
        Route::post('/{ticket}/rating', 'submitRating')->name('user.tickets.rating.submit');
    });
    Route::prefix('ckeditor')->controller(FileController::class)->group(function () {
        Route::post('/upload', 'upload')->name('ckeditor.upload');
        Route::post('/delete', 'delete')->name('ckeditor.delete');
        Route::post('/finalize', 'finalize')->name('ckeditor.finalize');
    });
});




// Handler area - protected by handler role
Route::middleware(['require.login', 'must_change_password', 'role:handler|admin|superadmin'])->prefix('handler')->group(function () {
    Route::get('dashboard', [HandlerController::class, 'dashboard'])->name('handler.dashboard');
    Route::get('tickets', [HandlerController::class, 'index'])->name('handler.tickets.index');
    Route::get('tickets/{ticket}', [HandlerController::class, 'show'])->name('handler.tickets.show');
    Route::post('tickets/close', [HandlerController::class, 'closeTicket'])->name('handler.tickets.close');
    Route::post('tickets/{ticket}/comments', [HandlerController::class, 'addComment'])->name('handler.tickets.comments.store');
    // Allow handlers to set ticket to pending
    Route::post('tickets/{ticket}/pending', [HandlerController::class, 'setPending'])->name('handler.tickets.pending.set');
    // Allow handler to close/clear pending state
    Route::post('tickets/{ticket}/pending/close', [HandlerController::class, 'closePending'])->name('handler.tickets.pending.close');
});

Route::get('report', [AdminController::class, 'report'])->name('admin.report');
// Admin area - protected by admin and superadmin roles
Route::middleware(['require.login', 'must_change_password', 'role:admin|superadmin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // User management:
    // - admin|superadmin can access the add-user (create/store) routes
    // - only superadmin can list/edit/delete users
    Route::controller(AdminController::class)->group(function () {
        // Accessible by admin and superadmin
        Route::get('users/create', 'usersCreate')->name('admin.users.create')->middleware('role:admin|superadmin');
        Route::post('users', 'usersStore')->name('admin.users.store')->middleware('role:admin|superadmin');

        // Only superadmin for listing and modifying users
        Route::middleware('role:superadmin')->group(function () {
            Route::get('users', 'usersIndex')->name('admin.users.index');
            Route::get('users/{user}/edit', 'usersEdit')->name('admin.users.edit');
            Route::put('users/{user}', 'usersUpdate')->name('admin.users.update');
            Route::delete('users/{user}', 'usersDestroy')->name('admin.users.destroy');
            Route::put('users/{user}/reset-password', 'resetUserPassword')->name('admin.users.reset-password');
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
        Route::post('/{ticket}/pending/close', 'closePendingAsAdmin')->name('admin.tickets.pending.close');
        Route::get('/{ticket}/edit', 'edit')->name('admin.tickets.edit');
        Route::put('/{ticket}', 'update')->name('admin.tickets.update');
        Route::delete('/{ticket}', 'destroy')->name('admin.tickets.destroy');
    });

    // Outlets management (only superadmin)
    Route::controller(\App\Http\Controllers\OutletController::class)->middleware('role:superadmin')->group(function () {
        Route::get('outlets', 'index')->name('admin.outlets.index');
        Route::get('outlets/create', 'create')->name('admin.outlets.create');
        Route::post('outlets', 'store')->name('admin.outlets.store');
    });
});

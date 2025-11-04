<?php

use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});

// Admin area - user CRUD
Route::prefix('admin')->controller(AdminController::class)->group(function () {
    Route::get('/', 'index')->name('admin.dashboard');
    Route::get('users', 'usersIndex')->name('admin.users.index');
    Route::get('users/create', 'usersCreate')->name('admin.users.create');
    Route::post('users', 'usersStore')->name('admin.users.store');
    Route::get('users/{user}/edit', 'usersEdit')->name('admin.users.edit');
    Route::put('users/{user}', 'usersUpdate')->name('admin.users.update');
    Route::delete('users/{user}', 'usersDestroy')->name('admin.users.destroy');
});

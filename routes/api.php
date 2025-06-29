<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;

Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'prefix' => 'v1'
], function () {
    // Login
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    // Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/test/time', function () {
        return [
            'time' => Carbon::now()->format('dmYHis')
        ];
    });
});

Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:sanctum'
], function () {
    // User
    Route::get('/user', UserController::class);

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

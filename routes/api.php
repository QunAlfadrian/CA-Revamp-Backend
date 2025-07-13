<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\OrganizerApplicationController;
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
    Route::get('/users', UserController::class);

    // User Identity
    Route::get('/identity', [IdentityController::class, 'show'])->name('identities.show');
    Route::post('/identity', [IdentityController::class, 'store'])->name('identities.store');
    Route::put('/identity', [IdentityController::class, 'update'])->name('identities.update');

    // Organizer Application
    Route::get('/application', [OrganizerApplicationController::class, 'show'])->name('organizer_applications.show');
    Route::post('/application', [OrganizerApplicationController::class, 'store'])->name('organizer_applications.store');
    Route::post('/application/reapply', [OrganizerApplicationController::class, 'update'])->name('organizer_applications.reapply');

    /** ADMIN */
    // organizer application
    Route::get('/admin/applications', [OrganizerApplicationController::class, 'index'])->name('organizer_applications.index');
    Route::post('/admin/applications/review', [OrganizerApplicationController::class, 'update'])->name('organizer_applications.review');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

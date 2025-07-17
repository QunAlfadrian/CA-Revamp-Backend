<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\OrganizerApplicationController;
use App\Http\Controllers\RoleController;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;

Route::post('/register', [AuthController::class, 'register']);

Route::group([
    'prefix' => 'v1'
], function () {
    Route::get('/test/time', function () {
        return [
            'time' => Carbon::now()->format('dmYHis')
        ];
    });

    // Login
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

    // Books
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
});

Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:sanctum'
], function () {
    // User
    Route::get('/users', UserController::class);

    // Roles
    Route::get('/roles', RoleController::class);

    // User Identity
    Route::get('/identity', [IdentityController::class, 'show'])->name('identities.show');
    Route::post('/identity', [IdentityController::class, 'store'])->name('identities.store');
    Route::put('/identity', [IdentityController::class, 'update'])->name('identities.update');

    // Organizer Application
    Route::get('/application', [OrganizerApplicationController::class, 'show'])->name('organizer_applications.show');
    Route::post('/application', [OrganizerApplicationController::class, 'store'])->name('organizer_applications.store');
    Route::post('/application/reapply', [OrganizerApplicationController::class, 'update'])->name('organizer_applications.reapply');

    // Books
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::post('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Campaigns
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');

    /** ADMIN */
    // organizer application
    Route::get('/admin/applications', [OrganizerApplicationController::class, 'index'])->name('organizer_applications.index');
    Route::post('/admin/applications/review', [OrganizerApplicationController::class, 'update'])->name('organizer_applications.review');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

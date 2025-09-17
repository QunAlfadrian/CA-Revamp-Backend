<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\V1\RoleResource;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OrganizerApplicationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DonatedItemController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\MidtransController;

Route::group([
    'prefix' => 'v1'
], function () {
    Route::post('/test', function () {
        return [
            'time' => Carbon::now()->format('dmYHis')
        ];
    });

    // Register
    Route::post('/register', [AuthController::class, 'register']);

    // Login
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

    // User
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Books
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

    // Campaigns
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{campaign}/funds', [FundController::class, 'indexByCampaign'])->name('campaigns.funds.show');
    Route::get('/campaigns/{campaign}/items', [DonatedItemController::class, 'indexByCampaign'])->name('campaigns.items.show');

    // Donate to campaigns
    Route::post('/campaigns/{campaign}/donations', [DonationController::class, 'store'])->name('campaigns.donations.store');
    Route::post('/campaigns/{campaign}/donations/finish', [DonationController::class, 'finish'])->name('campaigns.donations.finish');

    // Midtrans
    Route::post('/midtrans/notifications', [MidtransController::class, 'handleNotifications'])->name('midtrans.notifications');
});

Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:sanctum'
], function () {
    // User
    Route::get('/user', function (Request $request) {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => $user->name(),
                    'email' => $user->email()
                ],
                'roles' => RoleResource::collection($user->roles())
            ]
        ], 200);
    });

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

    /** ORGANIZER */
    // Campaigns
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    // My Campaigns
    Route::get('/organizer/campaigns', [CampaignController::class, 'organizerIndex'])->name('organizer.campaigns.index');
    Route::get('/organizer/campaigns/trashed', [CampaignController::class, 'organizerTrashed'])->name('organizer.campaigns.trashed');
    Route::put('/organizer/campaigns/trashed/{id}', [CampaignController::class, 'restore'])->name('organizer.campaigns.restore');

    // List Donation per Campaign
    Route::get('/organizer/{campaign}/donations', [DonationController::class, 'donationByCampaign'])->name('organizer.campaigns.donations.index');

    // List Donated Items per donations
    Route::get('/organizer/donations/{donation}/donated-items', [DonatedItemController::class, 'indexByDonation'])->name('organizer.donations.donated_items');
    Route::get('/organizer/donated-items/{donatedItem}', [DonatedItemController::class, 'show'])->name('organizer.donated_items.show');
    Route::post('/organizer/donated-items/{donatedItem}/verify', [DonatedItemController::class, 'verify'])->name('organizer.donated_items.verify');
    Route::post('/organizer/donated-items/{donatedItem}', [DonatedItemController::class, 'updateStatus'])->name('organizer.donated_items.update_status');

    /** DONOR */
    Route::get('/donor/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donor/donations/{donation}', [DonationController::class, 'index'])->name('donations.show');

    /** ADMIN */
    // users
    Route::get('/admin/users', [UserController::class, 'adminIndex'])->name("users.index.admin");

    // organizer application
    Route::get('/admin/applications', [OrganizerApplicationController::class, 'index'])->name('organizer_applications.index');
    Route::post('/admin/applications/review', [OrganizerApplicationController::class, 'update'])->name('organizer_applications.review');

    // Campaigns
    Route::get('/admin/campaigns', [CampaignController::class, 'adminIndex'])->name('admin.campaigns.index');
    Route::put('/admin/campaigns/{campaign}', [CampaignController::class, 'update'])->name('admin.campaigns.review');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

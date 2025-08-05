<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\User;
use App\Models\Campaign;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\Sanctum\PersonalAccessToken;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Sanctum::getAccessTokenFromRequestUsing(function ($request) {
            return $request->cookie('auth-token');
        });

        // custom model bindings
        Route::bind('user', function ($value) {
            return User::where('name', $value)
                ->firstOrFail();
        });

        Route::bind('campaign', function ($value) {
            return Campaign::where('slug', $value)
                ->firstOrFail();
        });

        Route::bind('book', function ($value) {
            return Book::where('isbn', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });
    }
}

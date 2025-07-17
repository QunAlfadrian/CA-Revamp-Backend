<?php

namespace App\Providers;

use App\Models\Book;
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

        // custom model bindings
        Route::bind('campaign', function ($value) {
            return Campaign::where('id', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });

        Route::bind('book', function ($value) {
            return Book::where('isbn', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });
    }
}

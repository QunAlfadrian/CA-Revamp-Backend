<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app' => 'Caritas Aeterna',
        'status' => 'up and running',
        'Laravel' => app()->version()
    ];
});

Route::get('/sanctum/csrf-cookie', \Laravel\Sanctum\Http\Controllers\CsrfCookieController::class.'@show');


// require __DIR__.'/auth.php';

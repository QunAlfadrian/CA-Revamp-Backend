<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:sanctum'
], function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app' => 'Caritas Aeterna',
        'status' => 'up and running',
        'Laravel' => app()->version()
    ];
});

// require __DIR__.'/auth.php';

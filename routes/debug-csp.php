<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-csp', function () {
    return response('<h1>Debug CSP Headers</h1>')
        ->withHeaders([
            'Content-Type' => 'text/html',
        ]);
});

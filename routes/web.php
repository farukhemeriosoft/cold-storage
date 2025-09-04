<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Serve frontend for all non-API routes
Route::get('/{any}', function () {
    return file_get_contents(public_path('frontend/index.html'));
})->where('any', '^(?!api).*$');

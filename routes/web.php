<?php

use Illuminate\Support\Facades\Route;

// Serve frontend for all non-API routes
Route::get('/{any?}', function () {
    $frontendPath = public_path('frontend/index.html');
    if (file_exists($frontendPath)) {
        return file_get_contents($frontendPath);
    }
    return response('Frontend not built. Run: npm run frontend:build', 404);
})->where('any', '^(?!api).*$');

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Serve static assets directly
Route::get('/frontend/assets/{file}', function ($file) {
    $filePath = public_path("frontend/assets/{$file}");
    if (File::exists($filePath)) {
        $mimeType = match (pathinfo($file, PATHINFO_EXTENSION)) {
            'js' => 'application/javascript',
            'css' => 'text/css',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'application/octet-stream'
        };

        return response()->file($filePath, ['Content-Type' => $mimeType]);
    }
    return response('Asset not found', 404);
});

// Serve manifest.json
Route::get('/frontend/manifest.json', function () {
    $manifestPath = public_path('manifest.json');
    if (File::exists($manifestPath)) {
        return response()->file($manifestPath, ['Content-Type' => 'application/json']);
    }
    return response('Manifest not found', 404);
});

// Serve favicon.ico
Route::get('/frontend/favicon.ico', function () {
    $faviconPath = public_path('favicon.ico');
    if (File::exists($faviconPath)) {
        return response()->file($faviconPath, ['Content-Type' => 'image/x-icon']);
    }
    return response('Favicon not found', 404);
});

// Clear cache page
Route::get('/clear-cache', function () {
    return response()->file(public_path('clear-cache.html'), [
        'Content-Type' => 'text/html'
    ]);
});

// Force clear cache page
Route::get('/force-clear-cache', function () {
    return response()->file(public_path('force-clear-cache.html'), [
        'Content-Type' => 'text/html'
    ]);
});

// Serve frontend for all other non-API routes
Route::get('/{any?}', function () {
    $frontendPath = public_path('frontend/index.html');
    if (file_exists($frontendPath)) {
        return file_get_contents($frontendPath);
    }
    return response('Frontend not built. Run: npm run frontend:build', 404);
})->where('any', '^(?!api).*$');

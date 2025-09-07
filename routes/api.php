<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\StorageUtilizationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Customer management routes
    Route::apiResource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate']);
    Route::patch('/customers/{customer}/activate', [CustomerController::class, 'activate']);
    Route::get('/customers/{customer}/baskets', [CustomerController::class, 'baskets']);

    // Storage management routes
    Route::get('/storage/structure', [StorageController::class, 'getStorageStructure']);
    Route::get('/storage/zones/available', [StorageController::class, 'getAvailableZones']);
    Route::get('/storage/capacity', [StorageController::class, 'getCapacitySummary']);

    // Storage utilization reports routes
    Route::get('/storage/utilization/overall', [StorageUtilizationController::class, 'getOverallUtilization']);
    Route::get('/storage/utilization/trends', [StorageUtilizationController::class, 'getUtilizationTrends']);
    Route::get('/storage/utilization/rooms', [StorageUtilizationController::class, 'getRoomUtilization']);
    Route::get('/storage/utilization/rooms/{roomId}', [StorageUtilizationController::class, 'getRoomUtilization']);
    Route::get('/storage/utilization/alerts', [StorageUtilizationController::class, 'getCapacityAlerts']);

    // Basket management routes
    Route::get('/batches', [BasketController::class, 'index']);
    Route::get('/batches/expiring', [BasketController::class, 'getExpiringBatches']);
    Route::post('/batches', [BasketController::class, 'createBatch']);
    Route::post('/batches/{batch}/baskets', [BasketController::class, 'addBasketsToBatch']);

    // Invoice management routes
    Route::get('/invoices/statistics', [InvoiceController::class, 'getStatistics']);
    Route::get('/invoices/unpaid/{customerId}', [InvoiceController::class, 'getUnpaidInvoices']);
    Route::get('/invoices/overdue', [InvoiceController::class, 'getOverdueInvoices']);
    Route::get('/invoices/due-soon', [InvoiceController::class, 'getDueSoonInvoices']);

    // Dispatch management routes
    Route::post('/dispatch/scan', [App\Http\Controllers\DispatchController::class, 'dispatchByBarcode']);
    Route::post('/dispatch/{dispatchId}/complete', [App\Http\Controllers\DispatchController::class, 'completeDispatch']);
    Route::get('/dispatch/pending-approvals', [App\Http\Controllers\DispatchController::class, 'getPendingApprovals']);
    Route::post('/dispatch/{dispatchId}/approve', [App\Http\Controllers\DispatchController::class, 'approveDispatch']);
    Route::get('/dispatch/history', [App\Http\Controllers\DispatchController::class, 'getDispatchHistory']);
    Route::get('/dispatch/statistics', [App\Http\Controllers\DispatchController::class, 'getDispatchStats']);

    Route::apiResource('invoices', InvoiceController::class)->except(['create', 'edit']);
    Route::post('/batches/{batch}/invoice', [InvoiceController::class, 'createForBatch']);
    Route::patch('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel']);

    // Payment management routes
    Route::apiResource('payments', PaymentController::class)->except(['create', 'edit', 'update']);
Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'processPayment']);
Route::post('/invoices/{invoice}/revert-payment', [PaymentController::class, 'revertPayment']);
Route::get('/invoices/{invoice}/payments', [PaymentController::class, 'getPaymentHistory']);
Route::get('/payments/statistics', [PaymentController::class, 'getStatistics']);
Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund']);
});

// Financial reports routes (temporarily without auth for testing)
Route::get('/financial/reports', [App\Http\Controllers\FinancialController::class, 'getReports']);
Route::post('/financial/export', [App\Http\Controllers\FinancialController::class, 'exportReport']);

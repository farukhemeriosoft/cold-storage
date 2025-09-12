<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\StorageUtilizationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Customer management routes
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{customer}', [CustomerController::class, 'show']);
        Route::get('/customers/{customer}/baskets', [CustomerController::class, 'baskets']);
    });

    Route::middleware('permission:customers.create')->group(function () {
        Route::post('/customers', [CustomerController::class, 'store']);
    });

    Route::middleware('permission:customers.edit')->group(function () {
        Route::put('/customers/{customer}', [CustomerController::class, 'update']);
        Route::patch('/customers/{customer}', [CustomerController::class, 'update']);
    });

    Route::middleware('permission:customers.toggle')->group(function () {
        Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate']);
        Route::patch('/customers/{customer}/activate', [CustomerController::class, 'activate']);
    });

    // Storage management routes
    Route::middleware('permission:storage.view')->group(function () {
        Route::get('/storage/structure', [StorageController::class, 'getStorageStructure']);
        Route::get('/storage/zones/available', [StorageController::class, 'getAvailableZones']);
        Route::get('/storage/capacity', [StorageController::class, 'getCapacitySummary']);
    });

    // Storage utilization reports routes
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/storage/utilization/overall', [StorageUtilizationController::class, 'getOverallUtilization']);
        Route::get('/storage/utilization/trends', [StorageUtilizationController::class, 'getUtilizationTrends']);
        Route::get('/storage/utilization/rooms', [StorageUtilizationController::class, 'getRoomUtilization']);
        Route::get('/storage/utilization/rooms/{roomId}', [StorageUtilizationController::class, 'getRoomUtilization']);
        Route::get('/storage/utilization/alerts', [StorageUtilizationController::class, 'getCapacityAlerts']);
    });

    // Basket management routes
    Route::middleware('permission:batches.view')->group(function () {
        Route::get('/batches', [BasketController::class, 'index']);
        Route::get('/batches/expiring', [BasketController::class, 'getExpiringBatches']);
    });

    Route::middleware('permission:batches.create')->group(function () {
        Route::post('/batches', [BasketController::class, 'createBatch']);
        Route::post('/batches/{batch}/baskets', [BasketController::class, 'addBasketsToBatch']);
    });

    // Invoice management routes
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::get('/invoices/statistics', [InvoiceController::class, 'getStatistics']);
        Route::get('/invoices/unpaid/{customerId}', [InvoiceController::class, 'getUnpaidInvoices']);
        Route::get('/invoices/overdue', [InvoiceController::class, 'getOverdueInvoices']);
        Route::get('/invoices/due-soon', [InvoiceController::class, 'getDueSoonInvoices']);
    });

    Route::middleware('permission:invoices.create')->group(function () {
        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::post('/batches/{batch}/invoice', [InvoiceController::class, 'createForBatch']);
    });

    Route::middleware('permission:invoices.edit')->group(function () {
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::patch('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel']);
    });

    // Dispatch management routes
    Route::middleware('permission:dispatches.view')->group(function () {
        Route::get('/dispatch/pending-approvals', [App\Http\Controllers\DispatchController::class, 'getPendingApprovals']);
        Route::get('/dispatch/history', [App\Http\Controllers\DispatchController::class, 'getDispatchHistory']);
        Route::get('/dispatch/statistics', [App\Http\Controllers\DispatchController::class, 'getDispatchStats']);
    });

    Route::middleware('permission:dispatches.create')->group(function () {
        Route::post('/dispatch/scan', [App\Http\Controllers\DispatchController::class, 'dispatchByBarcode']);
    });

    Route::middleware('permission:dispatches.approve')->group(function () {
        Route::post('/dispatch/{dispatchId}/approve', [App\Http\Controllers\DispatchController::class, 'approveDispatch']);
    });

    Route::middleware('permission:dispatches.process')->group(function () {
        Route::post('/dispatch/{dispatchId}/complete', [App\Http\Controllers\DispatchController::class, 'completeDispatch']);
    });

    // Payment management routes
    Route::middleware('permission:invoices.payments')->group(function () {
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'processPayment']);
        Route::post('/invoices/{invoice}/revert-payment', [PaymentController::class, 'revertPayment']);
        Route::get('/invoices/{invoice}/payments', [PaymentController::class, 'getPaymentHistory']);
        Route::get('/payments/statistics', [PaymentController::class, 'getStatistics']);
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund']);
    });

    // Role and Permission Management routes
    Route::middleware('permission:roles.manage')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'getPermissions']);
        Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
        Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission']);
    });

            // User Role Management routes
            Route::middleware('permission:users.edit')->group(function () {
                Route::get('/users', [UserRoleController::class, 'index']);
                Route::get('/users/{user}/roles', [UserRoleController::class, 'getUserRoles']);
                Route::post('/users/{user}/roles', [UserRoleController::class, 'assignRole']);
                Route::delete('/users/{user}/roles/{role}', [UserRoleController::class, 'removeRole']);
                Route::post('/users/{user}/roles/sync', [UserRoleController::class, 'syncRoles']);
            });

            // User Management routes
            Route::middleware('permission:users.manage')->group(function () {
                Route::apiResource('user-management', UserManagementController::class);
                Route::post('/user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus']);
                Route::get('/user-management/roles/available', [UserManagementController::class, 'getRoles']);
            });
});

// Financial reports routes (protected with permission)
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('permission:financial.reports')->group(function () {
        Route::get('/financial/reports', [App\Http\Controllers\FinancialController::class, 'getReports']);
        Route::post('/financial/export', [App\Http\Controllers\FinancialController::class, 'exportReport']);
    });
});

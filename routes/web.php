<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierProfileRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Shared between Admin and Manager (no suppliers allowed)
    Route::middleware(\App\Http\Middleware\AdminAccess::class)->group(function () {
        Route::patch('users/{user}/toggle-restrict', [\App\Http\Controllers\UserController::class, 'toggleRestrict'])->name('users.toggle-restrict');
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['destroy']);
        
        Route::get('trash', [\App\Http\Controllers\DataRestorationRequestController::class, 'trash'])->name('trash.index');
        Route::post('trash/request-restore', [\App\Http\Controllers\DataRestorationRequestController::class, 'store'])->name('trash.request-restore');
        
        // Reports (accessible by managers and admins)
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/preview', [\App\Http\Controllers\ReportController::class, 'preview'])->name('reports.preview');
        Route::get('reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    });

    // Manager Only Routes
    Route::middleware('role:manager')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['edit']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        
        Route::patch('suppliers/{supplier}/toggle-blacklist', [SupplierController::class, 'toggleBlacklist'])->name('suppliers.toggle-blacklist');
        Route::get('suppliers/blacklisted', [SupplierController::class, 'blacklisted'])->name('suppliers.blacklisted');
        Route::get('suppliers/{supplier}/transaction-history', [SupplierController::class, 'transactionHistory'])->name('suppliers.transaction-history');
        Route::resource('suppliers', SupplierController::class)->except(['destroy', 'edit', 'update']);
        
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('stock-movements/{stockMovement}/void', [StockMovementController::class, 'void'])->name('stock-movements.void');
        Route::get('stock-requests/create/{product}', [StockRequestController::class, 'create'])->name('stock-requests.create');
        
        // Product Edit Requests (Review/Approve)
        Route::get('supplier-requests', [\App\Http\Controllers\ProductEditRequestController::class, 'index'])->name('supplier-requests.index');
        Route::get('supplier-requests/{productEditRequest}', [\App\Http\Controllers\ProductEditRequestController::class, 'show'])->name('supplier-requests.show');
        Route::put('supplier-requests/{productEditRequest}', [\App\Http\Controllers\ProductEditRequestController::class, 'update'])->name('supplier-requests.update');

        // Profile Edit Requests (Review/Approve)
        Route::get('supplier-profile-requests', [SupplierProfileRequestController::class, 'index'])->name('supplier-profile-requests.index');
        Route::get('supplier-profile-requests/{supplierProfileRequest}', [SupplierProfileRequestController::class, 'show'])->name('supplier-profile-requests.show');
        Route::put('supplier-profile-requests/{supplierProfileRequest}', [SupplierProfileRequestController::class, 'update'])->name('supplier-profile-requests.update');
    });

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::post('audit-logs/{auditLog}/revert', [\App\Http\Controllers\AuditLogController::class, 'revert'])->name('audit-logs.revert');
        
        // Data Restorations
        Route::get('data-restorations', [\App\Http\Controllers\DataRestorationRequestController::class, 'index'])->name('data-restorations.index');
        Route::get('data-restorations/{dataRestoration}', [\App\Http\Controllers\DataRestorationRequestController::class, 'show'])->name('data-restorations.show');
        Route::patch('data-restorations/{dataRestoration}', [\App\Http\Controllers\DataRestorationRequestController::class, 'update'])->name('data-restorations.update');
    });

    // Shared Routes
    Route::resource('stock-requests', StockRequestController::class)->only(['index', 'show', 'store', 'update']);
    Route::get('suppliers/{supplier}/dashboard', [SupplierController::class, 'dashboard'])->name('suppliers.dashboard');
    // Suppliers and Managers can view the product catalog (read-only)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Supplier product edit requests (submit)
    Route::get('products/{product}/request-edit', [\App\Http\Controllers\ProductEditRequestController::class, 'create'])->name('supplier-requests.create');
    Route::post('products/{product}/request-edit', [\App\Http\Controllers\ProductEditRequestController::class, 'store'])->name('supplier-requests.store');
    // Supplier profile edit requests (submit)
    Route::get('supplier-profile/request-edit', [SupplierProfileRequestController::class, 'create'])->name('supplier-profile-requests.create');
    Route::post('supplier-profile/request-edit', [SupplierProfileRequestController::class, 'store'])->name('supplier-profile-requests.store');

    Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

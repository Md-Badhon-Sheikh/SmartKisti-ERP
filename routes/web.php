<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Customer\AreaController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Installment\InstallmentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\SubCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::post('change-locale', [LocaleController::class, 'update'])->name('change.locale');

// ── Guest (authentication) routes ────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.otp.send');

    Route::get('forgot-password/verify-otp', [OtpVerificationController::class, 'create'])->name('password.otp');
    Route::post('forgot-password/verify-otp', [OtpVerificationController::class, 'store'])->name('password.otp.verify');
    Route::post('forgot-password/resend-otp', [OtpVerificationController::class, 'resend'])->name('password.otp.resend');

    Route::get('reset-password', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ── Authenticated routes ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // ── User management ───────────────────────────────────────
    Route::middleware('role:super-admin|admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('data', [UserManagementController::class, 'Datatable'])->name('data');
        Route::get('create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('{user}', [UserManagementController::class, 'destroy'])->name('destroy');

        Route::middleware('role:super-admin')->group(function () {
            Route::post('{user}/promote-super-admin', [UserManagementController::class, 'promoteSuperAdmin'])
                ->name('promote-super-admin');
        });
    });

    // ── Products — Create/Edit are full pages; List/View/Delete/Toggle stay AJAX ──
    Route::get('products', [ProductController::class, 'Index'])->name('products.index');
    Route::get('products/datatable', [ProductController::class, 'Datatable'])->name('products.datatable');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::get('products/create', [ProductController::class, 'Create'])->name('products.create');
        Route::post('products', [ProductController::class, 'Store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'Edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'Update'])->name('products.update');

        Route::prefix('products')->name('products.')->group(function () {
            Route::post('{id}/delete', [ProductController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [ProductController::class, 'ToggleStatus'])->name('toggle');
            Route::post('{id}/images/{imageId}/delete', [ProductController::class, 'DeleteImage'])->name('images.delete');
        });

        // Categories — AJAX modal pattern (list/add/edit/view/delete/toggle all via JSON)
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'Index'])->name('index');
            Route::get('datatable', [CategoryController::class, 'Datatable'])->name('datatable');
            Route::post('/', [CategoryController::class, 'Store'])->name('store');
            Route::get('{id}', [CategoryController::class, 'Show'])->name('show');
            Route::post('{id}/update', [CategoryController::class, 'Update'])->name('update');
            Route::post('{id}/delete', [CategoryController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [CategoryController::class, 'ToggleStatus'])->name('toggle');
        });

        // Sub Categories — AJAX modal pattern (list/add/edit/view/delete/toggle all via JSON)
        Route::prefix('sub-categories')->name('sub-categories.')->group(function () {
            Route::get('/', [SubCategoryController::class, 'Index'])->name('index');
            Route::get('datatable', [SubCategoryController::class, 'Datatable'])->name('datatable');
            Route::post('/', [SubCategoryController::class, 'Store'])->name('store');
            Route::get('{id}', [SubCategoryController::class, 'Show'])->name('show');
            Route::post('{id}/update', [SubCategoryController::class, 'Update'])->name('update');
            Route::post('{id}/delete', [SubCategoryController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [SubCategoryController::class, 'ToggleStatus'])->name('toggle');
        });

        // Brands — AJAX modal pattern (list/add/edit/view/delete/toggle all via JSON)
        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/', [BrandController::class, 'Index'])->name('index');
            Route::get('datatable', [BrandController::class, 'Datatable'])->name('datatable');
            Route::post('/', [BrandController::class, 'Store'])->name('store');
            Route::get('{id}', [BrandController::class, 'Show'])->name('show');
            Route::post('{id}/update', [BrandController::class, 'Update'])->name('update');
            Route::post('{id}/delete', [BrandController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [BrandController::class, 'ToggleStatus'])->name('toggle');
        });
    });

    Route::get('products/{id}', [ProductController::class, 'Show'])->name('products.show');

    // ── Customers — Create/Edit are full pages; List/View/Delete/Toggle stay AJAX ──
    Route::get('customers', [CustomerController::class, 'Index'])->name('customers.index');
    Route::get('customers/datatable', [CustomerController::class, 'Datatable'])->name('customers.datatable');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::get('customers/create', [CustomerController::class, 'Create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'Store'])->name('customers.store');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'Edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'Update'])->name('customers.update');

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::post('{id}/delete', [CustomerController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [CustomerController::class, 'ToggleStatus'])->name('toggle');
            Route::post('{id}/documents/{documentId}/delete', [CustomerController::class, 'DeleteDocument'])->name('documents.delete');
        });

        // Areas — AJAX modal pattern (list/add/edit/view/delete/toggle all via JSON)
        Route::prefix('areas')->name('areas.')->group(function () {
            Route::get('/', [AreaController::class, 'Index'])->name('index');
            Route::get('datatable', [AreaController::class, 'Datatable'])->name('datatable');
            Route::post('/', [AreaController::class, 'Store'])->name('store');
            Route::get('{id}', [AreaController::class, 'Show'])->name('show');
            Route::post('{id}/update', [AreaController::class, 'Update'])->name('update');
            Route::post('{id}/delete', [AreaController::class, 'Delete'])->name('delete');
            Route::post('{id}/toggle', [AreaController::class, 'ToggleStatus'])->name('toggle');
        });
    });

    Route::get('customers/{id}', [CustomerController::class, 'Show'])->name('customers.show');

    // ── Sales — Create/Edit are full pages; List/View/Delete stay AJAX ──
    Route::get('sales', [SaleController::class, 'Index'])->name('sales.index');
    Route::get('sales/datatable', [SaleController::class, 'Datatable'])->name('sales.datatable');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::get('sales/create', [SaleController::class, 'Create'])->name('sales.create');
        Route::post('sales', [SaleController::class, 'Store'])->name('sales.store');
        Route::get('sales/{sale}/edit', [SaleController::class, 'Edit'])->name('sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'Update'])->name('sales.update');

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::post('{id}/delete', [SaleController::class, 'Delete'])->name('delete');
        });
    });

    Route::get('sales/{id}', [SaleController::class, 'Show'])->name('sales.show');

    // ── Installment Plans — list + schedule/payment collection page ──
    Route::get('installments', [InstallmentController::class, 'Index'])->name('installments.index');
    Route::get('installments/datatable', [InstallmentController::class, 'Datatable'])->name('installments.datatable');
    Route::get('installments/{installmentPlan}', [InstallmentController::class, 'Show'])->name('installments.show');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::post('installments/{installmentPlan}/payments', [InstallmentController::class, 'StorePayment'])->name('installments.payments.store');
    });
});

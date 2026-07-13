<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ManufacturerController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\SubCategoryController;
use App\Http\Controllers\ProfileController;
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
    Route::view('customers', 'customers.index')->name('customers.index');

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

    // ── Products ───────────────────────────────────────────────
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/data', [ProductController::class, 'Datatable'])->name('products.data');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

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

        Route::get('sub-categories/data', [SubCategoryController::class, 'Datatable'])->name('sub-categories.data');
        Route::resource('sub-categories', SubCategoryController::class)->except('show');

        Route::get('brands/data', [BrandController::class, 'Datatable'])->name('brands.data');
        Route::resource('brands', BrandController::class)->except('show');

        Route::get('manufacturers/data', [ManufacturerController::class, 'Datatable'])->name('manufacturers.data');
        Route::resource('manufacturers', ManufacturerController::class)->except('show');
    });

    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});

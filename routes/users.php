<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super-admin|admin'])->prefix('users')->name('users.')->group(function () {
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

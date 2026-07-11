<?php

use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ManufacturerController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/data', [ProductController::class, 'Datatable'])->name('products.data');

    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('categories/data', [CategoryController::class, 'Datatable'])->name('categories.data');
        Route::resource('categories', CategoryController::class)->except('show');

        Route::get('sub-categories/data', [SubCategoryController::class, 'Datatable'])->name('sub-categories.data');
        Route::resource('sub-categories', SubCategoryController::class)->except('show');

        Route::get('brands/data', [BrandController::class, 'Datatable'])->name('brands.data');
        Route::resource('brands', BrandController::class)->except('show');

        Route::get('manufacturers/data', [ManufacturerController::class, 'Datatable'])->name('manufacturers.data');
        Route::resource('manufacturers', ManufacturerController::class)->except('show');
    });

    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});

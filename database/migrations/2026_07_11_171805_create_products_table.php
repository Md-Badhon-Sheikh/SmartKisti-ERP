<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('sub_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('manufacturer_code')->nullable();
            $table->string('name');
            $table->enum('product_type', ['ready', 'custom'])->default('ready');

            // Electronics-style fields
            $table->string('model')->nullable();
            $table->string('imei_serial')->nullable();

            // Furniture-style fields
            $table->string('wood_type')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('polish')->nullable();

            $table->string('warranty')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

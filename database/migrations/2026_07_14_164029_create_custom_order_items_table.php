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
        Schema::create('custom_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained()->cascadeOnDelete();
            $table->string('product_type');
            $table->string('wood_type')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('glass_type')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('design_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_order_items');
    }
};

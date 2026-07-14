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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('custom_order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('delivery_date');
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->foreignId('delivery_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receiver_name');
            $table->string('receiver_mobile')->nullable();
            $table->enum('delivery_status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->string('signature')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

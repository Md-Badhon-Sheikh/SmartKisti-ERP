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
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->decimal('product_total', 12, 2);
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->decimal('total_due', 12, 2);
            $table->unsignedInteger('installment_month');
            $table->decimal('monthly_amount', 12, 2);
            $table->date('start_date');
            $table->date('next_payment_date')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('remaining_due', 12, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentPlan extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'product_total',
        'down_payment',
        'total_due',
        'installment_month',
        'monthly_amount',
        'start_date',
        'next_payment_date',
        'last_payment_date',
        'total_paid',
        'remaining_due',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'product_total' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'total_due' => 'decimal:2',
            'installment_month' => 'integer',
            'monthly_amount' => 'decimal:2',
            'start_date' => 'date',
            'next_payment_date' => 'date',
            'last_payment_date' => 'date',
            'total_paid' => 'decimal:2',
            'remaining_due' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class)->orderBy('installment_no');
    }
}

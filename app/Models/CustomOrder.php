<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomOrder extends Model
{
    protected $fillable = [
        'order_no',
        'customer_id',
        'order_date',
        'delivery_date',
        'order_type',
        'status',
        'estimated_price',
        'advance_amount',
        'remaining_amount',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'estimated_price' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomOrderItem::class);
    }

    public function productionStatuses(): HasMany
    {
        return $this->hasMany(ProductionStatus::class)->orderBy('date')->orderBy('id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentProductionStatus(): ?ProductionStatus
    {
        return $this->productionStatuses()->latest('date')->latest('id')->first();
    }
}

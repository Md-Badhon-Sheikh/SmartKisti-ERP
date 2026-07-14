<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'sale_id',
        'custom_order_id',
        'delivery_date',
        'delivery_charge',
        'delivery_by',
        'receiver_name',
        'receiver_mobile',
        'delivery_status',
        'signature',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'delivery_charge' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }

    public function deliveryBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_by');
    }
}

<?php

namespace App\Models;

use App\Enums\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOrderItem extends Model
{
    protected $fillable = [
        'custom_order_id',
        'product_type',
        'wood_type',
        'size',
        'color',
        'glass_type',
        'quantity',
        'price',
        'design_image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }

    public function woodTypeName(): ?string
    {
        return GlobalConstant::woodTypeName($this->wood_type);
    }

    public function colorName(): ?string
    {
        return GlobalConstant::colorName($this->color);
    }

    public function glassTypeName(): ?string
    {
        return GlobalConstant::glassTypeName($this->glass_type);
    }
}

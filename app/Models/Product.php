<?php

namespace App\Models;

use App\Enums\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'manufacturer_code',
        'name',
        'product_type',
        'model',
        'imei_serial',
        'wood_type',
        'color',
        'size',
        'polish',
        'warranty',
        'sku',
        'purchase_price',
        'selling_price',
        'stock',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function manufacturerName(): ?string
    {
        return GlobalConstant::manufacturerName($this->manufacturer_code);
    }

    public function woodTypeName(): ?string
    {
        return GlobalConstant::woodTypeName($this->wood_type);
    }

    public function colorName(): ?string
    {
        return GlobalConstant::colorName($this->color);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
}

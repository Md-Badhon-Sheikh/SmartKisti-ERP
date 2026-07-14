<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionStatus extends Model
{
    protected $table = 'production_status';

    protected $fillable = [
        'custom_order_id',
        'status',
        'date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }
}

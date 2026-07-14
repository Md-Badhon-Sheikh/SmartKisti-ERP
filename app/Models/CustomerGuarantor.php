<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerGuarantor extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'relation',
        'mobile',
        'nid',
        'address',
        'photo',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDocument extends Model
{
    protected $fillable = ['customer_id', 'file_path', 'file_name'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

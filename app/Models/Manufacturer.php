<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{
    /**
     * Production-source types a manufacturer record can be tagged with.
     */
    public const TYPES = ['own_factory', 'local_carpenter', 'outside_factory'];

    protected $fillable = ['name', 'type', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

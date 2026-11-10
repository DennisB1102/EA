<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    protected $fillable = [
        'material_id', 'location_id', 'user_id', 'quantity', 'type', 'purpose', 'notes', 'returned_at',
    ];

    protected $casts = ['returned_at' => 'datetime'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }
}

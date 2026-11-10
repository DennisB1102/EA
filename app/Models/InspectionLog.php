<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionLog extends Model
{
    protected $fillable = [
        'material_id', 'location_id', 'user_id', 'result', 'notes', 'next_inspection_due',
    ];

    protected $casts = ['next_inspection_due' => 'date'];

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
}

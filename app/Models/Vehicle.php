<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['location_id', 'name', 'call_sign', 'license_plate', 'tuev_date', 'notes', 'is_active', 'image_path'];

    protected $casts = [
        'is_active' => 'boolean',
        'tuev_date' => 'date',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(VehicleEquipment::class);
    }

    public function isTuevExpiringSoon(): bool
    {
        return $this->tuev_date && $this->tuev_date->lte(now()->addDays(30));
    }

    public function isTuevExpired(): bool
    {
        return $this->tuev_date && $this->tuev_date->lt(now());
    }
}

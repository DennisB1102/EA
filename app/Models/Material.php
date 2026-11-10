<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Material extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'min_stock', 'is_active'])->logOnlyDirty();
    }

    protected $fillable = [
        'name', 'article_number', 'category_id', 'description', 'unit',
        'min_stock', 'requires_inspection', 'inspection_interval_days',
        'has_expiry', 'is_active', 'image_path', 'is_device',
    ];

    protected $casts = [
        'requires_inspection' => 'boolean',
        'has_expiry'          => 'boolean',
        'is_active'           => 'boolean',
        'is_device'           => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    public function inspectionLogs(): HasMany
    {
        return $this->hasMany(InspectionLog::class);
    }

    public function deviceInstances(): HasMany
    {
        return $this->hasMany(DeviceInstance::class);
    }

    public function totalStock(): int
    {
        return $this->inventory()->sum('quantity');
    }

    public function isBelowMinStock(): bool
    {
        return $this->totalStock() < $this->min_stock;
    }
}

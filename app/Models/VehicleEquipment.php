<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleEquipment extends Model
{
    protected $fillable = ['vehicle_id', 'name', 'type', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const TYPES = [
        'notfallrucksack' => 'Notfallrucksack',
        'medizingeraet'   => 'Medizingerät',
        'trage'           => 'Trage',
        'sonstige'        => 'Sonstiges',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class, 'equipment_id');
    }

    public function protocols(): HasMany
    {
        return $this->hasMany(\App\Models\NfrProtocol::class, 'equipment_id');
    }

    public function assignedDevices(): HasMany
    {
        return $this->hasMany(\App\Models\DeviceInstance::class, 'equipment_id')
            ->where('is_active', true)
            ->with('material');
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}

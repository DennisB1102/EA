<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfrProtocolItem extends Model
{
    protected $fillable = [
        'protocol_id', 'material_id', 'device_instance_id',
        'is_ok', 'current_quantity', 'notes', 'photo_path',
        'resolved_at', 'resolved_by', 'resolution_notes',
    ];

    protected $casts = [
        'is_ok'       => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(NfrProtocol::class, 'protocol_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function deviceInstance(): BelongsTo
    {
        return $this->belongsTo(DeviceInstance::class, 'device_instance_id');
    }

    public function isDeviceItem(): bool
    {
        return $this->device_instance_id !== null;
    }
}

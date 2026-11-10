<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentInventory extends Model
{
    protected $table = 'equipment_inventory';

    protected $fillable = ['equipment_id', 'material_id', 'target_quantity', 'current_quantity', 'expiry_date', 'notes'];

    protected $casts = ['expiry_date' => 'date'];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(VehicleEquipment::class, 'equipment_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function isShort(): bool
    {
        return $this->current_quantity < $this->target_quantity;
    }
}

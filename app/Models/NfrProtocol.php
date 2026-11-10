<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NfrProtocol extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['equipment_id', 'checked_at', 'checked_by'])->logOnlyDirty();
    }

    protected $fillable = ['equipment_id', 'checked_at', 'checked_by', 'signature_data', 'notes'];

    protected $casts = ['checked_at' => 'date'];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(VehicleEquipment::class, 'equipment_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(NfrProtocolItem::class, 'protocol_id');
    }

    public function hasIssues(): bool
    {
        return $this->items->contains(fn ($i) => !$i->is_ok);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DeviceInstance extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['serial_number', 'mtk_ablauf', 'stk_ablauf', 'is_active'])->logOnlyDirty();
    }

    protected $fillable = ['material_id', 'equipment_id', 'serial_number', 'mtk_ablauf', 'stk_ablauf', 'notes', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'mtk_ablauf' => 'date',
        'stk_ablauf' => 'date',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(VehicleEquipment::class, 'equipment_id');
    }

    public function mtkStatus(): string
    {
        return $this->dateStatus($this->mtk_ablauf);
    }

    public function stkStatus(): string
    {
        return $this->dateStatus($this->stk_ablauf);
    }

    private function dateStatus(?Carbon $date): string
    {
        if (!$date) return 'none';
        if ($date->isPast()) return 'expired';
        if ($date->diffInDays(now()) <= 30) return 'warning';
        return 'ok';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationCheckout extends Model
{
    protected $fillable = [
        'material_id', 'inventory_id', 'quantity', 'taken_by', 'reason', 'via_qr', 'user_id',
    ];

    protected $casts = ['via_qr' => 'boolean'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfr_protocols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('vehicle_equipment')->cascadeOnDelete();
            $table->date('checked_at');
            $table->string('checked_by');
            $table->text('signature_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('nfr_protocol_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained('nfr_protocols')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->boolean('is_ok')->default(true);
            $table->unsignedSmallInteger('current_quantity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfr_protocol_items');
        Schema::dropIfExists('nfr_protocols');
    }
};

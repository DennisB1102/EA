<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_instances', function (Blueprint $table) {
            $table->foreignId('equipment_id')
                ->nullable()
                ->after('material_id')
                ->constrained('vehicle_equipment')
                ->nullOnDelete();
        });

        Schema::table('nfr_protocol_items', function (Blueprint $table) {
            $table->foreignId('device_instance_id')
                ->nullable()
                ->after('material_id')
                ->constrained('device_instances')
                ->nullOnDelete();
        });

        Schema::table('nfr_protocol_items', function (Blueprint $table) {
            $table->foreignId('material_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nfr_protocol_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('device_instance_id');
            $table->foreignId('material_id')->nullable(false)->change();
        });

        Schema::table('device_instances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->boolean('is_device')->default(false)->after('is_active');
        });

        Schema::create('device_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->string('serial_number')->nullable();
            $table->date('mtk_ablauf')->nullable();
            $table->date('stk_ablauf')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_instances');
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('is_device');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfr_protocol_items', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('notes');
            $table->timestamp('resolved_at')->nullable()->after('photo_path');
            $table->string('resolved_by')->nullable()->after('resolved_at');
            $table->string('resolution_notes')->nullable()->after('resolved_by');
        });
    }

    public function down(): void
    {
        Schema::table('nfr_protocol_items', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'resolved_at', 'resolved_by', 'resolution_notes']);
        });
    }
};

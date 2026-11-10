<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('article_number')->nullable()->unique();
            $table->foreignId('category_id')->constrained('material_categories')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('unit')->default('Stück');
            $table->integer('min_stock')->default(0);
            $table->boolean('requires_inspection')->default(false);
            $table->integer('inspection_interval_days')->nullable();
            $table->boolean('has_expiry')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

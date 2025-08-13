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
        Schema::create('raw_material_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_category_id')->comment('raw material categories id')->nullable();
            $table->foreign('material_category_id')->references('id')->on('raw_material_categories')->onDelete('cascade');
            $table->string('material_name')->nullable();
            $table->string('material_code')->nullable();
            $table->unsignedBigInteger('unit_id')->comment('units id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->decimal('alert_quantity',10,2)->nullable();
            $table->decimal('tax',10,2)->nullable();
            $table->timestamps();
            defaultMigration($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_masters');
    }
};

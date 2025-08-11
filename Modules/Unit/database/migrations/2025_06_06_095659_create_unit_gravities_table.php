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
        Schema::create('unit_gravities', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->comment('unit id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->unsignedBigInteger('child_id')->comment('child id')->nullable();
            $table->foreign('child_id')->references('id')->on('units')->onDelete('cascade');
            $table->double('unit_value', 8, 2)->nullable();
            $table->timestamps();
            defaultMigration($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_gravities');
    }
};

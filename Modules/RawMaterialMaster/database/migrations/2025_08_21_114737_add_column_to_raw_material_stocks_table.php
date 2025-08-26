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
        Schema::table('raw_material_stocks', function (Blueprint $table) {
            $table->decimal('price', 12, 3)->after('quantity')->nullable();
            $table->decimal('total', 12, 3)->after('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_stocks', function (Blueprint $table) {

        });
    }
};

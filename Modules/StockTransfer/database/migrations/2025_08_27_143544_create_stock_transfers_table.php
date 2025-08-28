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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->comment('raw material masters id')->nullable();
            $table->foreign('material_id')->references('id')->on('raw_material_masters')->onDelete('cascade');
            $table->unsignedBigInteger('material_stock_id')->comment('raw material stocks id')->nullable();
            $table->foreign('material_stock_id')->references('id')->on('raw_material_stocks')->onDelete('cascade');
            $table->unsignedBigInteger('from_site_id')->comment('site masters id')->nullable();
            $table->foreign('from_site_id')->references('id')->on('site_masters')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->comment('users id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to_site_id')->comment('site masters id')->nullable();
            $table->foreign('to_site_id')->references('id')->on('site_masters')->onDelete('cascade');
            $table->unsignedBigInteger('to_supervisor_id')->comment('users id')->nullable();
            $table->foreign('to_supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('quantity', 12, 3)->nullable();
            $table->unsignedBigInteger('unit_id')->comment('units id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('year_id')->comment('years id')->nullable();
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->timestamps();
            defaultMigration($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};

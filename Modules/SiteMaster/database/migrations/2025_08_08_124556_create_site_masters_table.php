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
        Schema::create('site_masters', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->unsignedBigInteger('country_id')->comment('countries id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->unsignedBigInteger('state_id')->comment('states id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->unsignedBigInteger('city_id')->comment('cities id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('site_master_status_id')->comment('site master statuses id')->nullable();
            $table->foreign('site_master_status_id')->references('id')->on('site_master_statuses')->onDelete('cascade');
            $table->timestamps();
            defaultMigration($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_masters');
    }
};

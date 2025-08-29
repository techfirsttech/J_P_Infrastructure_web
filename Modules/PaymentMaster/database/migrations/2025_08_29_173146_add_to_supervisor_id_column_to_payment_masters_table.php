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
        Schema::table('payment_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('to_supervisor_id')->after('supervisor_id')->comment('users id')->nullable();
            $table->foreign('to_supervisor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_masters', function (Blueprint $table) {});
    }
};

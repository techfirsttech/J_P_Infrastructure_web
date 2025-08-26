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
        Schema::create('labours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id')->comment('users id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('site_id')->comment('site masters id')->nullable();
            $table->foreign('site_id')->references('id')->on('site_masters')->onDelete('cascade');
            $table->string('labour_name')->nullable();
            $table->decimal('daily_wage', 12, 3)->comment('Per Day')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->nullable();
            $table->unsignedBigInteger('user_id')->comment('users id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('labours');
    }
};

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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->comment('site masters id')->nullable();
            $table->foreign('site_id')->references('id')->on('site_masters')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->comment('users id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('labour_id')->comment('labours id')->nullable();
            $table->foreign('labour_id')->references('id')->on('labours')->onDelete('cascade');
            $table->enum('type', ['Half', 'Full', 'Absent'])->comment('Half', 'Full', 'Absent')->default('Absent')->nullable();
            $table->decimal('amount', 12, 3)->nullable();
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
        Schema::dropIfExists('attendances');
    }
};

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
        Schema::create('expense_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->comment('site masters id')->nullable();
            $table->foreign('site_id')->references('id')->on('site_masters')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->comment('users id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('expense_category_id')->comment('expense categories id')->nullable();
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->decimal('amount',12,3)->nullable();
            $table->binary('document')->nullable();
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
        Schema::dropIfExists('expense_masters');
    }
};

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
        Schema::create('expense_category_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_category_status_name')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('color_class')->nullable();
            $table->timestamps();
            defaultMigration($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_category_statuses');
    }
};

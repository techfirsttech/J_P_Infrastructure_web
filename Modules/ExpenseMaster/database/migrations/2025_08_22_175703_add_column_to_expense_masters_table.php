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
        Schema::table('expense_masters', function (Blueprint $table) {
            $table->enum('status', ['Approve', 'Hold', 'Pending', 'Reject'])->default('Approve')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_masters', function (Blueprint $table) {});
    }
};

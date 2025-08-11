<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding mobile column with unique constraint
            $table->string('mobile')->unique()->after('name');

            // Adding username column with unique constraint
            $table->string('username')->unique()->after('mobile');

            // Making email nullable
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping unique constraints
            $table->dropUnique(['users_mobile_unique']);
            $table->dropUnique(['users_username_unique']);

            // Dropping columns
            $table->dropColumn('mobile');
            $table->dropColumn('username');

            // Reverting email to not nullable
            $table->string('email')->nullable(false)->change();
        });
    }
};
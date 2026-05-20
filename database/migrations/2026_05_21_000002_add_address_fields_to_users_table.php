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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('saved_latitude', 10, 8)->nullable()->after('email');
            $table->decimal('saved_longitude', 11, 8)->nullable()->after('saved_latitude');
            $table->string('saved_street')->nullable()->after('saved_longitude');
            $table->string('saved_postal_code')->nullable()->after('saved_street');
            $table->string('saved_province')->nullable()->after('saved_postal_code');
            $table->string('saved_city')->nullable()->after('saved_province');
            $table->string('saved_district')->nullable()->after('saved_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['saved_latitude', 'saved_longitude', 'saved_street', 'saved_postal_code', 'saved_province', 'saved_city', 'saved_district']);
        });
    }
};

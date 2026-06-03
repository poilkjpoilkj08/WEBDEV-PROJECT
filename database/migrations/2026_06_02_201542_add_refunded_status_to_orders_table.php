<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'refunded' status to the enum
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum without 'refunded'
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Add store_id to track which store each item was purchased from
            $table->foreignId('store_id')
                  ->nullable()
                  ->constrained('store_locations')
                  ->onDelete('set null')
                  ->after('book_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });
    }
};

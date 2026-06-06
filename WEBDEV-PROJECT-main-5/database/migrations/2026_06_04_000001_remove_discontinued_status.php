<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Remove "discontinued" status
     */
    public function up(): void
    {
        // Convert any "discontinued" books to "out_of_stock"
        DB::table('books')->where('status', 'discontinued')->update(['status' => 'out_of_stock']);
        
        // Drop and recreate the enum column without "discontinued"
        // MySQL approach
        Schema::table('books', function (Blueprint $table) {
            $table->string('status')->change(); // Temporarily make it a string
        });
        
        // Now change it back to enum without discontinued
        DB::statement("ALTER TABLE books MODIFY status ENUM('available', 'out_of_stock') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE books MODIFY status ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available'");
    }
};

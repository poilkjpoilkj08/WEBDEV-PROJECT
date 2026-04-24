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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->integer('stock')->default(0);
            $table->decimal('price',12,2)->change();
            $table->renameColumn('description', 'details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->dropColumn('stock');
            $table->decimal('price', 10, 2)->change();
            $table->renameColumn('details', 'description');
        });
    }
};

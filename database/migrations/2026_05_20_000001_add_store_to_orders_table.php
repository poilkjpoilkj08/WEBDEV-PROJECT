<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->constrained('store_locations')->onDelete('set null')->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'shipping_distance_km')) {
                $table->decimal('shipping_distance_km', 8, 2)->nullable()->after('shipping_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'store_id')) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            }
            if (Schema::hasColumn('orders', 'shipping_distance_km')) {
                $table->dropColumn('shipping_distance_km');
            }
        });
    }
};

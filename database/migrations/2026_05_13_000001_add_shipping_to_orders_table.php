<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('paid_at');
            $table->string('shipping_phone')->nullable()->after('shipping_name');
            $table->text('shipping_address')->nullable()->after('shipping_phone');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_province')->nullable()->after('shipping_city');
            $table->string('shipping_postal_code', 20)->nullable()->after('shipping_province');
            $table->string('shipping_country', 100)->default('Indonesia')->after('shipping_postal_code');
            $table->string('shipping_method', 50)->nullable()->after('shipping_country');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_method');
            $table->string('shipping_status', 50)->default('pending')->after('shipping_cost');
            $table->string('tracking_number')->nullable()->after('shipping_status');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name', 'shipping_phone', 'shipping_address',
                'shipping_city', 'shipping_province', 'shipping_postal_code',
                'shipping_country', 'shipping_method', 'shipping_cost',
                'shipping_status', 'tracking_number', 'shipped_at',
            ]);
        });
    }
};

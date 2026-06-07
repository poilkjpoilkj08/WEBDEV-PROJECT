<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_zone', 1)->nullable()->default('C')->after('shipping_cost');
            $table->json('shipping_breakdown')->nullable()->after('shipping_zone');
            // For idempotency: track if payment was already processed
            $table->boolean('payment_processed')->default(false)->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_zone', 'shipping_breakdown', 'payment_processed']);
        });
    }
};

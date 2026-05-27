<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Delivery confirmation system
            $table->dateTime('delivery_confirmed_at')->nullable()->after('shipped_at');
            $table->dateTime('delivery_confirmation_deadline')->nullable()->after('delivery_confirmed_at');
            $table->boolean('delivery_confirmed_by_user')->default(false)->after('delivery_confirmation_deadline');
            
            // Refund system
            $table->dateTime('refund_requested_at')->nullable()->after('delivery_confirmed_by_user');
            $table->string('refund_status')->default('none')->after('refund_requested_at'); // none, requested, approved, rejected, completed
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_reason');
            
            // Revenue tracking (only count when delivery confirmed)
            $table->boolean('revenue_recorded')->default(false)->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_confirmed_at',
                'delivery_confirmation_deadline',
                'delivery_confirmed_by_user',
                'refund_requested_at',
                'refund_status',
                'refund_reason',
                'refund_amount',
                'revenue_recorded',
            ]);
        });
    }
};

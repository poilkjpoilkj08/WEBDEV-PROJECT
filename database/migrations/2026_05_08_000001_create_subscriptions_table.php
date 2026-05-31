<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->enum('plan', ['free', 'basic', 'premium'])->default('free');
            $table->enum('status', ['active', 'cancelled', 'pending'])->default('active');
            $table->string('token')->unique(); // for unsubscribe link
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

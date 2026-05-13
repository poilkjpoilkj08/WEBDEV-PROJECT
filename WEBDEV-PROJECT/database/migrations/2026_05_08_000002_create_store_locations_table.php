<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('country')->default('Indonesia');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('opening_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table: which books are available at which stores
        Schema::create('book_store_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('store_location_id');
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('store_location_id')->references('id')->on('store_locations')->onDelete('cascade');
            $table->unique(['book_id', 'store_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_store_locations');
        Schema::dropIfExists('store_locations');
    }
};

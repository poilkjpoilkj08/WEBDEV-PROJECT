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
        // Drop existing tables
        Schema::dropIfExists('properties');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('property_types');

        // Create book_categories table (replaces property_types)
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create authors table (replaces agents)
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('publisher')->nullable(); // replaces license_number
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });

        // Create books table (replaces properties)
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('isbn')->nullable();
            $table->integer('pages')->nullable(); // replaces bedrooms
            $table->string('language')->default('English'); // replaces bathrooms
            $table->integer('publication_year')->nullable(); // replaces year_built
            $table->string('publisher')->nullable(); // replaces location
            $table->enum('status', ['available', 'out_of_stock', 'discontinued'])->default('available');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('cover_image_url')->nullable(); // replaces image_url
            $table->json('genres')->nullable(); // replaces amenities
            $table->json('images')->nullable();
            $table->decimal('weight_grams', 8, 2)->nullable(); // replaces lot_size_sqft
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('book_categories')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('book_categories');
    }
};

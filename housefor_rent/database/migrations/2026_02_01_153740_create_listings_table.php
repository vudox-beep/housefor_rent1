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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['rent', 'buy'])->default('rent');
            $table->enum('category', ['house', 'restaurant']);
            $table->decimal('price', 15, 2);
            $table->enum('currency', ['ZMW', 'USD']);
            $table->string('location');
            $table->json('images')->nullable();
            
            // House specific fields
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->string('area')->nullable();
            
            // Restaurant specific fields
            $table->string('cuisine')->nullable();
            
            // Common
            $table->json('amenities')->nullable();
            
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->enum('status', ['active', 'sold', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};

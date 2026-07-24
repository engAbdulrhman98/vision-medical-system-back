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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable product name
            $table->string('slug')->unique(); // Unique slug
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->json('description')->nullable(); // Translatable description
            $table->json('details')->nullable(); // Translatable details
            $table->string('image')->nullable(); // Product cover image path/URL
            $table->boolean('in_stock')->default(true);
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

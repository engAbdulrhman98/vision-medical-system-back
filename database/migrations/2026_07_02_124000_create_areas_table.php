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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Spatie Translatable stores translations in a JSON column
            $table->enum('type', ['governorate', 'borough', 'city', 'town', 'village', 'hamlet']);
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('areas')
                ->nullOnDelete(); // Or cascade on delete, nullOnDelete is typically safer for geographical data so that child entities are not deleted when a parent is deleted.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};

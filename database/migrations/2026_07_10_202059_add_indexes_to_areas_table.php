<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            // Speed up type filtering (governorate / city / town / village)
            $table->index('type', 'areas_type_idx');
            // Speed up parent-child lookups
            $table->index('parent_id', 'areas_parent_id_idx');
            // Speed up ordering by latest
            $table->index('created_at', 'areas_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropIndex('areas_type_idx');
            $table->dropIndex('areas_parent_id_idx');
            $table->dropIndex('areas_created_at_idx');
        });
    }
};

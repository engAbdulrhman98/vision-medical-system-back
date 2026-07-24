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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'type')) {
                $table->string('type')->default('Hospital')->after('name');
            }
            if (!Schema::hasColumn('clients', 'governorate')) {
                $table->string('governorate')->nullable()->after('area_id');
            }
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('governorate');
            }
            if (!Schema::hasColumn('clients', 'detailed_address')) {
                $table->text('detailed_address')->nullable()->after('city');
            }
            if (!Schema::hasColumn('clients', 'notes')) {
                $table->text('notes')->nullable()->after('detailed_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['type', 'governorate', 'city', 'detailed_address', 'notes']);
        });
    }
};

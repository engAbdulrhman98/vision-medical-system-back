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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('action_type')->nullable()->after('type'); // quotation_request, invoice_request, maintenance_request
            $table->text('rejection_reason')->nullable()->after('description');
            $table->text('accountant_note')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['action_type', 'rejection_reason', 'accountant_note', 'invoice_id']);
        });
    }
};

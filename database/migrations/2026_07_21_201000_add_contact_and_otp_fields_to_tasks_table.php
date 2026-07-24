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
            $table->foreignId('client_contact_id')->nullable()->constrained('client_contacts')->nullOnDelete()->after('client_id');
            $table->string('otp_code')->nullable()->after('completed_at');
            $table->dateTime('otp_expires_at')->nullable()->after('otp_code');
            $table->dateTime('otp_verified_at')->nullable()->after('otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['client_contact_id']);
            $table->dropColumn(['client_contact_id', 'otp_code', 'otp_expires_at', 'otp_verified_at']);
        });
    }
};

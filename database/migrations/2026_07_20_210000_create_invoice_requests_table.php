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
        Schema::create('invoice_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Outdoor Engineer / Sales Rep
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // Main client (or null if batch)
            $table->foreignId('accountant_id')->nullable()->constrained('users')->onDelete('set null'); // Accountant who issued
            $table->foreignId('collector_id')->nullable()->constrained('users')->onDelete('set null'); // Assigned collector
            $table->string('request_type')->default('maintenance_service'); // 'maintenance_service' or 'sales_product'
            $table->string('type')->default('single'); // 'single' or 'batch'
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('status')->default('pending_accountant'); 
            // Statuses: 'pending_accountant', 'issued', 'client_approved', 'client_rejected', 'ready_for_collection', 'collected'
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('client_responded_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_request_id')->constrained('invoice_requests')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // Specific client per item (for batch requests)
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->string('invoice_number')->nullable(); // Set when issued
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_request_items');
        Schema::dropIfExists('invoice_requests');
    }
};

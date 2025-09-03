<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simply drop and recreate all tables with integer IDs
        Schema::dropIfExists('basket_histories');
        Schema::dropIfExists('baskets');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');

        // Recreate customers table with integer ID
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->unique();
            $table->string('cnic_number')->unique();
            $table->string('phone_number');
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate batches table with integer ID
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('total_baskets');
            $table->decimal('total_weight', 12, 2);
            $table->decimal('total_value', 12, 2);
            $table->timestamps();
        });

        // Recreate baskets table with integer ID
        Schema::create('baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('barcode')->unique();
            $table->enum('status', ['unpaid', 'dispatched'])->default('unpaid');
            $table->timestamps();
        });

        // Recreate invoices table with integer ID
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('barcode');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });

        // Recreate basket_histories table with integer ID
        Schema::create('basket_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basket_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('barcode');
            $table->decimal('unit_price', 10, 2);
            $table->timestamp('dispatched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basket_histories');
        Schema::dropIfExists('baskets');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Room 1, Room 2, etc.
            $table->string('code', 10)->unique(); // R1, R2, etc.
            $table->integer('capacity')->default(20000); // Basket capacity
            $table->integer('current_usage')->default(0); // Current baskets stored
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

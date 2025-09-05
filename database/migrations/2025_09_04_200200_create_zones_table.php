<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->onDelete('cascade');
            $table->integer('zone_number'); // 1, 2, 3, etc.
            $table->string('name'); // Zone A, Zone B, Zone C, etc.
            $table->string('code', 10); // A, B, C, etc.
            $table->integer('capacity')->default(1667); // 5000/3 = ~1667 baskets per zone
            $table->integer('current_usage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['floor_id', 'zone_number']);
            $table->unique(['floor_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};

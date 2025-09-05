<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('batches')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('customer_id')->constrained()->onDelete('set null');
            $table->foreignId('floor_id')->nullable()->after('room_id')->constrained()->onDelete('set null');
            $table->foreignId('zone_id')->nullable()->after('floor_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('batches')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['floor_id']);
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['room_id', 'floor_id', 'zone_id']);
        });
    }
};

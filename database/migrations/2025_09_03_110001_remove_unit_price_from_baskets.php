<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('baskets')) {
            return;
        }

        Schema::table('baskets', function (Blueprint $table) {
            if (Schema::hasColumn('baskets', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('baskets')) {
            return;
        }

        Schema::table('baskets', function (Blueprint $table) {
            if (!Schema::hasColumn('baskets', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->after('weight');
            }
        });
    }
};

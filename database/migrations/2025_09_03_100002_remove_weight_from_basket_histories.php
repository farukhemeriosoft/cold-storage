<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('basket_histories')) {
            return;
        }

        Schema::table('basket_histories', function (Blueprint $table) {
            if (Schema::hasColumn('basket_histories', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('basket_histories')) {
            return;
        }

        Schema::table('basket_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('basket_histories', 'weight')) {
                $table->decimal('weight', 10, 2)->after('barcode');
            }
        });
    }
};

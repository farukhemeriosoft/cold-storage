<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('batches')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('created_at');
            }
        });

        // Update existing batches with expiry date (12 months from creation)
        DB::table('batches')->whereNull('expiry_date')->update([
            'expiry_date' => DB::raw('DATE_ADD(created_at, INTERVAL 1 YEAR)')
        ]);

        // Make the column not nullable after updating existing records
        Schema::table('batches', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('batches')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
        });
    }
};

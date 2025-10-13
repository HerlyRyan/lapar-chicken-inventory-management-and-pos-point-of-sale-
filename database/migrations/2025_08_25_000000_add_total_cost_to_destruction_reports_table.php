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
        Schema::table('destruction_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('destruction_reports', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destruction_reports', function (Blueprint $table) {
            if (Schema::hasColumn('destruction_reports', 'total_cost')) {
                $table->dropColumn('total_cost');
            }
        });
    }
};

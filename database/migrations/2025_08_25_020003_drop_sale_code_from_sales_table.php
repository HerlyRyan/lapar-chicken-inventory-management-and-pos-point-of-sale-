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
        if (Schema::hasColumn('sales', 'sale_code')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('sale_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('sales', 'sale_code')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('sale_code', 255)->default('')->after('sale_number');
            });
        }
    }
};

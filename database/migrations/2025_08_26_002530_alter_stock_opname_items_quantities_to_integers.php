<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('stock_opname_items')) {
            return;
        }

        // Round existing decimal values to nearest integer before type change
        DB::table('stock_opname_items')->update([
            'system_quantity' => DB::raw('ROUND(system_quantity, 0)'),
            'real_quantity' => DB::raw('ROUND(real_quantity, 0)'),
            'difference' => DB::raw('ROUND(difference, 0)'),
        ]);

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->unsignedInteger('system_quantity')->default(0)->change();
            $table->unsignedInteger('real_quantity')->default(0)->change();
            $table->integer('difference')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('stock_opname_items')) {
            return;
        }

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->decimal('system_quantity', 15, 3)->default(0)->change();
            $table->decimal('real_quantity', 15, 3)->default(0)->change();
            $table->decimal('difference', 15, 3)->default(0)->change();
        });
    }
};

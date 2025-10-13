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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['raw', 'semi']);
            $table->unsignedBigInteger('item_id'); // refers to raw_materials.id or semi_finished_products.id
            $table->string('item_code');
            $table->string('item_name');
            $table->string('unit_abbr')->nullable();

            $table->unsignedInteger('system_quantity')->default(0);
            $table->unsignedInteger('real_quantity')->default(0);
            $table->integer('difference')->default(0); // real - system
            $table->enum('status', ['matched', 'over', 'under'])->default('matched');

            // monetary snapshot (optional)
            $table->decimal('unit_cost', 15, 2)->default(0); // raw: unit_price; semi: production_cost
            $table->decimal('value_difference', 18, 2)->default(0); // difference * unit_cost

            $table->timestamps();

            $table->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};

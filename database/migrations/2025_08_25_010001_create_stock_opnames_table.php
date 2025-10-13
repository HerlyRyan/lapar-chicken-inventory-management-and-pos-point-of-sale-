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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique(); // SO-YYYYMMDD-XXX
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->enum('product_type', ['raw', 'semi']); // jenis item
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); // null untuk bahan baku terpusat
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // pembuat draft
            $table->timestamp('submitted_at')->nullable();
            $table->text('notes')->nullable();

            // ringkasan hasil (diisi saat submit)
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('matched_count')->default(0);
            $table->unsignedInteger('over_count')->default(0);
            $table->unsignedInteger('under_count')->default(0);
            $table->decimal('match_percentage', 5, 2)->default(0); // 0 - 100

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};

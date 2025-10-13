<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIsActiveColumnToCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasColumn('categories', 'is_active')) {
            DB::statement('ALTER TABLE categories ADD COLUMN is_active TINYINT(1) DEFAULT 1');
            $this->command->info('Added is_active column to categories table.');
        } else {
            $this->command->info('is_active column already exists in categories table.');
        }
    }
}

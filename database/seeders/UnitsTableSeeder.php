<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['unit_name' => 'Kilogram', 'abbreviation' => 'kg', 'description' => 'Satuan berat dalam kilogram', 'is_active' => true],
            ['unit_name' => 'Gram', 'abbreviation' => 'gr', 'description' => 'Satuan berat dalam gram', 'is_active' => true],
            ['unit_name' => 'Liter', 'abbreviation' => 'lt', 'description' => 'Satuan volume dalam liter', 'is_active' => true],
            ['unit_name' => 'Mililiter', 'abbreviation' => 'ml', 'description' => 'Satuan volume dalam mililiter', 'is_active' => true],
            ['unit_name' => 'Pcs', 'abbreviation' => 'pcs', 'description' => 'Satuan buah/pieces', 'is_active' => true],
            ['unit_name' => 'Pack', 'abbreviation' => 'pack', 'description' => 'Satuan kemasan', 'is_active' => true],
            ['unit_name' => 'Karton', 'abbreviation' => 'ktn', 'description' => 'Satuan kardus/karton', 'is_active' => true],
            ['unit_name' => 'Sak', 'abbreviation' => 'sak', 'description' => 'Satuan karung/sak', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['abbreviation' => $unit['abbreviation']], $unit);
        }
    }
}

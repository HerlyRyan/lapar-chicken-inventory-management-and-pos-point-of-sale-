<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Cabang Pusat',
                'code' => 'PUSAT',
                'address' => 'Jl. Raya Utama No. 123, Jakarta Pusat',
                'phone' => '021-1234567',
                'email' => 'pusat@laparchicken.com',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Bandung',
                'code' => 'BDG',
                'address' => 'Jl. Dago No. 45, Bandung',
                'phone' => '022-7654321',
                'email' => 'bandung@laparchicken.com',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Surabaya',
                'code' => 'SBY',
                'address' => 'Jl. Pemuda No. 78, Surabaya',
                'phone' => '031-9876543',
                'email' => 'surabaya@laparchicken.com',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Medan',
                'code' => 'MDN',
                'address' => 'Jl. Gatot Subroto No. 56, Medan',
                'phone' => '061-2468135',
                'email' => 'medan@laparchicken.com',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Yogyakarta',
                'code' => 'YGY',
                'address' => 'Jl. Malioboro No. 89, Yogyakarta',
                'phone' => '0274-1357924',
                'email' => 'yogyakarta@laparchicken.com',
                'is_active' => true,
            ]
        ];

        foreach ($branches as $branchData) {
            Branch::firstOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );
        }
    }
}

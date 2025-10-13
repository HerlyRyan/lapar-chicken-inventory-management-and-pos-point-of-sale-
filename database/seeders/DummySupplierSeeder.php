<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class DummySupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Bahan Baku Jaya',
                'code' => 'BBJ001',
                'address' => 'Jl. Raya No. 1, Jakarta',
                'phone' => '+6281234567890',
                'email' => 'bbj@example.com',
                'contact_person' => 'Budi Santoso',
                'is_active' => true,
            ],
            [
                'name' => 'CV. Distributor Pangan',
                'code' => 'DIP002',
                'address' => 'Jl. Pangan Sejahtera No. 5, Bandung',
                'phone' => '+6287654321098',
                'email' => 'distributor@example.com',
                'contact_person' => 'Siti Aminah',
                'is_active' => true,
            ],
            [
                'name' => 'UD. Mitra Usaha',
                'code' => 'MUT003',
                'address' => 'Jl. Usaha Bersama No. 10, Surabaya',
                'phone' => '+6285000111222',
                'email' => 'mitra@example.com',
                'contact_person' => 'Joko Susilo',
                'is_active' => false,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}

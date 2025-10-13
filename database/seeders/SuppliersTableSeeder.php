<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Charoen Pokphand Indonesia',
                'code' => 'CPI',
                'address' => 'Jl. Ancol VIII No.1, Jakarta Utara',
                'phone' => '021-6901234'
            ],
            [
                'name' => 'CV Bali Madura Fresh',
                'code' => 'BMF',
                'address' => 'Jl. Raya Denpasar-Gilimanuk Km 5, Bali',
                'phone' => '0361-234567'
            ],
            [
                'name' => 'PT Indo Food Sukses Makmur',
                'code' => 'IFS',
                'address' => 'Jl. Sudirman Kav 76-78, Jakarta Selatan',
                'phone' => '021-5747890'
            ],
            [
                'name' => 'CV Sayur Segar Nusantara',
                'code' => 'SSN',
                'address' => 'Jl. Raya Bogor Km 25, Bogor',
                'phone' => '0251-891234'
            ],
            [
                'name' => 'PT Multi Rasa Prima',
                'code' => 'MRP',
                'address' => 'Jl. Gatot Subroto Kav 18, Jakarta',
                'phone' => '021-5201234'
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}

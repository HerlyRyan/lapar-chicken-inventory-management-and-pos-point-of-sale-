<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-suppliers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert supplier data directly into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to insert suppliers...');

        try {
            // Check if suppliers table exists
            if (!Schema::hasTable('suppliers')) {
                $this->error('Suppliers table does not exist!');
                return 1;
            }

            // Insert supplier data
            $suppliers = [
                [
                    'name' => 'PT. Bahan Baku Jaya',
                    'code' => 'BBJ001',
                    'address' => 'Jl. Raya No. 1, Jakarta',
                    'phone' => '+6281234567890',
                    'email' => 'bbj@example.com',
                    'contact_person' => 'Budi Santoso',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'CV. Distributor Pangan',
                    'code' => 'DIP002',
                    'address' => 'Jl. Pangan Sejahtera No. 5, Bandung',
                    'phone' => '+6287654321098',
                    'email' => 'distributor@example.com',
                    'contact_person' => 'Siti Aminah',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'UD. Mitra Usaha',
                    'code' => 'MUT003',
                    'address' => 'Jl. Usaha Bersama No. 10, Surabaya',
                    'phone' => '+6285000111222',
                    'email' => 'mitra@example.com',
                    'contact_person' => 'Joko Susilo',
                    'is_active' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($suppliers as $supplier) {
                DB::table('suppliers')->insert($supplier);
                $this->info("Inserted supplier: {$supplier['name']}");
            }

            $this->info('All suppliers inserted successfully!');
            
            // Count suppliers
            $count = DB::table('suppliers')->count();
            $this->info("Total suppliers in database: {$count}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error inserting suppliers: ' . $e->getMessage());
            return 1;
        }
    }
}

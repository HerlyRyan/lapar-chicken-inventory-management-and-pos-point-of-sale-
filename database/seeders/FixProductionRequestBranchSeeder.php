<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixProductionRequestBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fix production requests that have null branch_id
        $firstBranch = \App\Models\Branch::first();
        
        if ($firstBranch) {
            $updated = \App\Models\ProductionRequest::whereNull('branch_id')
                ->update(['branch_id' => $firstBranch->id]);
            
            $this->command->info("Fixed {$updated} production requests with branch ID: {$firstBranch->id} ({$firstBranch->name})");
        } else {
            $this->command->error('No branches found in database');
        }
    }
}

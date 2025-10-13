<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuickDbCheck extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Quick database structure check';

    public function handle()
    {
        $this->info('🔍 Quick Database Structure Check');
        $this->line('================================');
        
        try {
            // Test connection
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
            
            // Check tables
            $tables = $this->getAllTables();
            $this->info("📊 Total tables: " . count($tables));
            
            $this->line('');
            $this->info('📋 Tables found:');
            
            foreach ($tables as $table) {
                $count = DB::table($table)->count();
                $this->line("  • {$table}: {$count} records");
            }
            
            $this->line('');
            $this->info('🔍 Key table structures:');
            
            // Check branches table
            if (in_array('branches', $tables)) {
                $this->checkTableStructure('branches');
            } else {
                $this->error('❌ branches table not found!');
            }
            
            // Check users table
            if (in_array('users', $tables)) {
                $this->checkTableStructure('users');
            } else {
                $this->error('❌ users table not found!');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Database error: ' . $e->getMessage());
        }
    }
    
    private function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";
        
        return array_map(function($table) use ($tableKey) {
            return $table->$tableKey;
        }, $tables);
    }
    
    private function checkTableStructure($tableName)
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
            $this->line("  📋 {$tableName}:");
            foreach ($columns as $column) {
                $this->line("     - {$column->Field} ({$column->Type})");
            }
        } catch (\Exception $e) {
            $this->error("     ❌ Error checking {$tableName}: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseFixer extends Command
{
    protected $signature = 'db:fix {--force} {--backup}';
    protected $description = 'Fix database issues and ensure all tables exist properly';

    public function handle()
    {
        $this->info('🔧 Database Fixer - LapArChicken Inventory');
        $this->line('=====================================');
        $this->line('');

        // Step 1: Check connection
        if (!$this->checkConnection()) {
            return 1;
        }

        // Step 2: Backup if requested
        if ($this->option('backup')) {
            $this->backupData();
        }

        // Step 3: Analyze current state
        $this->analyzeCurrentState();

        // Step 4: Fix issues
        $this->fixDatabaseIssues();

        // Step 5: Verify fix
        $this->verifyDatabase();

        $this->line('');
        $this->info('🎉 Database fix completed successfully!');
        
        return 0;
    }

    private function checkConnection()
    {
        $this->info('🔍 Step 1: Checking database connection...');
        
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
            
            $dbName = DB::getDatabaseName();
            $this->comment("   Connected to: {$dbName}");
            return true;
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed!');
            $this->error("   Error: {$e->getMessage()}");
            $this->line('');
            $this->comment('💡 Please check your .env file configuration:');
            $this->comment('   DB_CONNECTION=mysql');
            $this->comment('   DB_HOST=127.0.0.1');
            $this->comment('   DB_PORT=3306');
            $this->comment('   DB_DATABASE=laparchicken_inventory');
            $this->comment('   DB_USERNAME=root');
            $this->comment('   DB_PASSWORD=your_password');
            return false;
        }
    }

    private function backupData()
    {
        $this->info('💾 Step 2: Backing up existing data...');
        
        $tables = ['users', 'branches', 'categories', 'units', 'suppliers'];
        
        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    if ($count > 0) {
                        $data = DB::table($table)->get();
                        $filename = "backup_{$table}_" . date('Y_m_d_H_i_s') . ".json";
                        file_put_contents(storage_path("app/{$filename}"), $data->toJson(JSON_PRETTY_PRINT));
                        $this->comment("   ✅ {$table}: {$count} records backed up to {$filename}");
                    }
                }
            } catch (\Exception $e) {
                $this->comment("   ⚠️ {$table}: Could not backup - {$e->getMessage()}");
            }
        }
    }

    private function analyzeCurrentState()
    {
        $this->info('🔍 Step 3: Analyzing current database state...');
        
        try {
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            
            if ($tableCount === 0) {
                $this->comment('   📭 No tables found - will create from scratch');
                return;
            }
            
            $this->comment("   📊 Found {$tableCount} existing tables");
            
            // Check for key tables
            $requiredTables = ['branches', 'users', 'categories', 'units', 'materials', 'finished_products'];
            $missingTables = [];
            
            $existingTableNames = array_map(function($table) {
                $databaseName = DB::getDatabaseName();
                $tableKey = "Tables_in_{$databaseName}";
                return $table->$tableKey;
            }, $tables);
            
            foreach ($requiredTables as $required) {
                if (!in_array($required, $existingTableNames)) {
                    $missingTables[] = $required;
                }
            }
            
            if (!empty($missingTables)) {
                $this->warn('   ⚠️ Missing tables: ' . implode(', ', $missingTables));
            } else {
                $this->comment('   ✅ All key tables found');
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error analyzing database: {$e->getMessage()}");
        }
    }

    private function fixDatabaseIssues()
    {
        $this->info('🔧 Step 4: Fixing database issues...');
        
        if ($this->option('force') || $this->confirm('This will run fresh migrations. Continue?', true)) {
            
            $this->comment('   🔄 Running fresh migrations...');
            
            try {
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                
                // Run fresh migrations
                Artisan::call('migrate:fresh', ['--force' => true]);
                $output = Artisan::output();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
                
                $this->comment('   ✅ Fresh migrations completed');
                
                // Run seeders
                if ($this->confirm('Run database seeders?', true)) {
                    $this->comment('   🌱 Running seeders...');
                    Artisan::call('db:seed', ['--force' => true]);
                    $this->comment('   ✅ Seeders completed');
                }
                
            } catch (\Exception $e) {
                $this->error("   ❌ Migration failed: {$e->getMessage()}");
                
                // Try alternative approach
                $this->comment('   🔄 Trying alternative approach...');
                try {
                    Artisan::call('migrate', ['--force' => true]);
                    $this->comment('   ✅ Regular migration completed');
                } catch (\Exception $e2) {
                    $this->error("   ❌ Alternative approach also failed: {$e2->getMessage()}");
                }
            }
        }
    }

    private function verifyDatabase()
    {
        $this->info('✅ Step 5: Verifying database integrity...');
        
        try {
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            
            $this->comment("   📊 Total tables: {$tableCount}");
            
            // Test key tables
            $keyTables = ['branches', 'users', 'categories', 'units'];
            foreach ($keyTables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->comment("   ✅ {$table}: {$count} records");
                } catch (\Exception $e) {
                    $this->error("   ❌ {$table}: Error - {$e->getMessage()}");
                }
            }
            
            // Test a simple query that was failing before
            try {
                $branches = DB::table('branches')->where('is_active', 1)->get();
                $this->comment('   ✅ Branch query test: PASSED');
            } catch (\Exception $e) {
                $this->error("   ❌ Branch query test: FAILED - {$e->getMessage()}");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Verification failed: {$e->getMessage()}");
        }
    }
}

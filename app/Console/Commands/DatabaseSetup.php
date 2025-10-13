<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSetup extends Command
{
    protected $signature = 'db:setup {--fresh} {--seed} {--force}';
    protected $description = 'Setup database with migrations and optional seeding';

    public function handle()
    {
        $this->info('🚀 Setting up LapArChicken Database...');
        $this->line('');

        // Check database connection
        if (!$this->checkDatabaseConnection()) {
            return 1;
        }

        $fresh = $this->option('fresh');
        $seed = $this->option('seed');
        $force = $this->option('force');

        if ($fresh) {
            $this->freshMigration($force);
        } else {
            $this->runMigrations();
        }

        if ($seed) {
            $this->runSeeders();
        }

        $this->showCompletionInfo();
        return 0;
    }

    private function checkDatabaseConnection()
    {
        $this->info('🔍 Checking database connection...');
        
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful!');
            $this->line('');
            return true;
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->comment('💡 Please check your .env file:');
            $this->comment('   - DB_HOST=' . config('database.connections.mysql.host'));
            $this->comment('   - DB_PORT=' . config('database.connections.mysql.port'));
            $this->comment('   - DB_DATABASE=' . config('database.connections.mysql.database'));
            $this->comment('   - DB_USERNAME=' . config('database.connections.mysql.username'));
            return false;
        }
    }

    private function freshMigration($force)
    {
        if (!$force && !$this->confirm('⚠️ This will DROP ALL TABLES and recreate them. Continue?')) {
            $this->info('❌ Migration cancelled.');
            return;
        }

        $this->info('🔄 Running fresh migrations...');
        
        try {
            Artisan::call('migrate:fresh');
            $this->info('✅ Fresh migration completed!');
            $this->line(Artisan::output());
        } catch (\Exception $e) {
            $this->error('❌ Fresh migration failed: ' . $e->getMessage());
        }
    }

    private function runMigrations()
    {
        $this->info('📋 Running pending migrations...');
        
        try {
            Artisan::call('migrate');
            $output = Artisan::output();
            
            if (str_contains($output, 'Nothing to migrate')) {
                $this->comment('📭 No pending migrations found.');
            } else {
                $this->info('✅ Migrations completed!');
            }
            
            $this->line($output);
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
        }
    }

    private function runSeeders()
    {
        $this->info('🌱 Running database seeders...');
        
        try {
            Artisan::call('db:seed');
            $this->info('✅ Seeding completed!');
            $this->line(Artisan::output());
        } catch (\Exception $e) {
            $this->error('❌ Seeding failed: ' . $e->getMessage());
            $this->comment('💡 You can run seeders manually later with: php artisan db:seed');
        }
    }

    private function showCompletionInfo()
    {
        $this->line('');
        $this->info('🎉 Database setup completed!');
        $this->line('');
        
        $this->comment('📊 Database Statistics:');
        try {
            $tables = DB::select('SHOW TABLES');
            $this->comment('   Tables created: ' . count($tables));
            
            // Show some key tables
            $keyTables = ['branches', 'users', 'products', 'categories'];
            foreach ($keyTables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->comment("   {$table}: {$count} records");
                } catch (\Exception $e) {
                    // Table might not exist, skip
                }
            }
        } catch (\Exception $e) {
            $this->comment('   Could not retrieve statistics');
        }
        
        $this->line('');
        $this->comment('🛠️ Available Tools:');
        $this->comment('   php artisan db:inspect          - View database structure');
        $this->comment('   php artisan db:monitor --watch  - Monitor database');
        $this->comment('   php artisan db:query           - Interactive SQL');
        // Removed dev-only batch hint (database_tools.bat) — file no longer present
        $this->line('');
        $this->comment('🌐 Application URL: http://127.0.0.1:8000');
    }
}

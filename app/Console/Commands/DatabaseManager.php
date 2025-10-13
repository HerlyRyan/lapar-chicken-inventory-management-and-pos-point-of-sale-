<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseManager extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:manage 
                            {action : Action to perform (query|describe|tables|columns|migrate|seed|fresh)}
                            {--table= : Table name for specific operations}
                            {--sql= : Raw SQL query to execute}
                            {--class= : Seeder class name}
                            {--force : Force the operation}';

    /**
     * The console command description.
     */
    protected $description = 'Comprehensive database management tool';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'query':
                return $this->executeQuery();
            case 'describe':
                return $this->describeTable();
            case 'tables':
                return $this->listTables();
            case 'columns':
                return $this->listColumns();
            case 'migrate':
                return $this->runMigration();
            case 'seed':
                return $this->runSeeder();
            case 'fresh':
                return $this->freshDatabase();
            default:
                $this->error("Unknown action: {$action}");
                $this->showHelp();
                return 1;
        }
    }
    
    private function executeQuery()
    {
        $sql = $this->option('sql');
        
        if (!$sql) {
            $sql = $this->ask('Enter SQL query');
        }
        
        if (!$sql) {
            $this->error('No SQL query provided');
            return 1;
        }
        
        try {
            $this->info("🔍 Executing: {$sql}");
            $this->newLine();
            
            $results = DB::select($sql);
            
            if (empty($results)) {
                $this->warn('No results returned');
                return 0;
            }
            
            // Display results in table format
            $headers = array_keys((array) $results[0]);
            $rows = array_map(function($row) {
                return array_values((array) $row);
            }, $results);
            
            $this->table($headers, $rows);
            $this->info("📊 Total rows: " . count($results));
            
        } catch (\Exception $e) {
            $this->error("❌ Query failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function describeTable()
    {
        $table = $this->option('table') ?: $this->ask('Enter table name');
        
        if (!$table) {
            $this->error('No table specified');
            return 1;
        }
        
        try {
            $this->info("🗄️ Describing table: {$table}");
            $this->newLine();
            
            $columns = DB::select("DESCRIBE {$table}");
            
            $headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
            $rows = array_map(function($col) {
                return [
                    $col->Field,
                    $col->Type,
                    $col->Null,
                    $col->Key,
                    $col->Default ?? 'NULL',
                    $col->Extra
                ];
            }, $columns);
            
            $this->table($headers, $rows);
            
            // Show indexes
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            if (!empty($indexes)) {
                $this->newLine();
                $this->info("🔑 Indexes:");
                foreach ($indexes as $index) {
                    $this->line("  • {$index->Key_name} ({$index->Column_name})");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to describe table: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function listTables()
    {
        try {
            $this->info("📋 Database Tables");
            $this->newLine();
            
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $tableColumn = "Tables_in_{$dbName}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                $count = DB::table($tableName)->count();
                
                $this->line("  📊 <fg=cyan>{$tableName}</> ({$count} rows)");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to list tables: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function listColumns()
    {
        $table = $this->option('table') ?: $this->ask('Enter table name');
        
        if (!$table) {
            $this->error('No table specified');
            return 1;
        }
        
        try {
            $columns = Schema::getColumnListing($table);
            
            $this->info("📋 Columns in table: {$table}");
            $this->newLine();
            
            foreach ($columns as $column) {
                $this->line("  • {$column}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to list columns: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function runMigration()
    {
        $force = $this->option('force');
        
        if (!$force) {
            if (!$this->confirm('Are you sure you want to run migrations?')) {
                $this->info('Migration cancelled');
                return 0;
            }
        }
        
        $this->info('🔄 Running migrations...');
        $this->call('migrate');
        
        return 0;
    }
    
    private function runSeeder()
    {
        $class = $this->option('class');
        $force = $this->option('force');
        
        if (!$force) {
            if (!$this->confirm('Are you sure you want to run seeders?')) {
                $this->info('Seeding cancelled');
                return 0;
            }
        }
        
        $this->info('🌱 Running seeders...');
        
        if ($class) {
            $this->call('db:seed', ['--class' => $class]);
        } else {
            $this->call('db:seed');
        }
        
        return 0;
    }
    
    private function freshDatabase()
    {
        if (!$this->confirm('⚠️  This will DROP ALL TABLES and recreate them. Are you sure?')) {
            $this->info('Operation cancelled');
            return 0;
        }
        
        $this->warn('🗑️ Dropping all tables...');
        $this->call('migrate:fresh');
        
        if ($this->confirm('Run seeders?')) {
            $this->info('🌱 Running seeders...');
            $this->call('db:seed');
        }
        
        $this->info('✅ Database refreshed successfully');
        
        return 0;
    }
    
    private function showHelp()
    {
        $this->newLine();
        $this->info('📚 Available Actions:');
        $this->line('  <fg=cyan>query</> --sql="SELECT * FROM users"  Execute raw SQL query');
        $this->line('  <fg=cyan>describe</> --table=users              Show table structure');
        $this->line('  <fg=cyan>tables</>                             List all tables');
        $this->line('  <fg=cyan>columns</> --table=users              List table columns');
        $this->line('  <fg=cyan>migrate</> --force                    Run migrations');
        $this->line('  <fg=cyan>seed</> --class=BranchSeeder --force  Run specific seeder');
        $this->line('  <fg=cyan>fresh</> --force                      Fresh database (DANGEROUS)');
        $this->newLine();
        $this->info('💡 Examples:');
        $this->line('  php artisan db:manage query --sql="SELECT * FROM branches LIMIT 5"');
        $this->line('  php artisan db:manage describe --table=branches');
        $this->line('  php artisan db:manage tables');
    }
}

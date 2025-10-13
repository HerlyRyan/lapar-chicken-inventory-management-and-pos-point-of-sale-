<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseInspector extends Command
{
    protected $signature = 'db:inspect {table?} {--columns} {--indexes} {--foreign-keys} {--all}';
    protected $description = 'Inspect database structure and show tables, columns, indexes, and foreign keys';

    public function handle()
    {
        $table = $this->argument('table');
        
        if ($table) {
            $this->inspectTable($table);
        } else {
            $this->showAllTables();
        }
    }

    private function showAllTables()
    {
        $this->info('📊 Database Tables Overview');
        $this->line('');
        
        $tables = $this->getAllTables();
        
        $tableData = [];
        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);
            $tableData[] = [
                'Table' => $table,
                'Columns' => count($columns),
                'Primary Key' => $this->getPrimaryKey($table),
                'Created' => $this->tableExists($table) ? '✅' : '❌'
            ];
        }
        
        $this->table(['Table', 'Columns', 'Primary Key', 'Status'], $tableData);
        
        $this->line('');
        $this->comment('💡 Use: php artisan db:inspect {table_name} to inspect specific table');
        $this->comment('💡 Use: php artisan db:inspect {table_name} --all to see all details');
    }

    private function inspectTable($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            $this->error("❌ Table '{$tableName}' does not exist!");
            return;
        }

        $this->info("🔍 Inspecting Table: {$tableName}");
        $this->line('');

        // Show columns
        if ($this->option('columns') || $this->option('all') || !$this->hasAnyOption()) {
            $this->showTableColumns($tableName);
        }

        // Show indexes
        if ($this->option('indexes') || $this->option('all')) {
            $this->showTableIndexes($tableName);
        }

        // Show foreign keys
        if ($this->option('foreign-keys') || $this->option('all')) {
            $this->showTableForeignKeys($tableName);
        }
    }

    private function showTableColumns($tableName)
    {
        $this->info('📋 Columns:');
        
        $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
        $columnData = [];
        
        foreach ($columns as $column) {
            $columnData[] = [
                'Column' => $column->Field,
                'Type' => $column->Type,
                'Null' => $column->Null === 'YES' ? '✅' : '❌',
                'Key' => $column->Key ?: '-',
                'Default' => $column->Default ?: '-',
                'Extra' => $column->Extra ?: '-'
            ];
        }
        
        $this->table(['Column', 'Type', 'Nullable', 'Key', 'Default', 'Extra'], $columnData);
        $this->line('');
    }

    private function showTableIndexes($tableName)
    {
        $this->info('🔑 Indexes:');
        
        try {
            $indexes = DB::select("SHOW INDEX FROM {$tableName}");
            
            if (empty($indexes)) {
                $this->comment('No indexes found.');
                return;
            }
            
            $indexData = [];
            foreach ($indexes as $index) {
                $indexData[] = [
                    'Name' => $index->Key_name,
                    'Column' => $index->Column_name,
                    'Unique' => $index->Non_unique == 0 ? '✅' : '❌',
                    'Type' => $index->Index_type
                ];
            }
            
            $this->table(['Index Name', 'Column', 'Unique', 'Type'], $indexData);
        } catch (\Exception $e) {
            $this->error('Could not retrieve indexes: ' . $e->getMessage());
        }
        
        $this->line('');
    }

    private function showTableForeignKeys($tableName)
    {
        $this->info('🔗 Foreign Keys:');
        
        try {
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME,
                    CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = ? 
                AND TABLE_SCHEMA = DATABASE() 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$tableName]);
            
            if (empty($foreignKeys)) {
                $this->comment('No foreign keys found.');
                return;
            }
            
            $fkData = [];
            foreach ($foreignKeys as $fk) {
                $fkData[] = [
                    'Column' => $fk->COLUMN_NAME,
                    'References' => $fk->REFERENCED_TABLE_NAME . '.' . $fk->REFERENCED_COLUMN_NAME,
                    'Constraint' => $fk->CONSTRAINT_NAME
                ];
            }
            
            $this->table(['Column', 'References', 'Constraint Name'], $fkData);
        } catch (\Exception $e) {
            $this->error('Could not retrieve foreign keys: ' . $e->getMessage());
        }
        
        $this->line('');
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

    private function getPrimaryKey($tableName)
    {
        try {
            $primaryKey = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'");
            return !empty($primaryKey) ? $primaryKey[0]->Column_name : 'None';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function tableExists($tableName)
    {
        return Schema::hasTable($tableName);
    }

    private function hasAnyOption()
    {
        return $this->option('columns') || $this->option('indexes') || $this->option('foreign-keys') || $this->option('all');
    }
}

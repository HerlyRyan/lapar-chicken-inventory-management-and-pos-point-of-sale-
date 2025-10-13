<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseModifier extends Command
{
    protected $signature = 'db:modify {action} {table?} {--column=} {--type=} {--after=} {--rename-to=} {--default=} {--nullable} {--unique} {--index}';
    protected $description = 'Modify database structure directly from terminal';

    public function handle()
    {
        $action = $this->argument('action');
        $table = $this->argument('table');

        switch ($action) {
            case 'add-column':
                $this->addColumn($table);
                break;
            case 'drop-column':
                $this->dropColumn($table);
                break;
            case 'modify-column':
                $this->modifyColumn($table);
                break;
            case 'rename-column':
                $this->renameColumn($table);
                break;
            case 'add-index':
                $this->addIndex($table);
                break;
            case 'drop-index':
                $this->dropIndex($table);
                break;
            case 'create-table':
                $this->createTable($table);
                break;
            case 'drop-table':
                $this->dropTable($table);
                break;
            case 'truncate':
                $this->truncateTable($table);
                break;
            default:
                $this->showHelp();
        }
    }

    private function addColumn($table)
    {
        $column = $this->option('column');
        $type = $this->option('type');
        
        if (!$column || !$type) {
            $this->error('❌ Column name and type are required!');
            $this->info('Usage: php artisan db:modify add-column users --column=phone --type=string');
            return;
        }

        $sql = "ALTER TABLE {$table} ADD COLUMN {$column} {$type}";
        
        if ($this->option('after')) {
            $sql .= " AFTER " . $this->option('after');
        }
        
        if ($this->option('default')) {
            $sql .= " DEFAULT '" . $this->option('default') . "'";
        }
        
        if (!$this->option('nullable')) {
            $sql .= " NOT NULL";
        }

        $this->executeSQL($sql, "Added column '{$column}' to table '{$table}'");
    }

    private function dropColumn($table)
    {
        $column = $this->option('column');
        
        if (!$column) {
            $this->error('❌ Column name is required!');
            return;
        }

        if ($this->confirm("Are you sure you want to drop column '{$column}' from table '{$table}'?")) {
            $sql = "ALTER TABLE {$table} DROP COLUMN {$column}";
            $this->executeSQL($sql, "Dropped column '{$column}' from table '{$table}'");
        }
    }

    private function modifyColumn($table)
    {
        $column = $this->option('column');
        $type = $this->option('type');
        
        if (!$column || !$type) {
            $this->error('❌ Column name and type are required!');
            return;
        }

        $sql = "ALTER TABLE {$table} MODIFY COLUMN {$column} {$type}";
        
        if ($this->option('default')) {
            $sql .= " DEFAULT '" . $this->option('default') . "'";
        }
        
        if (!$this->option('nullable')) {
            $sql .= " NOT NULL";
        }

        $this->executeSQL($sql, "Modified column '{$column}' in table '{$table}'");
    }

    private function renameColumn($table)
    {
        $column = $this->option('column');
        $newName = $this->option('rename-to');
        
        if (!$column || !$newName) {
            $this->error('❌ Both column name and new name are required!');
            $this->info('Usage: php artisan db:modify rename-column users --column=old_name --rename-to=new_name');
            return;
        }

        $sql = "ALTER TABLE {$table} RENAME COLUMN {$column} TO {$newName}";
        $this->executeSQL($sql, "Renamed column '{$column}' to '{$newName}' in table '{$table}'");
    }

    private function addIndex($table)
    {
        $column = $this->option('column');
        
        if (!$column) {
            $this->error('❌ Column name is required!');
            return;
        }

        $indexType = $this->option('unique') ? 'UNIQUE INDEX' : 'INDEX';
        $indexName = "idx_{$table}_{$column}";
        
        $sql = "CREATE {$indexType} {$indexName} ON {$table} ({$column})";
        $this->executeSQL($sql, "Added index on column '{$column}' in table '{$table}'");
    }

    private function dropIndex($table)
    {
        $indexName = $this->option('index');
        
        if (!$indexName) {
            $this->error('❌ Index name is required!');
            $this->info('Usage: php artisan db:modify drop-index users --index=idx_users_email');
            return;
        }

        $sql = "DROP INDEX {$indexName} ON {$table}";
        $this->executeSQL($sql, "Dropped index '{$indexName}' from table '{$table}'");
    }

    private function createTable($table)
    {
        $this->info("🏗️ Creating basic table structure for '{$table}'...");
        
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )";

        $this->executeSQL($sql, "Created table '{$table}' with basic structure");
        $this->comment("💡 You can now add more columns using: php artisan db:modify add-column {$table} --column=name --type=varchar(255)");
    }

    private function dropTable($table)
    {
        if ($this->confirm("⚠️ Are you sure you want to DROP table '{$table}'? This action cannot be undone!")) {
            $sql = "DROP TABLE IF EXISTS {$table}";
            $this->executeSQL($sql, "Dropped table '{$table}'");
        }
    }

    private function truncateTable($table)
    {
        if ($this->confirm("⚠️ Are you sure you want to TRUNCATE table '{$table}'? All data will be deleted!")) {
            $sql = "TRUNCATE TABLE {$table}";
            $this->executeSQL($sql, "Truncated table '{$table}'");
        }
    }

    private function executeSQL($sql, $successMessage)
    {
        try {
            $this->info("🔧 Executing: {$sql}");
            DB::statement($sql);
            $this->info("✅ {$successMessage}");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }

    private function showHelp()
    {
        $this->info('🛠️ Database Modifier Commands:');
        $this->line('');
        
        $commands = [
            ['Action', 'Command', 'Example'],
            ['Add Column', 'db:modify add-column {table}', 'db:modify add-column users --column=phone --type="varchar(20)"'],
            ['Drop Column', 'db:modify drop-column {table}', 'db:modify drop-column users --column=phone'],
            ['Modify Column', 'db:modify modify-column {table}', 'db:modify modify-column users --column=phone --type="varchar(30)"'],
            ['Rename Column', 'db:modify rename-column {table}', 'db:modify rename-column users --column=phone --rename-to=mobile'],
            ['Add Index', 'db:modify add-index {table}', 'db:modify add-index users --column=email --unique'],
            ['Drop Index', 'db:modify drop-index {table}', 'db:modify drop-index users --index=idx_users_email'],
            ['Create Table', 'db:modify create-table {table}', 'db:modify create-table products'],
            ['Drop Table', 'db:modify drop-table {table}', 'db:modify drop-table old_table'],
            ['Truncate Table', 'db:modify truncate {table}', 'db:modify truncate logs'],
        ];
        
        $this->table(['Action', 'Command', 'Example'], $commands);
        
        $this->line('');
        $this->comment('💡 Options:');
        $this->comment('  --column=name     : Column name');
        $this->comment('  --type=varchar(255): Column type');
        $this->comment('  --after=column    : Add column after specific column');
        $this->comment('  --rename-to=name  : New name for rename operations');
        $this->comment('  --default=value   : Default value');
        $this->comment('  --nullable        : Allow NULL values');
        $this->comment('  --unique          : Create unique index');
        $this->comment('  --index=name      : Index name for drop operations');
    }
}

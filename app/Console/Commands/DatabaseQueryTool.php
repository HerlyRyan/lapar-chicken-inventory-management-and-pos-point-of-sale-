<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseQueryTool extends Command
{
    protected $signature = 'db:query {sql?} {--file=} {--export=} {--limit=100}';
    protected $description = 'Execute SQL queries directly from terminal with results formatting';

    public function handle()
    {
        $sql = $this->argument('sql');
        $file = $this->option('file');
        
        if ($file) {
            $this->executeFromFile($file);
        } elseif ($sql) {
            $this->executeSQLQuery($sql);
        } else {
            $this->interactiveMode();
        }
    }

    private function executeFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return;
        }

        $sql = file_get_contents($filePath);
        $this->info("📁 Executing SQL from file: {$filePath}");
        $this->line('');
        
        $this->executeSQLQuery($sql);
    }

    private function executeSQLQuery($sql)
    {
        $sql = trim($sql);
        
        if (empty($sql)) {
            $this->error('❌ Empty SQL query provided!');
            return;
        }

        $this->info("🔍 Executing: " . (strlen($sql) > 100 ? substr($sql, 0, 100) . '...' : $sql));
        $this->line('');

        try {
            $startTime = microtime(true);
            
            // Determine query type
            $queryType = $this->getQueryType($sql);
            
            if ($queryType === 'SELECT') {
                $this->executeSelectQuery($sql);
            } else {
                $this->executeNonSelectQuery($sql);
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            $this->line('');
            $this->comment("⏱️ Execution time: {$executionTime}ms");
            
        } catch (\Exception $e) {
            $this->error("❌ SQL Error: " . $e->getMessage());
        }
    }

    private function executeSelectQuery($sql)
    {
        $limit = (int) $this->option('limit');
        
        // Add LIMIT if not present in SELECT queries
        if (stripos($sql, 'LIMIT') === false && $limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = DB::select($sql);
        
        if (empty($results)) {
            $this->comment('📭 No results found.');
            return;
        }
        
        $this->displayResults($results);
        
        // Export option
        if ($this->option('export')) {
            $this->exportResults($results);
        }
    }

    private function executeNonSelectQuery($sql)
    {
        $result = DB::statement($sql);
        
        if ($result) {
            $affectedRows = DB::affectedRows();
            $this->info("✅ Query executed successfully!");
            
            if ($affectedRows > 0) {
                $this->comment("📊 Affected rows: {$affectedRows}");
            }
        } else {
            $this->error("❌ Query failed to execute.");
        }
    }

    private function displayResults($results)
    {
        $firstRow = (array) $results[0];
        $headers = array_keys($firstRow);
        
        $tableData = [];
        foreach ($results as $row) {
            $rowData = [];
            foreach ((array) $row as $value) {
                // Truncate long values for better display
                $displayValue = $value;
                if (is_string($value) && strlen($value) > 50) {
                    $displayValue = substr($value, 0, 47) . '...';
                }
                $rowData[] = $displayValue;
            }
            $tableData[] = $rowData;
        }
        
        $this->table($headers, $tableData);
        $this->comment("📊 Total records: " . count($results));
        
        if (count($results) >= $this->option('limit')) {
            $this->comment("⚠️ Results limited to " . $this->option('limit') . " rows. Use --limit option to change.");
        }
    }

    private function exportResults($results)
    {
        $exportFile = $this->option('export');
        $extension = pathinfo($exportFile, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'json':
                $this->exportToJson($results, $exportFile);
                break;
            case 'csv':
                $this->exportToCsv($results, $exportFile);
                break;
            default:
                $this->error("❌ Unsupported export format. Use .json or .csv");
        }
    }

    private function exportToJson($results, $file)
    {
        $json = json_encode($results, JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        $this->info("💾 Results exported to: {$file}");
    }

    private function exportToCsv($results, $file)
    {
        $handle = fopen($file, 'w');
        
        if (!empty($results)) {
            // Write headers
            $headers = array_keys((array) $results[0]);
            fputcsv($handle, $headers);
            
            // Write data
            foreach ($results as $row) {
                fputcsv($handle, (array) $row);
            }
        }
        
        fclose($handle);
        $this->info("💾 Results exported to: {$file}");
    }

    private function interactiveMode()
    {
        $this->info('🎯 Interactive SQL Query Mode');
        $this->comment('Type SQL queries and press Enter. Type "exit" to quit.');
        $this->line('');
        
        while (true) {
            $sql = $this->ask('SQL> ');
            
            if (strtolower(trim($sql)) === 'exit') {
                $this->info('👋 Goodbye!');
                break;
            }
            
            if (!empty(trim($sql))) {
                $this->line('');
                $this->executeSQLQuery($sql);
                $this->line('');
            }
        }
    }

    private function getQueryType($sql)
    {
        $sql = trim(strtoupper($sql));
        
        if (str_starts_with($sql, 'SELECT')) return 'SELECT';
        if (str_starts_with($sql, 'INSERT')) return 'INSERT';
        if (str_starts_with($sql, 'UPDATE')) return 'UPDATE';
        if (str_starts_with($sql, 'DELETE')) return 'DELETE';
        if (str_starts_with($sql, 'CREATE')) return 'CREATE';
        if (str_starts_with($sql, 'ALTER')) return 'ALTER';
        if (str_starts_with($sql, 'DROP')) return 'DROP';
        
        return 'OTHER';
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorSqlQueries extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitor:sql 
                            {--tail : Monitor SQL queries in real-time}
                            {--recent : Show recent queries from log}
                            {--lines=20 : Number of recent lines to show}
                            {--clear : Clear the SQL log file}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor SQL queries in real-time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            return $this->clearLogFile();
        }
        
        if ($this->option('recent')) {
            return $this->showRecentQueries();
        }
        
        if ($this->option('tail')) {
            $this->info('🔍 Monitoring SQL queries in real-time...');
            $this->info('Press Ctrl+C to stop monitoring');
            $this->newLine();
            
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                $this->error('Log file not found: ' . $logFile);
                return 1;
            }
            
            // Follow log file
            return $this->tailLogFile($logFile);
        }
        
        // Default behavior - show recent queries
        return $this->showRecentQueries();
    }
    
    private function tailLogFile($logFile)
    {
        $handle = fopen($logFile, 'r');
        fseek($handle, 0, SEEK_END);
        
        while (true) {
            $line = fgets($handle);
            
            if ($line === false) {
                usleep(100000); // Sleep for 100ms
                continue;
            }
            
            // Filter SQL query logs
            if (strpos($line, 'SQL Query') !== false) {
                $this->formatAndDisplayQuery($line);
            }
        }
        
        fclose($handle);
    }
    
    private function formatAndDisplayQuery($logLine)
    {
        // Parse log line to extract SQL info
        if (preg_match('/\[(.*?)\].*?"sql":"(.*?)".*?"time":"(.*?)".*?"url":"(.*?)"/', $logLine, $matches)) {
            $timestamp = $matches[1];
            $sql = str_replace('\\"', '"', $matches[2]);
            $time = $matches[3];
            $url = $matches[4];
            
            $this->line("<fg=cyan>[{$timestamp}]</>");
            $this->line("<fg=yellow>🕒 Time:</> {$time}");
            $this->line("<fg=blue>🌐 URL:</> {$url}");
            $this->line("<fg=green>🗄️ SQL:</> {$sql}");
            $this->newLine();
        }
    }
    
    private function showRecentQueries()
    {
        $this->info('📊 Recent SQL Queries Analysis');
        $this->newLine();
        
        // Show database connection info
        $connection = DB::connection();
        $this->line("<fg=cyan>Database:</> " . $connection->getDatabaseName());
        $this->line("<fg=cyan>Driver:</> " . $connection->getDriverName());
        $this->newLine();
        
        // Show tables
        $this->info('📋 Available Tables:');
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . $connection->getDatabaseName();
        
        foreach ($tables as $table) {
            $this->line("  • " . $table->$tableColumn);
        }
        
        $this->newLine();
        $this->info('💡 Use --tail option to monitor queries in real-time');
        $this->line('   php artisan monitor:sql --tail');
    }
    
    private function clearLogFile()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            $this->warn('Log file does not exist: ' . $logFile);
            return 0;
        }
        
        if ($this->confirm('Are you sure you want to clear the SQL log file?')) {
            file_put_contents($logFile, '');
            $this->info('✅ SQL log file cleared successfully');
        } else {
            $this->info('Operation cancelled');
        }
        
        return 0;
    }
}

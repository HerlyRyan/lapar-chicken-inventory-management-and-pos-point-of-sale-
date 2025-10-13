<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseMonitor extends Command
{
    protected $signature = 'db:monitor {--watch} {--interval=5} {--connections} {--size} {--queries}';
    protected $description = 'Monitor database connections, size, and running queries in real-time';

    public function handle()
    {
        if ($this->option('watch')) {
            $this->watchDatabase();
        } else {
            $this->showDatabaseStatus();
        }
    }

    private function watchDatabase()
    {
        $interval = (int) $this->option('interval');
        $this->info("🔄 Monitoring database every {$interval} seconds. Press Ctrl+C to stop.");
        $this->line('');

        while (true) {
            // Clear screen for Windows
            system('cls');
            
            $this->info('📊 Database Monitor - ' . now()->format('Y-m-d H:i:s'));
            $this->line('='.str_repeat('=', 60));
            
            $this->showDatabaseStatus();
            
            sleep($interval);
        }
    }

    private function showDatabaseStatus()
    {
        if ($this->option('connections') || !$this->hasSpecificOption()) {
            $this->showConnections();
        }

        if ($this->option('size') || !$this->hasSpecificOption()) {
            $this->showDatabaseSize();
        }

        if ($this->option('queries') || !$this->hasSpecificOption()) {
            $this->showRunningQueries();
        }
    }

    private function showConnections()
    {
        $this->info('🔌 Database Connections:');
        
        try {
            $status = DB::select('SHOW STATUS WHERE Variable_name IN ("Threads_connected", "Threads_running", "Max_used_connections", "Connections")');
            
            $connectionData = [];
            foreach ($status as $stat) {
                $connectionData[] = [
                    'Metric' => $this->formatStatusName($stat->Variable_name),
                    'Value' => number_format($stat->Value)
                ];
            }
            
            $this->table(['Metric', 'Value'], $connectionData);
        } catch (\Exception $e) {
            $this->error('Could not retrieve connection info: ' . $e->getMessage());
        }
        
        $this->line('');
    }

    private function showDatabaseSize()
    {
        $this->info('💾 Database Size Information:');
        
        try {
            $databaseName = DB::getDatabaseName();
            $tables = DB::select("
                SELECT 
                    table_name as 'Table',
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'Size_MB',
                    table_rows as 'Rows'
                FROM information_schema.TABLES 
                WHERE table_schema = ?
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ", [$databaseName]);
            
            $tableData = [];
            $totalSize = 0;
            
            foreach ($tables as $table) {
                $tableData[] = [
                    'Table' => $table->Table,
                    'Size (MB)' => $table->Size_MB,
                    'Rows' => number_format($table->Rows)
                ];
                $totalSize += $table->Size_MB;
            }
            
            $this->table(['Table', 'Size (MB)', 'Rows'], $tableData);
            $this->comment("Total size of top 10 tables: {$totalSize} MB");
        } catch (\Exception $e) {
            $this->error('Could not retrieve size info: ' . $e->getMessage());
        }
        
        $this->line('');
    }

    private function showRunningQueries()
    {
        $this->info('⚡ Running Queries:');
        
        try {
            $processes = DB::select('SHOW PROCESSLIST');
            
            if (empty($processes)) {
                $this->comment('No active queries found.');
                return;
            }
            
            $queryData = [];
            foreach ($processes as $process) {
                if ($process->Command !== 'Sleep' && $process->Info) {
                    $queryData[] = [
                        'ID' => $process->Id,
                        'User' => $process->User,
                        'Database' => $process->db ?: '-',
                        'Time' => $process->Time . 's',
                        'State' => $process->State ?: '-',
                        'Query' => strlen($process->Info) > 50 ? substr($process->Info, 0, 50) . '...' : $process->Info
                    ];
                }
            }
            
            if (empty($queryData)) {
                $this->comment('No active queries found.');
            } else {
                $this->table(['ID', 'User', 'Database', 'Time', 'State', 'Query'], $queryData);
            }
        } catch (\Exception $e) {
            $this->error('Could not retrieve query info: ' . $e->getMessage());
        }
        
        $this->line('');
    }

    private function formatStatusName($name)
    {
        $formatted = str_replace('_', ' ', $name);
        return ucwords(strtolower($formatted));
    }

    private function hasSpecificOption()
    {
        return $this->option('connections') || $this->option('size') || $this->option('queries');
    }
}

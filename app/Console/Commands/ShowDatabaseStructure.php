<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShowDatabaseStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-database-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menampilkan struktur database secara detail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa struktur database...');
        
        // Dapatkan semua tabel
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::connection()->getDatabaseName();
        $tableKey = "Tables_in_" . $dbName;
        
        if (empty($tables)) {
            $this->error('Tidak ada tabel di database!');
            return;
        }
        
        $this->info("\nDaftar tabel di database '$dbName':");
        $this->info('=====================================================');
        
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $this->info("\nTabel: $tableName");
            $this->info('-----------------------------------------------------');
            
            // Dapatkan struktur kolom tabel
            $columns = DB::select("DESCRIBE $tableName");
            $headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
            $rows = [];
            
            foreach ($columns as $column) {
                $rows[] = [
                    $column->Field,
                    $column->Type,
                    $column->Null,
                    $column->Key,
                    $column->Default ?? 'NULL',
                    $column->Extra
                ];
            }
            
            $this->table($headers, $rows);
            
            // Hitung jumlah record di tabel
            $count = DB::table($tableName)->count();
            $this->info("Total records: $count");
            
            // Dapatkan informasi indeks tabel
            $indices = DB::select("SHOW INDEXES FROM $tableName");
            if (!empty($indices)) {
                $this->info("\nIndeks tabel:");
                $indexHeaders = ['Key_name', 'Column_name', 'Non_unique', 'Type'];
                $indexRows = [];
                
                foreach ($indices as $index) {
                    $indexRows[] = [
                        $index->Key_name,
                        $index->Column_name,
                        $index->Non_unique,
                        $index->Index_type
                    ];
                }
                
                $this->table($indexHeaders, $indexRows);
            }
            
            // Tampilkan beberapa data sampel jika ada
            if ($count > 0) {
                $samples = DB::table($tableName)->limit(3)->get();
                $this->info("\nSampel data (max 3 records):");
                foreach ($samples as $sample) {
                    $this->info(json_encode($sample, JSON_PRETTY_PRINT));
                }
            }
        }
    }
}

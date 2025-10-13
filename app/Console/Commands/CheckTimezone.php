<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckTimezone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa zona waktu aplikasi saat ini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Informasi Zona Waktu Aplikasi:');
        $this->info('--------------------------------');
        $this->info('Timezone yang dikonfigurasi: ' . config('app.timezone'));
        $this->info('Timezone saat ini: ' . date_default_timezone_get());
        $this->info('Waktu saat ini: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('Waktu dalam UTC: ' . Carbon::now()->setTimezone('UTC')->format('Y-m-d H:i:s'));
        $this->info('Offset dari UTC: ' . Carbon::now()->offsetHours . ' jam');
    }
}

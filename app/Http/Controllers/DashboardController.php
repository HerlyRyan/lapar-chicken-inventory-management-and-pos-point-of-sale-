<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Grafik Tahunan
     * Isi: total penjualan per bulan (Jan–Des)
     */
    public function yearly(Request $request)
    {
        $year = $request->year ?? now()->year;

        $raw = Sale::selectRaw('MONTH(created_at) as month, SUM(final_amount) as total')
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // UX: pastikan Jan–Des selalu ada (walau 0)
        $labels = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        $values = collect(range(1, 12))->map(
            fn($m) =>
            (int) ($raw->firstWhere('month', $m)->total ?? 0)
        );

        return response()->json(compact('labels', 'values'));
    }

    /**
     * Grafik Bulanan
     * Isi: total penjualan per hari (Tanggal 1 sampai Akhir Bulan)
     */
    public function monthly(Request $request)
    {
        // Gunakan tahun dan bulan saat ini jika tidak ada input
        $year  = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        // 1. Ambil data dari database
        $query = Sale::selectRaw('DAY(created_at) as day, SUM(final_amount) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed');

        // Filter range tanggal jika diisi (opsional)
        if ($request->filled(['start', 'end'])) {
            $query->whereBetween('created_at', [
                $request->start . ' 00:00:00',
                $request->end   . ' 23:59:59'
            ]);
        }

        $raw = $query->groupBy('day')->get();

        // 2. Tentukan jumlah hari dalam bulan terpilih
        // Kita gunakan Carbon agar lebih mudah menghitung akhir bulan
        $dateContext = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $dateContext->daysInMonth;

        $labels = [];
        $values = [];

        // 3. Looping dari tanggal 1 sampai akhir bulan untuk memastikan data lengkap
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = $d; // Label cukup angka tanggalnya saja agar tidak penuh di grafik

            // Cari data yang harinya cocok, jika tidak ada set ke 0
            $dayData = $raw->firstWhere('day', $d);
            $values[] = $dayData ? (int) $dayData->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'month_name' => $dateContext->translatedFormat('F'), // Opsional: untuk debug nama bulan
        ]);
    }
}

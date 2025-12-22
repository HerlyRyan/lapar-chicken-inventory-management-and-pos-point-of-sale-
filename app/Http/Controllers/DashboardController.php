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
     * Isi: total penjualan per hari
     */
    public function monthly(Request $request)
    {
        $year  = $request->year;
        $month = $request->month;

        $query = Sale::selectRaw('DATE(created_at) as date, SUM(final_amount) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed');

        // Optional: filter range tanggal
        if ($request->filled(['start', 'end'])) {
            $query->whereBetween('created_at', [
                $request->start . ' 00:00:00',
                $request->end   . ' 23:59:59'
            ]);
        }

        $data = $query
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'values' => $data->pluck('total')->map(fn($v) => (int) $v),
        ]);
    }
}

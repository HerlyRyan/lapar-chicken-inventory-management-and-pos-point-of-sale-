<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Download PDF receipt for a sale
     */
    public function downloadPdf(Request $request, $id)
    {
        $user = auth()->user();
        $query = Sale::with(['items', 'branch', 'user'])
            ->where('id', $id);

        // Security: limit to same branch if user has a branch context
        if ($user && !empty($user->branch_id)) {
            $query->where('branch_id', $user->branch_id);
        }

        $sale = $query->firstOrFail();
        
        // Calculate dynamic paper height based on number of items
        $itemsCount = $sale->items->count();
        $ptPerMm = 72 / 25.4; // points per mm
        $widthMm = 80; // thermal paper width
        $baseMm = 110; // header + totals + footer baseline height
        $perItemMm = 12; // estimated per-item vertical space
        $minHeightMm = 150; // minimum page height
        $maxHeightMm = 800; // cap to avoid extreme lengths

        $heightMm = max($minHeightMm, min($baseMm + ($itemsCount * $perItemMm), $maxHeightMm));
        $widthPt = $widthMm * $ptPerMm;   // ~226.77pt
        $heightPt = $heightMm * $ptPerMm; // dynamic height in points

        // Generate PDF with dynamic paper size
        $pdf = Pdf::loadView('sales.receipt_pdf', compact('sale'))
            ->setPaper([0, 0, $widthPt, $heightPt], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);
        
        $filename = 'receipt-' . $sale->sale_number . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}

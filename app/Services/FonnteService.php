<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;

class FonnteService
{
    private $apiUrl = 'https://api.fonnte.com/send';
    private $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send receipt message via WhatsApp
     */
    public function sendReceipt(Sale $sale, string $whatsappNumber)
    {
        try {
            // Format receipt message
            $message = $this->formatReceiptMessage($sale);
            
            // Send via Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $whatsappNumber,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] == true) {
                Log::info('WhatsApp receipt sent successfully', [
                    'sale_id' => $sale->id,
                    'whatsapp_number' => $whatsappNumber
                ]);

                return [
                    'success' => true,
                    'message' => 'Receipt sent successfully'
                ];
            } else {
                Log::error('Failed to send WhatsApp receipt', [
                    'sale_id' => $sale->id,
                    'whatsapp_number' => $whatsappNumber,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['reason'] ?? 'Unknown error occurred'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp receipt', [
                'sale_id' => $sale->id,
                'whatsapp_number' => $whatsappNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format sale data into receipt message
     */
    private function formatReceiptMessage(Sale $sale)
    {
        $message = "🧾 *STRUK PEMBELIAN*\n";
        $message .= "═══════════════════════\n\n";
        
        // Store info
        $message .= "🏪 *{$sale->branch->name}*\n";
        $message .= "📅 {$sale->formatted_date}\n";
        $message .= "🔢 No: {$sale->sale_number}\n";
        $message .= "👤 Kasir: {$sale->user->name}\n\n";
        
        // Customer info (if available)
        if ($sale->customer_name) {
            $message .= "🙋‍♀️ Pelanggan: {$sale->customer_name}\n\n";
        }
        
        $message .= "*DETAIL PEMBELIAN:*\n";
        $message .= "────────────────────────\n";
        
        // Items
        foreach ($sale->saleItems as $item) {
            $product = $item->getProduct();
            $productName = $product ? $product->name : 'Unknown Product';
            
            $message .= "• {$productName}\n";
            $message .= "  {$item->quantity} x {$item->formatted_unit_price}\n";
            $message .= "  = {$item->formatted_total_price}\n\n";
        }
        
        $message .= "────────────────────────\n";
        
        // Totals
        $message .= "💰 *Subtotal: {$sale->formatted_total_amount}*\n";
        
        if ($sale->discount_amount > 0) {
            $message .= "🎯 Diskon: -{$sale->formatted_discount_amount}\n";
        }
        
        $message .= "💳 *TOTAL: {$sale->formatted_final_amount}*\n";
        $message .= "💵 Bayar: {$sale->payment_method_label}\n\n";
        
        // Notes (if available)
        if ($sale->notes) {
            $message .= "📝 Catatan: {$sale->notes}\n\n";
        }
        
        $message .= "═══════════════════════\n";
        $message .= "Terima kasih telah berbelanja! 🙏\n";
        $message .= "Semoga hari Anda menyenangkan! 😊";
        
        return $message;
    }
}

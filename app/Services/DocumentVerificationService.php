<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class DocumentVerificationService
{
    /**
     * Generate QR code for document verification
     */
    public function generateVerificationQR($documentId, $documentType, $verificationData = [])
    {
        $verificationUrl = config('app.url') . '/verify-document/' . $documentId;
        
        $qrData = [
            'document_id' => $documentId,
            'document_type' => $documentType,
            'generated_at' => now()->toISOString(),
            'verification_url' => $verificationUrl,
            'hash' => $this->generateDocumentHash($documentId, $documentType, $verificationData)
        ];

        // Generate QR code as SVG
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->generate(json_encode($qrData));

        return $qrCode;
    }

    /**
     * Generate QR code as base64 for PDF embedding
     */
    public function generateVerificationQRBase64($documentId, $documentType, $verificationData = [])
    {
        $verificationUrl = config('app.url') . '/verify-document/' . $documentId;
        
        $qrData = [
            'document_id' => $documentId,
            'document_type' => $documentType,
            'generated_at' => now()->toISOString(),
            'verification_url' => $verificationUrl,
            'hash' => $this->generateDocumentHash($documentId, $documentType, $verificationData)
        ];

        // Generate QR code as PNG and convert to base64
        $qrCode = QrCode::format('png')
            ->size(150)
            ->margin(1)
            ->generate(json_encode($qrData));

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    /**
     * Generate secure hash for document verification
     */
    private function generateDocumentHash($documentId, $documentType, $verificationData = [])
    {
        $hashData = [
            'id' => $documentId,
            'type' => $documentType,
            'timestamp' => now()->toISOString(),
            'app_key' => config('app.key'),
            'data' => $verificationData
        ];

        return hash('sha256', json_encode($hashData));
    }

    /**
     * Verify document authenticity
     */
    public function verifyDocument($documentId, $providedHash, $documentType, $verificationData = [])
    {
        $expectedHash = $this->generateDocumentHash($documentId, $documentType, $verificationData);
        
        return hash_equals($expectedHash, $providedHash);
    }

    /**
     * Generate digital signature for verified documents
     */
    public function generateDigitalSignature($documentId, $verifiedBy, $verificationDate)
    {
        $signatureData = [
            'document_id' => $documentId,
            'verified_by' => $verifiedBy,
            'verified_at' => $verificationDate,
            'system' => 'Lapar Chicken InvetPOS',
            'version' => '1.0'
        ];

        $signature = base64_encode(json_encode($signatureData));
        
        return [
            'signature' => $signature,
            'qr_code' => $this->generateVerificationQRBase64($documentId . '_verified', 'verified_document', $signatureData)
        ];
    }

    /**
     * Create barcode for quick document identification
     */
    public function generateDocumentBarcode($documentId)
    {
        // Simple barcode using Code 128
        $barcodeData = strtoupper(str_pad($documentId, 12, '0', STR_PAD_LEFT));
        
        return [
            'data' => $barcodeData,
            'display_text' => chunk_split($barcodeData, 4, '-')
        ];
    }

    /**
     * Validate verification timestamp
     */
    public function isVerificationValid($verificationTimestamp, $maxAge = 365)
    {
        $verificationDate = \Carbon\Carbon::parse($verificationTimestamp);
        $maxValidDate = now()->subDays($maxAge);
        
        return $verificationDate->isAfter($maxValidDate);
    }

    /**
     * Generate comprehensive verification report
     */
    public function generateVerificationReport($documentId, $documentType, $verificationData = [])
    {
        $verification = [
            'document_id' => $documentId,
            'document_type' => $documentType,
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'qr_code' => $this->generateVerificationQRBase64($documentId, $documentType, $verificationData),
            'barcode' => $this->generateDocumentBarcode($documentId),
            'verification_hash' => $this->generateDocumentHash($documentId, $documentType, $verificationData),
            'security_level' => 'HIGH',
            'validity_period' => '365 days',
            'issuing_system' => 'Lapar Chicken InvetPOS v1.0'
        ];

        return $verification;
    }
}

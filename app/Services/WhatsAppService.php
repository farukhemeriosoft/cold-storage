<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendMessage($to, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('WhatsApp message failed', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send a document (PDF) via WhatsApp
     */
    public function sendDocument($to, $documentPath, $filename, $caption = '')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])->attach('file', file_get_contents($documentPath), $filename)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'document',
                'document' => [
                    'filename' => $filename,
                    'caption' => $caption
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp document sent successfully', [
                    'to' => $to,
                    'filename' => $filename,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('WhatsApp document failed', [
                    'to' => $to,
                    'filename' => $filename,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp document service error', [
                'to' => $to,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment confirmation with invoice details
     */
    public function sendPaymentConfirmation($customer, $invoice, $batch, $pdfPath)
    {
        $message = $this->buildPaymentConfirmationMessage($customer, $invoice, $batch);
        $filename = "Invoice_{$invoice->invoice_number}.pdf";

        // Use customer phone or fallback to test number
        $phoneNumber = $customer->phone ?: '+923453561168';

        // Send text message first
        $textSent = $this->sendMessage($phoneNumber, $message);

        // Send PDF document
        $documentSent = $this->sendDocument($phoneNumber, $pdfPath, $filename, "Invoice #{$invoice->invoice_number}");

        return $textSent && $documentSent;
    }

    /**
     * Build payment confirmation message
     */
    protected function buildPaymentConfirmationMessage($customer, $invoice, $batch)
    {
        $paymentDate = now()->format('d/m/Y');
        $basketCount = $batch->total_baskets ?? 0;
        $amount = number_format($invoice->total_amount, 2);

        return "🎉 *Payment Confirmation*\n\n" .
               "Dear {$customer->full_name},\n\n" .
               "Your payment has been successfully processed!\n\n" .
               "📋 *Invoice Details:*\n" .
               "• Invoice #: {$invoice->invoice_number}\n" .
               "• Lot ID: {$batch->id}\n" .
               "• Number of Baskets: {$basketCount}\n" .
               "• Amount Paid: PKR {$amount}\n" .
               "• Payment Date: {$paymentDate}\n\n" .
               "📄 Please find your invoice attached.\n\n" .
               "Thank you for your business!\n" .
               "Cold Storage Management System";
    }

    /**
     * Format phone number for WhatsApp API
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if not present (assuming Pakistan +92)
        if (!str_starts_with($phone, '92')) {
            if (str_starts_with($phone, '0')) {
                $phone = '92' . substr($phone, 1);
            } else {
                $phone = '92' . $phone;
            }
        }

        return $phone;
    }
}

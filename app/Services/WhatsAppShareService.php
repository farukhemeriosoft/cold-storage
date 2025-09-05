<?php

namespace App\Services;

use App\Models\Invoice;

class WhatsAppShareService
{
    /**
     * Generate WhatsApp share URL for invoice
     */
    public function generateShareUrl(Invoice $invoice, $phoneNumber = '+923453561168')
    {
        $message = $this->buildShareMessage($invoice);
        $encodedMessage = urlencode($message);

        return "https://wa.me/" . $this->formatPhoneNumber($phoneNumber) . "?text=" . $encodedMessage;
    }

    /**
     * Build the message content for WhatsApp sharing
     */
    protected function buildShareMessage(Invoice $invoice)
    {
        $invoice->load(['customer', 'batch']);

        $customer = $invoice->customer;
        $batch = $invoice->batch;

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
               "📄 Invoice PDF will be sent separately.\n\n" .
               "Thank you for your business!\n" .
               "Cold Storage Management System";
    }

    /**
     * Generate WhatsApp share URL for payment confirmation
     */
    public function generatePaymentConfirmationUrl(Invoice $invoice, $phoneNumber = '+923453561168')
    {
        $message = $this->buildPaymentConfirmationMessage($invoice);
        $encodedMessage = urlencode($message);

        return "https://wa.me/" . $this->formatPhoneNumber($phoneNumber) . "?text=" . $encodedMessage;
    }

    /**
     * Build payment confirmation message
     */
    protected function buildPaymentConfirmationMessage(Invoice $invoice)
    {
        $invoice->load(['customer', 'batch']);

        $customer = $invoice->customer;
        $batch = $invoice->batch;

        $paymentDate = now()->format('d/m/Y');
        $basketCount = $batch->total_baskets ?? 0;
        $amount = number_format($invoice->total_amount, 2);

        return "✅ *Payment Received*\n\n" .
               "Dear {$customer->full_name},\n\n" .
               "We have received your payment for the following invoice:\n\n" .
               "📋 *Invoice Details:*\n" .
               "• Invoice #: {$invoice->invoice_number}\n" .
               "• Lot ID: {$batch->id}\n" .
               "• Number of Baskets: {$basketCount}\n" .
               "• Amount Paid: PKR {$amount}\n" .
               "• Payment Date: {$paymentDate}\n\n" .
               "Your lot is now ready for dispatch!\n\n" .
               "Thank you for your business!\n" .
               "Cold Storage Management System";
    }

    /**
     * Format phone number for WhatsApp URL
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

    /**
     * Generate WhatsApp share URL for general message
     */
    public function generateGeneralMessageUrl($message, $phoneNumber = '+923453561168')
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/" . $this->formatPhoneNumber($phoneNumber) . "?text=" . $encodedMessage;
    }
}

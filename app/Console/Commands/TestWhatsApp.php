<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Services\InvoicePdfService;
use App\Models\Invoice;

class TestWhatsApp extends Command
{
    protected $signature = 'whatsapp:test {phone=+923453561168}';
    protected $description = 'Test WhatsApp messaging functionality';

    public function handle()
    {
        $phone = $this->argument('phone');

        $this->info("Testing WhatsApp messaging to: {$phone}");

        // Test basic message
        $whatsappService = new WhatsAppService();
        $message = "🧪 *Test Message*\n\nThis is a test message from Cold Storage Management System.\n\nTime: " . now()->format('d/m/Y H:i:s');

        $result = $whatsappService->sendMessage($phone, $message);

        if ($result) {
            $this->info("✅ WhatsApp message sent successfully!");
        } else {
            $this->error("❌ Failed to send WhatsApp message. Check logs for details.");
        }

        // Test with PDF if invoice exists
        $invoice = Invoice::with(['customer', 'batch'])->first();
        if ($invoice) {
            $this->info("Testing PDF generation and WhatsApp document sending...");

            try {
                $pdfService = new InvoicePdfService();
                $pdfPath = $pdfService->generateForWhatsApp($invoice);

                $result = $whatsappService->sendDocument(
                    $phone,
                    $pdfPath,
                    "Test_Invoice.pdf",
                    "Test invoice PDF from Cold Storage Management System"
                );

                if ($result) {
                    $this->info("✅ WhatsApp document sent successfully!");
                } else {
                    $this->error("❌ Failed to send WhatsApp document. Check logs for details.");
                }

                // Clean up
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
            } catch (\Exception $e) {
                $this->error("❌ PDF generation failed: " . $e->getMessage());
            }
        } else {
            $this->warn("No invoices found for PDF testing.");
        }
    }
}

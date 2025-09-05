<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'batch', 'items', 'payments']);

        $data = [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'batch' => $invoice->batch,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('invoices.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate and save PDF to storage
     */
    public function generateAndSave(Invoice $invoice)
    {
        $pdf = $this->generatePdf($invoice);
        $filename = "invoice_{$invoice->invoice_number}_{$invoice->id}.pdf";
        $path = "invoices/{$filename}";

        // Save to storage
        Storage::disk('public')->put($path, $pdf->output());

        return [
            'path' => $path,
            'full_path' => Storage::disk('public')->path($path),
            'filename' => $filename
        ];
    }

    /**
     * Generate PDF and return file path for WhatsApp
     */
    public function generateForWhatsApp(Invoice $invoice)
    {
        $pdf = $this->generatePdf($invoice);
        $filename = "Invoice_{$invoice->invoice_number}.pdf";
        $tempPath = storage_path("app/temp/{$filename}");

        // Ensure temp directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // Save to temp location
        file_put_contents($tempPath, $pdf->output());

        return $tempPath;
    }
}

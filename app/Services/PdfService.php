<?php

namespace AppServices;

use AppModelsInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Renderiza el comprobante en formato PDF optimizado para impresión A4.
     */
    public function renderInvoicePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->loadMissing([
            'company',
            'branch',
            'customer.addresses',
            'items.variant',
            'payments'
        ]);

        return Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Helvetica',
            ]);
    }
}

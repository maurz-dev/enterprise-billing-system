<?php

namespace AppHttpControllers;

use AppHttpRequestsIssueInvoiceRequest;
use AppHttpRequestsStoreInvoiceRequest;
use AppHttpRequestsVoidInvoiceRequest;
use AppHttpResourcesInvoiceResource;
use AppModelsInvoice;
use AppServicesInvoiceService;
use AppServicesPdfService;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;
use IlluminateHttpResourcesJsonAnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = Invoice::with(['customer', 'items.variant'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->contact_id, fn ($q) => $q->where('contact_id', $request->contact_id))
            ->orderByDesc('id')
            ->paginate($request->get('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()?->id ?? 1;

        $items = $validated['items'];
        unset($validated['items']);

        $invoice = $this->invoiceService->createDraft($validated, $items);

        return response()->json([
            'message' => 'Comprobante creado exitosamente en modo borrador.',
            'data' => new InvoiceResource($invoice->load(['customer', 'items.variant'])),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with(['items.variant', 'customer.addresses', 'branch', 'payments'])
            ->findOrFail($id);

        return response()->json([
            'data' => new InvoiceResource($invoice),
        ]);
    }

    public function issue(IssueInvoiceRequest $request, int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $issued = $this->invoiceService->issue($invoice, $request->validated('series'));

        return response()->json([
            'message' => "Comprobante emitido correctamente con correlativo {$issued->full_correlative}.",
            'data' => new InvoiceResource($issued->load(['customer', 'items.variant'])),
        ]);
    }

    public function void(VoidInvoiceRequest $request, int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $voided = $this->invoiceService->void($invoice, $request->validated('reason'));

        return response()->json([
            'message' => 'Comprobante anulado y stock restituido en Kardex.',
            'data' => new InvoiceResource($voided),
        ]);
    }

    public function generatePdf(int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = $this->pdfService->renderInvoicePdf($invoice);

        $filename = ($invoice->full_correlative ?? "borrador-{$invoice->id}") . '.pdf';

        return $pdf->stream($filename);
    }
}

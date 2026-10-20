<?php

namespace AppHttpControllers;

use AppModelsInvoice;
use AppServicesInvoiceService;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::with(['customer', 'items'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->contact_id, fn ($q) => $q->where('contact_id', $request->contact_id))
            ->orderByDesc('id')
            ->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'contact_id' => 'required|exists:contacts,id',
            'doc_type' => 'required|string|in:01,03,07,08',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $data['user_id'] = $request->user()?->id ?? 1;
        $items = $data['items'];
        unset($data['items']);

        $invoice = $this->invoiceService->createDraft($data, $items);

        return response()->json([
            'message' => 'Comprobante creado exitosamente en modo borrador.',
            'data' => $invoice,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with(['items.variant', 'customer', 'branch', 'payments'])->findOrFail($id);
        return response()->json(['data' => $invoice]);
    }

    public function issue(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'series' => 'required|string|size:4',
        ]);

        $invoice = Invoice::findOrFail($id);
        $issued = $this->invoiceService->issue($invoice, $request->series);

        return response()->json([
            'message' => "Comprobante emitido correctamente con correlativo {$issued->full_correlative}.",
            'data' => $issued,
        ]);
    }

    public function void(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:255',
        ]);

        $invoice = Invoice::findOrFail($id);
        $voided = $this->invoiceService->void($invoice, $request->reason);

        return response()->json([
            'message' => 'Comprobante anulado y stock restituido en Kardex.',
            'data' => $voided,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Enums\DocType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentCondition;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Contact;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\Task;
use App\Models\Tax;
use App\Models\Warehouse;
use App\Services\InvoiceService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceWebController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Invoice::with(['customer', 'branch', 'items'])
            ->when($status !== 'all', function ($q) use ($status) {
                if ($status === 'due') {
                    $q->whereIn('status', [InvoiceStatus::ISSUED->value, 'partially_paid'])
                      ->where('due_date', '<', Carbon::today());
                } else {
                    $q->where('status', $status);
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('series', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('legal_name', 'like', "%{$search}%")
                              ->orWhere('trade_name', 'like', "%{$search}%")
                              ->orWhere('doc_number', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id');

        $invoices = $query->paginate(12)->withQueryString();

        $stats = [
            'total_invoiced' => Invoice::whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PAID])->sum('total'),
            'outstanding' => Invoice::whereIn('status', [InvoiceStatus::ISSUED, 'partially_paid'])->sum('total'),
            'drafts_count' => Invoice::where('status', InvoiceStatus::DRAFT)->count(),
            'total_count' => Invoice::count(),
            'draft_tab_count' => Invoice::where('status', InvoiceStatus::DRAFT)->count(),
            'due_tab_count' => Invoice::whereIn('status', [InvoiceStatus::ISSUED, 'partially_paid'])
                ->where('due_date', '<', Carbon::today())->count(),
        ];

        return view('invoices.index', compact('invoices', 'stats', 'status', 'search'));
    }

    public function create(Request $request)
    {
        $company = Company::first() ?? new Company(['legal_name' => 'DISTRIBUIDORA & LOGÍSTICA LOS ANDES S.A.C.', 'tax_id' => '20601234567']);
        $branches = Branch::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $customers = Contact::with('addresses')->where('is_customer', true)->where('is_active', true)->get();
        $variants = ProductVariant::with(['product.unit'])->where('is_active', true)->get();
        $taxes = Tax::all();
        $series = DocumentSeries::where('is_active', true)->get();

        $catalogVariantsData = $variants->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->product->name . ($v->name ? " ({$v->name})" : ''),
            'sku' => $v->sku,
            'price' => (float) $v->sale_price,
            'tax_rate' => 18.00,
            'unit_code' => $v->product->unit->code ?? 'NIU'
        ])->values()->toArray();

        // Si se importan tareas desde el Invoice Ninja Task Tracker:
        $preloadedItems = [];
        $selectedTaskIds = [];
        if ($request->has('tasks')) {
            $taskIds = is_array($request->tasks) ? $request->tasks : explode(',', $request->tasks);
            $tasks = Task::whereIn('id', $taskIds)->get();
            $selectedTaskIds = $tasks->pluck('id')->toArray();

            foreach ($tasks as $task) {
                $hours = $task->total_seconds > 0 ? round($task->total_seconds / 3600, 2) : (float) $task->hours;
                if ($hours <= 0) $hours = 1.0;

                $preloadedItems[] = [
                    'description' => "[Servicio / Tarea] {$task->title} (" . $task->formatted_duration . " hrs)",
                    'quantity' => $hours,
                    'unit_price' => (float) $task->hourly_rate,
                    'tax_rate' => 18.00,
                    'unit_code' => 'ZZ', // Unidad de servicio SUNAT
                    'product_variant_id' => null,
                ];
            }
        }

        return view('invoices.create', compact(
            'company',
            'branches',
            'warehouses',
            'customers',
            'variants',
            'taxes',
            'series',
            'catalogVariantsData',
            'preloadedItems',
            'selectedTaskIds'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'doc_type' => 'required|in:01,03',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'payment_condition' => 'required|in:cash,credit',
            'currency' => 'required|string|size:3',
            'series' => 'required|string|max:4',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
        ]);

        $company = Company::first();

        $invoiceData = [
            'company_id' => $company->id,
            'branch_id' => $request->branch_id,
            'warehouse_id' => $request->warehouse_id,
            'contact_id' => $request->contact_id,
            'user_id' => 1,
            'doc_type' => $request->doc_type,
            'payment_condition' => $request->payment_condition,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date ?? Carbon::parse($request->issue_date)->addDays(15),
            'currency' => $request->currency,
            'exchange_rate' => 1.0,
            'notes' => $request->notes,
        ];

        $items = [];
        foreach ($request->items as $it) {
            $items[] = [
                'product_variant_id' => !empty($it['product_variant_id']) ? $it['product_variant_id'] : null,
                'description' => $it['description'],
                'unit_code' => $it['unit_code'] ?? 'NIU',
                'quantity' => (float) $it['quantity'],
                'unit_price' => (float) $it['unit_price'],
                'tax_rate' => (float) ($it['tax_rate'] ?? 18.00),
                'discount_amount' => (float) ($it['discount_amount'] ?? 0.0),
            ];
        }

        $invoice = $this->invoiceService->createDraft($invoiceData, $items);

        // Si se vinculó tareas de Invoice Ninja:
        if ($request->filled('task_ids')) {
            $taskIds = is_array($request->task_ids) ? $request->task_ids : explode(',', $request->task_ids);
            Task::whereIn('id', $taskIds)->update([
                'invoice_id' => $invoice->id,
                'status' => 'invoiced',
            ]);
        }

        // Si el usuario eligió "Emitir y Asignar Correlativo Fiscal Inmediatamente":
        if ($request->input('action') === 'issue') {
            $invoice = $this->invoiceService->issue($invoice, $request->series);
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', "¡Comprobante fiscal {$invoice->full_correlative} emitido exitosamente!");
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Borrador de factura guardado exitosamente (#{$invoice->id}).");
    }

    public function show(int $id)
    {
        $invoice = Invoice::with([
            'company',
            'branch',
            'warehouse',
            'customer.addresses',
            'items.variant.product',
            'payments.paymentMethod',
            'tasks'
        ])->findOrFail($id);

        $paymentMethods = PaymentMethod::all();
        $series = DocumentSeries::where('branch_id', $invoice->branch_id)
            ->where('doc_type', $invoice->doc_type)
            ->where('is_active', true)
            ->get();

        return view('invoices.show', compact('invoice', 'paymentMethods', 'series'));
    }

    public function issue(Request $request, int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $request->validate(['series' => 'required|string|max:4']);

        $issued = $this->invoiceService->issue($invoice, $request->series);

        return redirect()->route('invoices.show', $issued->id)
            ->with('success', "¡Comprobante fiscal {$issued->full_correlative} emitido con éxito!");
    }

    public function void(Request $request, int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $request->validate(['reason' => 'required|string|max:255']);

        $voided = $this->invoiceService->void($invoice, $request->reason);

        return redirect()->route('invoices.show', $voided->id)
            ->with('warning', "El comprobante ha sido anulado y el inventario restituido al Kardex.");
    }

    public function pdf(int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = $this->pdfService->renderInvoicePdf($invoice);

        $filename = ($invoice->full_correlative ?? "borrador-{$invoice->id}") . '.pdf';

        return $pdf->stream($filename);
    }
}

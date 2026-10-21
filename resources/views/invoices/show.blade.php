@extends('layouts.app')

@section('title', 'Comprobante ' . ($invoice->full_correlative ?? ('#' . $invoice->id)))

@section('content')
<div class="crater-invoice-container">
    <!-- Top Action Header -->
    <div class="invoice-header-row">
        <div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <h1 class="invoice-title">{{ $invoice->full_correlative ?? ('Borrador #' . $invoice->id) }}</h1>
                <span class="badge badge-{{ $invoice->status->value }}">
                    {{ $invoice->status->label() }}
                </span>
            </div>
            <nav class="crater-breadcrumbs">
                <a href="{{ route('invoices.index') }}">Home</a>
                <span class="bc-sep">/</span>
                <a href="{{ route('invoices.index') }}">Invoices</a>
                <span class="bc-sep">/</span>
                <span class="bc-current">{{ $invoice->full_correlative ?? ('#' . $invoice->id) }}</span>
            </nav>
        </div>

        <div class="invoice-top-actions">
            <!-- Download PDF Button -->
            <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-secondary" title="Abrir y descargar documento PDF oficial">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Descargar PDF</span>
            </a>

            @if($invoice->status->value === 'draft')
                <!-- Issue button with modal -->
                <button type="button" class="btn btn-primary" onclick="openIssueModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Emitir y Numerar</span>
                </button>
            @elseif($invoice->status->value === 'issued' || $invoice->status->value === 'partially_paid')
                <!-- Void Button -->
                <button type="button" class="btn btn-secondary" style="color: #ef4444; border-color: #fca5a5;" onclick="openVoidModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span>Anular Comprobante</span>
                </button>
            @endif

            <a href="{{ route('invoices.create') }}" class="btn btn-outline-purple">
                <span>+ Nueva Factura</span>
            </a>
        </div>
    </div>

    <!-- Main Printable Invoice Preview Card -->
    <div class="crater-card printable-card">
        <!-- Top Invoice Meta: Company & Invoice Box -->
        <div class="doc-header-grid">
            <div class="company-fiscal-info">
                <div class="company-brand-name">{{ $invoice->company->trade_name ?? $invoice->company->legal_name }}</div>
                <div class="company-tax-id">RUC: {{ $invoice->company->tax_id }}</div>
                <div class="company-addr">{{ $invoice->company->fiscal_address }}</div>
                <div class="company-contact">Email: {{ $invoice->company->email }} | Tel: {{ $invoice->company->phone }}</div>
                <div class="branch-tag">Sucursal: {{ $invoice->branch->name }}</div>
            </div>

            <div class="fiscal-number-box">
                <div class="f-ruc">R.U.C. N° {{ $invoice->company->tax_id }}</div>
                <div class="f-type">{{ $invoice->doc_type->label() }}</div>
                <div class="f-correlative">{{ $invoice->full_correlative ?? 'BORRADOR' }}</div>
            </div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border); margin: 24px 0;">

        <!-- Client & Dates Information -->
        <div class="client-meta-grid">
            <div>
                <span class="meta-label">Señor(es):</span>
                <span class="meta-value">{{ $invoice->customer->legal_name ?? $invoice->customer->trade_name }}</span>
                <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                    <strong>{{ $invoice->customer->doc_type }}:</strong> {{ $invoice->customer->doc_number }}
                </div>
                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 2px;">
                    {{ $invoice->customer->addresses->first()->line ?? 'Dirección no registrada' }}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div>
                    <span class="meta-label">Fecha de Emisión:</span>
                    <span class="meta-value">{{ $invoice->issue_date->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="meta-label">Fecha de Vencimiento:</span>
                    <span class="meta-value">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'Contado' }}</span>
                </div>
                <div>
                    <span class="meta-label">Condición:</span>
                    <span class="meta-value">{{ ucfirst($invoice->payment_condition->value) }}</span>
                </div>
                <div>
                    <span class="meta-label">Moneda:</span>
                    <span class="meta-value">{{ $invoice->currency }}</span>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="crater-list-table" style="margin-top: 24px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción</th>
                    <th style="width: 12%; text-align: center;">Cant.</th>
                    <th style="width: 14%; text-align: right;">P. Unitario</th>
                    <th style="width: 10%; text-align: center;">IGV</th>
                    <th style="width: 14%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->description }}</div>
                            @if($item->variant)
                                <small style="color: #94a3b8; font-family: 'JetBrains Mono', monospace;">SKU: {{ $item->variant->sku }}</small>
                            @endif
                        </td>
                        <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">
                            {{ number_format($item->quantity, 2) }}
                        </td>
                        <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                            {{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td style="text-align: center; font-size: 0.8rem; color: #64748b;">
                            {{ number_format($item->tax_rate, 2) }}%
                        </td>
                        <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                            {{ $invoice->currency }} {{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Breakdown -->
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <div style="width: 320px; background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 18px 22px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #475569;">
                    <span>Subtotal</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #475569;">
                    <span>IGV (18%)</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">{{ $invoice->currency }} {{ number_format($invoice->tax_total, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: var(--crater-purple); border-top: 2px dashed #cbd5e1; padding-top: 10px; margin-top: 4px;">
                    <span>Total a Pagar</span>
                    <span style="font-family: 'JetBrains Mono', monospace;">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($invoice->tasks && $invoice->tasks->isNotEmpty())
            <!-- Linked Invoice Ninja Tasks Section -->
            <div style="margin-top: 32px; padding: 18px; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #5b21b6; margin-bottom: 8px;">
                    <span class="badge-ninja">Invoice Ninja</span>
                    <span>Tareas y Registro de Horas Facturadas en este comprobante:</span>
                </div>
                <ul style="padding-left: 20px; font-size: 0.88rem; color: #4c1d95;">
                    @foreach($invoice->tasks as $t)
                        <li><strong>{{ $t->title }}</strong> — {{ $t->formatted_duration }} hrs @ S/ {{ number_format($t->hourly_rate, 2) }}/h</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Emitir y Asignar Correlativo Fiscal -->
<div id="issueModal" class="crater-modal" style="display: none;">
    <div class="crater-modal-backdrop" onclick="closeIssueModal()"></div>
    <div class="crater-modal-dialog">
        <form action="{{ route('invoices.issue', $invoice->id) }}" method="POST">
            @csrf
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: #0f172a;">Emitir Comprobante Fiscal</h3>
            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 20px;">
                Esta acción bloqueará de forma pesimista (<code>lockForUpdate</code>) la serie fiscal seleccionada, asignará el número correlativo correlativo oficial y descontará el stock en el Kardex.
            </p>

            <div style="margin-bottom: 20px;">
                <label class="crater-label">Selecciona la Serie Fiscal:</label>
                <select name="series" class="crater-select" required>
                    @foreach($series as $s)
                        <option value="{{ $s->series }}">Serie {{ $s->series }} (Siguiente correlativo estimado: {{ $s->current_number + 1 }})</option>
                    @endforeach
                    @if($series->isEmpty())
                        <option value="F001">F001</option>
                    @endif
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeIssueModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar y Emitir</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Anular Comprobante -->
<div id="voidModal" class="crater-modal" style="display: none;">
    <div class="crater-modal-backdrop" onclick="closeVoidModal()"></div>
    <div class="crater-modal-dialog">
        <form action="{{ route('invoices.void', $invoice->id) }}" method="POST">
            @csrf
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: #991b1b;">Anular Comprobante Fiscal</h3>
            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 20px;">
                El comprobante quedará en estado <strong>Anulado</strong> y el inventario será restituido automáticamente mediante movimientos de entrada en el Kardex.
            </p>

            <div style="margin-bottom: 20px;">
                <label class="crater-label">Motivo de Anulación:</label>
                <input type="text" name="reason" class="crater-input" placeholder="Ej: Error en digitación de RUC / Devolución total" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeVoidModal()">Cancelar</button>
                <button type="submit" class="btn btn-secondary" style="background: #ef4444; color: white; border: none;">Confirmar Anulación</button>
            </div>
        </form>
    </div>
</div>

<style>
    .printable-card {
        padding: 40px;
        background: #ffffff;
    }
    .doc-header-grid {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 24px;
    }
    .company-brand-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1e1b4b;
    }
    .company-tax-id, .company-addr, .company-contact {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 3px;
    }
    .branch-tag {
        display: inline-block;
        margin-top: 8px;
        background: #f1f5f9;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        color: #475569;
    }
    .fiscal-number-box {
        border: 2px solid var(--crater-purple);
        border-radius: var(--radius-sm);
        padding: 16px 28px;
        text-align: center;
        background: #faf5ff;
    }
    .f-ruc {
        font-size: 0.9rem;
        font-weight: 700;
        color: #475569;
    }
    .f-type {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--crater-purple);
        margin: 4px 0;
    }
    .f-correlative {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
    }
    .client-meta-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 32px;
        background: #f8fafc;
        padding: 20px 24px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
    }
    .meta-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .meta-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }

    /* Modals */
    .crater-modal {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .crater-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
    }
    .crater-modal-dialog {
        position: relative;
        background: #ffffff;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-elevated);
        width: 480px;
        max-width: 90vw;
        padding: 28px;
        z-index: 101;
    }
</style>

<script>
    function openIssueModal() {
        document.getElementById('issueModal').style.display = 'flex';
    }
    function closeIssueModal() {
        document.getElementById('issueModal').style.display = 'none';
    }
    function openVoidModal() {
        document.getElementById('voidModal').style.display = 'flex';
    }
    function closeVoidModal() {
        document.getElementById('voidModal').style.display = 'none';
    }
</script>
@endsection

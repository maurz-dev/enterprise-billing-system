@extends('layouts.app')

@section('title', 'Nueva Factura - Crater Invoice Style')

@section('content')
<div class="crater-invoice-container">
    <!-- Header: Title, Breadcrumbs, Action Buttons (Image 2) -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title">New invoice</h1>
            <nav class="crater-breadcrumbs">
                <a href="{{ route('invoices.index') }}">Home</a>
                <span class="bc-sep">/</span>
                <a href="{{ route('invoices.index') }}">Invoices</a>
                <span class="bc-sep">/</span>
                <span class="bc-current">New Invoice</span>
            </nav>
        </div>

        <div class="invoice-top-actions">
            <button type="button" class="btn btn-secondary" onclick="showPdfNotice()" title="Descargar vista previa de factura">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Download PDF</span>
            </button>

            <!-- Save as Draft (Crater purple button) -->
            <button type="button" class="btn btn-primary" onclick="submitInvoiceForm('save_draft')" id="btnSaveInvoice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Save Invoice</span>
            </button>

            <!-- Issue Immediately Button -->
            <button type="button" class="btn btn-outline-purple" onclick="submitInvoiceForm('issue')" title="Emitir con asignación atómica de serie y correlativo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Emitir Fiscalmente</span>
            </button>
        </div>
    </div>

    @if(!empty($selectedTaskIds))
        <div class="task-import-banner">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge-ninja">Invoice Ninja</span>
                <span>Se han importado <strong>{{ count($selectedTaskIds) }} tareas registradas</strong> como líneas facturables para este comprobante.</span>
            </div>
            <button type="button" class="btn-text" onclick="this.parentElement.remove()" style="color: #6c5ce7; font-weight: 700;">Entendido</button>
        </div>
    @endif

    <!-- Invoice Form -->
    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <input type="hidden" name="action" id="invoiceAction" value="save_draft">
        <input type="hidden" name="branch_id" value="{{ $branches->first()->id ?? 1 }}">
        <input type="hidden" name="warehouse_id" value="{{ $warehouses->first()->id ?? 1 }}">
        <input type="hidden" name="task_ids" value="{{ implode(',', $selectedTaskIds) }}">

        <!-- Top Metadata Cards: Customer + Dates & Series (Image 2) -->
        <div class="invoice-meta-grid">
            <!-- Customer Card: "Add a customer" (Image 2) -->
            <div class="crater-card customer-card">
                <div class="card-title-row">
                    <label class="crater-label">Cliente / Receptor</label>
                    <a href="javascript:void(0)" onclick="quickSelectFirstClient()" class="quick-link">Usar cliente demo</a>
                </div>

                <div class="customer-picker-area">
                    <div class="customer-avatar-box" id="custAvatarBox">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c5ce7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <div style="flex: 1;">
                        <select name="contact_id" id="contactSelect" class="crater-select" required onchange="handleCustomerChange(this)">
                            <option value="">Selecciona o añade un cliente...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" 
                                    data-doc="{{ $c->doc_type }}: {{ $c->doc_number }}"
                                    data-email="{{ $c->email }}"
                                    data-address="{{ $c->addresses->first()->line ?? 'Lima, Perú' }}"
                                    {{ (old('contact_id') == $c->id || (empty(old('contact_id')) && $loop->first)) ? 'selected' : '' }}>
                                    {{ $c->trade_name ?? $c->legal_name }} ({{ $c->doc_number }})
                                </option>
                            @endforeach
                        </select>

                        <div class="customer-preview" id="customerPreview">
                            <div class="cust-name" id="custNameText">-</div>
                            <div class="cust-doc" id="custDocText">-</div>
                            <div class="cust-addr" id="custAddrText">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Parameters Card: Dates, Series, DocType (Image 2) -->
            <div class="crater-card params-card">
                <div class="params-grid">
                    <!-- Doc Type -->
                    <div class="form-field">
                        <label class="crater-label">Tipo de Comprobante</label>
                        <select name="doc_type" id="docTypeSelect" class="crater-input" onchange="updateSeriesList()">
                            <option value="01">01 - Factura Electrónica</option>
                            <option value="03">03 - Boleta de Venta</option>
                        </select>
                    </div>

                    <!-- Series -->
                    <div class="form-field">
                        <label class="crater-label">Serie Fiscal (lockForUpdate)</label>
                        <select name="series" id="seriesSelect" class="crater-input" required>
                            @foreach($series as $s)
                                <option value="{{ $s->series }}" data-type="{{ $s->doc_type }}">
                                    {{ $s->series }} (Correlativo actual: {{ $s->current_number }})
                                </option>
                            @endforeach
                            @if($series->isEmpty())
                                <option value="F001">F001</option>
                            @endif
                        </select>
                    </div>

                    <!-- Invoice Date -->
                    <div class="form-field">
                        <label class="crater-label">Invoice Date</label>
                        <input type="date" name="issue_date" id="issueDate" class="crater-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Due Date -->
                    <div class="form-field">
                        <label class="crater-label">Due Date</label>
                        <input type="date" name="due_date" id="dueDate" class="crater-input" value="{{ date('Y-m-d', strtotime('+15 days')) }}" required>
                    </div>

                    <!-- Currency -->
                    <div class="form-field">
                        <label class="crater-label">Moneda</label>
                        <select name="currency" id="currencySelect" class="crater-input" onchange="updateCurrencySymbols()">
                            <option value="PEN">PEN (S/ Nuevos Soles)</option>
                            <option value="USD">USD ($ Dólares Americanos)</option>
                        </select>
                    </div>

                    <!-- Payment Condition -->
                    <div class="form-field">
                        <label class="crater-label">Condición de Pago</label>
                        <select name="payment_condition" class="crater-input">
                            <option value="cash">Al Contado (Inmediato)</option>
                            <option value="credit">Al Crédito (15 días)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table Card (Image 2 replica) -->
        <div class="crater-card items-card">
            <div class="items-card-header">
                <h3>Detalle de Líneas y Productos</h3>
                <span class="text-hint">Selecciona ítems del catálogo o ingresa descripciones libres</span>
            </div>

            <div class="table-responsive">
                <table class="crater-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width: 44%;">Items</th>
                            <th style="width: 12%; text-align: center;">Quantity</th>
                            <th style="width: 16%; text-align: right;">Price</th>
                            <th style="width: 12%; text-align: center;">IGV / Tax</th>
                            <th style="width: 14%; text-align: right;">Amount</th>
                            <th style="width: 2%;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Populated by JS / Preloaded -->
                    </tbody>
                </table>
            </div>

            <!-- Add Item Button (Image 2) -->
            <div class="add-item-bar">
                <button type="button" class="btn-add-item" onclick="addItemRow()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span>+ Add an item</span>
                </button>
            </div>

            <!-- Summary & Calculations (Image 2) -->
            <div class="invoice-summary-box">
                <div class="notes-box">
                    <label class="crater-label">Notas & Términos Fiscales</label>
                    <textarea name="notes" class="crater-textarea" rows="3" placeholder="Información de transferencia bancaria, número de pedido o condiciones de entrega..."></textarea>
                </div>

                <div class="totals-table-box">
                    <div class="totals-row">
                        <span class="tot-label">Subtotal</span>
                        <span class="tot-value" id="subtotalDisplay">S/ 0.00</span>
                    </div>
                    <div class="totals-row">
                        <span class="tot-label">IGV (18.00%)</span>
                        <span class="tot-value" id="taxDisplay">S/ 0.00</span>
                    </div>
                    <div class="totals-row totals-grand">
                        <span class="tot-label">Total</span>
                        <span class="tot-value" id="grandTotalDisplay">S/ 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Catalog Variants Data for JavaScript -->
<script>
    const catalogVariants = @json($catalogVariantsData);
    const preloadedItems = @json($preloadedItems);
</script>

<style>
    .crater-invoice-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Header */
    .invoice-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .invoice-title {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    .crater-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .crater-breadcrumbs a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .crater-breadcrumbs a:hover {
        color: var(--crater-purple);
    }

    .bc-sep {
        color: #cbd5e1;
    }

    .bc-current {
        color: var(--crater-purple);
        font-weight: 700;
    }

    .invoice-top-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Task import banner */
    .task-import-banner {
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
        color: #5b21b6;
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .badge-ninja {
        background: #0284c7;
        color: white;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 9999px;
        text-transform: uppercase;
    }

    /* Metadata Grid */
    .invoice-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 24px;
    }

    @media (max-width: 950px) {
        .invoice-meta-grid {
            grid-template-columns: 1fr;
        }
    }

    .crater-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .card-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .quick-link {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--crater-purple);
        text-decoration: none;
    }

    .quick-link:hover {
        text-decoration: underline;
    }

    .customer-picker-area {
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }

    .customer-avatar-box {
        width: 48px;
        height: 48px;
        background: #f5f3ff;
        border: 1.5px dashed #c4b5fd;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .crater-select, .crater-input, .crater-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.9rem;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: var(--transition);
    }

    .crater-select:focus, .crater-input:focus, .crater-textarea:focus {
        border-color: var(--crater-purple);
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
    }

    .customer-preview {
        margin-top: 12px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 0.82rem;
    }

    .cust-name {
        font-weight: 700;
        color: #1e293b;
    }

    .cust-doc {
        color: #64748b;
        font-family: 'JetBrains Mono', monospace;
        margin: 2px 0;
    }

    .cust-addr {
        color: #94a3b8;
    }

    /* Params Grid */
    .params-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    /* Items Table Card */
    .items-card {
        padding: 24px;
    }

    .items-card-header {
        margin-bottom: 20px;
    }

    .items-card-header h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .text-hint {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .crater-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .crater-table th {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        padding: 8px 12px;
        border-bottom: 1.5px solid var(--border);
    }

    .crater-table td {
        padding: 8px 6px;
        vertical-align: top;
    }

    .item-desc-input {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        outline: none;
    }

    .item-desc-input:focus {
        border-color: var(--crater-purple);
    }

    .item-num-input {
        width: 100%;
        padding: 10px 10px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-family: 'JetBrains Mono', monospace;
        text-align: right;
        outline: none;
    }

    .item-num-input:focus {
        border-color: var(--crater-purple);
    }

    .item-amount-display {
        display: block;
        padding: 10px 12px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.95rem;
        font-weight: 700;
        text-align: right;
        color: #0f172a;
    }

    .btn-delete-item {
        background: none;
        border: none;
        color: #cbd5e1;
        cursor: pointer;
        padding: 10px 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }

    .btn-delete-item:hover {
        color: #ef4444;
    }

    /* Add Item Bar */
    .add-item-bar {
        padding: 16px 0;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
    }

    .btn-add-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--crater-purple);
        border: 1.5px dashed var(--crater-purple);
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-add-item:hover {
        background: #f5f3ff;
        transform: translateY(-1px);
    }

    /* Summary Box */
    .invoice-summary-box {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 32px;
    }

    @media (max-width: 800px) {
        .invoice-summary-box {
            grid-template-columns: 1fr;
        }
    }

    .totals-table-box {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .totals-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.92rem;
        color: #475569;
    }

    .tot-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: #0f172a;
    }

    .totals-grand {
        border-top: 2px dashed #cbd5e1;
        padding-top: 14px;
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e1b4b;
    }

    .totals-grand .tot-value {
        color: var(--crater-purple);
        font-size: 1.35rem;
    }
</style>

@push('scripts')
<script>
    let itemRowCount = 0;
    let currencySymbol = 'S/ ';

    function updateCurrencySymbols() {
        const cur = document.getElementById('currencySelect').value;
        currencySymbol = cur === 'USD' ? '$ ' : 'S/ ';
        calculateInvoiceTotals();
    }

    function handleCustomerChange(select) {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            document.getElementById('customerPreview').style.display = 'none';
            return;
        }

        document.getElementById('customerPreview').style.display = 'block';
        document.getElementById('custNameText').innerText = opt.text;
        document.getElementById('custDocText').innerText = opt.getAttribute('data-doc') || '';
        document.getElementById('custAddrText').innerText = opt.getAttribute('data-address') || '';
    }

    function quickSelectFirstClient() {
        const sel = document.getElementById('contactSelect');
        if (sel.options.length > 1) {
            sel.selectedIndex = 1;
            handleCustomerChange(sel);
            showToast('Cliente seleccionado automáticamente.');
        }
    }

    function updateSeriesList() {
        const docType = document.getElementById('docTypeSelect').value;
        const seriesSelect = document.getElementById('seriesSelect');
        let matched = false;

        Array.from(seriesSelect.options).forEach(opt => {
            const optType = opt.getAttribute('data-type');
            if (optType === docType || !optType) {
                opt.style.display = '';
                if (!matched) {
                    opt.selected = true;
                    matched = true;
                }
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function addItemRow(initialData = null) {
        const tbody = document.getElementById('itemsTableBody');
        const idx = itemRowCount++;

        const tr = document.createElement('tr');
        tr.id = `item-row-${idx}`;

        const desc = initialData ? initialData.description : '';
        const qty = initialData ? initialData.quantity : 1;
        const price = initialData ? initialData.unit_price : 0;
        const taxRate = initialData ? (initialData.tax_rate ?? 18) : 18;
        const variantId = initialData ? (initialData.product_variant_id ?? '') : '';

        tr.innerHTML = `
            <td>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <select class="crater-select" style="font-size: 0.82rem; padding: 6px 10px;" onchange="selectCatalogVariant(${idx}, this)">
                        <option value="">-- O elige del catálogo de productos --</option>
                        ${catalogVariants.map(v => `<option value="${v.id}" data-price="${v.price}" data-tax="${v.tax_rate}">${v.name} (${currencySymbol}${v.price.toFixed(2)})</option>`).join('')}
                    </select>
                    <input type="text" name="items[${idx}][description]" id="item-desc-${idx}" class="item-desc-input" placeholder="Descripción del producto o servicio..." value="${desc}" required>
                    <input type="hidden" name="items[${idx}][product_variant_id]" id="item-variant-${idx}" value="${variantId}">
                </div>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="items[${idx}][quantity]" id="item-qty-${idx}" class="item-num-input" value="${qty}" oninput="calculateRowAmount(${idx})" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${idx}][unit_price]" id="item-price-${idx}" class="item-num-input" value="${price}" oninput="calculateRowAmount(${idx})" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${idx}][tax_rate]" id="item-tax-${idx}" class="item-num-input" value="${taxRate}" oninput="calculateRowAmount(${idx})" style="text-align: center;">
            </td>
            <td>
                <span class="item-amount-display" id="item-amount-${idx}">${currencySymbol}0.00</span>
            </td>
            <td>
                <button type="button" class="btn-delete-item" onclick="deleteItemRow(${idx})" title="Eliminar ítem">&times;</button>
            </td>
        `;

        tbody.appendChild(tr);
        calculateRowAmount(idx);
    }

    function selectCatalogVariant(rowIdx, select) {
        const variantId = select.value;
        if (!variantId) return;

        const variant = catalogVariants.find(v => v.id == variantId);
        if (variant) {
            document.getElementById(`item-desc-${rowIdx}`).value = variant.name;
            document.getElementById(`item-price-${rowIdx}`).value = variant.price;
            document.getElementById(`item-tax-${rowIdx}`).value = variant.tax_rate;
            document.getElementById(`item-variant-${rowIdx}`).value = variant.id;
            calculateRowAmount(rowIdx);
        }
    }

    function deleteItemRow(rowIdx) {
        const tr = document.getElementById(`item-row-${rowIdx}`);
        if (tr) {
            tr.remove();
            calculateInvoiceTotals();
        }
    }

    function calculateRowAmount(rowIdx) {
        const qty = parseFloat(document.getElementById(`item-qty-${rowIdx}`)?.value) || 0;
        const price = parseFloat(document.getElementById(`item-price-${rowIdx}`)?.value) || 0;
        const taxRate = parseFloat(document.getElementById(`item-tax-${rowIdx}`)?.value) || 0;

        const subtotal = qty * price;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;

        const disp = document.getElementById(`item-amount-${rowIdx}`);
        if (disp) {
            disp.innerText = `${currencySymbol}${total.toFixed(2)}`;
        }

        calculateInvoiceTotals();
    }

    function calculateInvoiceTotals() {
        let subtotalAll = 0;
        let taxAll = 0;

        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach(tr => {
            const idx = tr.id.replace('item-row-', '');
            const qty = parseFloat(document.getElementById(`item-qty-${idx}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`item-price-${idx}`)?.value) || 0;
            const taxRate = parseFloat(document.getElementById(`item-tax-${idx}`)?.value) || 0;

            const itemSub = qty * price;
            const itemTax = itemSub * (taxRate / 100);

            subtotalAll += itemSub;
            taxAll += itemTax;
        });

        const grandTotal = subtotalAll + taxAll;

        document.getElementById('subtotalDisplay').innerText = `${currencySymbol}${subtotalAll.toFixed(2)}`;
        document.getElementById('taxDisplay').innerText = `${currencySymbol}${taxAll.toFixed(2)}`;
        document.getElementById('grandTotalDisplay').innerText = `${currencySymbol}${grandTotal.toFixed(2)}`;
    }

    function submitInvoiceForm(action) {
        document.getElementById('invoiceAction').value = action;
        const form = document.getElementById('invoiceForm');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const rows = document.querySelectorAll('#itemsTableBody tr');
        if (rows.length === 0) {
            showToast('Por favor agrega al menos un ítem o producto a la factura.', 'warning');
            return;
        }

        if (action === 'issue') {
            if (!confirm('¿Deseas emitir y asignar número fiscal definitivo (lockForUpdate) a este comprobante?')) {
                return;
            }
        }

        form.submit();
    }

    function showPdfNotice() {
        showToast('Guarda primero el borrador de la factura para generar el PDF oficial.', 'info');
    }

    document.addEventListener('DOMContentLoaded', () => {
        handleCustomerChange(document.getElementById('contactSelect'));
        updateSeriesList();

        // Load preloaded items if from Invoice Ninja
        if (preloadedItems && preloadedItems.length > 0) {
            preloadedItems.forEach(item => addItemRow(item));
        } else {
            // Default 1 blank row or first catalog item
            addItemRow(catalogVariants[0] ? {
                description: catalogVariants[0].name,
                quantity: 1,
                unit_price: catalogVariants[0].price,
                tax_rate: catalogVariants[0].tax_rate,
                product_variant_id: catalogVariants[0].id
            } : null);
        }
    });
</script>
@endpush
@endsection

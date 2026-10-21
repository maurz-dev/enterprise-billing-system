<?php $__env->startSection('title', 'Nueva Factura'); ?>

<?php $__env->startSection('content'); ?>
<div class="apex-invoice-container">
    <!-- Header: Title, Breadcrumbs, Action Buttons -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title" data-i18n="new_invoice_title">Nueva Factura</h1>
            <nav class="apex-breadcrumbs">
                <a href="<?php echo e(route('invoices.index')); ?>">Home</a>
                <span class="bc-sep">/</span>
                <a href="<?php echo e(route('invoices.index')); ?>" data-i18n="invoices_title">Facturación</a>
                <span class="bc-sep">/</span>
                <span class="bc-current" data-i18n="new_invoice_title">Nueva Factura</span>
            </nav>
        </div>

        <div class="invoice-top-actions">
            <button type="button" class="btn btn-secondary" onclick="showPdfNotice()" title="Descargar vista previa de factura">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span data-i18n="download_pdf">Descargar PDF</span>
            </button>

            <!-- Save as Draft -->
            <button type="button" class="btn btn-primary" onclick="submitInvoiceForm('save_draft')" id="btnSaveInvoice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span data-i18n="save_draft">Guardar Borrador</span>
            </button>

            <!-- Issue Immediately Button -->
            <button type="button" class="btn btn-outline-primary" onclick="submitInvoiceForm('issue')" title="Emitir con asignación atómica de serie y correlativo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span data-i18n="issue_fiscal">Emitir Fiscalmente</span>
            </button>
        </div>
    </div>

    <?php if(!empty($selectedTaskIds)): ?>
        <div class="task-import-banner">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge-tasks">Control de Tareas</span>
                <span>Se han importado <strong><?php echo e(count($selectedTaskIds)); ?> tareas registradas</strong> como líneas facturables para este comprobante.</span>
            </div>
            <button type="button" class="btn-text" onclick="this.parentElement.remove()" style="color: var(--primary); font-weight: 700;">Entendido</button>
        </div>
    <?php endif; ?>

    <!-- Invoice Form -->
    <form action="<?php echo e(route('invoices.store')); ?>" method="POST" id="invoiceForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" id="invoiceAction" value="save_draft">
        <input type="hidden" name="branch_id" value="<?php echo e($branches->first()->id ?? 1); ?>">
        <input type="hidden" name="warehouse_id" value="<?php echo e($warehouses->first()->id ?? 1); ?>">
        <input type="hidden" name="task_ids" value="<?php echo e(implode(',', $selectedTaskIds)); ?>">

        <!-- Top Metadata Cards: Customer + Dates & Series -->
        <div class="invoice-meta-grid">
            <!-- Customer Card -->
            <div class="apex-card customer-card">
                <div class="card-title-row">
                    <label class="apex-label" data-i18n="customer_receiver">Cliente / Receptor</label>
                    <a href="javascript:void(0)" onclick="quickSelectFirstClient()" class="quick-link" data-i18n="use_demo_customer">Usar cliente demo</a>
                </div>

                <div class="customer-picker-area">
                    <div class="customer-avatar-box" id="custAvatarBox">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <div style="flex: 1;">
                        <select name="contact_id" id="contactSelect" class="apex-select" required onchange="handleCustomerChange(this)">
                            <option value="">Selecciona o añade un cliente...</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" 
                                    data-doc="<?php echo e($c->doc_type); ?>: <?php echo e($c->doc_number); ?>"
                                    data-email="<?php echo e($c->email); ?>"
                                    data-address="<?php echo e($c->addresses->first()->line ?? 'Lima, Perú'); ?>"
                                    <?php echo e((old('contact_id') == $c->id || (empty(old('contact_id')) && $loop->first)) ? 'selected' : ''); ?>>
                                    <?php echo e($c->trade_name ?? $c->legal_name); ?> (<?php echo e($c->doc_number); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div class="customer-preview" id="customerPreview">
                            <div class="cust-name" id="custNameText">-</div>
                            <div class="cust-doc" id="custDocText">-</div>
                            <div class="cust-addr" id="custAddrText">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Parameters Card: Dates, Series, DocType -->
            <div class="apex-card params-card">
                <div class="params-grid">
                    <!-- Doc Type -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="doc_type_label">Tipo de Comprobante</label>
                        <select name="doc_type" id="docTypeSelect" class="apex-input" onchange="updateSeriesList()">
                            <option value="01">01 - Factura Electrónica</option>
                            <option value="03">03 - Boleta de Venta</option>
                        </select>
                    </div>

                    <!-- Series -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="series_label">Serie Fiscal (lockForUpdate)</label>
                        <select name="series" id="seriesSelect" class="apex-input" required>
                            <?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->series); ?>" data-type="<?php echo e($s->doc_type); ?>">
                                    <?php echo e($s->series); ?> (Correlativo actual: <?php echo e($s->current_number); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($series->isEmpty()): ?>
                                <option value="F001">F001</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Invoice Date -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="th_issue_date">Fecha Emisión</label>
                        <input type="date" name="issue_date" id="issueDate" class="apex-input" value="<?php echo e(date('Y-m-d')); ?>" required>
                    </div>

                    <!-- Due Date -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="th_due_date">Vencimiento</label>
                        <input type="date" name="due_date" id="dueDate" class="apex-input" value="<?php echo e(date('Y-m-d', strtotime('+15 days'))); ?>" required>
                    </div>

                    <!-- Currency -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="currency_label">Moneda</label>
                        <select name="currency" id="currencySelect" class="apex-input" onchange="updateCurrencySymbols()">
                            <option value="PEN">PEN (S/ Nuevos Soles)</option>
                            <option value="USD">USD ($ Dólares Americanos)</option>
                        </select>
                    </div>

                    <!-- Payment Condition -->
                    <div class="form-field">
                        <label class="apex-label" data-i18n="payment_condition_label">Condición de Pago</label>
                        <select name="payment_condition" class="apex-input">
                            <option value="cash" data-i18n="cash_label">Al Contado (Inmediato)</option>
                            <option value="credit" data-i18n="credit_label">Al Crédito (15 días)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="apex-card items-card">
            <div class="items-card-header">
                <h3 data-i18n="line_items_title">Detalle de Líneas y Productos</h3>
                <span class="text-hint" data-i18n="line_items_subtitle">Selecciona ítems del catálogo o ingresa descripciones libres</span>
            </div>

            <div class="table-responsive">
                <table class="apex-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width: 44%;">Ítems / Descripción</th>
                            <th style="width: 12%; text-align: center;">Cantidad</th>
                            <th style="width: 16%; text-align: right;">Precio</th>
                            <th style="width: 12%; text-align: center;">IGV / Tax</th>
                            <th style="width: 14%; text-align: right;">Total</th>
                            <th style="width: 2%;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Populated by JS / Preloaded -->
                    </tbody>
                </table>
            </div>

            <!-- Add Item Button -->
            <div class="add-item-bar">
                <button type="button" class="btn-add-item" onclick="addItemRow()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span data-i18n="add_item">+ Añadir Ítem</span>
                </button>
            </div>

            <!-- Summary & Calculations -->
            <div class="invoice-summary-box">
                <div class="notes-box">
                    <label class="apex-label" data-i18n="notes_label">Notas & Términos Fiscales</label>
                    <textarea name="notes" class="apex-textarea" rows="3" placeholder="Información de transferencia bancaria, número de pedido o condiciones de entrega..."></textarea>
                </div>

                <div class="totals-table-box">
                    <div class="totals-row">
                        <span class="tot-label" data-i18n="subtotal">Subtotal</span>
                        <span class="tot-value" id="subtotalDisplay">S/ 0.00</span>
                    </div>
                    <div class="totals-row">
                        <span class="tot-label" data-i18n="tax_igv">IGV (18.00%)</span>
                        <span class="tot-value" id="taxDisplay">S/ 0.00</span>
                    </div>
                    <div class="totals-row totals-grand">
                        <span class="tot-label" data-i18n="grand_total">Total</span>
                        <span class="tot-value" id="grandTotalDisplay">S/ 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Catalog Variants Data for JavaScript -->
<script>
    const catalogVariants = <?php echo json_encode($catalogVariantsData, 15, 512) ?>;
    const preloadedItems = <?php echo json_encode($preloadedItems, 15, 512) ?>;
</script>

<style>
    .apex-invoice-container {
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
        color: var(--text-main);
    }

    .apex-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .apex-breadcrumbs a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .apex-breadcrumbs a:hover {
        color: var(--primary);
    }

    .bc-sep {
        color: var(--text-light);
    }

    .bc-current {
        color: var(--primary);
        font-weight: 700;
    }

    .invoice-top-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Task import banner */
    .task-import-banner {
        background: var(--primary-light, #eff6ff);
        border: 1px solid var(--border-focus, #93c5fd);
        color: var(--primary, #2563eb);
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .badge-tasks {
        background: var(--primary);
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

    .apex-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
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
        color: var(--primary);
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
        background: var(--primary-light, #eff6ff);
        border: 1.5px dashed var(--border-focus, #93c5fd);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .apex-select, .apex-input, .apex-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--text-main);
        background: var(--surface, #ffffff);
        outline: none;
        transition: var(--transition);
    }

    .apex-select:focus, .apex-input:focus, .apex-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .customer-preview {
        margin-top: 12px;
        padding: 12px;
        background: var(--surface-subtle, #f8fafc);
        border-radius: 8px;
        font-size: 0.82rem;
    }

    .cust-name {
        font-weight: 700;
        color: var(--text-main);
    }

    .cust-doc {
        color: var(--text-muted);
        font-family: 'JetBrains Mono', monospace;
        margin: 2px 0;
    }

    .cust-addr {
        color: var(--text-light);
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
        color: var(--text-main);
    }

    .text-hint {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .apex-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .apex-table th {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        padding: 8px 12px;
        border-bottom: 1.5px solid var(--border);
    }

    .apex-table td {
        padding: 8px 6px;
        vertical-align: top;
    }

    .item-desc-input {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        background: var(--surface, #ffffff);
        color: var(--text-main);
        outline: none;
    }

    .item-desc-input:focus {
        border-color: var(--primary);
    }

    .item-num-input {
        width: 100%;
        padding: 10px 10px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-family: 'JetBrains Mono', monospace;
        text-align: right;
        background: var(--surface, #ffffff);
        color: var(--text-main);
        outline: none;
    }

    .item-num-input:focus {
        border-color: var(--primary);
    }

    .item-amount-display {
        display: block;
        padding: 10px 12px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.95rem;
        font-weight: 700;
        text-align: right;
        color: var(--text-main);
    }

    .btn-delete-item {
        background: none;
        border: none;
        color: var(--text-light);
        cursor: pointer;
        padding: 10px 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }

    .btn-delete-item:hover {
        color: var(--danger);
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
        color: var(--primary);
        border: 1.5px dashed var(--primary);
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-add-item:hover {
        background: var(--primary-light, #eff6ff);
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
        background: var(--surface-subtle, #f8fafc);
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
        color: var(--text-muted);
    }

    .tot-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: var(--text-main);
    }

    .totals-grand {
        border-top: 2px dashed var(--border);
        padding-top: 14px;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .totals-grand .tot-value {
        color: var(--primary);
        font-size: 1.35rem;
    }
</style>

<?php $__env->startPush('scripts'); ?>
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
                    <select class="apex-select" style="font-size: 0.82rem; padding: 6px 10px;" onchange="selectCatalogVariant(${idx}, this)">
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

        // Load preloaded items
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/invoices/create.blade.php ENDPATH**/ ?>
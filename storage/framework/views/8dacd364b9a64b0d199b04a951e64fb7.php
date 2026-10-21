<?php $__env->startSection('title', 'Comprobante ' . ($invoice->full_correlative ?? ('#' . $invoice->id))); ?>

<?php $__env->startSection('content'); ?>
<div class="apex-invoice-container">
    <!-- Top Action Header -->
    <div class="invoice-header-row">
        <div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <h1 class="invoice-title"><?php echo e($invoice->full_correlative ?? ('Borrador #' . $invoice->id)); ?></h1>
                <span class="badge badge-<?php echo e($invoice->status->value); ?>">
                    <?php echo e($invoice->status->label()); ?>

                </span>
            </div>
            <nav class="apex-breadcrumbs">
                <a href="<?php echo e(route('invoices.index')); ?>">Home</a>
                <span class="bc-sep">/</span>
                <a href="<?php echo e(route('invoices.index')); ?>" data-i18n="invoices_title">Facturación</a>
                <span class="bc-sep">/</span>
                <span class="bc-current"><?php echo e($invoice->full_correlative ?? ('#' . $invoice->id)); ?></span>
            </nav>
        </div>

        <div class="invoice-top-actions">
            <!-- Download PDF Button -->
            <a href="<?php echo e(route('invoices.pdf', $invoice->id)); ?>" target="_blank" class="btn btn-secondary" title="Abrir y descargar documento PDF oficial">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span data-i18n="download_pdf">Descargar PDF</span>
            </a>

            <?php if($invoice->status->value === 'draft'): ?>
                <!-- Issue button with modal -->
                <button type="button" class="btn btn-primary" onclick="openIssueModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span data-i18n="issue_fiscal">Emitir y Numerar</span>
                </button>
            <?php elseif($invoice->status->value === 'issued' || $invoice->status->value === 'partially_paid'): ?>
                <!-- Void Button -->
                <button type="button" class="btn btn-secondary" style="color: #ef4444; border-color: #fca5a5;" onclick="openVoidModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span>Anular Comprobante</span>
                </button>
            <?php endif; ?>

            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-outline-primary">
                <span data-i18n="btn_new_invoice">+ Nueva Factura</span>
            </a>
        </div>
    </div>

    <!-- Main Printable Invoice Preview Card -->
    <div class="apex-card printable-card">
        <!-- Top Invoice Meta: Company & Invoice Box -->
        <div class="doc-header-grid">
            <div class="company-fiscal-info">
                <div class="company-brand-name"><?php echo e($invoice->company->trade_name ?? $invoice->company->legal_name); ?></div>
                <div class="company-tax-id">RUC: <?php echo e($invoice->company->tax_id); ?></div>
                <div class="company-addr"><?php echo e($invoice->company->fiscal_address); ?></div>
                <div class="company-contact">Email: <?php echo e($invoice->company->email); ?> | Tel: <?php echo e($invoice->company->phone); ?></div>
                <div class="branch-tag">Sucursal: <?php echo e($invoice->branch->name); ?></div>
            </div>

            <div class="fiscal-number-box">
                <div class="f-ruc">R.U.C. N° <?php echo e($invoice->company->tax_id); ?></div>
                <div class="f-type"><?php echo e($invoice->doc_type->label()); ?></div>
                <div class="f-correlative"><?php echo e($invoice->full_correlative ?? 'BORRADOR'); ?></div>
            </div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border); margin: 24px 0;">

        <!-- Client & Dates Information -->
        <div class="client-meta-grid">
            <div>
                <span class="meta-label">Señor(es):</span>
                <span class="meta-value"><?php echo e($invoice->customer->legal_name ?? $invoice->customer->trade_name); ?></span>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">
                    <strong><?php echo e($invoice->customer->doc_type); ?>:</strong> <?php echo e($invoice->customer->doc_number); ?>

                </div>
                <div style="font-size: 0.85rem; color: var(--text-light); margin-top: 2px;">
                    <?php echo e($invoice->customer->addresses->first()->line ?? 'Dirección no registrada'); ?>

                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div>
                    <span class="meta-label" data-i18n="th_issue_date">Fecha de Emisión:</span>
                    <span class="meta-value"><?php echo e($invoice->issue_date->format('d/m/Y')); ?></span>
                </div>
                <div>
                    <span class="meta-label" data-i18n="th_due_date">Fecha de Vencimiento:</span>
                    <span class="meta-value"><?php echo e($invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'Contado'); ?></span>
                </div>
                <div>
                    <span class="meta-label">Condición:</span>
                    <span class="meta-value"><?php echo e(ucfirst($invoice->payment_condition->value)); ?></span>
                </div>
                <div>
                    <span class="meta-label">Moneda:</span>
                    <span class="meta-value"><?php echo e($invoice->currency); ?></span>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="apex-list-table" style="margin-top: 24px;">
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
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);"><?php echo e($item->description); ?></div>
                            <?php if($item->variant): ?>
                                <small style="color: var(--text-light); font-family: 'JetBrains Mono', monospace;">SKU: <?php echo e($item->variant->sku); ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">
                            <?php echo e(number_format($item->quantity, 2)); ?>

                        </td>
                        <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                            <?php echo e($invoice->currency); ?> <?php echo e(number_format($item->unit_price, 2)); ?>

                        </td>
                        <td style="text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                            <?php echo e(number_format($item->tax_rate, 2)); ?>%
                        </td>
                        <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                            <?php echo e($invoice->currency); ?> <?php echo e(number_format($item->total, 2)); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- Totals Breakdown -->
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <div style="width: 320px; background: var(--surface-subtle, #f8fafc); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 18px 22px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-muted);">
                    <span data-i18n="subtotal">Subtotal</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->subtotal, 2)); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-muted);">
                    <span data-i18n="tax_igv">IGV (18%)</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->tax_total, 2)); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: var(--primary); border-top: 2px dashed var(--border); padding-top: 10px; margin-top: 4px;">
                    <span data-i18n="grand_total">Total a Pagar</span>
                    <span style="font-family: 'JetBrains Mono', monospace;"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->total, 2)); ?></span>
                </div>
            </div>
        </div>

        <?php if($invoice->tasks && $invoice->tasks->isNotEmpty()): ?>
            <!-- Linked Tasks Section -->
            <div style="margin-top: 32px; padding: 18px; background: var(--primary-light, #eff6ff); border: 1px solid var(--border-focus, #93c5fd); border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: var(--primary, #2563eb); margin-bottom: 8px;">
                    <span class="badge-tasks">Control de Tareas</span>
                    <span>Tareas y Registro de Horas Facturadas en este comprobante:</span>
                </div>
                <ul style="padding-left: 20px; font-size: 0.88rem; color: var(--text-main);">
                    <?php $__currentLoopData = $invoice->tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><strong><?php echo e($t->title); ?></strong> — <?php echo e($t->formatted_duration); ?> hrs @ S/ <?php echo e(number_format($t->hourly_rate, 2)); ?>/h</li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Emitir y Asignar Correlativo Fiscal -->
<div id="issueModal" class="apex-modal" style="display: none;">
    <div class="apex-modal-backdrop" onclick="closeIssueModal()"></div>
    <div class="apex-modal-dialog">
        <form action="<?php echo e(route('invoices.issue', $invoice->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main);">Emitir Comprobante Fiscal</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
                Esta acción bloqueará de forma pesimista (<code>lockForUpdate</code>) la serie fiscal seleccionada, asignará el número correlativo oficial y descontará el stock en el Kardex.
            </p>

            <div style="margin-bottom: 20px;">
                <label class="apex-label">Selecciona la Serie Fiscal:</label>
                <select name="series" class="apex-select" required>
                    <?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->series); ?>">Serie <?php echo e($s->series); ?> (Siguiente correlativo estimado: <?php echo e($s->current_number + 1); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($series->isEmpty()): ?>
                        <option value="F001">F001</option>
                    <?php endif; ?>
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
<div id="voidModal" class="apex-modal" style="display: none;">
    <div class="apex-modal-backdrop" onclick="closeVoidModal()"></div>
    <div class="apex-modal-dialog">
        <form action="<?php echo e(route('invoices.void', $invoice->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: #991b1b;">Anular Comprobante Fiscal</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
                El comprobante quedará en estado <strong>Anulado</strong> y el inventario será restituido automáticamente mediante movimientos de entrada en el Kardex.
            </p>

            <div style="margin-bottom: 20px;">
                <label class="apex-label">Motivo de Anulación:</label>
                <input type="text" name="reason" class="apex-input" placeholder="Ej: Error en digitación de RUC / Devolución total" required>
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
        background: var(--surface, #ffffff);
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
        color: var(--text-main);
    }
    .company-tax-id, .company-addr, .company-contact {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 3px;
    }
    .branch-tag {
        display: inline-block;
        margin-top: 8px;
        background: var(--surface-subtle, #f1f5f9);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        color: var(--text-muted);
    }
    .fiscal-number-box {
        border: 2px solid var(--primary);
        border-radius: var(--radius-sm);
        padding: 16px 28px;
        text-align: center;
        background: var(--primary-light, #eff6ff);
    }
    .f-ruc {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
    }
    .f-type {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--primary);
        margin: 4px 0;
    }
    .f-correlative {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .client-meta-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 32px;
        background: var(--surface-subtle, #f8fafc);
        padding: 20px 24px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
    }
    .meta-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-light);
    }
    .meta-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Modals */
    .apex-modal {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .apex-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
    }
    .apex-modal-dialog {
        position: relative;
        background: var(--surface, #ffffff);
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/invoices/show.blade.php ENDPATH**/ ?>
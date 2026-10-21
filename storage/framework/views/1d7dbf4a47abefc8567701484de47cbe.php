<?php $__env->startSection('title', 'Facturas & Boletas - Crater Style'); ?>

<?php $__env->startSection('content'); ?>
<div class="crater-invoice-container">
    <!-- Top Header: Title, Search, and New Invoice CTA -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title">Invoices</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Gestión de comprobantes fiscales, cobranzas y facturación electrónica</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <button type="button" class="btn btn-secondary" onclick="toggleTimerDrawer()" title="Abrir temporizador Invoice Ninja">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Tareas & Timer</span>
            </button>

            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary" id="btnNewInvoice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ New Invoice</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (Crater SaaS Dashboard) -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Facturado</span>
            <span class="stat-value" style="color: #6c5ce7;">S/ <?php echo e(number_format($stats['total_invoiced'], 2)); ?></span>
            <small class="stat-sub">Comprobantes emitidos y pagados</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Por Cobrar (Outstanding)</span>
            <span class="stat-value" style="color: #f59e0b;">S/ <?php echo e(number_format($stats['outstanding'], 2)); ?></span>
            <small class="stat-sub">Saldos pendientes de pago</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Borradores (Drafts)</span>
            <span class="stat-value" style="color: #64748b;"><?php echo e($stats['drafts_count']); ?></span>
            <small class="stat-sub">Sin correlativo asignado</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Comprobantes</span>
            <span class="stat-value"><?php echo e($stats['total_count']); ?></span>
            <small class="stat-sub">Histórico total</small>
        </div>
    </div>

    <!-- Invoices Card with Tabs (Crater UI matching Image 2 mobile/web) -->
    <div class="crater-card" style="padding: 0;">
        <!-- Status Filter Tabs: All, Due, Draft, Paid, Void -->
        <div class="crater-tabs-header">
            <div class="tabs-group">
                <a href="<?php echo e(route('invoices.index', ['status' => 'all'])); ?>" class="tab-link <?php echo e($status === 'all' ? 'active' : ''); ?>">
                    <span>All</span>
                    <span class="tab-count"><?php echo e($stats['total_count']); ?></span>
                </a>
                <a href="<?php echo e(route('invoices.index', ['status' => 'due'])); ?>" class="tab-link <?php echo e($status === 'due' ? 'active' : ''); ?>">
                    <span>Due</span>
                    <?php if($stats['due_tab_count'] > 0): ?>
                        <span class="tab-count count-due"><?php echo e($stats['due_tab_count']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('invoices.index', ['status' => 'draft'])); ?>" class="tab-link <?php echo e($status === 'draft' ? 'active' : ''); ?>">
                    <span>Draft</span>
                    <span class="tab-count"><?php echo e($stats['draft_tab_count']); ?></span>
                </a>
                <a href="<?php echo e(route('invoices.index', ['status' => 'issued'])); ?>" class="tab-link <?php echo e($status === 'issued' ? 'active' : ''); ?>">
                    <span>Issued</span>
                </a>
                <a href="<?php echo e(route('invoices.index', ['status' => 'paid'])); ?>" class="tab-link <?php echo e($status === 'paid' ? 'active' : ''); ?>">
                    <span>Paid</span>
                </a>
                <a href="<?php echo e(route('invoices.index', ['status' => 'voided'])); ?>" class="tab-link <?php echo e($status === 'voided' ? 'active' : ''); ?>">
                    <span>Voided</span>
                </a>
            </div>

            <!-- Search Form -->
            <form action="<?php echo e(route('invoices.index')); ?>" method="GET" class="search-form">
                <input type="hidden" name="status" value="<?php echo e($status); ?>">
                <div class="search-input-wrapper">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Buscar por número o cliente..." class="search-input">
                    <?php if($search): ?>
                        <a href="<?php echo e(route('invoices.index', ['status' => $status])); ?>" style="font-size: 0.8rem; color: #94a3b8; text-decoration: none;">&times;</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="table-responsive">
            <table class="crater-list-table">
                <thead>
                    <tr>
                        <th>Número / Serie</th>
                        <th>Cliente</th>
                        <th>Fecha Emisión</th>
                        <th>Vencimiento</th>
                        <th style="text-align: right;">Total</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: right; width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('invoices.show', $inv->id)); ?>" class="invoice-number-link">
                                    <?php echo e($inv->full_correlative ?? ('Borrador #' . $inv->id)); ?>

                                </a>
                                <span class="doc-badge"><?php echo e($inv->doc_type->label() ?? 'Doc'); ?></span>
                            </td>
                            <td>
                                <div class="client-name"><?php echo e($inv->customer->trade_name ?? $inv->customer->legal_name ?? 'Cliente General'); ?></div>
                                <small class="client-doc"><?php echo e($inv->customer->doc_type); ?>: <?php echo e($inv->customer->doc_number); ?></small>
                            </td>
                            <td class="date-col">
                                <?php echo e($inv->issue_date ? $inv->issue_date->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="date-col">
                                <?php echo e($inv->due_date ? $inv->due_date->format('d/m/Y') : '-'); ?>

                            </td>
                            <td style="text-align: right;">
                                <span class="amount-val"><?php echo e($inv->currency); ?> <?php echo e(number_format($inv->total, 2)); ?></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-<?php echo e($inv->status->value); ?>">
                                    <?php echo e($inv->status->label()); ?>

                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-buttons-group">
                                    <a href="<?php echo e(route('invoices.show', $inv->id)); ?>" class="action-btn" title="Ver detalle">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    <a href="<?php echo e(route('invoices.pdf', $inv->id)); ?>" target="_blank" class="action-btn" title="Descargar PDF">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 12px;">🧾</div>
                                <div style="font-weight: 700; font-size: 1.1rem; color: #1e293b;">No se encontraron facturas</div>
                                <p style="font-size: 0.88rem; margin-top: 6px;">Comienza creando tu primera factura o emitiendo tareas registradas.</p>
                                <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary" style="margin-top: 16px;">
                                    + Crear Primera Factura
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($invoices->hasPages()): ?>
            <div style="padding: 16px 24px; border-top: 1px solid var(--border);">
                <?php echo e($invoices->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 18px 22px;
        box-shadow: var(--shadow-subtle);
    }
    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }
    .stat-value {
        display: block;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.6rem;
        font-weight: 800;
        margin: 6px 0 2px;
        color: #0f172a;
    }
    .stat-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Tabs Header */
    .crater-tabs-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 12px;
    }
    .tabs-group {
        display: flex;
        gap: 8px;
    }
    .tab-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 14px;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-muted);
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .tab-link:hover {
        color: var(--crater-purple);
    }
    .tab-link.active {
        color: var(--crater-purple);
        border-bottom-color: var(--crater-purple);
        font-weight: 700;
    }
    .tab-count {
        background: #f1f5f9;
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 9999px;
        color: #475569;
    }
    .count-due {
        background: #fee2e2;
        color: #dc2626;
        font-weight: 800;
    }

    /* Search */
    .search-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 6px 12px;
    }
    .search-input {
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.85rem;
        color: #0f172a;
        width: 220px;
    }

    /* List Table */
    .crater-list-table {
        width: 100%;
        border-collapse: collapse;
    }
    .crater-list-table th {
        background: #fafafa;
        padding: 12px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
    }
    .crater-list-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .crater-list-table tr:hover td {
        background: #f8fafc;
    }
    .invoice-number-link {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: var(--crater-purple);
        text-decoration: none;
    }
    .invoice-number-link:hover {
        text-decoration: underline;
    }
    .doc-badge {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 6px;
    }
    .client-name {
        font-weight: 600;
        color: #1e293b;
    }
    .client-doc {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        color: #94a3b8;
    }
    .amount-val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: #0f172a;
    }
    .action-buttons-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid var(--border);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .action-btn:hover {
        background: #f1f5f9;
        color: var(--crater-purple);
        border-color: #cbd5e1;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/invoices/index.blade.php ENDPATH**/ ?>
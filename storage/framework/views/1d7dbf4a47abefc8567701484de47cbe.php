<?php $__env->startSection('title', 'Facturas & Boletas'); ?>

<?php $__env->startSection('content'); ?>
<div class="apex-invoice-container">
    <!-- Top Header: Title, Search, and New Invoice CTA -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title" data-i18n="invoices_title">Facturación & Comprobantes</h1>
            <p class="invoice-subtitle" data-i18n="invoices_subtitle">Gestión de comprobantes fiscales, cobranzas y facturación electrónica</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <button type="button" class="btn btn-secondary" onclick="toggleTimerDrawer()" title="Abrir temporizador de tareas">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span data-i18n="btn_tasks_timer">Tareas & Timer</span>
            </button>

            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary" id="btnNewInvoice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span data-i18n="btn_new_invoice">Nueva Factura</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label" data-i18n="stat_total_invoiced">Total Facturado</span>
            <span class="stat-value" style="color: var(--primary);">S/ <?php echo e(number_format($stats['total_invoiced'], 2)); ?></span>
            <small class="stat-sub" data-i18n="stat_total_invoiced_sub">Comprobantes emitidos y pagados</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="stat_outstanding">Por Cobrar (Pendiente)</span>
            <span class="stat-value" style="color: #f59e0b;">S/ <?php echo e(number_format($stats['outstanding'], 2)); ?></span>
            <small class="stat-sub" data-i18n="stat_outstanding_sub">Saldos pendientes de pago</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="stat_drafts">Borradores</span>
            <span class="stat-value" style="color: var(--text-muted);"><?php echo e($stats['drafts_count']); ?></span>
            <small class="stat-sub" data-i18n="stat_drafts_sub">Sin correlativo asignado</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="stat_total_docs">Total Comprobantes</span>
            <span class="stat-value"><?php echo e($stats['total_count']); ?></span>
            <small class="stat-sub" data-i18n="stat_total_docs_sub">Histórico general</small>
        </div>
    </div>

    <!-- Invoices Card with Tabs -->
    <div class="apex-card" style="padding: 0;">
        <!-- Status Filter Tabs: All, Due, Draft, Issued, Paid, Voided -->
        <div class="apex-tabs-header">
            <div class="tabs-group" id="invoiceTabsGroup">
                <button type="button" onclick="filterTab('all', this)" class="tab-btn <?php echo e($status === 'all' ? 'active' : ''); ?>" data-tab="all">
                    <span data-i18n="tab_all">Todos</span>
                    <span class="tab-count"><?php echo e($stats['total_count']); ?></span>
                </button>
                <button type="button" onclick="filterTab('due', this)" class="tab-btn <?php echo e($status === 'due' ? 'active' : ''); ?>" data-tab="due">
                    <span data-i18n="tab_due">Vencidos</span>
                    <?php if($stats['due_tab_count'] > 0): ?>
                        <span class="tab-count count-due"><?php echo e($stats['due_tab_count']); ?></span>
                    <?php endif; ?>
                </button>
                <button type="button" onclick="filterTab('draft', this)" class="tab-btn <?php echo e($status === 'draft' ? 'active' : ''); ?>" data-tab="draft">
                    <span data-i18n="tab_draft">Borrador</span>
                    <span class="tab-count"><?php echo e($stats['draft_tab_count']); ?></span>
                </button>
                <button type="button" onclick="filterTab('issued', this)" class="tab-btn <?php echo e($status === 'issued' ? 'active' : ''); ?>" data-tab="issued">
                    <span data-i18n="tab_issued">Emitidos</span>
                </button>
                <button type="button" onclick="filterTab('paid', this)" class="tab-btn <?php echo e($status === 'paid' ? 'active' : ''); ?>" data-tab="paid">
                    <span data-i18n="tab_paid">Pagados</span>
                </button>
                <button type="button" onclick="filterTab('voided', this)" class="tab-btn <?php echo e($status === 'voided' ? 'active' : ''); ?>" data-tab="voided">
                    <span data-i18n="tab_voided">Anulados</span>
                </button>
            </div>

            <!-- Instant Search Input -->
            <div class="search-form">
                <div class="search-input-wrapper">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="invoiceLiveSearch" value="<?php echo e($search); ?>" placeholder="Buscar por número o cliente..." class="search-input" oninput="handleInstantSearch(this.value)">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" style="display: <?php echo e($search ? 'block' : 'none'); ?>; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1rem;">&times;</button>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="table-responsive">
            <table class="apex-list-table">
                <thead>
                    <tr>
                        <th data-i18n="th_number_series">Número / Serie</th>
                        <th data-i18n="th_client">Cliente</th>
                        <th data-i18n="th_issue_date">Fecha Emisión</th>
                        <th data-i18n="th_due_date">Vencimiento</th>
                        <th style="text-align: right;" data-i18n="th_total">Total</th>
                        <th style="text-align: center;" data-i18n="th_status">Estado</th>
                        <th style="text-align: right; width: 140px;" data-i18n="th_actions">Acciones</th>
                    </tr>
                </thead>
                <tbody id="invoiceTableBody">
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="invoice-data-row" 
                            data-status="<?php echo e(strtolower($inv->status->value)); ?>" 
                            data-is-due="<?php echo e(($inv->due_date && $inv->due_date->isPast() && $inv->status->value !== 'paid') ? '1' : '0'); ?>"
                            data-search-term="<?php echo e(strtolower(($inv->full_correlative ?? '') . ' ' . ($inv->customer->trade_name ?? '') . ' ' . ($inv->customer->legal_name ?? '') . ' ' . ($inv->customer->doc_number ?? ''))); ?>">
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
                        <tr id="emptyTableRow">
                            <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 12px;">🧾</div>
                                <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main);" data-i18n="no_invoices_found">No se encontraron facturas</div>
                                <p style="font-size: 0.88rem; margin-top: 6px;" data-i18n="no_invoices_sub">Comienza creando tu primera factura o emitiendo tareas registradas.</p>
                                <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary" style="margin-top: 16px;" data-i18n="btn_create_first">
                                    + Crear Primera Factura
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    
                    <!-- Dynamic client-side empty state when filter finds nothing -->
                    <tr id="noResultsFilterRow" style="display: none;">
                        <td colspan="7" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                            <div style="font-size: 2rem; margin-bottom: 8px;">🔍</div>
                            <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">No hay comprobantes para este filtro o búsqueda</div>
                        </td>
                    </tr>
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
    .apex-invoice-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .invoice-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--text-main);
    }

    .invoice-subtitle {
        color: var(--text-muted);
        font-size: 0.88rem;
        margin-top: 4px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: var(--surface, #ffffff);
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
        color: var(--text-main);
    }
    .stat-sub {
        font-size: 0.75rem;
        color: var(--text-light);
    }

    /* Tabs Header */
    .apex-tabs-header {
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
        gap: 4px;
    }
    .tab-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 14px;
        background: transparent;
        border: none;
        border-bottom: 2.5px solid transparent;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .tab-btn:hover {
        color: var(--primary);
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        font-weight: 700;
    }
    .tab-count {
        background: var(--surface-subtle, #f1f5f9);
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 9999px;
        color: var(--text-main, #475569);
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
        background: var(--surface-subtle, #f8fafc);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 6px 12px;
        color: var(--text-muted);
    }
    .search-input {
        background: transparent;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 0.85rem;
        color: var(--text-main);
        width: 220px;
    }

    /* List Table */
    .apex-list-table {
        width: 100%;
        border-collapse: collapse;
    }
    .apex-list-table th {
        background: var(--surface-subtle, #fafafa);
        padding: 12px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
    }
    .apex-list-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        font-size: 0.88rem;
        color: var(--text-main);
    }
    .apex-list-table tr:hover td {
        background: var(--surface-subtle, #f8fafc);
    }
    .invoice-number-link {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
    }
    .invoice-number-link:hover {
        text-decoration: underline;
    }
    .doc-badge {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 700;
        background: var(--surface-subtle, #f1f5f9);
        color: var(--text-muted);
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 6px;
    }
    .client-name {
        font-weight: 600;
        color: var(--text-main);
    }
    .client-doc {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        color: var(--text-light);
    }
    .amount-val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: var(--text-main);
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
        background: var(--surface-subtle, #f8fafc);
        border: 1px solid var(--border);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .action-btn:hover {
        background: var(--surface-hover, #f1f5f9);
        color: var(--primary);
        border-color: var(--primary);
    }
</style>

<script>
    let activeFilterStatus = '<?php echo e($status ?? "all"); ?>';
    let currentSearchTerm = '';

    // Instant 0ms Tab Filtering
    function filterTab(status, clickedBtn) {
        activeFilterStatus = status;

        // Update active class immediately
        document.querySelectorAll('#invoiceTabsGroup .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Update URL quietly without page reload
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        window.history.replaceState({}, '', url.toString());

        // Perform instant DOM row filter
        applyInstantFilter();
    }

    // Instant Keystroke Search
    function handleInstantSearch(term) {
        currentSearchTerm = term.toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            clearBtn.style.display = currentSearchTerm ? 'block' : 'none';
        }
        applyInstantFilter();
    }

    function clearSearch() {
        const input = document.getElementById('invoiceLiveSearch');
        if (input) {
            input.value = '';
            handleInstantSearch('');
        }
    }

    function applyInstantFilter() {
        const rows = document.querySelectorAll('#invoiceTableBody .invoice-data-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const isDue = row.getAttribute('data-is-due') === '1';
            const searchTerm = row.getAttribute('data-search-term') || '';

            let matchesTab = false;
            if (activeFilterStatus === 'all') {
                matchesTab = true;
            } else if (activeFilterStatus === 'due') {
                matchesTab = isDue || rowStatus === 'due';
            } else {
                matchesTab = (rowStatus === activeFilterStatus);
            }

            let matchesSearch = true;
            if (currentSearchTerm) {
                matchesSearch = searchTerm.includes(currentSearchTerm);
            }

            if (matchesTab && matchesSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty message
        const noResultsRow = document.getElementById('noResultsFilterRow');
        if (noResultsRow) {
            if (rows.length > 0 && visibleCount === 0) {
                noResultsRow.style.display = '';
            } else {
                noResultsRow.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyInstantFilter();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/invoices/index.blade.php ENDPATH**/ ?>
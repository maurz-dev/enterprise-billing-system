<?php $__env->startSection('title', 'Kardex & Stock'); ?>

<?php $__env->startSection('content'); ?>
<div class="apex-invoice-container">
    <!-- Header -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title" data-i18n="inventory_title">Kardex & Inventario</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;" data-i18n="inventory_subtitle">
                Control multialmacén de existencias, alertas de stock mínimo y valoración por Costo Promedio Ponderado (WAC)
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span data-i18n="btn_new_invoice">+ Nueva Factura</span>
            </a>
        </div>
    </div>

    <!-- Inventory Metrics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label" data-i18n="total_units">Unidades en Stock</span>
            <span class="stat-value" style="color: var(--primary);"><?php echo e(number_format($totalStockUnits, 2)); ?></span>
            <small class="stat-sub" data-i18n="total_units_sub">Suma física en todos los almacenes</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="active_variants">Productos Activos</span>
            <span class="stat-value"><?php echo e($totalVariantsCount); ?></span>
            <small class="stat-sub" data-i18n="active_variants_sub">Ítems catalogados en sistema</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="low_stock">Alertas Stock Crítico</span>
            <span class="stat-value" style="color: var(--danger);"><?php echo e($lowStockCount); ?></span>
            <small class="stat-sub" data-i18n="low_stock_sub">Ítems con stock menor al mínimo</small>
        </div>
        <div class="stat-card">
            <span class="stat-label" data-i18n="active_warehouse">Almacén Principal</span>
            <span class="stat-value" style="font-size: 1.25rem; font-weight: 700; color: #10b981;">ALM-01</span>
            <small class="stat-sub">Sede Central Miraflores</small>
        </div>
    </div>

    <!-- Stock Table Card -->
    <div class="apex-card" style="padding: 0;">
        <div class="apex-tabs-header">
            <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);" data-i18n="stock_levels_title">
                Niveles de Existencias por Almacén
            </div>

            <!-- Search Filter -->
            <form action="<?php echo e(route('inventory.index')); ?>" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Buscar por SKU o producto..." class="search-input">
                    <?php if($search): ?>
                        <a href="<?php echo e(route('inventory.index')); ?>" style="font-size: 0.8rem; color: var(--text-light); text-decoration: none;">&times;</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="apex-list-table">
                <thead>
                    <tr>
                        <th data-i18n="th_sku">SKU / Código</th>
                        <th data-i18n="th_product">Producto / Descripción</th>
                        <th data-i18n="th_category">Categoría</th>
                        <th data-i18n="th_unit">Unidad</th>
                        <th style="text-align: right;" data-i18n="th_stock">Stock Actual</th>
                        <th style="text-align: right;" data-i18n="th_min_stock">Stock Mín.</th>
                        <th style="text-align: right;" data-i18n="th_cost">Costo Promedio (CPP)</th>
                        <th style="text-align: center;" data-i18n="th_status">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $currentQty = $v->stocks->sum('quantity');
                            $isLow = $currentQty <= $v->min_stock;
                        ?>
                        <tr>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary);">
                                    <?php echo e($v->sku); ?>

                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);"><?php echo e($v->product->name); ?></div>
                                <?php if($v->name): ?>
                                    <small style="color: var(--text-muted);"><?php echo e($v->name); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--surface-subtle); color: var(--text-muted);">
                                    <?php echo e($v->product->category->name ?? 'General'); ?>

                                </span>
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                <?php echo e($v->product->unit->code ?? 'NIU'); ?>

                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 1rem;">
                                <?php echo e(number_format($currentQty, 2)); ?>

                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                                <?php echo e(number_format($v->min_stock, 2)); ?>

                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 600;">
                                S/ <?php echo e(number_format($v->purchase_price, 2)); ?>

                            </td>
                            <td style="text-align: center;">
                                <?php if($currentQty <= 0): ?>
                                    <span class="badge badge-due">Agotado</span>
                                <?php elseif($isLow): ?>
                                    <span class="badge" style="background: #fffbeb; color: #b45309;">Stock Bajo</span>
                                <?php else: ?>
                                    <span class="badge badge-paid">Óptimo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 36px 20px; color: var(--text-muted);">
                                No se encontraron productos en el almacén.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($variants->hasPages()): ?>
            <div style="padding: 16px 24px; border-top: 1px solid var(--border);">
                <?php echo e($variants->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Kardex Movements Ledger Table -->
    <div class="apex-card" style="margin-top: 24px; padding: 0;">
        <div class="apex-tabs-header">
            <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);" data-i18n="kardex_ledger_title">
                Libro Mayor de Movimientos (Kardex Valorizado)
            </div>
        </div>

        <div class="table-responsive">
            <table class="apex-list-table">
                <thead>
                    <tr>
                        <th data-i18n="th_date">Fecha / Hora</th>
                        <th data-i18n="th_product">Producto</th>
                        <th data-i18n="th_warehouse">Almacén</th>
                        <th data-i18n="th_direction">Tipo</th>
                        <th data-i18n="th_reason">Motivo</th>
                        <th style="text-align: right;" data-i18n="th_qty">Cantidad</th>
                        <th style="text-align: right;" data-i18n="th_unit_cost">Costo Unitario</th>
                        <th style="text-align: right;" data-i18n="th_balance">Saldo Resultante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="date-col">
                                <?php echo e($m->created_at ? $m->created_at->format('d/m/Y H:i') : '-'); ?>

                            </td>
                            <td>
                                <strong style="color: var(--text-main);"><?php echo e($m->variant->product->name ?? 'Producto'); ?></strong>
                                <small style="display: block; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);"><?php echo e($m->variant->sku ?? ''); ?></small>
                            </td>
                            <td><?php echo e($m->warehouse->name ?? 'Almacén 01'); ?></td>
                            <td>
                                <?php if($m->direction->value === 'in'): ?>
                                    <span class="badge badge-paid">Entrada (+)</span>
                                <?php else: ?>
                                    <span class="badge badge-due">Salida (-)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?php echo e($m->reason->label() ?? $m->reason->value); ?>

                                </span>
                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                <?php echo e(number_format($m->quantity, 2)); ?>

                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                                S/ <?php echo e(number_format($m->unit_cost, 4)); ?>

                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary);">
                                <?php echo e(number_format($m->stock_after, 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                No se registran movimientos recientes en el Kardex.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/inventory/index.blade.php ENDPATH**/ ?>
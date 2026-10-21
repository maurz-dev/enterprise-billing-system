<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($invoice->full_correlative ?? 'Comprobante'); ?></title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2b2f3a;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .company-col {
            width: 60%;
            vertical-align: top;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .company-info {
            color: #475569;
            font-size: 10px;
        }
        .fiscal-box-col {
            width: 40%;
            vertical-align: top;
            text-align: center;
        }
        .fiscal-box {
            border: 2px solid #0f172a;
            border-radius: 6px;
            padding: 12px;
            background-color: #f8fafc;
        }
        .fiscal-ruc {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .fiscal-type {
            font-size: 14px;
            font-weight: bold;
            margin: 6px 0;
            color: #0284c7;
            text-transform: uppercase;
        }
        .fiscal-number {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }
        .customer-card {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        .customer-card table {
            width: 100%;
        }
        .customer-card td {
            padding: 3px 6px;
            font-size: 10.5px;
        }
        .label {
            font-weight: bold;
            color: #64748b;
            width: 15%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            margin-top: 10px;
        }
        .totals-col-left {
            width: 60%;
            vertical-align: top;
            padding-right: 20px;
        }
        .totals-col-right {
            width: 40%;
            vertical-align: top;
        }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 5px 8px;
            font-size: 11px;
        }
        .summary-box tr.grand-total td {
            border-top: 2px solid #0f172a;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            background-color: #f1f5f9;
        }
        .notes-box {
            border-left: 3px solid #0284c7;
            padding-left: 10px;
            margin-top: 10px;
            color: #475569;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-issued {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-voided {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="company-col">
                <div class="company-name"><?php echo e($invoice->company->legal_name); ?></div>
                <div class="company-info">
                    <strong>RUC:</strong> <?php echo e($invoice->company->tax_id); ?><br>
                    <strong>Dirección Fiscal:</strong> <?php echo e($invoice->company->fiscal_address); ?><br>
                    <strong>Sucursal:</strong> <?php echo e($invoice->branch->name); ?> - <?php echo e($invoice->branch->address); ?><br>
                    <strong>Contacto:</strong> <?php echo e($invoice->company->email); ?> | <?php echo e($invoice->company->phone); ?>

                </div>
            </td>
            <td class="fiscal-box-col">
                <div class="fiscal-box">
                    <div class="fiscal-ruc">R.U.C. N° <?php echo e($invoice->company->tax_id); ?></div>
                    <div class="fiscal-type"><?php echo e($invoice->doc_type->label()); ?></div>
                    <div class="fiscal-number"><?php echo e($invoice->full_correlative ?? 'BORRADOR'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer Information Card -->
    <div class="customer-card">
        <table>
            <tr>
                <td class="label">CLIENTE:</td>
                <td><strong><?php echo e($invoice->customer->legal_name); ?></strong></td>
                <td class="label">FECHA EMISIÓN:</td>
                <td><?php echo e(CarbonCarbon::parse($invoice->issue_date)->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td class="label"><?php echo e($invoice->customer->doc_type); ?>:</td>
                <td><?php echo e($invoice->customer->doc_number); ?></td>
                <td class="label">FECHA VENC.:</td>
                <td><?php echo e($invoice->due_date ? CarbonCarbon::parse($invoice->due_date)->format('d/m/Y') : '-'); ?></td>
            </tr>
            <tr>
                <td class="label">DIRECCIÓN:</td>
                <td><?php echo e($invoice->customer->addresses->first()->line ?? 'No especificada'); ?></td>
                <td class="label">MONEDA:</td>
                <td><?php echo e($invoice->currency); ?> (Tipo de Cambio: <?php echo e(number_format($invoice->exchange_rate, 2)); ?>)</td>
            </tr>
            <tr>
                <td class="label">CONDICIÓN:</td>
                <td><?php echo e(strtoupper($invoice->payment_condition->value)); ?></td>
                <td class="label">ESTADO:</td>
                <td>
                    <span class="badge-status badge-<?php echo e($invoice->status->value); ?>">
                        <?php echo e(strtoupper($invoice->status->value)); ?>

                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">CANT.</th>
                <th class="text-center" style="width: 10%;">UNIDAD</th>
                <th>DESCRIPCIÓN</th>
                <th class="text-right" style="width: 14%;">P. UNITARIO</th>
                <th class="text-right" style="width: 10%;">DESC.</th>
                <th class="text-right" style="width: 14%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e(number_format($item->quantity, 2)); ?></td>
                <td class="text-center"><?php echo e($item->unit_code); ?></td>
                <td>
                    <strong><?php echo e($item->description); ?></strong>
                    <?php if($item->variant && $item->variant->sku): ?>
                        <br><span style="color: #64748b; font-size: 9px;">SKU: <?php echo e($item->variant->sku); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-right"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($item->discount_amount, 2)); ?></td>
                <td class="text-right"><strong><?php echo e(number_format($item->total, 2)); ?></strong></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Totals and Summary -->
    <table class="totals-table">
        <tr>
            <td class="totals-col-left">
                <?php if($invoice->notes): ?>
                <div class="notes-box">
                    <strong>Observaciones:</strong><br>
                    <?php echo e($invoice->notes); ?>

                </div>
                <?php endif; ?>
                <div style="margin-top: 15px; font-size: 9px; color: #64748b;">
                    Representación impresa de la Factura Electrónica.<br>
                    Consulte la validez de este comprobante mediante nuestro portal web.
                </div>
            </td>
            <td class="totals-col-right">
                <table class="summary-box">
                    <tr>
                        <td class="label">OP. GRAVADA:</td>
                        <td class="text-right"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->subtotal, 2)); ?></td>
                    </tr>
                    <?php if($invoice->discount_total > 0): ?>
                    <tr>
                        <td class="label">DESCUENTO:</td>
                        <td class="text-right">- <?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->discount_total, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">I.G.V. (18%):</td>
                        <td class="text-right"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->tax_total, 2)); ?></td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL A PAGAR:</td>
                        <td class="text-right"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->total, 2)); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Generado automáticamente por Enterprise Billing Engine | Desarrollado por @maurz-dev | Página 1 de 1
    </div>

</body>
</html>
<?php /**PATH /var/www/resources/views/pdf/invoice.blade.php ENDPATH**/ ?>
<?php

namespace App\Services;

use App\Enums\DocType;
use App\Enums\InvoiceStatus;
use App\Exceptions\BusinessException;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Crea un comprobante en estado borrador (draft) calculando subtotales e impuestos.
     */
    public function createDraft(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $invoice = new Invoice($data);
            $invoice->uuid = (string) Str::uuid();
            $invoice->status = InvoiceStatus::DRAFT;
            $invoice->save();

            $subtotal = 0.0;
            $discountTotal = 0.0;
            $taxTotal = 0.0;
            $total = 0.0;

            foreach ($items as $itemData) {
                $qty = (float) $itemData['quantity'];
                $price = (float) $itemData['unit_price'];
                $discount = (float) ($itemData['discount_amount'] ?? 0.0);
                $taxRate = (float) ($itemData['tax_rate'] ?? 0.0);

                $itemSubtotal = ($qty * $price) - $discount;
                $itemTax = round($itemSubtotal * ($taxRate / 100), 2);
                $itemTotal = $itemSubtotal + $itemTax;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_variant_id' => $itemData['product_variant_id'] ?? null,
                    'description' => $itemData['description'],
                    'unit_code' => $itemData['unit_code'] ?? 'NIU',
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_amount' => $discount,
                    'tax_id' => $itemData['tax_id'] ?? null,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemSubtotal;
                $discountTotal += $discount;
                $taxTotal += $itemTax;
                $total += $itemTotal;
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => $taxTotal,
                'total' => $total,
            ]);

            return $invoice->load('items');
        });
    }

    /**
     * Emite formalmente el comprobante: bloquea correlativo con lockForUpdate(), descuenta inventario y fija número.
     */
    public function issue(Invoice $invoice, string $seriesPrefix): Invoice
    {
        if ($invoice->status !== InvoiceStatus::DRAFT) {
            throw new BusinessException("Solo se pueden emitir comprobantes en estado borrador. Estado actual: {$invoice->status->value}.");
        }

        return DB::transaction(function () use ($invoice, $seriesPrefix) {
            // Bloqueo pesimista del correlativo de la sucursal para garantizar orden atómico
            $seriesRecord = DocumentSeries::where('branch_id', $invoice->branch_id)
                ->where('doc_type', $invoice->doc_type)
                ->where('series', $seriesPrefix)
                ->lockForUpdate()
                ->first();

            if (!$seriesRecord) {
                throw new BusinessException("No existe la serie [{$seriesPrefix}] para el tipo de documento [{$invoice->doc_type->value}] en esta sucursal.");
            }

            if (!$seriesRecord->is_active) {
                throw new BusinessException("La serie [{$seriesPrefix}] se encuentra inactiva.");
            }

            $nextNumber = $seriesRecord->current_number + 1;
            $seriesRecord->update(['current_number' => $nextNumber]);

            $invoice->update([
                'series' => $seriesPrefix,
                'number' => $nextNumber,
                'status' => InvoiceStatus::ISSUED,
                'issued_at' => now(),
            ]);

            // Descontar inventario mediante Kardex
            $this->inventoryService->recordSaleMovement($invoice);

            return $invoice->fresh(['items', 'customer', 'branch']);
        });
    }

    /**
     * Anula un comprobante emitido y devuelve los productos al stock mediante Kardex.
     */
    public function void(Invoice $invoice, string $reason): Invoice
    {
        if ($invoice->status === InvoiceStatus::VOIDED) {
            throw new BusinessException('El comprobante ya fue anulado previamente.');
        }

        if ($invoice->status === InvoiceStatus::DRAFT) {
            throw new BusinessException('Un borrador no se anula fiscalmente; simplemente se descarta o elimina.');
        }

        return DB::transaction(function () use ($invoice, $reason) {
            $invoice->update([
                'status' => InvoiceStatus::VOIDED,
                'voided_at' => now(),
                'void_reason' => $reason,
            ]);

            // Revertir inventario en Kardex
            $this->inventoryService->recordVoidMovement($invoice);

            return $invoice->fresh();
        });
    }

    /**
     * Registra un pago/cobro para la factura y actualiza automáticamente su saldo.
     */
    public function registerPayment(Invoice $invoice, array $data): InvoicePayment
    {
        if ($invoice->status === InvoiceStatus::VOIDED) {
            throw new BusinessException('No se pueden registrar cobros a comprobantes anulados.');
        }

        $amount = (float) $data['amount'];
        $pending = $invoice->pending_amount;

        if ($amount <= 0) {
            throw new BusinessException('El monto del cobro debe ser mayor a cero.');
        }

        if ($amount > $pending) {
            throw new BusinessException("El monto a pagar (${$amount}) excede el saldo pendiente (${$pending}).");
        }

        return DB::transaction(function () use ($invoice, $data, $amount, $pending) {
            $payment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'payment_method_id' => $data['payment_method_id'],
                'cash_session_id' => $data['cash_session_id'] ?? null,
                'user_id' => $data['user_id'],
                'amount' => $amount,
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
            ]);

            $newPaidTotal = (float) $invoice->paid_total + $amount;
            $newStatus = ($newPaidTotal >= (float) $invoice->total)
                ? InvoiceStatus::PAID
                : InvoiceStatus::PARTIALLY_PAID;

            $invoice->update([
                'paid_total' => $newPaidTotal,
                'status' => $newStatus,
            ]);

            return $payment;
        });
    }
}

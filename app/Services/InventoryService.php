<?php

namespace App\Services;

use App\Enums\StockMovementDirection;
use App\Enums\StockMovementReason;
use App\Exceptions\BusinessException;
use App\Models\Invoice;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Registra un movimiento inmutable en el Kardex y actualiza el saldo de stock en tiempo real.
     */
    public function recordMovement(
        int $companyId,
        int $warehouseId,
        int $productVariantId,
        StockMovementDirection $direction,
        StockMovementReason $reason,
        float $quantity,
        float $unitCost,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new BusinessException('La cantidad del movimiento debe ser mayor a cero.');
        }

        return DB::transaction(function () use (
            $companyId,
            $warehouseId,
            $productVariantId,
            $direction,
            $reason,
            $quantity,
            $unitCost,
            $reference,
            $userId,
            $notes
        ) {
            // Bloqueo pesimista del registro de stock para evitar condiciones de carrera
            $stock = Stock::where('product_variant_id', $productVariantId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            $previousQuantity = $stock ? (float) $stock->quantity : 0.0;
            $previousAvgCost = $stock ? (float) $stock->avg_cost : 0.0;

            if ($direction === StockMovementDirection::OUT) {
                if ($previousQuantity < $quantity) {
                    $variant = ProductVariant::find($productVariantId);
                    $name = $variant ? $variant->sku : "ID {$productVariantId}";
                    throw new BusinessException("Stock insuficiente para el producto [{$name}]. Saldo actual: {$previousQuantity}, requerido: {$quantity}.");
                }

                $newQuantity = $previousQuantity - $quantity;
                // En salidas, el costo promedio ponderado no cambia
                $newAvgCost = $previousAvgCost;
            } else {
                $newQuantity = $previousQuantity + $quantity;
                // En entradas, se recalcula el Costo Promedio Ponderado
                if ($newQuantity > 0) {
                    $totalValue = ($previousQuantity * $previousAvgCost) + ($quantity * $unitCost);
                    $newAvgCost = round($totalValue / $newQuantity, 6);
                } else {
                    $newAvgCost = $unitCost;
                }
            }

            // Actualizar o crear registro en tabla stocks
            if ($stock) {
                $stock->update([
                    'quantity' => $newQuantity,
                    'avg_cost' => $newAvgCost,
                ]);
            } else {
                $stock = Stock::create([
                    'product_variant_id' => $productVariantId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $newQuantity,
                    'avg_cost' => $newAvgCost,
                ]);
            }

            // Registrar movimiento append-only en Kardex
            return StockMovement::create([
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $productVariantId,
                'direction' => $direction,
                'reason' => $reason,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => round($quantity * $unitCost, 6),
                'balance_quantity' => $newQuantity,
                'balance_avg_cost' => $newAvgCost,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->getKey() : null,
                'user_id' => $userId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Rebaja stock al emitir una factura/boleta
     */
    public function recordSaleMovement(Invoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            if (!$item->product_variant_id) {
                continue;
            }

            $variant = $item->variant;
            if (!$variant || !$variant->product->tracks_inventory) {
                continue;
            }

            $currentStock = Stock::where('product_variant_id', $item->product_variant_id)
                ->where('warehouse_id', $invoice->warehouse_id)
                ->first();

            $unitCost = $currentStock ? (float) $currentStock->avg_cost : 0.0;

            // Almacenar snapshot del costo en el ítem de la factura para cálculo de margen
            $item->update(['unit_cost' => $unitCost]);

            $this->recordMovement(
                companyId: $invoice->company_id,
                warehouseId: $invoice->warehouse_id,
                productVariantId: $item->product_variant_id,
                direction: StockMovementDirection::OUT,
                reason: StockMovementReason::SALE,
                quantity: (float) $item->quantity,
                unitCost: $unitCost,
                reference: $invoice,
                userId: $invoice->user_id,
                notes: "Venta comprobante {$invoice->full_correlative}"
            );
        }
    }

    /**
     * Revierte el stock al anular una factura
     */
    public function recordVoidMovement(Invoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            if (!$item->product_variant_id) {
                continue;
            }

            $variant = $item->variant;
            if (!$variant || !$variant->product->tracks_inventory) {
                continue;
            }

            $this->recordMovement(
                companyId: $invoice->company_id,
                warehouseId: $invoice->warehouse_id,
                productVariantId: $item->product_variant_id,
                direction: StockMovementDirection::IN,
                reason: StockMovementReason::VOID,
                quantity: (float) $item->quantity,
                unitCost: (float) $item->unit_cost,
                reference: $invoice,
                userId: $invoice->user_id,
                notes: "Anulación de comprobante {$invoice->full_correlative}"
            );
        }
    }
}

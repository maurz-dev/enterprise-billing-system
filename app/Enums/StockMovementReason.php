<?php

namespace App\Enums;

enum StockMovementReason: string
{
    case INITIAL = 'initial';
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RETURN = 'return';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';
    case VOID = 'void';

    public function label(): string
    {
        return match ($this) {
            self::INITIAL => 'Inventario Inicial',
            self::PURCHASE => 'Compra / Entrada',
            self::SALE => 'Venta / Facturación',
            self::RETURN => 'Devolución de Cliente',
            self::ADJUSTMENT => 'Ajuste de Auditoría',
            self::TRANSFER => 'Transferencia entre Almacenes',
            self::VOID => 'Anulación de Comprobante',
        };
    }
}

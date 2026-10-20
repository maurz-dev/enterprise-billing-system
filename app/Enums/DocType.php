<?php

namespace AppEnums;

enum DocType: string
{
    case INVOICE = '01';
    case BOLETA = '03';
    case CREDIT_NOTE = '07';
    case DEBIT_NOTE = '08';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE => 'Factura Electrónica',
            self::BOLETA => 'Boleta de Venta',
            self::CREDIT_NOTE => 'Nota de Crédito',
            self::DEBIT_NOTE => 'Nota de Débito',
        };
    }
}

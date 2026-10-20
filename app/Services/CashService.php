<?php

namespace AppServices;

use AppEnumsCashSessionStatus;
use AppExceptionsBusinessException;
use AppModelsCashMovement;
use AppModelsCashRegister;
use AppModelsCashSession;
use AppModelsCashSessionCount;
use IlluminateSupportFacadesDB;

class CashService
{
    /**
     * Abre un nuevo turno de caja. La restricción UNIQUE(cash_register_id, open_flag)
     * garantiza atómicamente a nivel de base de datos que no existan dos turnos abiertos.
     */
    public function openSession(int $cashRegisterId, int $userId, float $openingAmount, ?string $notes = null): CashSession
    {
        if ($openingAmount < 0) {
            throw new BusinessException('El monto de apertura de caja no puede ser negativo.');
        }

        $activeSession = CashSession::where('cash_register_id', $cashRegisterId)
            ->where('status', CashSessionStatus::OPEN)
            ->first();

        if ($activeSession) {
            throw new BusinessException("La caja registradora ya tiene un turno abierto (Sesión #{$activeSession->id}).");
        }

        return CashSession::create([
            'cash_register_id' => $cashRegisterId,
            'opened_by' => $userId,
            'status' => CashSessionStatus::OPEN,
            'open_flag' => true,
            'opening_amount' => $openingAmount,
            'notes' => $notes,
            'opened_at' => now(),
        ]);
    }

    /**
     * Cierra el turno de caja, concilia el efectivo esperado vs real y guarda el arqueo físico.
     */
    public function closeSession(
        CashSession $session,
        int $userId,
        float $actualCash,
        array $denominations = [],
        ?string $notes = null
    ): CashSession {
        if ($session->status !== CashSessionStatus::OPEN) {
            throw new BusinessException('Esta sesión de caja ya se encuentra cerrada.');
        }

        return DB::transaction(function () use ($session, $userId, $actualCash, $denominations, $notes) {
            // Calcular ingresos en efectivo de comprobantes cobrados durante el turno
            $cashPayments = (float) $session->payments()
                ->whereHas('paymentMethod', fn ($q) => $q->where('type', 'cash'))
                ->sum('amount');

            // Calcular movimientos manuales de caja (ingresos y egresos)
            $manualIn = (float) $session->movements()->where('type', 'in')->sum('amount');
            $manualOut = (float) $session->movements()->where('type', 'out')->sum('amount');

            $expectedCash = (float) $session->opening_amount + $cashPayments + $manualIn - $manualOut;
            $difference = round($actualCash - $expectedCash, 2);

            // Guardar arqueo detallado de billetes y monedas
            foreach ($denominations as $count) {
                CashSessionCount::create([
                    'cash_session_id' => $session->id,
                    'denomination' => (float) $count['denomination'],
                    'quantity' => (int) $count['quantity'],
                    'total' => round((float) $count['denomination'] * (int) $count['quantity'], 2),
                ]);
            }

            // Al cerrar, open_flag pasa a NULL: esto libera la restricción única para futuros turnos
            $session->update([
                'closed_by' => $userId,
                'status' => CashSessionStatus::CLOSED,
                'open_flag' => null,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
                'notes' => $notes ? ($session->notes . "
" . $notes) : $session->notes,
                'closed_at' => now(),
            ]);

            return $session->fresh(['counts', 'register']);
        });
    }

    /**
     * Registra un movimiento manual de entrada o salida de efectivo (ej. retiro para cambio o pago a proveedor en caja).
     */
    public function addMovement(
        CashSession $session,
        int $userId,
        string $type,
        float $amount,
        int $paymentMethodId,
        string $reason
    ): CashMovement {
        if ($session->status !== CashSessionStatus::OPEN) {
            throw new BusinessException('No se pueden registrar movimientos en una caja cerrada.');
        }

        if ($amount <= 0) {
            throw new BusinessException('El monto del movimiento debe ser superior a cero.');
        }

        return CashMovement::create([
            'cash_session_id' => $session->id,
            'payment_method_id' => $paymentMethodId,
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'reason' => $reason,
        ]);
    }
}

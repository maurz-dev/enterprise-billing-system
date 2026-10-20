<?php

namespace AppHttpControllers;

use AppModelsCashSession;
use AppServicesCashService;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class CashSessionController extends Controller
{
    public function __construct(
        protected CashService $cashService
    ) {}

    public function open(Request $request): JsonResponse
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $userId = $request->user()?->id ?? 1;
        $session = $this->cashService->openSession(
            cashRegisterId: $request->cash_register_id,
            userId: $userId,
            openingAmount: (float) $request->opening_amount,
            notes: $request->notes
        );

        return response()->json([
            'message' => 'Turno de caja abierto correctamente.',
            'data' => $session,
        ], 201);
    }

    public function close(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'denominations' => 'nullable|array',
            'denominations.*.denomination' => 'required_with:denominations|numeric',
            'denominations.*.quantity' => 'required_with:denominations|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $session = CashSession::findOrFail($id);
        $userId = $request->user()?->id ?? 1;

        $closed = $this->cashService->closeSession(
            session: $session,
            userId: $userId,
            actualCash: (float) $request->actual_cash,
            denominations: $request->get('denominations', []),
            notes: $request->notes
        );

        return response()->json([
            'message' => 'Turno de caja cerrado con arqueo físico completado.',
            'data' => $closed,
        ]);
    }

    public function addMovement(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reason' => 'required|string|max:200',
        ]);

        $session = CashSession::findOrFail($id);
        $userId = $request->user()?->id ?? 1;

        $movement = $this->cashService->addMovement(
            session: $session,
            userId: $userId,
            type: $request->type,
            amount: (float) $request->amount,
            paymentMethodId: $request->payment_method_id,
            reason: $request->reason
        );

        return response()->json([
            'message' => 'Movimiento de caja registrado exitosamente.',
            'data' => $movement,
        ], 201);
    }
}

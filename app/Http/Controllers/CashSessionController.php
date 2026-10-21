<?php

namespace AppHttpControllers;

use AppHttpRequestsAddCashMovementRequest;
use AppHttpRequestsCloseCashSessionRequest;
use AppHttpRequestsOpenCashSessionRequest;
use AppHttpResourcesCashSessionResource;
use AppModelsCashSession;
use AppServicesCashService;
use IlluminateHttpJsonResponse;

class CashSessionController extends Controller
{
    public function __construct(
        protected CashService $cashService
    ) {}

    public function open(OpenCashSessionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()?->id ?? 1;

        $session = $this->cashService->openSession(
            cashRegisterId: (int) $validated['cash_register_id'],
            userId: $userId,
            openingAmount: (float) $validated['opening_amount'],
            notes: $validated['notes'] ?? null
        );

        return response()->json([
            'message' => 'Turno de caja abierto correctamente.',
            'data' => new CashSessionResource($session),
        ], 201);
    }

    public function close(CloseCashSessionRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $session = CashSession::findOrFail($id);
        $userId = $request->user()?->id ?? 1;

        $closed = $this->cashService->closeSession(
            session: $session,
            userId: $userId,
            actualCash: (float) $validated['actual_cash'],
            denominations: $validated['denominations'] ?? [],
            notes: $validated['notes'] ?? null
        );

        return response()->json([
            'message' => 'Turno de caja cerrado con arqueo físico completado.',
            'data' => new CashSessionResource($closed),
        ]);
    }

    public function addMovement(AddCashMovementRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $session = CashSession::findOrFail($id);
        $userId = $request->user()?->id ?? 1;

        $movement = $this->cashService->addMovement(
            session: $session,
            userId: $userId,
            type: $validated['type'],
            amount: (float) $validated['amount'],
            paymentMethodId: (int) $validated['payment_method_id'],
            reason: $validated['reason']
        );

        return response()->json([
            'message' => 'Movimiento de caja registrado exitosamente.',
            'data' => $movement,
        ], 201);
    }
}

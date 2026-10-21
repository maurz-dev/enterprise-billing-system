<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\CashSession;
use App\Models\PaymentMethod;
use App\Services\CashService;
use Illuminate\Http\Request;

class PosWebController extends Controller
{
    public function __construct(
        protected CashService $cashService
    ) {}

    public function index(Request $request)
    {
        $branches = Branch::where('is_active', true)->get();
        $registers = CashRegister::with('branch')->where('is_active', true)->get();
        $paymentMethods = PaymentMethod::all();

        // Active open session
        $activeSession = CashSession::with(['register', 'user', 'movements.paymentMethod', 'payments.invoice'])
            ->where('open_flag', true)
            ->latest()
            ->first();

        // Recent closed sessions
        $recentSessions = CashSession::with(['register', 'user'])
            ->whereNull('open_flag')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $totalClosedSessionsCount = CashSession::whereNull('open_flag')->count();
        $totalRegistersCount = $registers->count();
        $currentCashInDrawer = $activeSession 
            ? ($activeSession->opening_balance + $activeSession->movements->where('type', 'inflow')->sum('amount') - $activeSession->movements->where('type', 'outflow')->sum('amount'))
            : 0.0;

        return view('pos.index', compact(
            'branches',
            'registers',
            'paymentMethods',
            'activeSession',
            'recentSessions',
            'totalClosedSessionsCount',
            'totalRegistersCount',
            'currentCashInDrawer'
        ));
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $session = $this->cashService->openSession(
            registerId: (int) $request->cash_register_id,
            userId: 1,
            openingBalance: (float) $request->opening_balance,
            notes: $request->notes
        );

        return redirect()->route('pos.index')
            ->with('success', "Turno de caja abierto exitosamente (Sesión #{$session->id}).");
    }

    public function closeSession(Request $request, int $id)
    {
        $session = CashSession::findOrFail($id);
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
        ]);

        $closed = $this->cashService->closeSession(
            session: $session,
            userId: 1,
            physicalCount: (float) $request->closing_balance,
            closingNotes: $request->closing_notes
        );

        $varianceText = $closed->cash_difference != 0
            ? " Descuadre detectado: S/ {$closed->cash_difference}"
            : " Cuadre de caja exacto.";

        return redirect()->route('pos.index')
            ->with('success', "Turno de caja cerrado exitosamente.{$varianceText}");
    }
}

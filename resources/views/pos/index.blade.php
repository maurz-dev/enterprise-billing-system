@extends('layouts.app')

@section('title', 'Caja POS & Turnos')

@section('content')
<div class="apex-invoice-container">
    <!-- Header -->
    <div class="invoice-header-row">
        <div>
            <h1 class="invoice-title" data-i18n="pos_title">Caja POS & Control de Turnos</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;" data-i18n="pos_subtitle">
                Gestión atómica de turnos de cajero, arqueos físicos y conciliación de cobros en efectivo y pasarelas
            </p>
        </div>

        <div>
            @if($activeSession)
                <button type="button" class="btn btn-secondary" style="border-color: #ef4444; color: #ef4444;" onclick="openCloseSessionModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                    </svg>
                    <span data-i18n="btn_close_shift">Cerrar Turno de Caja</span>
                </button>
            @else
                <button type="button" class="btn btn-primary" onclick="openOpenSessionModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span data-i18n="btn_open_shift">Abrir Turno de Caja</span>
                </button>
            @endif
        </div>
    </div>

    <!-- POS Metrics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Estado de Caja</span>
            <span class="stat-value" style="color: {{ $activeSession ? '#10b981' : 'var(--text-muted)' }};">
                {{ $activeSession ? 'EN SERVICIO' : 'CERRADA' }}
            </span>
            <small class="stat-sub">{{ $activeSession ? $activeSession->register->name : 'Sin sesión abierta' }}</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Efectivo en Gaveta</span>
            <span class="stat-value" style="color: var(--primary);">S/ {{ number_format($currentCashInDrawer, 2) }}</span>
            <small class="stat-sub">Monto físico estimado</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Turnos Auditoría</span>
            <span class="stat-value">{{ $totalClosedSessionsCount }}</span>
            <small class="stat-sub">Arqueos históricos cerrados</small>
        </div>
        <div class="stat-card">
            <span class="stat-label">Terminales POS</span>
            <span class="stat-value" style="color: var(--accent);">{{ $totalRegistersCount }}</span>
            <small class="stat-sub">Cajas registradoras en sede</small>
        </div>
    </div>

    <!-- Active Terminal Status Banner -->
    <div class="apex-card" style="margin-bottom: 24px; border-left: 5px solid {{ $activeSession ? '#10b981' : '#94a3b8' }};">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="pulse-indicator" style="background: {{ $activeSession ? '#10b981' : '#94a3b8' }};"></span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                        {{ $activeSession ? 'Turno Activo en Terminal' : 'No Hay Turnos Abiertos Actualmente' }}
                    </h3>
                </div>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
                    @if($activeSession)
                        Terminal: <strong>{{ $activeSession->register->name }}</strong> • Cajero: <strong>{{ $activeSession->user->name ?? 'David Bomba' }}</strong> • Iniciado el {{ $activeSession->opened_at->format('d/m/Y H:i') }}
                    @else
                        Abre un turno para registrar cobros en el terminal y permitir ventas POS.
                    @endif
                </p>
            </div>

            @if($activeSession)
                <div style="display: flex; gap: 24px;">
                    <div>
                        <span class="stat-label" data-i18n="opening_balance">Saldo Inicial</span>
                        <span class="stat-value" style="font-size: 1.3rem;">S/ {{ number_format($activeSession->opening_balance, 2) }}</span>
                    </div>
                    <div>
                        <span class="stat-label" data-i18n="cash_collected">Efectivo Recaudado</span>
                        <span class="stat-value" style="font-size: 1.3rem; color: #10b981;">
                            S/ {{ number_format($activeSession->movements->where('type', 'inflow')->sum('amount'), 2) }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Closed Sessions History Table -->
    <div class="apex-card" style="padding: 0;">
        <div class="apex-tabs-header">
            <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);" data-i18n="closed_shifts_history">
                Historial de Turnos y Auditoría de Descuadres
            </div>
        </div>

        <div class="table-responsive">
            <table class="apex-list-table">
                <thead>
                    <tr>
                        <th data-i18n="th_session_id">Sesión #</th>
                        <th data-i18n="th_register">Caja Registradora</th>
                        <th data-i18n="th_cashier">Cajero</th>
                        <th data-i18n="th_opened_at">Apertura</th>
                        <th data-i18n="th_closed_at">Cierre</th>
                        <th style="text-align: right;" data-i18n="th_opening_amt">Monto Inicial</th>
                        <th style="text-align: right;" data-i18n="th_physical_amt">Conteo Físico</th>
                        <th style="text-align: center;" data-i18n="th_variance">Descuadre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSessions as $s)
                        <tr>
                            <td>
                                <strong style="font-family: 'JetBrains Mono', monospace; color: var(--primary);">#{{ $s->id }}</strong>
                            </td>
                            <td>{{ $s->register->name ?? 'Caja 01' }}</td>
                            <td>{{ $s->user->name ?? 'Cajero 01' }}</td>
                            <td class="date-col">{{ $s->opened_at ? $s->opened_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="date-col">{{ $s->closed_at ? $s->closed_at->format('d/m/Y H:i') : '-' }}</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                                S/ {{ number_format($s->opening_balance, 2) }}
                            </td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                S/ {{ number_format($s->closing_balance, 2) }}
                            </td>
                            <td style="text-align: center;">
                                @if($s->cash_difference == 0)
                                    <span class="badge badge-paid">Exacto (S/ 0.00)</span>
                                @elseif($s->cash_difference > 0)
                                    <span class="badge" style="background: #ecfdf5; color: #047857;">Sobrante (+S/ {{ number_format($s->cash_difference, 2) }})</span>
                                @else
                                    <span class="badge badge-due">Faltante (S/ {{ number_format($s->cash_difference, 2) }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 24px; color: var(--text-muted);" data-i18n="empty_shifts">
                                No hay turnos cerrados en el historial reciente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Abrir Turno de Caja -->
<div id="openSessionModal" class="apex-modal" style="display: none;">
    <div class="apex-modal-backdrop" onclick="closeOpenSessionModal()"></div>
    <div class="apex-modal-dialog">
        <form action="{{ route('pos.open') }}" method="POST">
            @csrf
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main);" data-i18n="modal_open_shift_title">Abrir Turno de Caja</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;" data-i18n="modal_open_shift_desc">
                Inicia un turno formal en el terminal asignado. La restricción única de base de datos previene la apertura de turnos duplicados.
            </p>

            <div style="margin-bottom: 16px;">
                <label class="apex-label" data-i18n="lbl_select_register">Caja Registradora:</label>
                <select name="cash_register_id" class="apex-select" required>
                    @foreach($registers as $r)
                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->branch->name ?? 'Sede Central' }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="apex-label" data-i18n="lbl_initial_cash">Fondo Inicial de Caja (Soles):</label>
                <input type="number" step="0.01" min="0" name="opening_balance" class="apex-input" placeholder="Ej: 150.00" value="150.00" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label class="apex-label" data-i18n="lbl_notes">Observaciones:</label>
                <input type="text" name="notes" class="apex-input" placeholder="Apertura de turno mañana...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeOpenSessionModal()" data-i18n="btn_cancel">Cancelar</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_confirm_open">Abrir Turno</button>
            </div>
        </form>
    </div>
</div>

@if($activeSession)
<!-- Modal: Cerrar Turno de Caja -->
<div id="closeSessionModal" class="apex-modal" style="display: none;">
    <div class="apex-modal-backdrop" onclick="closeCloseSessionModal()"></div>
    <div class="apex-modal-dialog">
        <form action="{{ route('pos.close', $activeSession->id) }}" method="POST">
            @csrf
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main);" data-i18n="modal_close_shift_title">Cerrar Turno & Conteo Físico</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;" data-i18n="modal_close_shift_desc">
                Ingresa el monto total de dinero contado en el cajón para calcular automáticamente cualquier variación o descuadre.
            </p>

            <div style="margin-bottom: 16px;">
                <label class="apex-label" data-i18n="lbl_physical_counted">Monto Físico Contado (Soles):</label>
                <input type="number" step="0.01" min="0" name="closing_balance" class="apex-input" placeholder="0.00" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label class="apex-label" data-i18n="lbl_closing_notes">Notas de Cierre / Justificación:</label>
                <input type="text" name="closing_notes" class="apex-input" placeholder="Arqueo verificado...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeCloseSessionModal()" data-i18n="btn_cancel">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444;" data-i18n="btn_confirm_close">Cerrar Turno</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    window.openOpenSessionModal = function() {
        const m = document.getElementById('openSessionModal');
        if (m) m.style.display = 'flex';
    };
    window.closeOpenSessionModal = function() {
        const m = document.getElementById('openSessionModal');
        if (m) m.style.display = 'none';
    };
    window.openCloseSessionModal = function() {
        const m = document.getElementById('closeSessionModal');
        if (m) m.style.display = 'flex';
    };
    window.closeCloseSessionModal = function() {
        const m = document.getElementById('closeSessionModal');
        if (m) m.style.display = 'none';
    };
</script>
@endsection

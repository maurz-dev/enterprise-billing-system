<!-- Task & Time Tracker Slide-over Drawer -->
<div id="timerDrawerOverlay" class="timer-drawer-overlay" onclick="toggleTimerDrawer()"></div>

<div id="timerDrawer" class="timer-drawer" aria-labelledby="timerDrawerTitle" role="dialog" aria-modal="true">
    <!-- Header: Avatar, Info, Icons -->
    <div class="timer-header">
        <div class="timer-header-left">
            <div class="timer-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="timer-header-info">
                <span class="timer-title" id="timerDrawerTitle" data-i18n="timer_title">Control de Tareas & Tiempo</span>
                <span class="timer-subtitle" data-i18n="timer_subtitle">Módulo Integrado • Facturación</span>
            </div>
        </div>
        <div class="timer-header-actions">
            <button type="button" class="timer-icon-btn" title="Buscar tareas" onclick="document.getElementById('taskDescInput').focus()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <button type="button" class="timer-icon-btn" title="Cerrar panel" onclick="toggleTimerDrawer()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Active Stopwatch Display Banner (When running) -->
    <div id="activeStopwatchBanner" class="active-stopwatch-banner" style="display: none;">
        <div class="stopwatch-details">
            <span class="pulse-indicator"></span>
            <div>
                <div class="stopwatch-task-title" id="activeTaskTitle">Tarea activa</div>
                <div class="stopwatch-counter" id="activeCounter">00:00:00</div>
            </div>
        </div>
        <button type="button" class="btn-stop-timer" onclick="stopActiveTimer()" title="Detener y registrar tiempo">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <rect x="6" y="6" width="12" height="12" rx="2"></rect>
            </svg>
            <span data-i18n="stop_timer">Detener</span>
        </button>
    </div>

    <!-- Start a timer Section -->
    <div class="timer-body">
        <div class="start-timer-header">
            <h3 data-i18n="start_timer">Iniciar Registro</h3>
            <div class="timer-control-buttons">
                <!-- Checkmark action (Save/Create Task) -->
                <button type="button" class="btn-circle btn-circle-check" id="btnSaveTaskManual" onclick="saveTaskManual()" title="Guardar registro de tarea">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
                <!-- Play/Clock action (Start Live Stopwatch) -->
                <button type="button" class="btn-circle btn-circle-timer" id="btnStartLiveTimer" onclick="startNewLiveTimer()" title="Iniciar cronómetro en vivo">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Form Elements -->
        <div class="timer-form">
            <!-- Date Picker -->
            <div class="form-group">
                <label for="taskDate" data-i18n="date">Fecha</label>
                <div class="input-with-icon">
                    <input type="date" id="taskDate" class="timer-input" value="<?php echo e(date('Y-m-d')); ?>">
                </div>
            </div>

            <!-- Start & end vs. Hours Mode Selector -->
            <div class="mode-toggle-group">
                <button type="button" class="mode-toggle-btn" id="modeStartEnd" onclick="setTimerMode('range')" data-i18n="start_and_end">Inicio & Fin</button>
                <button type="button" class="mode-toggle-btn active" id="modeHours" onclick="setTimerMode('hours')" data-i18n="hours_mode">Horas</button>
            </div>

            <!-- Hours Input -->
            <div class="form-group" id="hoursGroup">
                <label for="taskHours" data-i18n="hours">Horas</label>
                <input type="number" step="0.1" min="0" id="taskHours" class="timer-input" placeholder="1" value="1.0">
                <small class="timer-hint" data-i18n="hours_hint">Ejemplo: ingresa 1.5 para 1 hora 30 minutos.</small>
            </div>

            <!-- Start & End inputs (Shown when mode is range) -->
            <div class="form-group" id="rangeGroup" style="display: none;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label for="startTime" data-i18n="start">Inicio</label>
                        <input type="time" id="startTime" class="timer-input" value="09:00">
                    </div>
                    <div>
                        <label for="endTime" data-i18n="end">Fin</label>
                        <input type="time" id="endTime" class="timer-input" value="10:30">
                    </div>
                </div>
            </div>

            <!-- Task Description -->
            <div class="form-group">
                <input type="text" id="taskDescInput" class="timer-input timer-input-desc" placeholder="Descripción de la tarea...">
            </div>

            <!-- Metadata Pills: Client, Project, Assignee, Status -->
            <div class="timer-pills-row">
                <div class="pill-dropdown-wrapper">
                    <select id="taskClientSelect" class="timer-pill-select" title="Seleccionar Cliente">
                        <option value="" data-i18n="select_client">👤 Cliente</option>
                    </select>
                </div>

                <div class="pill-dropdown-wrapper">
                    <select id="taskProjectSelect" class="timer-pill-select" title="Seleccionar Proyecto">
                        <option value="" data-i18n="select_project">📁 Proyecto</option>
                    </select>
                </div>

                <div class="pill-badge" title="Asignado a">
                    <span>DB</span> <span data-i18n="assignee">Asignado</span>
                </div>

                <div class="pill-badge status-logged" id="taskStatusPill" title="Estado" data-i18n="status">
                    Estado
                </div>
            </div>
        </div>

        <hr class="timer-divider">

        <!-- Recent Timers Section -->
        <div class="recent-timers-section">
            <div class="recent-header">
                <h4 data-i18n="recent">Recientes</h4>
                <button type="button" class="btn-text-primary" onclick="refreshTasksList()" data-i18n="refresh">Actualizar</button>
            </div>

            <div class="recent-list" id="recentTasksList">
                <!-- Loaded dynamically by JS -->
                <div class="task-loading">Cargando tareas recientes...</div>
            </div>
        </div>
    </div>

    <!-- Bottom Actions: Convert to Invoice & Tabs -->
    <div class="timer-footer">
        <!-- Billing Integration Button -->
        <div class="invoice-bridge-box">
            <button type="button" class="btn-bridge-invoice" onclick="convertSelectedTasksToInvoice()" id="btnInvoiceTasks">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <span data-i18n="bill_tasks">Facturar Tareas Seleccionadas</span>
            </button>
        </div>

        <!-- Today & History Tabs -->
        <div class="timer-tabs">
            <button type="button" class="timer-tab active" id="tabToday" onclick="switchTimerTab('today')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span data-i18n="today">Hoy</span>
            </button>
            <button type="button" class="timer-tab" id="tabHistory" onclick="switchTimerTab('history')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v4l3 3"></path>
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
                <span data-i18n="history">Historial</span>
            </button>
        </div>
    </div>
</div>

<style>
    /* Drawer Overlay */
    .timer-drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        z-index: 80;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
    }
    .timer-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    /* Drawer Container (Right side) */
    .timer-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 420px;
        max-width: 95vw;
        background: var(--surface, #ffffff);
        box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2);
        z-index: 90;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        border-left: 1px solid var(--border);
    }
    .timer-drawer.open {
        transform: translateX(0);
    }

    /* Header */
    .timer-header {
        height: 64px;
        padding: 0 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface, #ffffff);
    }
    .timer-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .timer-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--accent-gradient, linear-gradient(135deg, #2563eb, #06b6d4));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.25);
    }
    .timer-header-info {
        display: flex;
        flex-direction: column;
    }
    .timer-title {
        font-size: 0.96rem;
        font-weight: 800;
        color: var(--text-main, #0f172a);
    }
    .timer-subtitle {
        font-size: 0.72rem;
        color: var(--text-muted, #64748b);
        font-weight: 600;
    }
    .timer-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .timer-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: var(--text-muted, #64748b);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .timer-icon-btn:hover {
        background: var(--surface-subtle, #f1f5f9);
        color: var(--text-main, #0f172a);
    }

    /* Active Stopwatch Banner */
    .active-stopwatch-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.15);
    }
    .stopwatch-details {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pulse-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3);
        animation: pulseGreen 1.5s infinite;
    }
    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .stopwatch-task-title {
        font-size: 0.8rem;
        color: #e0f2fe;
        font-weight: 600;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stopwatch-counter {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .btn-stop-timer {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ef4444;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-stop-timer:hover {
        background: #dc2626;
        transform: scale(1.03);
    }

    /* Body & Start a timer Section */
    .timer-body {
        padding: 20px;
        flex: 1;
        overflow-y: auto;
        background: var(--surface, #ffffff);
    }
    .start-timer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .start-timer-header h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main, #0f172a);
        letter-spacing: -0.02em;
    }
    .timer-control-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        color: white;
    }
    .btn-circle-check {
        background: var(--primary, #2563eb);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }
    .btn-circle-check:hover {
        background: var(--primary-hover, #1d4ed8);
        transform: scale(1.05);
    }
    .btn-circle-timer {
        background: var(--accent, #06b6d4);
        box-shadow: 0 4px 10px rgba(6, 182, 212, 0.3);
    }
    .btn-circle-timer:hover {
        background: #0891b2;
        transform: scale(1.05);
    }

    /* Form Fields */
    .timer-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted, #64748b);
        margin-bottom: 6px;
    }
    .timer-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.92rem;
        color: var(--text-main, #0f172a);
        background: var(--surface-bg, #ffffff);
        outline: none;
        transition: border-color 0.2s ease;
    }
    .timer-input:focus {
        border-color: var(--primary, #2563eb);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .timer-input-desc {
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        font-weight: 500;
    }
    .timer-hint {
        display: block;
        font-size: 0.74rem;
        color: var(--text-light, #94a3b8);
        margin-top: 4px;
    }

    /* Mode Toggle */
    .mode-toggle-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: var(--surface-subtle, #f1f5f9);
        padding: 4px;
        border-radius: var(--radius-sm);
        gap: 4px;
    }
    .mode-toggle-btn {
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted, #64748b);
        background: transparent;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .mode-toggle-btn.active {
        background: var(--surface, #ffffff);
        color: var(--primary, #2563eb);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        font-weight: 700;
    }

    /* Pills Row */
    .timer-pills-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 4px;
    }
    .timer-pill-select {
        background: var(--surface-subtle, #f1f5f9);
        border: 1px solid var(--border);
        border-radius: 9999px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-main, #475569);
        cursor: pointer;
        outline: none;
    }
    .timer-pill-select:hover {
        background: var(--surface-hover, #e2e8f0);
    }
    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--surface-subtle, #f1f5f9);
        border: 1px solid var(--border);
        border-radius: 9999px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-main, #475569);
    }
    .pill-badge span {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--primary-light, #eff6ff);
        color: var(--primary, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 800;
    }

    .timer-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 22px 0;
    }

    /* Recent Timers List */
    .recent-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .recent-header h4 {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main, #0f172a);
    }
    .btn-text-primary {
        background: none;
        border: none;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--primary, #2563eb);
        cursor: pointer;
    }
    .recent-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .recent-item-card {
        background: var(--surface, #ffffff);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .recent-item-card:hover {
        border-color: var(--primary, #2563eb);
        box-shadow: var(--shadow-subtle);
    }
    .recent-item-card.is-selected {
        border-color: var(--primary, #2563eb);
        background: var(--primary-light, #eff6ff);
    }
    .recent-item-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }
    .recent-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent, #06b6d4);
        flex-shrink: 0;
    }
    .recent-task-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-main, #1e293b);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .recent-item-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .recent-duration {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-muted, #475569);
    }
    .btn-play-resume {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--surface-subtle, #f1f5f9);
        border: none;
        color: var(--primary, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-play-resume:hover {
        background: var(--primary, #2563eb);
        color: white;
        transform: scale(1.08);
    }
    .btn-popout-edit {
        background: none;
        border: none;
        color: var(--text-light, #94a3b8);
        cursor: pointer;
    }
    .btn-popout-edit:hover {
        color: var(--text-main, #475569);
    }

    /* Bottom Actions & Tabs */
    .timer-footer {
        border-top: 1px solid var(--border);
        background: var(--surface, #ffffff);
        padding: 12px 20px 0;
    }
    .invoice-bridge-box {
        margin-bottom: 12px;
    }
    .btn-bridge-invoice {
        width: 100%;
        background: var(--accent-gradient, linear-gradient(135deg, #2563eb 0%, #06b6d4 100%));
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        font-size: 0.88rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: all 0.2s ease;
    }
    .btn-bridge-invoice:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .timer-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid var(--border);
    }
    .timer-tab {
        padding: 12px;
        background: none;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-muted, #64748b);
        cursor: pointer;
        position: relative;
        transition: color 0.15s ease;
    }
    .timer-tab:hover {
        color: var(--primary, #2563eb);
    }
    .timer-tab.active {
        color: var(--primary, #2563eb);
        font-weight: 700;
    }
    .timer-tab.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20%;
        right: 20%;
        height: 3px;
        background: var(--primary, #2563eb);
        border-radius: 3px 3px 0 0;
    }
</style>

<script>
    let activeTimerState = null;
    let stopwatchInterval = null;
    let currentTimerMode = 'hours';
    let loadedTasks = [];
    let selectedTaskIdsForBilling = new Set();

    // Toggle Drawer Open/Close
    function toggleTimerDrawer() {
        const drawer = document.getElementById('timerDrawer') || document.getElementById('ninjaDrawer');
        const overlay = document.getElementById('timerDrawerOverlay') || document.getElementById('ninjaDrawerOverlay');
        const isOpen = drawer.classList.contains('open');

        if (isOpen) {
            drawer.classList.remove('open');
            overlay.classList.remove('open');
        } else {
            drawer.classList.add('open');
            overlay.classList.add('open');
            refreshTasksList();
        }
    }

    // Set Timer Mode (Hours vs Range)
    function setTimerMode(mode) {
        currentTimerMode = mode;
        const btnHours = document.getElementById('modeHours');
        const btnRange = document.getElementById('modeStartEnd');
        const groupHours = document.getElementById('hoursGroup');
        const groupRange = document.getElementById('rangeGroup');

        if (mode === 'hours') {
            btnHours.classList.add('active');
            btnRange.classList.remove('active');
            groupHours.style.display = 'block';
            groupRange.style.display = 'none';
        } else {
            btnRange.classList.add('active');
            btnHours.classList.remove('active');
            groupHours.style.display = 'none';
            groupRange.style.display = 'block';
        }
    }

    // Format Seconds to HH:MM:SS
    function formatTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    // Fetch and populate tasks
    async function refreshTasksList() {
        try {
            const res = await fetch("<?php echo e(route('tasks.index')); ?>");
            const data = await res.json();
            loadedTasks = data.tasks || [];

            // Populate Clients dropdown
            const clientSelect = document.getElementById('taskClientSelect');
            if (clientSelect) {
                clientSelect.innerHTML = '<option value="">👤 Cliente</option>';
                (data.clients || []).forEach(c => {
                    clientSelect.innerHTML += `<option value="${c.id}">${c.trade_name || c.legal_name}</option>`;
                });
            }

            // Populate Projects dropdown
            const projectSelect = document.getElementById('taskProjectSelect');
            if (projectSelect) {
                projectSelect.innerHTML = '<option value="">📁 Proyecto</option>';
                (data.projects || []).forEach(p => {
                    projectSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            }

            // Render Recent list
            renderRecentList(data.recent || loadedTasks);

            // Handle active stopwatch if any
            if (data.active_timer) {
                startLocalStopwatch(data.active_timer);
            } else {
                clearLocalStopwatch();
            }
        } catch (e) {
            console.error("Error cargando tareas:", e);
        }
    }

    // Render Recent tasks list
    function renderRecentList(tasks) {
        const list = document.getElementById('recentTasksList');
        if (!list) return;

        if (tasks.length === 0) {
            list.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px; font-size: 0.85rem;">No hay tareas registradas aún.</div>';
            return;
        }

        list.innerHTML = '';
        tasks.forEach(t => {
            const isRunning = activeTimerState && activeTimerState.task_id === t.id;
            const durationFormatted = formatTime(t.total_seconds || 0);
            const isChecked = selectedTaskIdsForBilling.has(t.id);

            const card = document.createElement('div');
            card.className = `recent-item-card ${isChecked ? 'is-selected' : ''}`;
            card.innerHTML = `
                <div class="recent-item-left">
                    <input type="checkbox" ${isChecked ? 'checked' : ''} onchange="toggleSelectTask(${t.id}, this.checked)" title="Seleccionar para facturar">
                    <span class="recent-dot" style="${isRunning ? 'background: #22c55e;' : ''}"></span>
                    <div style="min-width: 0;">
                        <div class="recent-task-name" title="${t.title}">${t.title}</div>
                        <small style="font-size: 0.72rem; color: var(--text-muted);">${t.project ? t.project.name : 'General'} • S/ ${parseFloat(t.hourly_rate).toFixed(2)}/h</small>
                    </div>
                </div>
                <div class="recent-item-right">
                    <span class="recent-duration" id="task-duration-${t.id}">${durationFormatted}</span>
                    <button type="button" class="btn-play-resume" onclick="handleTimerPlayPause(${t.id})" title="${isRunning ? 'Pausar' : 'Reanudar'}">
                        ${isRunning ? `
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="6" y="4" width="4" height="16"></rect>
                                <rect x="14" y="4" width="4" height="16"></rect>
                            </svg>
                        ` : `
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        `}
                    </button>
                    <button type="button" class="btn-popout-edit" onclick="prefillTask(${t.id})" title="Editar o cargar datos">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </button>
                </div>
            `;
            list.appendChild(card);
        });
    }

    // Toggle task selection for billing
    function toggleSelectTask(id, checked) {
        if (checked) {
            selectedTaskIdsForBilling.add(id);
        } else {
            selectedTaskIdsForBilling.delete(id);
        }
        updateBillingButtonBadge();
    }

    function updateBillingButtonBadge() {
        const btn = document.getElementById('btnInvoiceTasks');
        if (!btn) return;
        const count = selectedTaskIdsForBilling.size;
        if (count > 0) {
            btn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span>Facturar (${count}) Tareas Seleccionadas</span>
            `;
        } else {
            btn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span>Facturar Tareas Seleccionadas</span>
            `;
        }
    }

    // Convert selected tasks to New Invoice
    function convertSelectedTasksToInvoice() {
        let taskIds = Array.from(selectedTaskIdsForBilling);
        if (taskIds.length === 0) {
            taskIds = loadedTasks.filter(t => t.total_seconds > 0 || t.hours > 0).map(t => t.id).slice(0, 3);
        }

        if (taskIds.length === 0) {
            showToast('Primero registra o selecciona al menos una tarea con tiempo.', 'warning');
            return;
        }

        showToast('Generando borrador de factura con las tareas seleccionadas...');
        window.location.href = `<?php echo e(route('invoices.create')); ?>?tasks=${taskIds.join(',')}`;
    }

    // Start New Live Timer from input field
    async function startNewLiveTimer() {
        const title = document.getElementById('taskDescInput').value.trim() || 'Tarea sin título';
        const projectId = document.getElementById('taskProjectSelect').value || null;
        const clientId = document.getElementById('taskClientSelect').value || null;

        try {
            const res = await fetch("<?php echo e(route('tasks.store')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: title,
                    project_id: projectId,
                    contact_id: clientId,
                    start_timer_now: true
                })
            });

            const data = await res.json();
            if (data.status === 'ok') {
                showToast(`✓ Cronómetro iniciado: ${title}`);
                document.getElementById('taskDescInput').value = '';
                refreshTasksList();
            }
        } catch (e) {
            showToast('Error al iniciar el temporizador', 'danger');
        }
    }

    // Save task manually with hours (Check button)
    async function saveTaskManual() {
        const title = document.getElementById('taskDescInput').value.trim();
        if (!title) {
            showToast('Por favor escribe una descripción para la tarea.', 'warning');
            document.getElementById('taskDescInput').focus();
            return;
        }

        let hours = 1.0;
        if (currentTimerMode === 'hours') {
            hours = parseFloat(document.getElementById('taskHours').value) || 1.0;
        } else {
            const start = document.getElementById('startTime').value;
            const end = document.getElementById('endTime').value;
            if (start && end) {
                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);
                hours = Math.max(0.1, ((eh * 60 + em) - (sh * 60 + sm)) / 60);
            }
        }

        const projectId = document.getElementById('taskProjectSelect').value || null;
        const clientId = document.getElementById('taskClientSelect').value || null;
        const dueDate = document.getElementById('taskDate').value;

        try {
            const res = await fetch("<?php echo e(route('tasks.store')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: title,
                    hours: hours,
                    project_id: projectId,
                    contact_id: clientId,
                    due_date: dueDate,
                    start_timer_now: false
                })
            });

            const data = await res.json();
            if (data.status === 'ok') {
                showToast(`✓ Tarea guardada con ${hours.toFixed(2)} horas`);
                document.getElementById('taskDescInput').value = '';
                refreshTasksList();
            }
        } catch (e) {
            showToast('Error al registrar la tarea', 'danger');
        }
    }

    // Toggle play/pause on existing task
    async function handleTimerPlayPause(taskId) {
        if (activeTimerState && activeTimerState.task_id === taskId) {
            await stopActiveTimer();
        } else {
            try {
                const res = await fetch(`/tasks/${taskId}/start-timer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await res.json();
                if (data.status === 'ok') {
                    showToast(data.message);
                    refreshTasksList();
                }
            } catch (e) {
                showToast('Error al iniciar timer', 'danger');
            }
        }
    }

    // Stop currently running timer
    async function stopActiveTimer() {
        if (!activeTimerState) return;
        const taskId = activeTimerState.task_id;

        try {
            const res = await fetch(`/tasks/${taskId}/stop-timer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            if (data.status === 'ok') {
                showToast(`✓ Tiempo registrado: ${formatTime(data.elapsed_seconds || 0)}`);
                clearLocalStopwatch();
                refreshTasksList();
            }
        } catch (e) {
            showToast('Error al detener timer', 'danger');
        }
    }

    // Start local ticking stopwatch UI
    function startLocalStopwatch(timerInfo) {
        activeTimerState = timerInfo;
        const banner = document.getElementById('activeStopwatchBanner');
        const titleEl = document.getElementById('activeTaskTitle');
        const counterEl = document.getElementById('activeCounter');
        const globalBtn = document.getElementById('globalTimerBtn');
        const globalClock = document.getElementById('globalTimerClock');

        if (banner) banner.style.display = 'flex';
        if (titleEl) titleEl.innerText = timerInfo.task_title;
        if (globalBtn) globalBtn.classList.add('is-active');

        if (stopwatchInterval) clearInterval(stopwatchInterval);

        const startTime = new Date(timerInfo.started_at).getTime();

        function tick() {
            const now = new Date().getTime();
            const diffSeconds = Math.max(0, Math.floor((now - startTime) / 1000));
            const formatted = formatTime(diffSeconds);
            if (counterEl) counterEl.innerText = formatted;
            if (globalClock) globalClock.innerText = formatted;
        }

        tick();
        stopwatchInterval = setInterval(tick, 1000);
    }

    function clearLocalStopwatch() {
        activeTimerState = null;
        if (stopwatchInterval) {
            clearInterval(stopwatchInterval);
            stopwatchInterval = null;
        }
        const banner = document.getElementById('activeStopwatchBanner');
        const globalBtn = document.getElementById('globalTimerBtn');
        const globalClock = document.getElementById('globalTimerClock');

        if (banner) banner.style.display = 'none';
        if (globalBtn) globalBtn.classList.remove('is-active');
        if (globalClock) globalClock.innerText = '00:00:00';
    }

    function prefillTask(taskId) {
        const task = loadedTasks.find(t => t.id === taskId);
        if (task) {
            document.getElementById('taskDescInput').value = task.title;
            if (task.contact_id) document.getElementById('taskClientSelect').value = task.contact_id;
            if (task.project_id) document.getElementById('taskProjectSelect').value = task.project_id;
            document.getElementById('taskHours').value = (task.total_seconds > 0 ? (task.total_seconds / 3600).toFixed(1) : task.hours);
            showToast(`Cargados datos de "${task.title}"`);
        }
    }

    function switchTimerTab(tab) {
        document.getElementById('tabToday').classList.toggle('active', tab === 'today');
        document.getElementById('tabHistory').classList.toggle('active', tab === 'history');
        if (tab === 'history') {
            renderRecentList(loadedTasks);
        } else {
            renderRecentList(loadedTasks.slice(0, 4));
        }
    }

    // Auto load on page readiness
    document.addEventListener('DOMContentLoaded', () => {
        refreshTasksList();
    });
</script>
<?php /**PATH /var/www/resources/views/components/timer-drawer.blade.php ENDPATH**/ ?>
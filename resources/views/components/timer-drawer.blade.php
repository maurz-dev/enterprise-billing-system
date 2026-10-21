<!-- Invoice Ninja: Tasks & Time Tracker Slide-over Drawer (Image 1) -->
<div id="ninjaDrawerOverlay" class="ninja-drawer-overlay" onclick="toggleTimerDrawer()"></div>

<div id="ninjaDrawer" class="ninja-drawer" aria-labelledby="ninjaDrawerTitle" role="dialog" aria-modal="true">
    <!-- Header: Avatar, Analytics, Icons -->
    <div class="ninja-header">
        <div class="ninja-header-left">
            <div class="ninja-avatar">N</div>
            <div class="ninja-header-info">
                <span class="ninja-title" id="ninjaDrawerTitle">Invoice Ninja: Tasks</span>
                <span class="ninja-subtitle">Chrome Extension v2 • Companion</span>
            </div>
        </div>
        <div class="ninja-header-actions">
            <button type="button" class="ninja-icon-btn" title="Buscar tareas" onclick="document.getElementById('taskDescInput').focus()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <button type="button" class="ninja-icon-btn" title="Cerrar panel" onclick="toggleTimerDrawer()">
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
                <div class="stopwatch-task-title" id="activeTaskTitle">Self-serve billing upgrade</div>
                <div class="stopwatch-counter" id="activeCounter">00:00:00</div>
            </div>
        </div>
        <button type="button" class="btn-stop-timer" onclick="stopActiveTimer()" title="Detener y registrar tiempo">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <rect x="6" y="6" width="12" height="12" rx="2"></rect>
            </svg>
            <span>Detener</span>
        </button>
    </div>

    <!-- Start a timer Section (Image 1 layout) -->
    <div class="ninja-body">
        <div class="start-timer-header">
            <h3>Start a timer</h3>
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

        <!-- Form Elements matching Image 1 -->
        <div class="ninja-form">
            <!-- Date Picker -->
            <div class="form-group">
                <label for="taskDate">Date</label>
                <div class="input-with-icon">
                    <input type="date" id="taskDate" class="ninja-input" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <!-- Start & end vs. Hours Mode Selector -->
            <div class="mode-toggle-group">
                <button type="button" class="mode-toggle-btn" id="modeStartEnd" onclick="setTimerMode('range')">Start & end</button>
                <button type="button" class="mode-toggle-btn active" id="modeHours" onclick="setTimerMode('hours')">Hours</button>
            </div>

            <!-- Hours Input -->
            <div class="form-group" id="hoursGroup">
                <label for="taskHours">Hours</label>
                <input type="number" step="0.1" min="0" id="taskHours" class="ninja-input" placeholder="1" value="1.0">
                <small class="ninja-hint">For example, enter 1.5 for 1 hour 30 minutes.</small>
            </div>

            <!-- Start & End inputs (Shown when mode is range) -->
            <div class="form-group" id="rangeGroup" style="display: none;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label for="startTime">Start</label>
                        <input type="time" id="startTime" class="ninja-input" value="09:00">
                    </div>
                    <div>
                        <label for="endTime">End</label>
                        <input type="time" id="endTime" class="ninja-input" value="10:30">
                    </div>
                </div>
            </div>

            <!-- Task Description -->
            <div class="form-group">
                <input type="text" id="taskDescInput" class="ninja-input ninja-input-desc" placeholder="Add task description...">
            </div>

            <!-- Metadata Pills: Client, Project, Assignee, Status -->
            <div class="ninja-pills-row">
                <div class="pill-dropdown-wrapper">
                    <select id="taskClientSelect" class="ninja-pill-select" title="Seleccionar Cliente">
                        <option value="">👤 Client</option>
                    </select>
                </div>

                <div class="pill-dropdown-wrapper">
                    <select id="taskProjectSelect" class="ninja-pill-select" title="Seleccionar Proyecto">
                        <option value="">📁 Project</option>
                    </select>
                </div>

                <div class="pill-badge" title="Asignado a">
                    <span>DB</span> Assignee
                </div>

                <div class="pill-badge status-logged" id="taskStatusPill" title="Estado">
                    Status
                </div>
            </div>
        </div>

        <hr class="ninja-divider">

        <!-- Recent Timers Section (Image 1) -->
        <div class="recent-timers-section">
            <div class="recent-header">
                <h4>Recent</h4>
                <button type="button" class="btn-text-purple" onclick="refreshTasksList()">Actualizar</button>
            </div>

            <div class="recent-list" id="recentTasksList">
                <!-- Loaded dynamically by JS -->
                <div class="task-loading">Cargando tareas recientes...</div>
            </div>
        </div>
    </div>

    <!-- Bottom Actions: Convert to Crater Invoice & Tabs (Image 1) -->
    <div class="ninja-footer">
        <!-- Billing Integration Button (Bridge to Crater) -->
        <div class="invoice-bridge-box">
            <button type="button" class="btn-bridge-crater" onclick="convertSelectedTasksToInvoice()" id="btnInvoiceTasks">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <span>Facturar Tareas a Factura Crater</span>
            </button>
        </div>

        <!-- Today & History Tabs (Image 1) -->
        <div class="ninja-tabs">
            <button type="button" class="ninja-tab active" id="tabToday" onclick="switchNinjaTab('today')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Today</span>
            </button>
            <button type="button" class="ninja-tab" id="tabHistory" onclick="switchNinjaTab('history')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v4l3 3"></path>
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
                <span>History</span>
            </button>
        </div>
    </div>
</div>

<style>
    /* Drawer Overlay */
    .ninja-drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 80;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
    }
    .ninja-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    /* Drawer Container (Right side) */
    .ninja-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 420px;
        max-width: 95vw;
        background: #ffffff;
        box-shadow: -10px 0 35px rgba(0, 0, 0, 0.15);
        z-index: 90;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        border-left: 1px solid var(--border);
    }
    .ninja-drawer.open {
        transform: translateX(0);
    }

    /* Header */
    .ninja-header {
        height: 64px;
        padding: 0 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }
    .ninja-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ninja-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #0284c7;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.05rem;
    }
    .ninja-header-info {
        display: flex;
        flex-direction: column;
    }
    .ninja-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
    }
    .ninja-subtitle {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
    }
    .ninja-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ninja-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .ninja-icon-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Active Stopwatch Banner */
    .active-stopwatch-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #4338ca;
        animation: pulseSubtle 3s infinite;
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
        color: #c7d2fe;
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
    .ninja-body {
        padding: 20px;
        flex: 1;
        overflow-y: auto;
    }
    .start-timer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .start-timer-header h3 {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .timer-control-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-circle {
        width: 42px;
        height: 42px;
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
        background: #0284c7;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
    }
    .btn-circle-check:hover {
        background: #0369a1;
        transform: scale(1.05);
    }
    .btn-circle-timer {
        background: #0284c7;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
    }
    .btn-circle-timer:hover {
        background: #0369a1;
        transform: scale(1.05);
    }

    /* Form Fields */
    .ninja-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
    }
    .ninja-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.92rem;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .ninja-input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
    .ninja-input-desc {
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        font-weight: 500;
    }
    .ninja-hint {
        display: block;
        font-size: 0.74rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Mode Toggle */
    .mode-toggle-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #f1f5f9;
        padding: 4px;
        border-radius: var(--radius-sm);
        gap: 4px;
    }
    .mode-toggle-btn {
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .mode-toggle-btn.active {
        background: #ffffff;
        color: #0284c7;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        font-weight: 700;
    }

    /* Pills Row */
    .ninja-pills-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 4px;
    }
    .ninja-pill-select {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        outline: none;
    }
    .ninja-pill-select:hover {
        background: #e2e8f0;
    }
    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
    }
    .pill-badge span {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 800;
    }

    .ninja-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 22px 0;
    }

    /* Recent Timers List (Image 1) */
    .recent-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .recent-header h4 {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-text-purple {
        background: none;
        border: none;
        font-size: 0.78rem;
        font-weight: 700;
        color: #0284c7;
        cursor: pointer;
    }
    .recent-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .recent-item-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .recent-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .recent-item-card.is-selected {
        border-color: #0284c7;
        background: #f0f9ff;
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
        background: #38bdf8;
    }
    .recent-task-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1e293b;
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
        color: #475569;
    }
    .btn-play-resume {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-play-resume:hover {
        background: #0284c7;
        color: white;
        transform: scale(1.08);
    }
    .btn-popout-edit {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
    }
    .btn-popout-edit:hover {
        color: #475569;
    }

    /* Bottom Actions & Tabs */
    .ninja-footer {
        border-top: 1px solid var(--border);
        background: #ffffff;
        padding: 12px 20px 0;
    }
    .invoice-bridge-box {
        margin-bottom: 12px;
    }
    .btn-bridge-crater {
        width: 100%;
        background: linear-gradient(135deg, #6c5ce7 0%, #5850ec 100%);
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
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25);
        transition: all 0.2s ease;
    }
    .btn-bridge-crater:hover {
        background: linear-gradient(135deg, #5b4bd8 0%, #4f46e5 100%);
        transform: translateY(-1px);
    }
    .ninja-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid #f1f5f9;
    }
    .ninja-tab {
        padding: 12px;
        background: none;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        position: relative;
        transition: color 0.15s ease;
    }
    .ninja-tab:hover {
        color: #0284c7;
    }
    .ninja-tab.active {
        color: #0284c7;
        font-weight: 700;
    }
    .ninja-tab.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20%;
        right: 20%;
        height: 3px;
        background: #0284c7;
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
        const drawer = document.getElementById('ninjaDrawer');
        const overlay = document.getElementById('ninjaDrawerOverlay');
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
            const res = await fetch("{{ route('tasks.index') }}");
            const data = await res.json();
            loadedTasks = data.tasks || [];

            // Populate Clients dropdown
            const clientSelect = document.getElementById('taskClientSelect');
            if (clientSelect) {
                clientSelect.innerHTML = '<option value="">👤 Client</option>';
                (data.clients || []).forEach(c => {
                    clientSelect.innerHTML += `<option value="${c.id}">${c.trade_name || c.legal_name}</option>`;
                });
            }

            // Populate Projects dropdown
            const projectSelect = document.getElementById('taskProjectSelect');
            if (projectSelect) {
                projectSelect.innerHTML = '<option value="">📁 Project</option>';
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

    // Render Recent tasks list matching Image 1
    function renderRecentList(tasks) {
        const list = document.getElementById('recentTasksList');
        if (!list) return;

        if (tasks.length === 0) {
            list.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 20px; font-size: 0.85rem;">No hay tareas registradas aún.</div>';
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
                        <small style="font-size: 0.72rem; color: #94a3b8;">${t.project ? t.project.name : 'General'} • S/ ${parseFloat(t.hourly_rate).toFixed(2)}/h</small>
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
                <span>Facturar Tareas a Factura Crater</span>
            `;
        }
    }

    // Convert selected tasks to Crater New Invoice
    function convertSelectedTasksToInvoice() {
        let taskIds = Array.from(selectedTaskIdsForBilling);
        if (taskIds.length === 0) {
            // Select all billable tasks if none checked
            taskIds = loadedTasks.filter(t => t.total_seconds > 0 || t.hours > 0).map(t => t.id).slice(0, 3);
        }

        if (taskIds.length === 0) {
            showToast('Primero registra o selecciona al menos una tarea con tiempo.', 'warning');
            return;
        }

        showToast('Generando borrador de factura con las tareas seleccionadas...');
        window.location.href = `{{ route('invoices.create') }}?tasks=${taskIds.join(',')}`;
    }

    // Start New Live Timer from input field
    async function startNewLiveTimer() {
        const title = document.getElementById('taskDescInput').value.trim() || 'Tarea sin título';
        const projectId = document.getElementById('taskProjectSelect').value || null;
        const clientId = document.getElementById('taskClientSelect').value || null;

        try {
            const res = await fetch("{{ route('tasks.store') }}", {
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
            const res = await fetch("{{ route('tasks.store') }}", {
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

    function switchNinjaTab(tab) {
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

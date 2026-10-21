<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Enterprise Billing System') - Crater & Invoice Ninja</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --crater-purple: #6c5ce7;
            --crater-purple-hover: #5a45ff;
            --surface: #ffffff;
            --surface-subtle: #f8fafc;
            --surface-hover: #f1f5f9;
            --border: #e2e8f0;
            --border-focus: #c7d2fe;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --sidebar-width: 250px;
            --header-height: 64px;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-elevated: 0 10px 25px -5px rgba(99, 102, 241, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Styles (Crater theme) */
        .crater-sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            transition: var(--transition);
        }

        .crater-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            border-bottom: 1px solid var(--border);
        }

        .crater-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6c5ce7 0%, #6366f1 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.35);
        }

        .crater-brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #1e1b4b;
        }

        .crater-brand-title span {
            color: var(--crater-purple);
        }

        .crater-nav {
            padding: 20px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-light);
            padding: 10px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .nav-item:hover {
            background-color: var(--surface-subtle);
            color: var(--crater-purple);
            transform: translateX(3px);
        }

        .nav-item.active {
            background-color: #f5f3ff;
            color: var(--crater-purple);
            font-weight: 700;
        }

        .nav-item.active svg {
            stroke: var(--crater-purple);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--crater-purple);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
        }

        /* Main Workspace Wrapper */
        .crater-workspace {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* Top Bar */
        .crater-topbar {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(8px);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .branch-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--success);
            box-shadow: 0 0 0 2px var(--success-light);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Invoice Ninja Timer Trigger Button */
        .btn-ninja-timer {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: #ffffff;
            border: 1.5px solid #e0e7ff;
            color: var(--crater-purple);
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(99, 102, 241, 0.08);
            transition: var(--transition);
        }

        .btn-ninja-timer:hover {
            border-color: var(--crater-purple);
            background: #f5f3ff;
            transform: translateY(-1px);
        }

        .btn-ninja-timer.is-active {
            background: linear-gradient(135deg, #6c5ce7 0%, #4f46e5 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(108, 92, 231, 0.4);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(108, 92, 231, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(108, 92, 231, 0); }
        }

        .timer-time-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.95rem;
            letter-spacing: -0.02em;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Main Content Container */
        .crater-content {
            padding: 32px;
            flex: 1;
        }

        /* Buttons & Badges Common */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6c5ce7 0%, #5850ec 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5b4bd8 0%, #4f46e5 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(108, 92, 231, 0.4);
        }

        .btn-secondary {
            background: #ffffff;
            border-color: var(--border);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: var(--surface-subtle);
            border-color: #cbd5e1;
        }

        .btn-outline-purple {
            background: #ffffff;
            border: 1.5px solid var(--crater-purple);
            color: var(--crater-purple);
        }

        .btn-outline-purple:hover {
            background: #f5f3ff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-draft { background: #f1f5f9; color: #475569; }
        .badge-issued { background: #eff6ff; color: #2563eb; }
        .badge-paid { background: #ecfdf5; color: #059669; }
        .badge-due { background: #fef2f2; color: #dc2626; }
        .badge-voided { background: #fee2e2; color: #991b1b; }

        /* Card Layout */
        .crater-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            padding: 24px;
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-msg {
            background: #1e1b4b;
            color: white;
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            animation: slideInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .crater-sidebar { transform: translateX(-100%); }
            .crater-sidebar.open { transform: translateX(0); }
            .crater-workspace { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Crater Left Sidebar Navigation -->
    <aside class="crater-sidebar" id="craterSidebar">
        <div class="crater-brand">
            <div class="crater-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 10 10 0 0 0 0-20"></path>
                    <path d="M2 12h20"></path>
                </svg>
            </div>
            <div class="crater-brand-title">Crater <span>Billing</span></div>
        </div>

        <nav class="crater-nav">
            <span class="nav-label">Menú Principal</span>
            
            <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.index') ? 'active' : '' }}" id="nav-invoices">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>Facturas & Boletas</span>
            </a>

            <a href="{{ route('invoices.create') }}" class="nav-item {{ request()->routeIs('invoices.create') ? 'active' : '' }}" id="nav-create-invoice">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Nueva Factura</span>
                <span class="nav-badge">+</span>
            </a>

            <a href="javascript:void(0)" onclick="toggleTimerDrawer()" class="nav-item" id="nav-ninja-tasks">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Tareas & Timer (Ninja)</span>
            </a>

            <span class="nav-label">Operaciones & Almacén</span>

            <a href="javascript:void(0)" class="nav-item" title="Gestión de inventario y Kardex">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <span>Kardex & Stock</span>
            </a>

            <a href="javascript:void(0)" class="nav-item" title="Cajas registradoras y turnos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Caja POS & Turnos</span>
            </a>

            <span class="nav-label">Configuración</span>

            <a href="javascript:void(0)" class="nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>Ajustes & Series</span>
            </a>
        </nav>
    </aside>

    <!-- Workspace -->
    <div class="crater-workspace">
        <!-- Top Bar -->
        <header class="crater-topbar">
            <div class="topbar-left">
                <div class="branch-badge">
                    <span class="status-dot"></span>
                    <span>Sede Central Miraflores</span>
                    <span style="color: var(--text-light);">•</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">RUC 20601234567</span>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Invoice Ninja Timer Widget Trigger Button -->
                <button type="button" class="btn-ninja-timer" id="globalTimerBtn" onclick="toggleTimerDrawer()" title="Abrir panel de tareas y cronómetro Invoice Ninja">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span class="timer-time-display" id="globalTimerClock">00:00:00</span>
                    <span style="font-size: 0.75rem; background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 4px;">Tasks</span>
                </button>

                <!-- User Profile -->
                <div class="user-avatar" title="David Bomba (Administrador)">DB</div>
            </div>
        </header>

        <!-- Main View Content -->
        <main class="crater-content">
            @if(session('success'))
                <div style="background: #ecfdf5; border-left: 4px solid var(--success); color: #065f46; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>✓ {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #065f46;">&times;</button>
                </div>
            @endif

            @if(session('warning'))
                <div style="background: #fffbeb; border-left: 4px solid var(--warning); color: #92400e; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 600;">
                    ⚠ {{ session('warning') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div style="background: #fef2f2; border-left: 4px solid var(--danger); color: #991b1b; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                    <p style="font-weight: 700; margin-bottom: 6px;">Por favor corrige los siguientes errores:</p>
                    <ul style="padding-left: 20px; font-size: 0.9rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Invoice Ninja Slide-over Companion Drawer (Image 1) -->
    @include('components.timer-drawer')

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Global Toast Notification Helper
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast-msg';
            toast.innerHTML = `<span>⚡</span> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    </script>

    @stack('scripts')
</body>
</html>

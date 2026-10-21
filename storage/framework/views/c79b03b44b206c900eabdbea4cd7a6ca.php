<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Enterprise Billing System'); ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Modern FinTech Color System - Deep Sapphire & Electric Cyan */
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --primary-glow: rgba(37, 99, 235, 0.25);
            --accent: #06b6d4;
            --accent-gradient: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #06b6d4 100%);
            --surface: #ffffff;
            --surface-bg: #f8fafc;
            --surface-subtle: #f1f5f9;
            --surface-hover: #e2e8f0;
            --border: #e2e8f0;
            --border-focus: #93c5fd;
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
            --shadow-elevated: 0 10px 25px -5px rgba(37, 99, 235, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --surface: #1e293b;
            --surface-bg: #0f172a;
            --surface-subtle: #0f172a;
            --surface-hover: #334155;
            --border: #334155;
            --border-focus: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-light: #64748b;
            --primary-light: #1e3a8a;
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
            --shadow-card: 0 4px 12px 0 rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Sidebar Styles */
        .apex-sidebar {
            width: var(--sidebar-width);
            background: var(--surface);
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

        .apex-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            border-bottom: 1px solid var(--border);
        }

        .apex-logo-icon {
            width: 38px;
            height: 38px;
            background: var(--accent-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .apex-brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
        }

        .apex-brand-title span {
            color: var(--primary);
        }

        .apex-nav {
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
            color: var(--primary);
            transform: translateX(3px);
        }

        .nav-item.active {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
        }

        .nav-item.active svg {
            stroke: var(--primary);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 9999px;
        }

        /* Main Workspace Wrapper */
        .apex-workspace {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            background-color: var(--surface-bg);
        }

        /* Top Bar */
        .apex-topbar {
            height: var(--header-height);
            background: var(--surface);
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
            background: var(--surface-subtle);
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
            gap: 12px;
        }

        /* Top Bar Action Buttons (Language & Theme) */
        .btn-topbar-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            padding: 0 12px;
            background: var(--surface-subtle);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-topbar-action:hover {
            background: var(--surface-hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Timer Trigger Button */
        .btn-timer-trigger {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.08);
            transition: var(--transition);
        }

        .btn-timer-trigger:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-1px);
        }

        .btn-timer-trigger.is-active {
            background: var(--accent-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(37, 99, 235, 0); }
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
            background: var(--surface-subtle);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            box-shadow: var(--shadow-subtle);
        }

        /* Main Content Container */
        .apex-content {
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
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: var(--surface-subtle);
            border-color: var(--border-focus);
        }

        .btn-outline-primary, .btn-outline-purple {
            background: var(--surface);
            border: 1.5px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover, .btn-outline-purple:hover {
            background: var(--primary-light);
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

        .badge-draft { background: var(--surface-subtle); color: var(--text-muted); }
        .badge-issued { background: #eff6ff; color: #1d4ed8; }
        .badge-paid { background: #ecfdf5; color: #047857; }
        .badge-due { background: #fef2f2; color: #b91c1c; }
        .badge-voided { background: #f1f5f9; color: #475569; }

        /* Card Layout */
        .apex-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            padding: 24px;
        }

        /* Headers & Containers */
        .apex-invoice-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .invoice-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .invoice-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-main);
            margin: 0;
        }
        .invoice-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-top: 4px;
        }

        /* Stats Grid & Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 8px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 18px 22px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }
        .stat-card:hover {
            box-shadow: var(--shadow-card);
            border-color: var(--border-focus);
            transform: translateY(-1px);
        }
        .stat-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }
        .stat-value {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.6rem;
            font-weight: 800;
            margin: 6px 0 2px;
            color: var(--text-main);
        }
        .stat-sub {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        /* Tabs Header */
        .apex-tabs-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 12px;
        }
        .tabs-group {
            display: flex;
            gap: 4px;
        }
        .tab-btn, .tab-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            background: transparent;
            border: none;
            border-bottom: 2.5px solid transparent;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .tab-btn:hover, .tab-link:hover {
            color: var(--primary);
        }
        .tab-btn.active, .tab-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 700;
        }
        .tab-count {
            background: var(--surface-subtle);
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 9999px;
            color: var(--text-main);
        }
        .count-due {
            background: #fee2e2;
            color: #dc2626;
            font-weight: 800;
        }

        /* Search Form */
        .search-form {
            display: flex;
            align-items: center;
        }
        .search-input-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface-subtle);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            color: var(--text-muted);
        }
        .search-input {
            background: transparent;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 0.85rem;
            color: var(--text-main);
            width: 220px;
        }

        /* Table & Lists */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .apex-list-table {
            width: 100%;
            border-collapse: collapse;
        }
        .apex-list-table th {
            background: var(--surface-subtle);
            padding: 14px 20px;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            text-align: left;
        }
        .apex-list-table td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
            color: var(--text-main);
            vertical-align: middle;
        }
        .apex-list-table tr:hover td {
            background: var(--surface-subtle);
        }
        .invoice-number-link {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .invoice-number-link:hover {
            text-decoration: underline;
        }
        .doc-badge {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 700;
            background: var(--surface-subtle);
            color: var(--text-muted);
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 6px;
        }
        .client-name {
            font-weight: 600;
            color: var(--text-main);
        }
        .client-doc {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-light);
        }
        .amount-val {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--text-main);
        }
        .date-col {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        /* Pulse Indicator */
        .pulse-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
            display: inline-block;
        }

        /* Modals & Forms */
        .apex-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .apex-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
        }
        .apex-modal-dialog {
            position: relative;
            background: var(--surface);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-elevated);
            width: 480px;
            max-width: 90vw;
            padding: 28px;
            z-index: 101;
            border: 1px solid var(--border);
        }
        .apex-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .apex-select, .apex-input, .apex-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--text-main);
            background: var(--surface);
            outline: none;
            transition: var(--transition);
        }
        .apex-select:focus, .apex-input:focus, .apex-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        /* Top Progress Bar Loader */
        #spaLoader {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            z-index: 9999;
            transition: width 0.2s ease, opacity 0.3s ease;
            box-shadow: 0 0 8px rgba(37, 99, 235, 0.6);
            opacity: 0;
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
            background: #0f172a;
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
            .apex-sidebar { transform: translateX(-100%); }
            .apex-sidebar.open { transform: translateX(0); }
            .apex-workspace { margin-left: 0; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div id="spaLoader"></div>

    <!-- Left Sidebar Navigation -->
    <aside class="apex-sidebar" id="apexSidebar">
        <div class="apex-brand">
            <div class="apex-logo-icon">
                <!-- Original Shield Geometric Brand Icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                </svg>
            </div>
            <div class="apex-brand-title">Apex <span>Billing</span></div>
        </div>

        <nav class="apex-nav">
            <span class="nav-label" data-i18n="menu_main">Menú Principal</span>
            
            <a href="<?php echo e(route('invoices.index')); ?>" class="nav-item <?php echo e(request()->routeIs('invoices.index') ? 'active' : ''); ?>" id="nav-invoices">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span data-i18n="nav_invoices">Facturas & Boletas</span>
            </a>

            <a href="<?php echo e(route('invoices.create')); ?>" class="nav-item <?php echo e(request()->routeIs('invoices.create') ? 'active' : ''); ?>" id="nav-create-invoice">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span data-i18n="nav_new_invoice">Nueva Factura</span>
                <span class="nav-badge">+</span>
            </a>

            <a href="javascript:void(0)" onclick="toggleTimerDrawer()" class="nav-item" id="nav-tasks">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span data-i18n="nav_tasks">Tareas & Timer</span>
            </a>

            <span class="nav-label" data-i18n="menu_operations">Operaciones & Almacén</span>

            <a href="<?php echo e(route('inventory.index')); ?>" class="nav-item <?php echo e(request()->routeIs('inventory.index') ? 'active' : ''); ?>" id="nav-inventory" title="Gestión de inventario y Kardex">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <span data-i18n="nav_kardex">Kardex & Stock</span>
            </a>

            <a href="<?php echo e(route('pos.index')); ?>" class="nav-item <?php echo e(request()->routeIs('pos.index') ? 'active' : ''); ?>" id="nav-pos" title="Cajas registradoras y turnos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span data-i18n="nav_pos">Caja POS & Turnos</span>
            </a>

            <span class="nav-label" data-i18n="menu_settings">Configuración</span>

            <a href="<?php echo e(route('invoices.index')); ?>" class="nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span data-i18n="nav_settings">Ajustes & Series</span>
            </a>
        </nav>
    </aside>

    <!-- Workspace -->
    <div class="apex-workspace">
        <!-- Top Bar -->
        <header class="apex-topbar">
            <div class="topbar-left">
                <div class="branch-badge">
                    <span class="status-dot"></span>
                    <span data-i18n="branch_name">Sede Central Miraflores</span>
                    <span style="color: var(--text-light);">•</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">RUC 20601234567</span>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Language Toggle Button (ESP / ENG) -->
                <button type="button" class="btn-topbar-action" id="langToggleBtn" onclick="toggleLanguage()" title="Cambiar idioma (ESP / ENG)">
                    <span id="langLabel">🌐 ES</span>
                </button>

                <!-- Dark / Light Mode Toggle Button -->
                <button type="button" class="btn-topbar-action" id="themeToggleBtn" onclick="toggleTheme()" title="Modo Claro / Oscuro">
                    <span id="themeIcon">🌙</span>
                </button>

                <!-- Timer Trigger Button -->
                <button type="button" class="btn-timer-trigger" id="globalTimerBtn" onclick="toggleTimerDrawer()" title="Abrir panel de tareas y cronómetro">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span class="timer-time-display" id="globalTimerClock">00:00:00</span>
                    <span style="font-size: 0.75rem; background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 4px;" data-i18n="tag_tasks">Tasks</span>
                </button>

                <!-- User Profile -->
                <div class="user-avatar" title="David Bomba (Administrador)">DB</div>
            </div>
        </header>

        <!-- Main View Content -->
        <main class="apex-content">
            <?php if(session('success')): ?>
                <div style="background: var(--success-light); border-left: 4px solid var(--success); color: #065f46; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>✓ <?php echo e(session('success')); ?></span>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #065f46;">&times;</button>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div style="background: var(--warning-light); border-left: 4px solid var(--warning); color: #92400e; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 600;">
                    ⚠ <?php echo e(session('warning')); ?>

                </div>
            <?php endif; ?>

            <?php if(isset($errors) && $errors->any()): ?>
                <div style="background: var(--danger-light); border-left: 4px solid var(--danger); color: #991b1b; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                    <p style="font-weight: 700; margin-bottom: 6px;">Por favor corrige los siguientes errores:</p>
                    <ul style="padding-left: 20px; font-size: 0.9rem;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- Task & Time Tracker Slide-over Companion Drawer -->
    <?php echo $__env->make('components.timer-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Global i18n Translation Dictionary
        const i18nDict = {
            es: {
                menu_main: "Menú Principal",
                nav_invoices: "Facturas & Boletas",
                nav_new_invoice: "Nueva Factura",
                nav_tasks: "Tareas & Timer",
                menu_operations: "Operaciones & Almacén",
                nav_kardex: "Kardex & Stock",
                nav_pos: "Caja POS & Turnos",
                menu_settings: "Configuración",
                nav_settings: "Ajustes & Series",
                branch_name: "Sede Central Miraflores",
                tag_tasks: "Tareas",
                invoices_title: "Facturación & Comprobantes",
                invoices_subtitle: "Gestión de comprobantes fiscales, cobranzas y facturación electrónica",
                btn_new_invoice: "Nueva Factura",
                btn_tasks_timer: "Tareas & Timer",
                stat_total_invoiced: "Total Facturado",
                stat_total_invoiced_sub: "Comprobantes emitidos y pagados",
                stat_outstanding: "Por Cobrar (Pendiente)",
                stat_outstanding_sub: "Saldos pendientes de pago",
                stat_drafts: "Borradores",
                stat_drafts_sub: "Sin correlativo asignado",
                stat_total_docs: "Total Comprobantes",
                stat_total_docs_sub: "Histórico general",
                tab_all: "Todos",
                tab_due: "Vencidos",
                tab_draft: "Borrador",
                tab_issued: "Emitidos",
                tab_paid: "Pagados",
                tab_voided: "Anulados",
                th_number_series: "Número / Serie",
                th_client: "Cliente",
                th_issue_date: "Fecha Emisión",
                th_due_date: "Vencimiento",
                th_total: "Total",
                th_status: "Estado",
                th_actions: "Acciones",
                inventory_title: "Kardex & Inventario",
                inventory_subtitle: "Control multialmacén de existencias, alertas de stock mínimo y valoración por Costo Promedio Ponderado (WAC)",
                total_units: "Unidades en Stock",
                total_units_sub: "Suma física en todos los almacenes",
                active_variants: "Productos Activos",
                active_variants_sub: "Ítems catalogados en sistema",
                low_stock: "Alertas Stock Crítico",
                low_stock_sub: "Ítems con stock menor al mínimo",
                active_warehouse: "Almacén Principal",
                stock_levels_title: "Niveles de Existencias por Almacén",
                th_sku: "SKU / Código",
                th_product: "Producto / Descripción",
                th_category: "Categoría",
                th_unit: "Unidad",
                th_stock: "Stock Actual",
                th_min_stock: "Stock Mín.",
                th_cost: "Costo Promedio (CPP)",
                kardex_ledger_title: "Libro Mayor de Movimientos (Kardex Valorizado)",
                th_date: "Fecha / Hora",
                th_warehouse: "Almacén",
                th_direction: "Tipo",
                th_reason: "Motivo",
                th_qty: "Cantidad",
                th_unit_cost: "Costo Unitario",
                th_balance: "Saldo Resultante",
                pos_title: "Caja POS & Control de Turnos",
                pos_subtitle: "Gestión atómica de turnos de cajero, arqueos físicos y conciliación de cobros",
                btn_open_shift: "Abrir Turno de Caja",
                btn_close_shift: "Cerrar Turno de Caja",
                opening_balance: "Saldo Inicial",
                cash_collected: "Efectivo Recaudado",
                closed_shifts_history: "Historial de Turnos y Auditoría de Descuadres",
                th_session_id: "Sesión #",
                th_register: "Caja Registradora",
                th_cashier: "Cajero",
                th_opened_at: "Apertura",
                th_closed_at: "Cierre",
                th_opening_amt: "Monto Inicial",
                th_physical_amt: "Conteo Físico",
                th_variance: "Descuadre",
                timer_title: "Control de Tareas & Tiempo",
                timer_subtitle: "Módulo Integrado • Facturación",
                start_timer: "Iniciar Registro",
                date: "Fecha",
                start_and_end: "Inicio & Fin",
                hours_mode: "Horas",
                hours: "Horas",
                hours_hint: "Ejemplo: ingresa 1.5 para 1 hora 30 minutos.",
                start: "Inicio",
                end: "Fin",
                select_client: "👤 Cliente",
                select_project: "📁 Proyecto",
                assignee: "Asignado",
                status: "Estado",
                recent: "Recientes",
                refresh: "Actualizar",
                bill_tasks: "Facturar Tareas Seleccionadas",
                today: "Hoy",
                history: "Historial",
                stop_timer: "Detener",
                no_invoices_found: "No se encontraron facturas",
                no_invoices_sub: "Comienza creando tu primera factura o emitiendo tareas registradas.",
                btn_create_first: "Crear Primera Factura",
                new_invoice_title: "Nueva Factura",
                download_pdf: "Descargar PDF",
                save_draft: "Guardar Borrador",
                issue_fiscal: "Emitir Fiscalmente",
                customer_receiver: "Cliente / Receptor",
                use_demo_customer: "Usar cliente demo",
                doc_type_label: "Tipo de Comprobante",
                series_label: "Serie Fiscal (lockForUpdate)",
                currency_label: "Moneda",
                payment_condition_label: "Condición de Pago",
                cash_label: "Al Contado (Inmediato)",
                credit_label: "Al Crédito (15 días)",
                line_items_title: "Detalle de Líneas y Productos",
                line_items_subtitle: "Selecciona ítems del catálogo o ingresa descripciones libres",
                add_item: "Añadir Ítem",
                notes_label: "Notas & Términos Fiscales",
                subtotal: "Subtotal",
                tax_igv: "IGV (18.00%)",
                grand_total: "Total",
                lbl_select_register: "Caja Registradora:",
                lbl_initial_cash: "Fondo Inicial de Caja (Soles):",
                lbl_notes: "Observaciones:",
                lbl_physical_counted: "Monto Físico Contado (Soles):",
                lbl_closing_notes: "Notas de Cierre / Justificación:"
            },
            en: {
                menu_main: "Main Menu",
                nav_invoices: "Invoices & Billing",
                nav_new_invoice: "New Invoice",
                nav_tasks: "Tasks & Timer",
                menu_operations: "Operations & Inventory",
                nav_kardex: "Kardex & Stock",
                nav_pos: "POS Cash Registers",
                menu_settings: "Configuration",
                nav_settings: "Settings & Series",
                branch_name: "Miraflores Headquarters",
                tag_tasks: "Tasks",
                invoices_title: "Invoices & Billing",
                invoices_subtitle: "Fiscal billing, accounts receivable, and electronic document management",
                btn_new_invoice: "New Invoice",
                btn_tasks_timer: "Tasks & Timer",
                stat_total_invoiced: "Total Invoiced",
                stat_total_invoiced_sub: "Issued and settled invoices",
                stat_outstanding: "Outstanding Balance",
                stat_outstanding_sub: "Pending collection",
                stat_drafts: "Drafts",
                stat_drafts_sub: "Unissued documents",
                stat_total_docs: "Total Documents",
                stat_total_docs_sub: "Overall history",
                tab_all: "All",
                tab_due: "Due",
                tab_draft: "Draft",
                tab_issued: "Issued",
                tab_paid: "Paid",
                tab_voided: "Voided",
                th_number_series: "Number / Series",
                th_client: "Customer",
                th_issue_date: "Issue Date",
                th_due_date: "Due Date",
                th_total: "Total",
                th_status: "Status",
                th_actions: "Actions",
                inventory_title: "Kardex & Inventory",
                inventory_subtitle: "Multi-warehouse stock levels, reorder alerts, and Weighted Average Costing (WAC)",
                total_units: "Units in Stock",
                total_units_sub: "Total physical warehouse units",
                active_variants: "Active Products",
                active_variants_sub: "Cataloged SKUs in system",
                low_stock: "Critical Stock Alerts",
                low_stock_sub: "Items below minimum threshold",
                active_warehouse: "Main Warehouse",
                stock_levels_title: "Stock Levels by Warehouse",
                th_sku: "SKU / Code",
                th_product: "Product / Description",
                th_category: "Category",
                th_unit: "Unit",
                th_stock: "Current Stock",
                th_min_stock: "Min Stock",
                th_cost: "Average Cost (WAC)",
                kardex_ledger_title: "Movement Ledger (Valuated Kardex)",
                th_date: "Date / Time",
                th_warehouse: "Warehouse",
                th_direction: "Direction",
                th_reason: "Reason",
                th_qty: "Quantity",
                th_unit_cost: "Unit Cost",
                th_balance: "Resulting Balance",
                pos_title: "POS Terminal & Shift Shifts",
                pos_subtitle: "Atomic cashier shift management, physical counts, and payment reconciliation",
                btn_open_shift: "Open Register Shift",
                btn_close_shift: "Close Register Shift",
                opening_balance: "Opening Balance",
                cash_collected: "Collected Cash",
                closed_shifts_history: "Closed Shifts & Audit Variance",
                th_session_id: "Session #",
                th_register: "Cash Register",
                th_cashier: "Cashier",
                th_opened_at: "Opened At",
                th_closed_at: "Closed At",
                th_opening_amt: "Opening Cash",
                th_physical_amt: "Physical Count",
                th_variance: "Variance",
                timer_title: "Tasks & Time Tracker",
                timer_subtitle: "Integrated Module • Billing",
                start_timer: "Start a timer",
                date: "Date",
                start_and_end: "Start & end",
                hours_mode: "Hours",
                hours: "Hours",
                hours_hint: "For example, enter 1.5 for 1 hour 30 minutes.",
                start: "Start",
                end: "End",
                select_client: "👤 Client",
                select_project: "📁 Project",
                assignee: "Assignee",
                status: "Status",
                recent: "Recent",
                refresh: "Refresh",
                bill_tasks: "Bill Selected Tasks",
                today: "Today",
                history: "History",
                stop_timer: "Stop",
                no_invoices_found: "No invoices found",
                no_invoices_sub: "Get started by creating your first invoice or billing recorded tasks.",
                btn_create_first: "Create First Invoice",
                new_invoice_title: "New Invoice",
                download_pdf: "Download PDF",
                save_draft: "Save Draft",
                issue_fiscal: "Issue Invoice",
                customer_receiver: "Customer / Recipient",
                use_demo_customer: "Use demo customer",
                doc_type_label: "Document Type",
                series_label: "Fiscal Series (lockForUpdate)",
                currency_label: "Currency",
                payment_condition_label: "Payment Terms",
                cash_label: "Cash / Immediate",
                credit_label: "Credit (15 days)",
                line_items_title: "Line Items & Products",
                line_items_subtitle: "Select catalog items or enter custom descriptions",
                add_item: "Add Item",
                notes_label: "Notes & Fiscal Terms",
                subtotal: "Subtotal",
                tax_igv: "Tax (18.00%)",
                grand_total: "Total",
                lbl_select_register: "Cash Register:",
                lbl_initial_cash: "Opening Cash (Soles):",
                lbl_notes: "Notes / Observations:",
                lbl_physical_counted: "Physical Count (Soles):",
                lbl_closing_notes: "Closing Notes / Justification:"
            }
        };

        let currentLang = localStorage.getItem('app_lang') || 'es';
        let currentTheme = localStorage.getItem('app_theme') || 'light';

        // Apply language across DOM
        function applyLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('app_lang', lang);
            document.documentElement.setAttribute('lang', lang);
            document.getElementById('langLabel').innerText = lang === 'es' ? '🌐 ES' : '🌐 EN';

            const dict = i18nDict[lang];
            if (!dict) return;

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) {
                    el.innerText = dict[key];
                }
            });
        }

        function toggleLanguage() {
            const newLang = currentLang === 'es' ? 'en' : 'es';
            applyLanguage(newLang);
            showToast(newLang === 'es' ? 'Idioma cambiado a Español' : 'Language changed to English');
        }

        // Apply Dark/Light theme
        function applyTheme(theme) {
            currentTheme = theme;
            localStorage.setItem('app_theme', theme);
            document.documentElement.setAttribute('data-theme', theme);
            document.getElementById('themeIcon').innerText = theme === 'dark' ? '☀️' : '🌙';
        }

        function toggleTheme() {
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
            showToast(newTheme === 'dark' ? 'Modo Oscuro activado' : 'Modo Claro activado');
        }

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

        // ==========================================
        // Ultra-Fast SPA Navigation & Hover Prefetch
        // ==========================================
        const pageCache = new Map();

        function showTopLoader() {
            const l = document.getElementById('spaLoader');
            if (!l) return;
            l.style.opacity = '1';
            l.style.width = '35%';
            setTimeout(() => { if (l.style.opacity === '1') l.style.width = '75%'; }, 60);
        }

        function hideTopLoader() {
            const l = document.getElementById('spaLoader');
            if (!l) return;
            l.style.width = '100%';
            setTimeout(() => {
                l.style.opacity = '0';
                setTimeout(() => { l.style.width = '0%'; }, 200);
            }, 100);
        }

        function prefetchPage(url) {
            if (pageCache.has(url) || url.includes('/pdf') || url.includes('/api/')) return;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => pageCache.set(url, html))
                .catch(() => {});
        }

        async function spaNavigate(url, push = true) {
            try {
                showTopLoader();
                let html = pageCache.get(url);
                if (!html) {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Fetch failed');
                    html = await res.text();
                    pageCache.set(url, html);
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update Page Title
                document.title = doc.title;

                // Swap Content
                const newContent = doc.querySelector('.apex-content');
                const currentContent = document.querySelector('.apex-content');
                if (newContent && currentContent) {
                    currentContent.innerHTML = newContent.innerHTML;
                }

                // Update Active Link in Sidebar
                const currentPath = new URL(url).pathname;
                document.querySelectorAll('.apex-nav .nav-item').forEach(item => {
                    const href = item.getAttribute('href');
                    if (href && !href.startsWith('javascript:')) {
                        const itemPath = new URL(href, window.location.origin).pathname;
                        item.classList.toggle('active', itemPath === currentPath);
                    }
                });

                // Execute scripts in the view
                if (newContent) {
                    const scripts = newContent.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        document.body.appendChild(newScript);
                        setTimeout(() => newScript.remove(), 400);
                    });
                }

                if (push) {
                    window.history.pushState({}, '', url);
                }

                // Re-apply language and theme instantly
                applyLanguage(currentLang);
                applyTheme(currentTheme);

                window.scrollTo({ top: 0, behavior: 'instant' });
                hideTopLoader();
            } catch (err) {
                hideTopLoader();
                window.location.href = url;
            }
        }

        // Hover prefetch for 0ms instantaneous clicking
        document.addEventListener('mouseover', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:') && !link.target && !href.includes('/pdf')) {
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin) {
                    prefetchPage(url.href);
                }
            }
        });

        // Intercept clicks on links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank' || href.includes('/pdf') || link.hasAttribute('download')) {
                return;
            }
            const url = new URL(link.href, window.location.origin);
            if (url.origin === window.location.origin) {
                e.preventDefault();
                spaNavigate(url.href);
            }
        });

        window.addEventListener('popstate', () => {
            spaNavigate(window.location.href, false);
        });

        function warmNavigationCache() {
            const routesToWarm = [
                "<?php echo e(route('invoices.index')); ?>",
                "<?php echo e(route('inventory.index')); ?>",
                "<?php echo e(route('pos.index')); ?>"
            ];
            routesToWarm.forEach(url => {
                if (url !== window.location.href) {
                    prefetchPage(url);
                }
            });
        }

        // Initialize settings on page load
        document.addEventListener('DOMContentLoaded', () => {
            applyTheme(currentTheme);
            applyLanguage(currentLang);
            setTimeout(warmNavigationCache, 800);
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>
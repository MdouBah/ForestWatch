<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a4731">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FORESTWATCH">
    <meta name="mobile-web-app-capable" content="yes">
    <title>FORESTWATCH — @yield('title', 'Tableau de bord')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* ═══════════════════════════════════════
           RESET & VARIABLES
        ═══════════════════════════════════════ */
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        :root {
            --vert-fonce:  #1a4731;
            --vert-moyen: #2d6a4f;
            --vert-clair: #52b788;
            --vert-pale:  #d8f3dc;
            --orange:     #e07b39;
            --rouge:      #c0392b;
            --gris-bg:    #f4f6f3;
            --gris-card:  #ffffff;
            --texte:      #2c3e35;
            --texte-clair:#6b7c72;
            --sidebar-w:  260px;
            --topbar-h:   58px;
            --bottom-nav-h: 64px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gris-bg);
            color: var(--texte);
            display: flex;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* ═══════════════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ═══════════════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99;
            opacity: 0;
            transition: opacity .3s;
        }
        .sidebar-overlay.open {
            display: block;
            opacity: 1;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--vert-fonce);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            height: 100dvh;
            z-index: 100;
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            will-change: transform;
        }
        .sidebar-logo {
            padding: 20px 18px 14px;
            border-bottom: 1px solid rgba(255,255,255,.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-logo .logo-icon {
            width: 40px; height: 40px;
            background: var(--vert-clair);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .sidebar-logo .logo-text { font-size: 17px; font-weight: 700; letter-spacing: .5px; }
        .sidebar-logo .logo-sub  { font-size: 10.5px; color: rgba(255,255,255,.5); margin-top: 2px; }

        /* Bouton fermer sidebar (mobile) */
        .sidebar-close {
            display: none;
            background: none;
            border: none;
            color: rgba(255,255,255,.7);
            font-size: 22px;
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
            line-height: 1;
        }

        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
        .nav-section {
            padding: 10px 18px 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255,255,255,.4);
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 14px;
            border-left: 3px solid transparent;
            transition: all .2s;
            min-height: 48px; /* Touch target */
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,.08);
            color: #fff;
            border-left-color: var(--vert-clair);
        }
        .nav-item:active { background: rgba(255,255,255,.14); }
        .nav-item .icon { font-size: 18px; width: 24px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            padding: 14px 18px;
            padding-bottom: calc(14px + var(--safe-bottom));
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--vert-clair);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px;
            flex-shrink: 0;
        }
        .user-name  { font-size: 13px; font-weight: 600; }
        .user-role  { font-size: 11px; color: rgba(255,255,255,.5); }
        .btn-logout {
            margin-top: 10px;
            width: 100%;
            padding: 9px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            color: rgba(255,255,255,.8);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: background .2s;
            min-height: 42px;
        }
        .btn-logout:hover, .btn-logout:active { background: rgba(255,255,255,.18); }

        /* ═══════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* ═══════════════════════════════════════
           TOPBAR
        ═══════════════════════════════════════ */
        .topbar {
            background: #fff;
            padding: 0 20px;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e8ede9;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-left { display: flex; align-items: center; gap: 10px; }
        .topbar-title { font-size: 18px; font-weight: 700; color: var(--vert-fonce); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .topbar-right  { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .badge-role {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: var(--vert-pale);
            color: var(--vert-moyen);
            white-space: nowrap;
        }
        .badge-role.admin { background: #fde8d8; color: #c05621; }

        /* Bouton hamburger */
        #menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--vert-fonce);
            font-size: 22px;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            min-width: 40px;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }
        #menu-btn:active { background: var(--vert-pale); }

        /* ═══════════════════════════════════════
           CONTENT
        ═══════════════════════════════════════ */
        .content {
            padding: 24px;
            flex: 1;
            padding-bottom: calc(24px + var(--safe-bottom));
        }

        /* ═══════════════════════════════════════
           STAT CARDS
        ═══════════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            border-left: 4px solid var(--vert-clair);
        }
        .stat-card.orange { border-left-color: var(--orange); }
        .stat-card.rouge  { border-left-color: var(--rouge); }
        .stat-card.bleu   { border-left-color: #3498db; }
        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 10px;
            background: var(--vert-pale);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .stat-card.orange .stat-icon { background: #fde8d8; }
        .stat-card.rouge  .stat-icon { background: #fde; }
        .stat-card.bleu   .stat-icon { background: #d6eaf8; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--vert-fonce); line-height: 1.1; }
        .stat-label { font-size: 12px; color: var(--texte-clair); margin-top: 2px; }

        /* ═══════════════════════════════════════
           CARDS
        ═══════════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #eef1ee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .card-title { font-size: 14.5px; font-weight: 600; color: var(--vert-fonce); }
        .card-body { padding: 18px; }

        /* ═══════════════════════════════════════
           TABLEAUX
        ═══════════════════════════════════════ */
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        /* Indicateur de défilement sur mobile */
        .table-wrap::after {
            content: '';
            display: none;
        }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; min-width: 540px; }
        thead tr { background: var(--vert-pale); }
        th { padding: 10px 14px; text-align: left; font-weight: 600; color: var(--vert-fonce); font-size: 11.5px; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        td { padding: 11px 14px; border-bottom: 1px solid #f0f3f0; }
        tr:hover td { background: #f8faf8; }

        /* ═══════════════════════════════════════
           BADGES
        ═══════════════════════════════════════ */
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-sain     { background: #d8f3dc; color: #1a4731; }
        .badge-degrade  { background: #fef3cd; color: #7d5a00; }
        .badge-critique { background: #fde; color: #c0392b; }
        .badge-commun   { background: #d6eaf8; color: #1a5276; }
        .badge-rare     { background: #fde8d8; color: #a04000; }
        .badge-menace   { background: #fde; color: #922b21; }

        /* ═══════════════════════════════════════
           BOUTONS
        ═══════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .18s;
            text-decoration: none;
            min-height: 40px;
            white-space: nowrap;
            user-select: none;
            -webkit-user-select: none;
        }
        .btn:active { transform: scale(.97); }
        .btn-primary  { background: var(--vert-moyen); color: #fff; }
        .btn-primary:hover  { background: var(--vert-fonce); }
        .btn-outline  { background: transparent; border: 1.5px solid var(--vert-moyen); color: var(--vert-moyen); }
        .btn-outline:hover { background: var(--vert-pale); }
        .btn-danger   { background: var(--rouge); color: #fff; }
        .btn-danger:hover { background: #a93226; }
        .btn-sm { padding: 6px 12px; font-size: 12px; min-height: 34px; }

        /* ═══════════════════════════════════════
           FORMULAIRES
        ═══════════════════════════════════════ */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--texte); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 11px 13px;
            border: 1.5px solid #d0ddd2;
            border-radius: 8px;
            font-size: 16px; /* 16px = évite le zoom automatique iOS */
            color: var(--texte);
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
            -webkit-appearance: none;
            appearance: none;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--vert-clair);
            box-shadow: 0 0 0 3px rgba(82,183,136,.18);
        }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* ═══════════════════════════════════════
           MODALS — BOTTOM SHEET SUR MOBILE
        ═══════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff;
            border-radius: 16px;
            width: 92%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overscroll-behavior: contain;
        }
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #eef1ee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }
        .modal-title { font-size: 15.5px; font-weight: 700; color: var(--vert-fonce); }
        .modal-close {
            background: #f0f5f1;
            border: none;
            width: 32px; height: 32px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            color: var(--texte-clair);
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            flex-shrink: 0;
        }
        .modal-close:active { background: #d8f3dc; }
        .modal-body   { padding: 20px; }
        .modal-footer {
            padding: 14px 20px;
            padding-bottom: calc(14px + var(--safe-bottom));
            border-top: 1px solid #eef1ee;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            position: sticky;
            bottom: 0;
            background: #fff;
        }

        /* ═══════════════════════════════════════
           ALERTS
        ═══════════════════════════════════════ */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 16px;
            display: none;
            line-height: 1.5;
        }
        .alert-success { background: #d8f3dc; color: #1a4731; border: 1px solid #b7e4c7; }
        .alert-error   { background: #fde; color: #c0392b; border: 1px solid #f5b7b1; }
        .alert.show { display: block; }

        /* ═══════════════════════════════════════
           SPINNER
        ═══════════════════════════════════════ */
        .spinner {
            display: inline-block;
            width: 18px; height: 18px;
            border: 3px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ═══════════════════════════════════════
           BOTTOM NAV (mobile uniquement)
        ═══════════════════════════════════════ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: calc(var(--bottom-nav-h) + var(--safe-bottom));
            background: #fff;
            border-top: 1px solid #e8ede9;
            z-index: 80;
            padding-bottom: var(--safe-bottom);
        }
        .bottom-nav-inner {
            display: flex;
            height: var(--bottom-nav-h);
        }
        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: var(--texte-clair);
            font-size: 10px;
            font-weight: 500;
            transition: color .18s;
            padding: 6px 4px;
            cursor: pointer;
            border: none;
            background: none;
            min-height: 64px;
        }
        .bottom-nav-item:active { background: var(--vert-pale); border-radius: 10px; }
        .bottom-nav-item.active { color: var(--vert-moyen); }
        .bottom-nav-item .bn-icon { font-size: 22px; line-height: 1; }
        .bottom-nav-item.active .bn-icon { transform: scale(1.12); }
        /* Point indicateur actif */
        .bottom-nav-item.active::before {
            content: '';
            display: block;
            width: 4px; height: 4px;
            background: var(--vert-moyen);
            border-radius: 50%;
            position: absolute;
            top: 6px;
        }
        .bottom-nav-item { position: relative; }

        /* Masquer le contenu visiteur-hide sur le bottom nav */
        .bn-visiteur-hide { display: flex; }

        /* ═══════════════════════════════════════
           SCROLL HINT (tables)
        ═══════════════════════════════════════ */
        .scroll-hint {
            display: none;
            font-size: 11px;
            color: var(--texte-clair);
            text-align: right;
            padding: 4px 14px 8px;
        }

        /* ═══════════════════════════════════════
           RESPONSIVE — TABLETTE (768px)
        ═══════════════════════════════════════ */
        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        }

        /* ═══════════════════════════════════════
           RESPONSIVE — MOBILE (768px)
        ═══════════════════════════════════════ */
        @media (max-width: 768px) {
            /* Sidebar cachée par défaut */
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 6px 0 24px rgba(0,0,0,.3);
            }
            .sidebar-close { display: flex; }

            /* Main sans marge */
            .main { margin-left: 0; }

            /* Hamburger visible */
            #menu-btn { display: flex !important; }

            /* Badge rôle caché sur mobile pour gagner de la place */
            #badge-role { display: none; }

            /* Contenu avec bottom nav */
            .content {
                padding: 16px;
                padding-bottom: calc(var(--bottom-nav-h) + var(--safe-bottom) + 16px);
            }

            /* Bottom nav visible */
            .bottom-nav { display: block; }

            /* Stats 2 colonnes sur mobile */
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 16px; }
            .stat-card { padding: 14px 12px; gap: 10px; }
            .stat-icon { width: 40px; height: 40px; font-size: 18px; }
            .stat-value { font-size: 20px; }
            .stat-label { font-size: 11px; }

            /* Cards */
            .card-header { padding: 12px 14px; flex-wrap: wrap; }
            .card-body { padding: 14px; }

            /* Grilles 1fr 1fr → 1 colonne */
            .grid-2col { grid-template-columns: 1fr !important; }

            /* Modals en bottom sheet */
            .modal-overlay {
                align-items: flex-end;
                padding: 0;
            }
            .modal {
                width: 100%;
                max-width: 100%;
                border-radius: 20px 20px 0 0;
                max-height: 92vh;
                max-height: 92dvh;
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
            }
            /* Poignée de glissement */
            .modal::before {
                content: '';
                display: block;
                width: 40px;
                height: 4px;
                background: #d0ddd2;
                border-radius: 4px;
                margin: 12px auto 0;
            }
            .modal-header { padding-top: 12px; }

            /* Scroll hint sur les tables */
            .scroll-hint { display: block; }

            /* Table min-width ajusté */
            table { min-width: 480px; }

            /* Topbar */
            .topbar { padding: 0 14px; }
            .topbar-title { font-size: 16px; max-width: 160px; }
        }

        /* ═══════════════════════════════════════
           RESPONSIVE — PETIT MOBILE (480px)
        ═══════════════════════════════════════ */
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .stat-value { font-size: 18px; }
            .content { padding: 12px; }

            /* Boutons full-width dans les footers de modal */
            .modal-footer { flex-direction: column-reverse; }
            .modal-footer .btn { width: 100%; justify-content: center; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- OVERLAY SIDEBAR (mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ═══════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🌳</div>
        <div style="flex:1;">
            <div class="logo-text">FORESTWATCH</div>
            <div class="logo-sub">Gestion des forêts</div>
        </div>
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer le menu">✕</button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">📊</span> Tableau de bord
        </a>
        <a href="/carte" class="nav-item {{ request()->is('carte') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">🗺️</span> Carte interactive
        </a>

        <div class="nav-section">Consultation</div>
        <a href="/zones" class="nav-item {{ request()->is('zones*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">🌲</span> Zones forestières
        </a>
        <a href="/especes" class="nav-item {{ request()->is('especes*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">🌿</span> Espèces d'arbres
        </a>
        <a href="/cours-eaux" class="nav-item {{ request()->is('cours-eaux*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">💧</span> Cours d'eaux
        </a>
        <a href="/analyses" class="nav-item visiteur-hide {{ request()->is('analyses*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">📈</span> Analyses
        </a>
        <a href="/rapports" class="nav-item visiteur-hide {{ request()->is('rapports*') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
            <span class="icon">📄</span> Rapports
        </a>

        <div id="agent-menu" style="display:none;">
            <div class="nav-section">Gestion forestière</div>
            <a href="/zones" class="nav-item {{ request()->is('zones*') ? 'active' : '' }}"
               id="nav-zones-agent" style="display:none;" onclick="closeSidebarOnMobile()">
                <span class="icon">🌲</span> Mes zones forestières
            </a>
        </div>

        <div id="admin-menu" style="display:none;">
            <div class="nav-section">Administration</div>
            <a href="/admin/utilisateurs" class="nav-item {{ request()->is('admin/utilisateurs') ? 'active' : '' }}" onclick="closeSidebarOnMobile()">
                <span class="icon">👥</span> Gestion des comptes
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar" id="user-avatar">?</div>
            <div>
                <div class="user-name" id="user-name">Chargement...</div>
                <div class="user-role" id="user-role"></div>
            </div>
        </div>
        <a href="/profil"
           onclick="closeSidebarOnMobile()"
           style="display:block;margin-top:10px;padding:8px 12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:8px;color:rgba(255,255,255,.75);font-size:13px;text-decoration:none;text-align:center;transition:background .2s;min-height:40px;display:flex;align-items:center;justify-content:center;">
            👤 Mon profil
        </a>
        <button class="btn-logout" onclick="logout()">⬡ Déconnexion</button>
    </div>
</aside>

<!-- ═══════════════════════════════════════
     MAIN
═══════════════════════════════════════ -->
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button id="menu-btn" onclick="openSidebar()" aria-label="Ouvrir le menu">☰</button>
            <span class="topbar-title">@yield('title', 'Tableau de bord')</span>
        </div>
        <div class="topbar-right">
            <span class="badge-role" id="badge-role">Utilisateur</span>
            <a href="/profil"
               style="display:none;width:34px;height:34px;border-radius:50%;background:var(--vert-clair);color:#fff;font-weight:700;font-size:13px;align-items:center;justify-content:center;text-decoration:none;"
               id="topbar-avatar">?</a>
        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>
</div>

<!-- ═══════════════════════════════════════
     BOTTOM NAVIGATION (mobile)
═══════════════════════════════════════ -->
<nav class="bottom-nav" id="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="/dashboard" class="bottom-nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="bn-icon">📊</span>
            <span>Accueil</span>
        </a>
        <a href="/carte" class="bottom-nav-item {{ request()->is('carte') ? 'active' : '' }}">
            <span class="bn-icon">🗺️</span>
            <span>Carte</span>
        </a>
        <a href="/zones" class="bottom-nav-item {{ request()->is('zones*') ? 'active' : '' }}">
            <span class="bn-icon">🌲</span>
            <span>Zones</span>
        </a>
        <a href="/analyses" class="bottom-nav-item bn-visiteur-hide {{ request()->is('analyses*') ? 'active' : '' }}" id="bn-analyses">
            <span class="bn-icon">📈</span>
            <span>Analyses</span>
        </a>
        <button class="bottom-nav-item" onclick="openSidebar()">
            <span class="bn-icon">☰</span>
            <span>Menu</span>
        </button>
    </div>
</nav>

<script>
    /* ══════════════════════════════════════════
       JWT & AUTH
    ══════════════════════════════════════════ */
    const TOKEN_KEY = 'fw_token';
    const USER_KEY  = 'fw_user';

    function getToken() { return localStorage.getItem(TOKEN_KEY); }
    function getUser()  { const u = localStorage.getItem(USER_KEY); return u ? JSON.parse(u) : null; }

    if (!getToken()) window.location.href = '/login';

    function isAdmin()    { const u = getUser(); return u && u.role === 'admin'; }
    function isVisiteur() { const u = getUser(); return u && u.role === 'visiteur'; }

    const ROLE_LABELS = { admin: 'Administrateur', user: 'Agent forestier', visiteur: 'Visiteur' };

    if (window.location.pathname.startsWith('/admin') && !isAdmin()) {
        window.location.href = '/dashboard';
    }
    const visiteurBlocked = ['/analyses', '/rapports'];
    if (isVisiteur() && visiteurBlocked.some(p => window.location.pathname.startsWith(p))) {
        window.location.href = '/dashboard';
    }

    /* ══════════════════════════════════════════
       SIDEBAR MOBILE
    ══════════════════════════════════════════ */
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebar-overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeSidebarOnMobile() {
        if (window.innerWidth <= 768) closeSidebar();
    }

    // Fermer avec Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSidebar();
    });

    /* ══════════════════════════════════════════
       AFFICHAGE UTILISATEUR
    ══════════════════════════════════════════ */
    const user = getUser();
    if (user) {
        const roleLabel = ROLE_LABELS[user.role] || user.role;
        const initials  = ((user.prenom?.[0] ?? '') + (user.nom?.[0] ?? '')).toUpperCase();

        document.getElementById('user-name').textContent   = user.prenom + ' ' + user.nom;
        document.getElementById('user-role').textContent   = roleLabel;
        document.getElementById('user-avatar').textContent = initials;

        // Avatar dans la topbar (mobile)
        const topAvatar = document.getElementById('topbar-avatar');
        topAvatar.textContent = initials;
        topAvatar.style.display = 'flex';

        if (isAdmin()) {
            document.getElementById('badge-role').textContent = 'Administrateur';
            document.getElementById('badge-role').classList.add('admin');
            document.getElementById('admin-menu').style.display = 'block';
            document.getElementById('agent-menu').style.display = 'block';
        } else if (user.role === 'user') {
            document.getElementById('badge-role').textContent = 'Agent forestier';
            document.getElementById('agent-menu').style.display = 'block';
            document.getElementById('nav-zones-agent').style.display = 'flex';
        } else if (isVisiteur()) {
            document.getElementById('badge-role').textContent = 'Visiteur';
            // Masquer "Analyses" dans le bottom nav pour les visiteurs
            const bnAnalyses = document.getElementById('bn-analyses');
            if (bnAnalyses) bnAnalyses.style.display = 'none';
        }
    }

    /* ══════════════════════════════════════════
       RÔLES UI
    ══════════════════════════════════════════ */
    function applyRoleUI() {
        if (!isAdmin()) {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'none');
        }
        if (isVisiteur()) {
            document.querySelectorAll('.visiteur-hide').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.bn-visiteur-hide').forEach(el => el.style.display = 'none');
        }
    }
    document.addEventListener('DOMContentLoaded', applyRoleUI);

    /* ══════════════════════════════════════════
       API FETCH (JWT)
    ══════════════════════════════════════════ */
    async function apiFetch(url, options = {}) {
        let res;
        try {
            res = await fetch('/api' + url, {
                ...options,
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',   // ← force JSON, évite redirect 500
                    'Authorization': 'Bearer ' + getToken(),
                    ...(options.headers || {})
                }
            });
        } catch (networkErr) {
            // Erreur réseau (serveur éteint, offline, etc.)
            console.error('Erreur réseau:', networkErr);
            throw networkErr;
        }

        // Token expiré ou invalide → déconnecter
        if (res.status === 401) {
            localStorage.clear();
            window.location.href = '/login';
            throw new Error('Non authentifié');
        }

        return res;
    }

    /* ══════════════════════════════════════════
       DÉCONNEXION
    ══════════════════════════════════════════ */
    async function logout() {
        await apiFetch('/auth/logout', { method: 'POST' }).catch(() => {});
        localStorage.clear();
        window.location.href = '/login';
    }

    /* ══════════════════════════════════════════
       SCROLL HINT (tables)
       Affiche un indice visuel si la table déborde
    ══════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.table-wrap').forEach(wrap => {
            const hint = document.createElement('div');
            hint.className = 'scroll-hint';
            hint.textContent = '← Faire défiler →';
            wrap.parentNode.insertBefore(hint, wrap);

            function checkScroll() {
                hint.style.display = (window.innerWidth <= 768 && wrap.scrollWidth > wrap.clientWidth)
                    ? 'block' : 'none';
            }
            checkScroll();
            window.addEventListener('resize', checkScroll);
            wrap.addEventListener('scroll', () => { hint.style.opacity = '0'; });
        });
    });

    /* ══════════════════════════════════════════
       SWIPE POUR FERMER LA SIDEBAR (touch)
    ══════════════════════════════════════════ */
    (function() {
        let startX = 0;
        const sidebar = document.getElementById('sidebar');

        document.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        document.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - startX;
            // Swipe gauche (> 60px) ferme la sidebar
            if (sidebar.classList.contains('open') && dx < -60) {
                closeSidebar();
            }
            // Swipe droit depuis le bord gauche (< 20px) ouvre la sidebar
            if (!sidebar.classList.contains('open') && startX < 20 && dx > 60) {
                openSidebar();
            }
        }, { passive: true });
    })();
</script>
@stack('scripts')
</body>
</html>

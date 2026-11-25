<!DOCTYPE html>
<html lang="id">
<head>
  <script src="../../assets/js/admin-guard.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pencairan Dana Admin - Staplr Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Design tokens — disamakan dengan branch perusahaan (dashboard.css :root) */
    :root {
      --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --color-navy: #122f51;
      --color-navy-dark: #0d223c;
      --color-yellow: #ffc700;
      --color-avatar-green: #155f50;
      --color-bg-main: #f8fafc;
      --color-card-bg: #ffffff;
      --color-border: #e8ecf2;
      --color-text-dark: #0f172a;
      --color-text-muted: #64748b;
      --sidebar-width: 215px;
      --radius-card: 16px;
      --radius-pill: 999px;
      --shadow-card: 0 2px 12px -2px rgba(18, 47, 81, 0.04);
    }

    /* ==========================================================
       STAPLR ADMIN - KEUANGAN (PIXEL-PERFECT REPLICA)
       ========================================================== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: #f8fafc;
      color: #0f2a4a;
      overflow-x: hidden;
    }

    /* ============ SIDEBAR ============ */
    .sidebar {
      width: 215px;
      flex-shrink: 0;
      /* Exact 7-stop gradient matching Admin design */
      background: linear-gradient(180deg, #87ab95 0%, #afc5aa 30%, #ebedc8 55%, #d2dcba 75%, #82a78f 100%);
      padding: 20px 14px;
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      border-right: 1px solid rgba(0, 0, 0, 0.04);
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 2px 8px 18px;
      text-decoration: none;
      user-select: none;
    }

    .sidebar-brand-icon {
      height: 34px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.08));
    }

    .sidebar-brand-name {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 1.25rem;
      font-weight: 800;
      color: #122f51;
      letter-spacing: -0.02em;
    }

    .sidebar-brand-dot {
      color: #ffc700;
      font-size: 1.1rem;
      line-height: 1;
    }

    .menu {
      display: flex;
      flex-direction: column;
      gap: 3px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 9px 12px;
      border-radius: 12px;
      font-size: 0.92rem;
      font-weight: 600;
      text-decoration: none;
      color: #1a3327;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      white-space: nowrap;
    }

    .menu-item .icon {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu-item .icon svg {
      width: 100%;
      height: 100%;
      display: block;
      stroke: #ffc700;
      stroke-width: 2.2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }

    .menu-item.active .icon svg {
      fill: rgba(255, 199, 0, 0.25);
    }

    .menu-item:hover {
      background: rgba(255, 255, 255, 0.28);
      color: #0d2218;
    }

    /* Active Keuangan state: dark teal text, translucent pill */
    .menu-item.active {
      background: rgba(255, 255, 255, 0.52);
      color: #0b1f14;
      font-weight: 700;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    /* ============ MAIN LAYOUT ============ */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    /* ============ TOPBAR ============ */
    .topbar {
      background: #ffffff;
      padding: 18px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #e8ecf2;
    }

    .topbar h1 {
      font-size: 1.45rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .admin-profile {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 14.5px;
      font-weight: 600;
      color: #0f2a4a;
      cursor: pointer;
      padding: 6px 12px;
      border-radius: 20px;
      transition: background 0.15s ease;
    }

    .admin-profile:hover {
      background: #f1f5f9;
    }

    .admin-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #155f50;
      flex-shrink: 0;
    }

    /* ============ POPUP PROFIL ADMIN (dropdown di topbar) ============ */
    .topbar-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
    }

    .admin-profile .chevron {
      width: 15px;
      height: 15px;
      stroke: #64748b;
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }

    .admin-profile.open .chevron {
      transform: rotate(180deg);
    }

    .admin-dropdown {
      position: absolute;
      top: calc(100% + 10px);
      right: 0;
      width: 248px;
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 16px;
      box-shadow: 0 12px 32px -8px rgba(18, 47, 81, 0.18);
      padding: 8px;
      display: none;
      z-index: 100;
    }

    .admin-dropdown.show {
      display: block;
    }

    .dropdown-head {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
    }

    .dropdown-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #155f50;
      flex-shrink: 0;
    }

    .dropdown-name {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: -0.01em;
    }

    .dropdown-status {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 2px;
      font-size: 12px;
      font-weight: 600;
      color: #16a34a;
    }

    .dropdown-status::before {
      content: '';
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #22c55e;
    }

    .dropdown-divider {
      height: 1px;
      background: #f1f5f9;
      margin: 4px 4px 6px;
    }

    .dropdown-logout {
      display: flex;
      align-items: center;
      gap: 9px;
      width: 100%;
      padding: 10px 12px;
      border: none;
      border-radius: 10px;
      background: transparent;
      color: #dc2626;
      font-family: inherit;
      font-size: 13.5px;
      font-weight: 700;
      text-align: left;
      cursor: pointer;
      transition: background 0.15s ease;
    }

    .dropdown-logout:hover {
      background: #fef2f2;
    }

    .dropdown-logout svg {
      width: 17px;
      height: 17px;
      stroke: #dc2626;
      flex-shrink: 0;
    }

    /* ============ CONTENT ============ */
    .content {
      padding: 24px 36px 36px;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    /* Common Card Styling */
    .card {
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 16px;
      box-shadow: 0 2px 12px -2px rgba(18, 47, 81, 0.04);
    }

    /* ============ 1. SALDO CARD ============ */
    .saldo-card {
      padding: 34px 28px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .saldo-label {
      font-size: 12px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .saldo-amount {
      font-size: 34px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.5px;
    }

    /* ============ 2. FORM PENCAIRAN ============ */
    .form-card {
      padding: 26px 28px 28px;
    }

    .card-title {
      font-size: 18px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.2px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 13.5px;
      font-weight: 600;
      color: #0f2a4a;
      margin-bottom: 8px;
    }

    /* Nominal Input with Prefix */
    .input-nominal-wrap {
      display: flex;
      align-items: center;
      background: #f8fafc;
      border: 1px solid #e8ecf2;
      border-radius: 8px;
      height: 48px;
      padding: 0 16px;
      gap: 10px;
      transition: border-color 0.15s ease, background 0.15s ease;
    }

    .input-nominal-wrap:focus-within {
      border-color: #0f2a4a;
      background: #ffffff;
    }

    .input-prefix {
      font-size: 14.5px;
      font-weight: 600;
      color: #94a3b8;
    }

    .input-nominal {
      flex: 1;
      border: none;
      outline: none;
      background: transparent;
      font-size: 15px;
      font-weight: 700;
      color: #0f2a4a;
      font-family: inherit;
    }

    /* Bank Account Select Box & Dropdown */
    .account-select-wrap {
      position: relative;
    }

    .account-select {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #eff6ff;
      border: 1px solid #dbeafe;
      border-radius: 10px;
      padding: 12px 18px;
      cursor: pointer;
      transition: all 0.15s ease;
      user-select: none;
    }

    .account-select:hover {
      background: #e0f2fe;
      border-color: #bfdbfe;
    }

    .account-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .bank-logo-box {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      background: #ffffff;
      border: 1px solid #e8ecf2;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 1px 2px rgba(15, 42, 74, 0.04);
    }

    .account-info {
      display: flex;
      flex-direction: column;
    }

    .account-name {
      font-size: 14px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .account-sub {
      font-size: 12px;
      color: #64748b;
      line-height: 1.3;
    }

    .select-chevron {
      color: #3b82f6;
      font-size: 18px;
      display: flex;
      align-items: center;
      transition: transform 0.15s ease;
    }

    .select-chevron.open {
      transform: rotate(180deg);
    }

    /* Dropdown Options */
    .account-dropdown-menu {
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      background: #ffffff;
      border: 1px solid #dbeafe;
      border-radius: 10px;
      box-shadow: 0 10px 25px -5px rgba(15, 42, 74, 0.12), 0 8px 10px -6px rgba(15, 42, 74, 0.08);
      padding: 6px;
      z-index: 100;
      display: none;
    }

    .account-dropdown-menu.show {
      display: block;
      animation: fadeIn 0.15s ease-out;
    }

    .account-option {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.12s ease;
    }

    .account-option:hover {
      background: #eff6ff;
    }

    .account-option.selected {
      background: #e0f2fe;
    }

    /* Submit Button (Cairkan Dana) */
    .btn-submit {
      width: 100%;
      height: 50px;
      background: #143358;
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      cursor: pointer;
      transition: all 0.15s ease;
      margin-top: 24px;
      box-shadow: 0 2px 4px rgba(20, 51, 88, 0.15);
      font-family: inherit;
    }

    .btn-submit:hover {
      background: #0f2a4a;
      transform: translateY(-1px);
    }

    .btn-submit svg {
      width: 16px;
      height: 16px;
      transform: rotate(0deg);
    }

    .btn-submit:disabled {
      background: #94a3b8;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* Daftar pencairan siap dicairkan (Form Pencairan) */
    .pending-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 16px;
    }

    .pending-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 12px 14px;
      cursor: pointer;
    }

    .pending-item input {
      margin-top: 3px;
      width: 18px;
      height: 18px;
      accent-color: #143358;
      flex-shrink: 0;
    }

    .pending-item-nama {
      font-weight: 700;
      font-size: 14px;
      color: #0f172a;
    }

    .pending-item-sub {
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
    }

    .pending-item-nominal {
      margin-left: auto;
      font-weight: 700;
      font-size: 14px;
      color: #0f172a;
      white-space: nowrap;
    }

    .pending-pilih-semua {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-top: 12px;
      cursor: pointer;
    }

    .pending-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 16px;
      padding-top: 14px;
      border-top: 1px solid #e2e8f0;
      font-size: 14px;
      color: #334155;
    }

    .pending-empty {
      font-size: 13px;
      color: #64748b;
      padding: 8px 0;
    }

    /* ============ 3. RIWAYAT PENCAIRAN ============ */
    .history-card {
      padding: 26px 28px;
      overflow: hidden;
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    thead {
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }

    thead th {
      background: #f8fafc;
      padding: 15px 20px;
      font-size: 13px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 1px solid #edf1f6;
      transition: background-color 0.1s ease;
    }

    tbody tr:hover {
      background-color: #fbfcfe;
    }

    tbody td {
      padding: 16px 20px;
      font-size: 14px;
      color: #334155;
      vertical-align: middle;
      white-space: nowrap;
    }

    .td-date-title {
      font-size: 14px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .td-date-sub {
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.3;
    }

    .td-nominal {
      font-size: 14.5px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .td-target-title {
      font-size: 13.5px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .td-target-sub {
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.3;
    }

    /* Status Badge */
    .badge-success {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      background: #d1fae5;
      color: #059669;
      white-space: nowrap;
    }

    .badge-dot {
      font-size: 9px;
      line-height: 1;
    }

    /* ============ TOAST NOTIFICATION ============ */
    .toast-container {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }

    .toast {
      pointer-events: auto;
      min-width: 300px;
      max-width: 420px;
      background: #0f2a4a;
      color: #ffffff;
      border-radius: 10px;
      padding: 12px 18px;
      box-shadow: 0 10px 25px -5px rgba(15, 42, 74, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideToastIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      transition: opacity 0.25s, transform 0.25s;
    }

    .toast.toast-leave {
      opacity: 0;
      transform: translateX(30px);
    }

    @keyframes slideToastIn {
      from { opacity: 0; transform: translateY(20px) scale(0.95); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .toast-icon {
      width: 22px;
      height: 22px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toast-icon svg {
      width: 100%;
      height: 100%;
    }

    .toast-message {
      font-size: 13.5px;
      font-weight: 600;
      line-height: 1.4;
      flex: 1;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 820px) {
      body {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        height: auto;
        position: static;
        padding: 20px;
      }
      .topbar {
        padding: 0 20px;
      }
      .content {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- ==================== SIDEBAR ==================== -->
  <aside class="sidebar">
    <!-- Logo with shopping basket + yellow dot + Staplr -->
    <a href="dashboard.php" class="sidebar-brand" title="Dashboard Staplr"><img src="../../assets/img/logo-transparent.png" alt="Staplr Icon" class="sidebar-brand-icon"><span class="sidebar-brand-name">Staplr<span class="sidebar-brand-dot">.</span></span></a>

    <!-- Navigation Menu -->
    <nav class="menu">
      <!-- 1. Dashboard -->
      <a class="menu-item" href="dashboard.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        </span>
        <span>Dashboard</span>
      </a>

      <!-- 2. Verifikasi -->
      <a class="menu-item" href="verifikasi.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L20 6 V11 C20 16 16.5 20 12 21 C7.5 20 4 16 4 11 V6 Z"/><path d="M9 12 L11 14 L15 10"/></svg>
        </span>
        <span>Verifikasi</span>
      </a>

      <!-- 3. Dispute -->
      <a class="menu-item" href="dispute.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </span>
        <span>Dispute</span>
      </a>

      <!-- 4. Keuangan (Active) -->
      <a class="menu-item active" href="keuangan.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="rgba(255,199,0,0.25)" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </span>
        <span>Keuangan</span>
      </a>

      <!-- 5. Manajemen User -->
      <a class="menu-item" href="manajemen-user.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20 C2.5 16 5.5 14 9 14 C12.5 14 15.5 16 15.5 20"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14.5 C18 14.5 20 16 20.5 18"/></svg>
        </span>
        <span>Manajemen User</span>
      </a>

      <!-- 6. Riwayat -->
      <a class="menu-item" href="riwayat.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
        <span>Riwayat</span>
      </a>
    </nav>
  </aside>

  <!-- ==================== MAIN CONTENT ==================== -->
  <main class="main">
    <!-- Topbar -->
    <header class="topbar">
      <h1>Pencairan Dana Admin</h1>
      <div class="topbar-actions">
        <div class="admin-profile" id="adminProfileBtn" style="cursor: pointer;" title="Profil Admin">
          <div class="admin-avatar"></div>
          <span>Admin</span>
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </div>

        <div class="admin-dropdown" id="adminDropdown">
          <div class="dropdown-head">
            <div class="dropdown-avatar"></div>
            <div>
              <div class="dropdown-name">Superadmin Staplr</div>
              <div class="dropdown-status">Aktif</div>
            </div>
          </div>
          <div class="dropdown-divider"></div>
          <button type="button" class="dropdown-logout" id="btnLogout">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
          </button>
        </div>
      </div>
    </header>

    <!-- Page Body -->
    <section class="content">

      <!-- ============ 1. SALDO CARD ============ -->
      <div class="card saldo-card">
        <span class="saldo-label">SALDO YANG TERSEDIA</span>
        <div class="saldo-amount" id="saldoDisplay">Rp 0</div>
      </div>

      <!-- ============ 2. FORM PENCAIRAN ============ -->
      <div class="card form-card">
        <h2 class="card-title">Form Pencairan</h2>
          <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">Centang baris pencairan yang sudah ditransfer ke rekening perusahaan, lalu catat. Transfer dilakukan manual di luar sistem; yang dicatat di sini hanya buktinya.</p>

        <!-- Daftar dana siap dicairkan -->
        <div class="pending-list" id="pendingList">
          <!-- Rendered via JavaScript -->
        </div>
        <label class="pending-pilih-semua"><input type="checkbox" id="pilihSemuaPending"> Pilih semua</label>

        <!-- Field: Referensi transfer -->
        <div class="form-group">
          <label class="form-label" for="referensiPencairan">No. Referensi Transfer (opsional)</label>
          <div class="input-nominal-wrap">
            <input type="text" id="referensiPencairan" class="input-nominal" value="" placeholder="cth: TRF-20261125-001">
          </div>
        </div>

        <div class="pending-total-row">
          <span>Total terpilih</span>
          <strong id="totalTerpilih">Rp 0</strong>
        </div>

        <!-- Submit Button -->
        <button type="button" class="btn-submit" id="btnSubmitPencairan" disabled>
          <span>Catat Pencairan</span>
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
          </svg>
        </button>
      </div>

      <!-- ============ 3. DANA ESCROW PER PESANAN ============ -->
      <div class="card history-card">
        <h2 class="card-title">Dana Escrow per Pesanan</h2>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>TANGGAL & WAKTU</th>
                <th>NOMINAL</th>
                <th>TUJUAN</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody id="riwayatTableBody">
              <!-- Rendered via JavaScript / default HTML -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- ============ 4. RIWAYAT PENCAIRAN ============ -->
      <div class="card history-card">
        <h2 class="card-title">Riwayat Pencairan</h2>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID PENCAIRAN</th>
                <th>TANGGAL CAIR</th>
                <th>NOMINAL</th>
                <th>TUJUAN</th>
                <th>REFERENSI</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody id="riwayatPencairanBody">
              <!-- Rendered via JavaScript -->
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </main>

  <!-- ==================== TOAST NOTIFICATION ============ -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- ==================== JAVASCRIPT ==================== -->
  <script>
    (function() {
      let ESCROW_LIST = [];
      let PENDING_LIST = [];
      let RIWAYAT_PENCAIRAN = [];
      let SALDO_DITAHAN = 0;
      // Didefinisikan di halaman ini (sebelumnya tidak ada sehingga
      // muatKeuanganAdmin() gagal dan saldo selalu tampil Rp 0).
      function formatRupiah(nilai) {
        return 'Rp ' + Number(nilai || 0).toLocaleString('id-ID');
      }
      const BULAN_ID = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
      function formatTanggalApi(waktu) {
        if (!waktu) return '—';
        const m = String(waktu).match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})/);
        if (!m) return String(waktu);
        return {
          date: parseInt(m[3], 10) + ' ' + BULAN_ID[parseInt(m[2], 10) - 1] + ' ' + m[1],
          time: m[4] + ':' + m[5] + ' WIB'
        };
      }
      function labelStatusEscrow(status) {
        if (status === 'cair_dp') return 'DP Cair';
        if (status === 'cair_sisa') return 'Sisa Cair';
        if (status === 'kembali') return 'Kembali';
        return 'Ditahan';
      }

      function muatKeuanganAdmin() {
        return fetch('../../api/admin/keuangan.php')
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (!res.ok || !res.data) {
              renderSaldo();
              renderTable();
              renderPending();
              renderRiwayatPencairan();
              return;
            }
            const ringkasan = res.data.ringkasan || {};
            SALDO_DITAHAN = Number(ringkasan.masih_ditahan || 0);
            PENDING_LIST = res.data.pending || [];
            RIWAYAT_PENCAIRAN = res.data.riwayat_pencairan || [];
            ESCROW_LIST = (res.data.escrow || []).map(function (e) {
              const w = formatTanggalApi(e.created_at);
              return {
                date: (typeof w === 'object') ? w.date : '—',
                time: (typeof w === 'object') ? w.time : '',
                nominal: formatRupiah(Math.round(Number(e.jumlah_ditahan || 0))),
                target: (e.nama_perusahaan || '—') + ' — Order #' + e.order_id,
                targetSub: (e.nama_produk || '') + (e.nama_buyer ? ' • ' + e.nama_buyer : ''),
                status: labelStatusEscrow(e.status)
              };
            });
            renderSaldo();
            renderTable();
            renderPending();
            renderRiwayatPencairan();
          })
          .catch(function () { renderSaldo(); renderTable(); renderPending(); renderRiwayatPencairan(); });
      }

      const saldoDisplay = document.getElementById('saldoDisplay');
      const riwayatTableBody = document.getElementById('riwayatTableBody');
      const btnSubmitPencairan = document.getElementById('btnSubmitPencairan');
      const adminProfileBtn = document.getElementById('adminProfileBtn');
      const toastContainer = document.getElementById('toastContainer');

      function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast';
        let iconSvg = '';
        if (type === 'success') {
          iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        } else if (type === 'warning') {
          iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
        } else {
          iconSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        }

        toast.innerHTML = `<div class="toast-icon">${iconSvg}</div><div class="toast-message">${message}</div>`;
        toastContainer.appendChild(toast);

        setTimeout(() => {
          toast.classList.add('toast-leave');
          setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
          }, 250);
        }, 3200);
      }

      function renderSaldo() {
        saldoDisplay.textContent = formatRupiah(Math.round(SALDO_DITAHAN));
      }

      function renderTable() {
        riwayatTableBody.innerHTML = '';
        if (!ESCROW_LIST.length) {
          riwayatTableBody.innerHTML = '<tr class="empty-row"><td colspan="4">Belum ada dana escrow.</td></tr>';
          return;
        }
        ESCROW_LIST.forEach(item => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>
              <div class="td-date-title">${item.date}</div>
              <div class="td-date-sub">${item.time}</div>
            </td>
            <td>
              <div class="td-nominal">${item.nominal}</div>
            </td>
            <td>
              <div class="td-target-title">${item.target}</div>
              <div class="td-target-sub">${item.targetSub}</div>
            </td>
            <td>
              <span class="badge-success">
                <span class="badge-dot">●</span> ${item.status}
              </span>
            </td>
          `;
          riwayatTableBody.appendChild(tr);
        });
      }

      // ---- Form pencairan: centang baris yang sudah ditransfer ----
      const pendingListEl = document.getElementById('pendingList');
      const pilihSemuaEl = document.getElementById('pilihSemuaPending');
      const totalTerpilihEl = document.getElementById('totalTerpilih');
      const referensiEl = document.getElementById('referensiPencairan');

      function labelTahap(tahap) {
        return tahap === 1 ? 'DP 50%' : (tahap === 2 ? 'Pelunasan' : 'Tahap ' + tahap);
      }

      function rekeningText(p) {
        const bank = p.nama_bank || '—';
        const no = p.no_rekening || '—';
        const nama = p.atas_nama || (p.nama_perusahaan || '—');
        return bank + ' · ' + no + ' (' + nama + ')';
      }

      function renderPending() {
        if (!pendingListEl) return;
        pendingListEl.innerHTML = '';
        if (!PENDING_LIST.length) {
          pendingListEl.innerHTML = '<div class="pending-empty">Tidak ada dana siap dicairkan.</div>';
        }
        PENDING_LIST.forEach(function (p) {
          const label = document.createElement('label');
          label.className = 'pending-item';
          const cb = document.createElement('input');
          cb.type = 'checkbox';
          cb.className = 'pending-cb';
          cb.value = String(p.id);
          cb.addEventListener('change', updateTotalTerpilih);
          const teks = document.createElement('div');
          const nama = document.createElement('div');
          nama.className = 'pending-item-nama';
          nama.textContent = (p.nama_perusahaan || '—') + ' — Order #' + p.order_id + ' · ' + labelTahap(p.tahap);
          const sub = document.createElement('div');
          sub.className = 'pending-item-sub';
          sub.textContent = rekeningText(p);
          teks.appendChild(nama);
          teks.appendChild(sub);
          const nominal = document.createElement('div');
          nominal.className = 'pending-item-nominal';
          nominal.textContent = formatRupiah(Math.round(Number(p.nominal || 0)));
          label.appendChild(cb);
          label.appendChild(teks);
          label.appendChild(nominal);
          pendingListEl.appendChild(label);
        });
        if (pilihSemuaEl) pilihSemuaEl.checked = false;
        updateTotalTerpilih();
      }

      function updateTotalTerpilih() {
        let total = 0;
        let terpilih = 0;
        document.querySelectorAll('.pending-cb').forEach(function (cb) {
          if (!cb.checked) return;
          terpilih += 1;
          const item = PENDING_LIST.find(function (p) { return String(p.id) === cb.value; });
          if (item) total += Number(item.nominal || 0);
        });
        if (totalTerpilihEl) totalTerpilihEl.textContent = formatRupiah(Math.round(total));
        if (btnSubmitPencairan) btnSubmitPencairan.disabled = terpilih === 0;
        if (pilihSemuaEl) {
          const semua = document.querySelectorAll('.pending-cb');
          pilihSemuaEl.checked = semua.length > 0 && terpilih === semua.length;
        }
      }

      if (pilihSemuaEl) {
        pilihSemuaEl.addEventListener('change', function () {
          document.querySelectorAll('.pending-cb').forEach(function (cb) { cb.checked = pilihSemuaEl.checked; });
          updateTotalTerpilih();
        });
      }

      function catatPencairan() {
        const ids = [];
        document.querySelectorAll('.pending-cb:checked').forEach(function (cb) { ids.push(parseInt(cb.value, 10)); });
        if (!ids.length) { showToast('Pilih minimal satu baris pencairan', 'warning'); return; }
        if (btnSubmitPencairan) btnSubmitPencairan.disabled = true;
        fetch('../../api/admin/pencairan.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ids: ids, referensi: referensiEl ? referensiEl.value.trim() : '' })
        })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (!res.ok) {
              showToast('Gagal: ' + (res.error || 'tidak diketahui'), 'warning');
              updateTotalTerpilih();
              return;
            }
            showToast('Pencairan ' + res.data.dicatat + ' baris dicatat: ' + formatRupiah(Math.round(Number(res.data.total_nominal || 0))));
            if (referensiEl) referensiEl.value = '';
            muatKeuanganAdmin();
          })
          .catch(function () {
            showToast('Gagal menghubungi server.', 'warning');
            updateTotalTerpilih();
          });
      }

      if (btnSubmitPencairan) btnSubmitPencairan.addEventListener('click', catatPencairan);

      // ---- Riwayat pencairan (baris yang sudah 'berhasil') ----
      function renderRiwayatPencairan() {
        const body = document.getElementById('riwayatPencairanBody');
        if (!body) return;
        body.innerHTML = '';
        if (!RIWAYAT_PENCAIRAN.length) {
          body.innerHTML = '<tr class="empty-row"><td colspan="6">Belum ada pencairan yang dicatat.</td></tr>';
          return;
        }
        RIWAYAT_PENCAIRAN.forEach(function (p) {
          const w = formatTanggalApi(p.tanggal_cair);
          const tr = document.createElement('tr');
          tr.innerHTML = '\n            <td>\n              <div class="td-target-sub">' + (p.external_id || '—') + '</div>\n            </td>\n            <td>\n              <div class="td-date-title">' + ((typeof w === 'object') ? w.date : '—') + '</div>\n              <div class="td-date-sub">' + ((typeof w === 'object') ? w.time : '') + '</div>\n            </td>\n            <td>\n              <div class="td-nominal">' + formatRupiah(Math.round(Number(p.nominal || 0))) + '</div>\n            </td>\n            <td>\n              <div class="td-target-title">' + (p.nama_perusahaan || '—') + ' — Order #' + p.order_id + '</div>\n              <div class="td-target-sub">' + labelTahap(p.tahap) + ' · ' + rekeningText(p) + '</div>\n            </td>\n            <td>\n              <div class="td-target-sub">' + (p.referensi || '—') + '</div>\n            </td>\n            <td>\n              <span class="badge-success">\n                <span class="badge-dot">●</span> Berhasil\n              </span>\n            </td>\n          ';
          body.appendChild(tr);
        });
      }

      // Saldo dan riwayat di atas berasal dari escrow asli (API admin/keuangan.php).

      // Initial Render: muat dari API
      muatKeuanganAdmin();
    })();
  </script>

  <!-- Popup profil admin -->
  <script>
    function toggleAdminDropdown(e) {
      if (e) e.stopPropagation();
      document.getElementById('adminDropdown').classList.toggle('show');
      document.getElementById('adminProfileBtn').classList.toggle('open');
    }
    function closeAdminDropdown() {
      var d = document.getElementById('adminDropdown');
      var b = document.getElementById('adminProfileBtn');
      if (d) d.classList.remove('show');
      if (b) b.classList.remove('open');
    }
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.topbar-actions')) closeAdminDropdown();
    });
    document.getElementById('btnLogout').addEventListener('click', function() {
      // Langsung keluar tanpa confirm(): dialog bawaan browser membuat
      // halaman menggantung dan menghalangi logout.
      closeAdminDropdown();
      fetch('../../api/auth/logout.php').then(function() {
        window.location.href = '../login.php';
      });
    });
  </script>
</body>
</html>

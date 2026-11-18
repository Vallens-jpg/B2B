<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Transaksi - Staplr Admin</title>
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
       STAPLR ADMIN - DETAIL RIWAYAT (PIXEL-PERFECT REPLICA)
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

    .logo-link {
      display: inline-block;
      text-decoration: none;
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

    /* Active Riwayat state */
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

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 14px;
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
      user-select: none;
      padding: 6px 10px;
      border-radius: 8px;
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
    }

    /* ============ DETAIL CARD ============ */
    .detail-card {
      background: #ffffff;
      border: 1px solid #edf1f6;
      border-radius: 16px;
      padding: 28px 32px;
      box-shadow: 0 2px 12px -2px rgba(18, 47, 81, 0.04);
      width: 100%;
    }

    .detail-title {
      font-size: 18px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.2px;
      margin-bottom: 20px;
    }

    .info-group {
      margin-bottom: 16px;
    }

    .info-label {
      font-size: 12px;
      color: #94a3b8;
      font-weight: 500;
      margin-bottom: 4px;
    }

    .info-value {
      font-size: 14px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .divider {
      border: 0;
      border-top: 1px solid #edf1f6;
      margin: 18px 0;
    }

    /* Financial Summary Box */
    .summary-box {
      background: #f8fafc;
      border: 1px solid #edf1f6;
      border-radius: 8px;
      padding: 16px 20px;
      margin: 22px 0 18px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .summary-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 13px;
    }

    .summary-row .label {
      color: #64748b;
    }

    .summary-row .val {
      font-weight: 700;
      color: #0f2a4a;
    }

    .summary-row.total-row {
      margin-top: 4px;
      padding-top: 12px;
      font-size: 14px;
    }

    .summary-row.total-row .label,
    .summary-row.total-row .val {
      font-weight: 800;
      color: #0f2a4a;
      font-size: 14.5px;
    }

    .notes-title {
      font-size: 13px;
      font-weight: 700;
      color: #0f2a4a;
      margin-top: 22px;
      margin-bottom: 14px;
    }

    /* Action Buttons */
    .btn-action-primary {
      width: 100%;
      height: 44px;
      background: #143358;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.15s ease;
      margin-bottom: 10px;
      text-decoration: none;
    }

    .btn-action-primary:hover {
      background: #0f2a4a;
      transform: translateY(-1px);
    }

    .btn-action-secondary {
      width: 100%;
      height: 44px;
      background: #ffffff;
      color: #0f2a4a;
      border: 1px solid #d0d5dd;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.15s ease;
      text-decoration: none;
    }

    .btn-action-secondary:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      transform: translateY(-1px);
    }

    /* ============ BUKTI TRANSFER MODAL ============ */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(15, 42, 74, 0.55);
      backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.2s ease, visibility 0.2s ease;
      padding: 20px;
    }

    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-box {
      background: #ffffff;
      border-radius: 16px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 12px 36px rgba(15, 42, 74, 0.18);
      overflow: hidden;
      transform: translateY(12px) scale(0.98);
      transition: transform 0.2s ease;
    }

    .modal-overlay.active .modal-box {
      transform: translateY(0) scale(1);
    }

    .modal-header {
      padding: 18px 24px;
      border-bottom: 1px solid #edf1f6;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fafcff;
    }

    .modal-header-title {
      font-size: 16px;
      font-weight: 800;
      color: #0f2a4a;
    }

    .modal-close-btn {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: none;
      background: #f1f5f9;
      color: #64748b;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.15s ease;
    }

    .modal-close-btn:hover {
      background: #e2e8f0;
      color: #0f2a4a;
    }

    /* Receipt Card */
    .receipt-body {
      padding: 24px;
    }

    .receipt-card {
      background: #fdfefe;
      border: 1px solid #e8ecf2;
      border-radius: 12px;
      padding: 20px;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }

    .receipt-top {
      text-align: center;
      padding-bottom: 16px;
      border-bottom: 1px dashed #cbd5e1;
      margin-bottom: 16px;
    }

    .receipt-bank-badge {
      display: inline-block;
      background: #00529c;
      color: #ffffff;
      font-weight: 800;
      font-size: 13px;
      padding: 4px 12px;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .receipt-amount-title {
      font-size: 12px;
      color: #64748b;
      margin-bottom: 2px;
    }

    .receipt-amount {
      font-size: 24px;
      font-weight: 800;
      color: #0f2a4a;
    }

    .receipt-status-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ecfdf5;
      color: #059669;
      font-size: 12px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 999px;
      margin-top: 6px;
    }

    .receipt-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      font-size: 12.5px;
    }

    .receipt-item {
      display: flex;
      justify-content: space-between;
    }

    .receipt-item .item-label {
      color: #64748b;
    }

    .receipt-item .item-val {
      font-weight: 700;
      color: #0f2a4a;
      text-align: right;
    }

    .receipt-footer-actions {
      padding: 16px 24px 20px;
      border-top: 1px solid #edf1f6;
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      background: #fafcff;
    }

    .btn-receipt-action {
      height: 38px;
      padding: 0 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s ease;
    }

    .btn-receipt-secondary {
      background: #ffffff;
      border: 1px solid #d0d5dd;
      color: #0f2a4a;
    }

    .btn-receipt-secondary:hover {
      background: #f1f5f9;
    }

    .btn-receipt-primary {
      background: #0f2a4a;
      border: none;
      color: #ffffff;
    }

    .btn-receipt-primary:hover {
      background: #143358;
    }

    /* ============ INVOICE MODAL / PRINT VIEW ============ */
    .invoice-card-content {
      padding: 28px;
      font-size: 13px;
    }

    .inv-header {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px solid #0f2a4a;
      padding-bottom: 16px;
      margin-bottom: 20px;
    }

    .inv-brand {
      font-size: 20px;
      font-weight: 900;
      color: #0f2a4a;
    }

    .inv-tagline {
      font-size: 11.5px;
      color: #64748b;
    }

    .inv-num {
      text-align: right;
      font-size: 15px;
      font-weight: 800;
      color: #0f2a4a;
    }

    .inv-date {
      font-size: 11.5px;
      color: #64748b;
    }

    .inv-table {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
    }

    .inv-table th {
      background: #f1f5f9;
      padding: 8px 12px;
      text-align: left;
      font-size: 11.5px;
      color: #475569;
    }

    .inv-table td {
      padding: 10px 12px;
      border-bottom: 1px solid #edf1f6;
      font-size: 12.5px;
    }

    /* ============ TOAST ============ */
    .toast {
      position: fixed;
      bottom: 28px;
      right: 28px;
      background: #0f2a4a;
      color: #ffffff;
      padding: 14px 22px;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
      font-size: 13.5px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      z-index: 9999;
      pointer-events: none;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
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
      .detail-card {
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

      <!-- 4. Keuangan -->
      <a class="menu-item" href="keuangan.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="none" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
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

      <!-- 6. Riwayat (Active) -->
      <a class="menu-item active" href="riwayat.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="rgba(255,199,0,0.25)" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
        <span>Riwayat</span>
      </a>
    </nav>
  </aside>

  <!-- ==================== MAIN CONTENT ==================== -->
  <main class="main">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <a href="riwayat.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
          <h1>Riwayat</h1>
        </a>
      </div>
      <div class="topbar-actions">
        <div class="admin-profile" id="adminProfileBtn" title="Profil Admin">
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

      <!-- Detail Card (Exact Replication) -->
      <div class="detail-card">
        <h3 class="detail-title">Detail Transaksi</h3>

        <div class="info-group">
          <div class="info-label">ID Transaksi</div>
          <div class="info-value" id="valTrxId">—</div>
        </div>

        <hr class="divider">

        <div class="info-group">
          <div class="info-label">Pembeli</div>
          <div class="info-value" id="valBuyer">—</div>
        </div>

        <div class="info-group">
          <div class="info-label">Metode Pembayaran</div>
          <div class="info-value" id="valPaymentMethod">—</div>
        </div>

        <div class="info-group">
          <div class="info-label">Waktu Transaksi</div>
          <div class="info-value" id="valTrxTime">—</div>
        </div>

        <!-- Financial Summary Box -->
        <div class="summary-box">
          <div class="summary-row">
            <span class="label">Subtotal</span>
            <span class="val" id="valSubtotal">Rp 0</span>
          </div>
          <div class="summary-row">
            <span class="label">Biaya Pengiriman</span>
            <span class="val" id="valShipping">Rp 0</span>
          </div>
          <div class="summary-row">
            <span class="label">Diskon</span>
            <span class="val" id="valDiscount">Rp 0</span>
          </div>
          <div class="summary-row total-row">
            <span class="label">Total</span>
            <span class="val" id="valTotal">Rp 0</span>
          </div>
        </div>

        <!-- Notes & Action Buttons -->
        <div class="notes-title">Catatan Pembayaran</div>
        <button type="button" class="btn-action-primary" id="btnViewTransferProof">
          Lihat Bukti Transfer
        </button>
        <button type="button" class="btn-action-secondary" id="btnPrintInvoice">
          Cetak Invoice
        </button>
      </div>

    </section>
  </main>

  <!-- ==================== MODAL BUKTI TRANSFER ==================== -->
  <div class="modal-overlay" id="transferProofModal">
    <div class="modal-box">
      <div class="modal-header">
        <h4 class="modal-header-title">Bukti Transfer Pembayaran</h4>
        <button type="button" class="modal-close-btn" id="btnCloseProofModal">&times;</button>
      </div>
      <div class="receipt-body">
        <div class="receipt-card">
          <div class="receipt-top">
            <span class="receipt-bank-badge" id="proofBankName">—</span>
            <div class="receipt-amount-title">Total Transfer</div>
            <div class="receipt-amount" id="proofAmount">Rp 0</div>
            <div>
              <span class="receipt-status-pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span id="proofStatusText">—</span>
              </span>
            </div>
          </div>
          <div class="receipt-list">
            <div class="receipt-item">
              <span class="item-label">No. Referensi</span>
              <span class="item-val" id="proofRef">—</span>
            </div>
            <div class="receipt-item">
              <span class="item-label">Waktu</span>
              <span class="item-val" id="proofTime">—</span>
            </div>
            <div class="receipt-item">
              <span class="item-label">Pengirim</span>
              <span class="item-val" id="proofSender">—</span>
            </div>
            <div class="receipt-item">
              <span class="item-label">Rekening Pengirim</span>
              <span class="item-val">BCA •••• 9412</span>
            </div>
            <div class="receipt-item">
              <span class="item-label">Penerima</span>
              <span class="item-val">PT STAPLR SEMBAKO INDONESIA</span>
            </div>
            <div class="receipt-item">
              <span class="item-label">Rekening Tujuan</span>
              <span class="item-val">—</span>
            </div>
          </div>
        </div>
      </div>
      <div class="receipt-footer-actions">
        <button type="button" class="btn-receipt-action btn-receipt-secondary" id="btnCloseProofModal2">Tutup</button>
        <button type="button" class="btn-receipt-action btn-receipt-primary" id="btnDownloadProof">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Unduh Bukti</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ==================== MODAL CETAK INVOICE ==================== -->
  <div class="modal-overlay" id="invoiceModal">
    <div class="modal-box" style="max-width: 540px;">
      <div class="modal-header">
        <h4 class="modal-header-title">Invoice Pembelian</h4>
        <button type="button" class="modal-close-btn" id="btnCloseInvoiceModal">&times;</button>
      </div>
      <div class="invoice-card-content" id="printableInvoiceArea">
        <div class="inv-header">
          <div>
            <div class="inv-brand">STAPLR</div>
            <div class="inv-tagline">Solusi Pengadaan Sembako B2B</div>
          </div>
          <div>
            <div class="inv-num" id="invModalId">—</div>
            <div class="inv-date" id="invModalDate">Tanggal: —</div>
          </div>
        </div>

        <div style="margin-bottom: 16px;">
          <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Ditujukan Kepada:</div>
          <div style="font-weight: 700; color: #0f2a4a; font-size: 14px;" id="invModalCustomer">—</div>
          <div style="color: #64748b; font-size: 12px;">Metode Pembayaran: <span id="invModalPayment" style="font-weight: 600; color: #0f2a4a;">—</span></div>
        </div>

        <table class="inv-table">
          <thead>
            <tr>
              <th>Deskripsi Produk</th>
              <th style="text-align: right;">Jumlah</th>
              <th style="text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div style="font-weight: 600; color: #0f2a4a;" id="invModalProduct">—</div>
                <div style="font-size: 11px; color: #64748b;">Kualitas Premium Terjamin</div>
              </td>
              <td style="text-align: right;" id="invModalQty">—</td>
              <td style="text-align: right; font-weight: 600;" id="invModalSubtotal">Rp 0</td>
            </tr>
          </tbody>
        </table>

        <div style="display: flex; flex-direction: column; gap: 6px; padding-top: 10px; border-top: 1px solid #edf1f6; font-size: 12.5px;">
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #64748b;">Subtotal:</span>
            <span style="font-weight: 600;" id="invModalSubtotal2">Rp 0</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #64748b;">Biaya Pengiriman:</span>
            <span style="font-weight: 600;" id="invModalShipping">Rp 0</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #64748b;">Diskon:</span>
            <span style="font-weight: 600;" id="invModalDiscount">Rp 0</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 800; color: #0f2a4a; padding-top: 8px; border-top: 1px dashed #cbd5e1;">
            <span>Total Akhir:</span>
            <span id="invModalTotal">Rp 0</span>
          </div>
        </div>
      </div>
      <div class="receipt-footer-actions">
        <button type="button" class="btn-receipt-action btn-receipt-secondary" id="btnCloseInvoiceModal2">Tutup</button>
        <button type="button" class="btn-receipt-action btn-receipt-primary" id="btnExecutePrint">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
          </svg>
          <span>Cetak Sekarang</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div class="toast" id="toastBox">
    <span id="toastMessage">Aksi berhasil dilakukan</span>
  </div>

  <!-- ==================== JAVASCRIPT ==================== -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Data Transaksi
      const BULAN_ID = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
      function formatRupiah(nilai) {
        return 'Rp ' + Math.round(Number(nilai || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      }
      function formatTanggalApi(waktu) {
        if (!waktu) return '—';
        const mm = String(waktu).match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})/);
        if (!mm) return String(waktu);
        return parseInt(mm[3], 10) + ' ' + BULAN_ID[parseInt(mm[2], 10) - 1] + ' ' + mm[1] + ', ' + mm[4] + ':' + mm[5] + ' WIB';
      }
      function labelStatusOrder(status) {
        if (status === 'selesai') return 'Transaksi Berhasil';
        if (status === 'dibatalkan') return 'Dibatalkan';
        if (status === 'menunggu_bayar') return 'Menunggu Pembayaran';
        return 'Diproses';
      }

      // Muat detail order asli dari API (?id=<angka order>).
      const urlParams = new URLSearchParams(window.location.search);
      const orderIdApi = parseInt(urlParams.get('id') || '0', 10) || 0;
      if (orderIdApi <= 0) {
        showToast('Transaksi tidak ditemukan — buka dari daftar riwayat.', 'info');
      } else {
        fetch('../../api/order/detail.php?id=' + orderIdApi)
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (!res.ok || !res.data || !res.data.order) {
              showToast('Transaksi tidak ditemukan.', 'info');
              return;
            }
            const o = res.data.order;
            const totalBayar = Number(o.total_bayar || 0);
            const buyer = o.nama_buyer + (o.nama_perusahaan ? ' (' + o.nama_perusahaan + ')' : '');
            const nomorInvoice = o.nomor_invoice || '—';
            const waktu = formatTanggalApi(o.created_at);

            // Populate Elements
            document.getElementById('valTrxId').textContent = '#ORD-2026-' + String(o.id).padStart(3, '0');
            document.getElementById('valBuyer').textContent = buyer;
            document.getElementById('valPaymentMethod').textContent = nomorInvoice;
            document.getElementById('valTrxTime').textContent = waktu;
            document.getElementById('valSubtotal').textContent = formatRupiah(o.total);
            document.getElementById('valShipping').textContent = 'Rp 0';
            document.getElementById('valDiscount').textContent = 'Rp 0';
            document.getElementById('valTotal').textContent = formatRupiah(totalBayar);

            // Modal Proof Data
            document.getElementById('proofBankName').textContent = '—';
            document.getElementById('proofAmount').textContent = formatRupiah(totalBayar);
            document.getElementById('proofStatusText').textContent = labelStatusOrder(o.status);
            document.getElementById('proofRef').textContent = nomorInvoice;
            document.getElementById('proofTime').textContent = waktu;
            document.getElementById('proofSender').textContent = buyer;

            // Modal Invoice Data
            document.getElementById('invModalId').textContent = nomorInvoice;
            document.getElementById('invModalDate').textContent = 'Tanggal: ' + waktu;
            document.getElementById('invModalCustomer').textContent = buyer;
            document.getElementById('invModalPayment').textContent = nomorInvoice;
            document.getElementById('invModalProduct').textContent = o.nama_produk || '—';
            document.getElementById('invModalQty').textContent = Number(o.jumlah_kg || 0).toLocaleString('id-ID') + ' Kg';
            document.getElementById('invModalSubtotal').textContent = formatRupiah(o.total);
            document.getElementById('invModalSubtotal2').textContent = formatRupiah(o.total);
            document.getElementById('invModalShipping').textContent = 'Rp 0';
            document.getElementById('invModalDiscount').textContent = 'Rp 0';
            document.getElementById('invModalTotal').textContent = formatRupiah(totalBayar);
          })
          .catch(function () { showToast('Gagal memuat transaksi.', 'info'); });
      }

      // Toast function
      let toastTimer = null;
      function showToast(message) {
        const toast = document.getElementById('toastBox');
        const text = document.getElementById('toastMessage');
        text.textContent = message;
        toast.classList.add('show');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
          toast.classList.remove('show');
        }, 3000);
      }

      // Proof Modal Handlers
      const transferProofModal = document.getElementById('transferProofModal');
      const btnViewTransferProof = document.getElementById('btnViewTransferProof');
      const btnCloseProofModal = document.getElementById('btnCloseProofModal');
      const btnCloseProofModal2 = document.getElementById('btnCloseProofModal2');
      const btnDownloadProof = document.getElementById('btnDownloadProof');

      btnViewTransferProof.addEventListener('click', () => {
        transferProofModal.classList.add('active');
      });

      function closeProofModal() {
        transferProofModal.classList.remove('active');
      }

      btnCloseProofModal.addEventListener('click', closeProofModal);
      btnCloseProofModal2.addEventListener('click', closeProofModal);
      transferProofModal.addEventListener('click', (e) => {
        if (e.target === transferProofModal) closeProofModal();
      });

      btnDownloadProof.addEventListener('click', () => {
        showToast('Mengunduh berkas bukti transfer (' + currentData.id + '.pdf)...');
        setTimeout(() => {
          showToast('Bukti transfer ' + currentData.id + ' berhasil diunduh');
          closeProofModal();
        }, 1200);
      });

      // Invoice Modal Handlers
      const invoiceModal = document.getElementById('invoiceModal');
      const btnPrintInvoice = document.getElementById('btnPrintInvoice');
      const btnCloseInvoiceModal = document.getElementById('btnCloseInvoiceModal');
      const btnCloseInvoiceModal2 = document.getElementById('btnCloseInvoiceModal2');
      const btnExecutePrint = document.getElementById('btnExecutePrint');

      btnPrintInvoice.addEventListener('click', () => {
        invoiceModal.classList.add('active');
      });

      function closeInvoiceModal() {
        invoiceModal.classList.remove('active');
      }

      btnCloseInvoiceModal.addEventListener('click', closeInvoiceModal);
      btnCloseInvoiceModal2.addEventListener('click', closeInvoiceModal);
      invoiceModal.addEventListener('click', (e) => {
        if (e.target === invoiceModal) closeInvoiceModal();
      });

      btnExecutePrint.addEventListener('click', () => {
        showToast('Membuka dialog pencetakan invoice...');
        setTimeout(() => {
          window.print();
        }, 500);
      });

      // Admin Profile Handler
      const adminProfileBtn = document.getElementById('adminProfileBtn');
      adminProfileBtn.addEventListener('click', () => {
        toggleAdminDropdown(event);
      });
    });
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
      if (confirm('Keluar dari akun admin?')) {
        closeAdminDropdown();
        showToast('Anda telah keluar dari akun admin.', 'info');
      }
    });
  </script>
</body>
</html>
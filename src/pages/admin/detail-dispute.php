<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Dispute - Staplr Admin</title>
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
       STAPLR ADMIN - DETAIL DISPUTE (PIXEL-PERFECT REPLICA)
       Reference: assets/tampilan admin/5. Detail Dispute.png
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

    /* Active Dispute state: dark teal text, translucent pill, golden yellow icon */
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
    }

    /* Kembali ke Daftar Button */
    .btn-back {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      height: 42px;
      padding: 0 22px;
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 700;
      color: #0f2a4a;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.15s ease;
      margin-bottom: 22px;
      box-shadow: 0 1px 2px rgba(15, 42, 74, 0.02);
    }

    .btn-back:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    /* Content Title */
    .content-title {
      font-size: 20px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.2px;
      margin-bottom: 18px;
    }

    /* ============ STEPPER / BREADCRUMBS ============ */
    .stepper-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 26px;
      flex-wrap: wrap;
    }

    .step-pill {
      height: 32px;
      padding: 0 16px;
      border-radius: 999px;
      font-size: 12.5px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      color: #64748b;
      border: 1px solid #e8ecf2;
      transition: all 0.15s ease;
      white-space: nowrap;
    }

    .step-pill.active {
      border: 1.5px solid #0f2a4a;
      color: #0f2a4a;
      font-weight: 700;
      background: #ffffff;
    }

    .step-arrow {
      color: #94a3b8;
      font-size: 14px;
      user-select: none;
    }

    /* ============ TWO CARDS GRID ============ */
    .detail-grid {
      display: grid;
      grid-template-columns: 338px 1fr;
      gap: 24px;
      align-items: start;
    }

    /* Common Card Styling */
    .card {
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 16px;
      box-shadow: 0 2px 12px -2px rgba(18, 47, 81, 0.04);
      padding: 24px 28px;
    }

    /* ============ LEFT CARD: RINGKASAN KASUS ============ */
    .left-card h3 {
      font-size: 18px;
      font-weight: 800;
      color: #0f2a4a;
      margin-bottom: 22px;
      letter-spacing: -0.2px;
    }

    .case-group {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .case-item {
      display: flex;
      flex-direction: column;
    }

    .case-label {
      font-size: 12px;
      color: #94a3b8;
      font-weight: 500;
      margin-bottom: 3px;
    }

    .case-value {
      font-size: 14.5px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .case-divider {
      height: 1px;
      background: #edf1f6;
      margin: 16px 0;
    }

    /* Badges */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      white-space: nowrap;
      height: 26px;
      box-sizing: border-box;
      width: fit-content;
    }

    .badge-dot {
      font-size: 9px;
      line-height: 1;
    }

    .badge-pending {
      background: #fff4cc;
      color: #8a6400;
      border: 1px solid #fde68a;
    }

    .badge-approved {
      background: #e3f6ec;
      color: #14803f;
      border: 1px solid #a7f3d0;
    }

    .badge-rejected {
      background: #fde8e8;
      color: #b42318;
      border: 1px solid #fecaca;
    }

    .badge-waiting {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }

    /* ============ RIGHT CARD: DETAIL DISPUTE ============ */
    .right-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .right-card-header h3 {
      font-size: 18px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.2px;
    }

    .field-section {
      margin-bottom: 22px;
    }

    .field-label {
      display: block;
      font-size: 13.5px;
      font-weight: 700;
      color: #0f2a4a;
      margin-bottom: 8px;
    }

    .field-box {
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 8px;
      padding: 13px 16px;
      font-size: 14px;
      color: #0f2a4a;
      line-height: 1.4;
    }

    /* Attachment Rows */
    .attachment-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .attachment-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 14px;
      border: 1px solid #e8ecf2;
      border-radius: 8px;
      background: #ffffff;
      transition: border-color 0.15s ease;
    }

    .attachment-item:hover {
      border-color: #cbd5e1;
    }

    .attachment-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .attachment-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      border: 1px solid #e8ecf2;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      color: #0f2a4a;
    }

    .attachment-icon svg {
      width: 20px;
      height: 20px;
      stroke: #0f2a4a;
    }

    .attachment-info {
      display: flex;
      flex-direction: column;
    }

    .attachment-title {
      font-size: 13.5px;
      font-weight: 700;
      color: #0f2a4a;
      line-height: 1.3;
    }

    .attachment-sub {
      font-size: 12px;
      color: #64748b;
      line-height: 1.3;
    }

    .attachment-tag {
      font-size: 12px;
      font-weight: 600;
      color: #94a3b8;
      letter-spacing: 0.5px;
    }

    /* Action Buttons Row */
    .action-row {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 14px;
      margin-top: 30px;
    }

    .btn-reject {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      height: 42px;
      padding: 0 42px;
      background: #ffffff;
      border: 1px solid #d0d5dd;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      color: #0f2a4a;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-reject:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    .btn-forward {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      height: 42px;
      padding: 0 26px;
      background: #0f2a4a;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      color: #ffffff;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-forward:hover {
      background: #17385f;
    }

    /* Toast Notification */
    .staplr-toast {
      position: fixed;
      bottom: 28px;
      right: 28px;
      padding: 14px 20px;
      border-radius: 10px;
      background: #0f2a4a;
      color: #ffffff;
      font-size: 13.5px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 8px 24px rgba(15, 42, 74, 0.2);
      z-index: 9999;
      transform: translateY(60px);
      opacity: 0;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      pointer-events: none;
    }

    .staplr-toast.show {
      transform: translateY(0);
      opacity: 1;
      pointer-events: auto;
    }

    .staplr-toast.success {
      background: #065f46;
      border: 1px solid #059669;
    }

    .staplr-toast.danger {
      background: #991b1b;
      border: 1px solid #dc2626;
    }

    .staplr-toast.info {
      background: #0f2a4a;
      border: 1px solid #1e3a8a;
    }

    /* Attachment item hover */
    .attachment-item {
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .attachment-item:hover {
      border-color: #94a3b8;
      background: #f8fafc;
      transform: translateY(-1px);
    }

    /* Modal Backdrop & Lightbox */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 42, 74, 0.6);
      backdrop-filter: blur(3px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9998;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s ease;
      padding: 20px;
    }

    .modal-backdrop.open {
      opacity: 1;
      pointer-events: auto;
    }

    .modal-card {
      background: #ffffff;
      border-radius: 16px;
      width: 100%;
      max-width: 520px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(15, 42, 74, 0.2);
      transform: scale(0.95);
      transition: transform 0.2s ease;
    }

    .modal-backdrop.open .modal-card {
      transform: scale(1);
    }

    .modal-header {
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #edf1f6;
    }

    .modal-header h4 {
      font-size: 15px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .btn-modal-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 6px;
      transition: background 0.15s;
    }

    .btn-modal-close:hover {
      background: #f1f5f9;
      color: #0f2a4a;
    }

    .modal-body {
      padding: 24px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 14px;
      text-align: center;
    }

    .modal-preview-box {
      width: 100%;
      height: 220px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: #64748b;
    }

    .modal-footer {
      padding: 14px 20px;
      background: #f8fafc;
      border-top: 1px solid #edf1f6;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .btn-modal-action {
      height: 36px;
      padding: 0 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
    }

    .btn-modal-secondary {
      background: #ffffff;
      border: 1px solid #d0d5dd;
      color: #344054;
    }

    .btn-modal-primary {
      background: #0f2a4a;
      border: none;
      color: #ffffff;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 1100px) {
      .detail-grid {
        grid-template-columns: 1fr;
      }
    }

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
      .action-row {
        flex-direction: column;
        width: 100%;
      }
      .btn-reject, .btn-forward {
        width: 100%;
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

      <!-- 3. Dispute (Active) -->
      <a class="menu-item active" href="dispute.php">
        <span class="icon">
          
          <svg viewBox="0 0 24 24" fill="rgba(255,199,0,0.25)" stroke="#ffc700" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
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
      <h1>Dispute</h1>
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
      <!-- Back Button -->
      <a href="dispute.php" class="btn-back">Kembali ke Daftar</a>

      <!-- Content Heading -->
      <h2 class="content-title">Detail Dispute</h2>

      <!-- Stepper / Breadcrumbs -->
      <div class="stepper-row">
        <a href="dispute.php" class="step-pill">Daftar Retur</a>
        <span class="step-arrow">→</span>
        <div class="step-pill active">Detail</div>
        <span class="step-arrow">→</span>
        <a href="verifikasi-dispute.php" id="stepperVerifikasiLink" class="step-pill">Verifikasi Admin</a>
      </div>

      <!-- Two Cards Layout -->
      <div class="detail-grid">

        <!-- ============ LEFT CARD: RINGKASAN KASUS ============ -->
        <div class="card left-card">
          <h3>Ringkasan Kasus</h3>

          <div class="case-group">
            <div class="case-item">
              <span class="case-label">ID Retur</span>
              <span class="case-value" id="caseIdRetur">—</span>
            </div>
            <div class="case-item">
              <span class="case-label">ID Pesanan</span>
              <span class="case-value" id="caseIdPesanan">—</span>
            </div>
          </div>

          <div class="case-divider"></div>

          <div class="case-group">
            <div class="case-item">
              <span class="case-label">Pembeli</span>
              <span class="case-value" id="casePembeli">—</span>
            </div>
            <div class="case-item">
              <span class="case-label">Produk</span>
              <span class="case-value" id="caseProduk">—</span>
            </div>
          </div>

          <div class="case-divider"></div>

          <div class="case-group">
            <div class="case-item">
              <span class="case-label">Tgl Pembelian</span>
              <span class="case-value" id="caseTglBeli">—</span>
            </div>
            <div class="case-item">
              <span class="case-label">Tgl Komplain</span>
              <span class="case-value" id="caseTglKomplain">—</span>
            </div>
          </div>

          <div class="case-divider"></div>

          <div class="case-group">
            <div class="case-item">
              <span class="case-label">Alasan Utama</span>
              <span class="case-value" id="caseAlasan">—</span>
            </div>
            <div class="case-item">
              <span class="case-label">Bukti</span>
              <span class="case-value" id="caseBukti">—</span>
            </div>
          </div>

          <div class="case-divider"></div>

          <div class="case-group">
            <div class="case-item">
              <span class="case-label">Status Saat Ini</span>
              <span id="caseStatusBadge">
                <span class="badge badge-pending">
                  <span class="badge-dot">●</span> Pending
                </span>
              </span>
            </div>
          </div>
        </div>

        <!-- ============ RIGHT CARD: DETAIL DISPUTE ============ -->
        <div class="card right-card">
          <!-- Header with Title & Badge -->
          <div class="right-card-header">
            <h3 id="rightCardTitle">Detail Dispute —</h3>
            <span id="rightCardBadge">
              <span class="badge badge-pending">
                <span class="badge-dot">●</span> Pending
              </span>
            </span>
          </div>

          <!-- Section 1: Komplain -->
          <div class="field-section">
            <label class="field-label">Komplain</label>
            <div class="field-box" id="fieldKomplain">—</div>
          </div>

          <!-- Section 2: Bukti -->
          <div class="field-section">
            <label class="field-label">Bukti</label>
            <div class="attachment-list" id="attachmentList">
              <!-- Dynamically populated -->
            </div>
          </div>

          <!-- Section 3: Usulan Penyelesaian -->
          <div class="field-section">
            <label class="field-label">Usulan Penyelesaian</label>
            <div class="field-box" id="fieldUsulan">—</div>
          </div>

          <!-- Section 4: Action Buttons -->
          <div class="action-row">
            <button type="button" class="btn-reject" id="btnRejectDispute">Tolak</button>
            <a href="verifikasi-dispute.php" class="btn-forward" id="btnForwardDispute">Teruskan ke Verifikasi ke 2 Pihak</a>
          </div>

        </div>

      </div>
    </section>
  </main>

  <!-- Modal Preview Lampiran / Lightbox -->
  <div id="attachmentModal" class="modal-backdrop">
    <div class="modal-card">
      <div class="modal-header">
        <h4 id="modalAttachmentTitle">Pratinjau Lampiran Bukti</h4>
        <button type="button" class="btn-modal-close" id="btnCloseModal" title="Tutup">✕</button>
      </div>
      <div class="modal-body">
        <div class="modal-preview-box">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#0F2A4A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
          <div style="font-weight: 600; font-size: 14px; color: #0F2A4A;" id="modalPreviewName">Foto 1 - Kerusakan Tray Telur</div>
          <div style="font-size: 12.5px; color: #64748B;" id="modalPreviewDesc">Lampiran foto 1 dari pembeli</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modal-action btn-modal-secondary" id="btnModalCloseAction">Tutup</button>
        <button type="button" class="btn-modal-action btn-modal-primary" id="btnModalDownload">Unduh Berkas</button>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toastNotification" class="staplr-toast"></div>

  <!-- Client-side Logic Script -->
  <script>
    // ==========================================
    // STAPLR ADMIN - DETAIL DISPUTE LOGIC
    // ==========================================

    // ID dispute asli (angka) dari ?id= — dipakai untuk memanggil API.

    const BULAN_ID = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    function formatTanggalApi(waktu) {
      if (!waktu) return '—';
      const m = String(waktu).match(/^(\d{4})-(\d{2})-(\d{2})/);
      if (!m) return String(waktu);
      return parseInt(m[3], 10) + ' ' + BULAN_ID[parseInt(m[2], 10) - 1] + ' ' + m[1];
    }

    // Badge untuk status asli dari API (dibuka/diproses/diputuskan).
    function badgeForApi(d) {
      if (d.status === 'diproses') return '<span class="badge badge-waiting"><span class="badge-dot">●</span> Diproses</span>';
      if (d.status === 'diputuskan' && d.putusan === 'uang_kembali') return '<span class="badge badge-approved"><span class="badge-dot">●</span> Diputuskan: Uang Kembali</span>';
      if (d.status === 'diputuskan' && d.putusan === 'lanjut_perusahaan') return '<span class="badge badge-rejected"><span class="badge-dot">●</span> Diputuskan: Lanjut Perusahaan</span>';
      return '<span class="badge badge-pending"><span class="badge-dot">●</span> Pending</span>';
    }

    function showToast(message, type = 'info') {
      const toast = document.getElementById('toastNotification');
      toast.className = `staplr-toast ${type} show`;
      toast.textContent = message;
      setTimeout(() => {
        toast.className = 'staplr-toast';
      }, 2500);
    }

    document.addEventListener('DOMContentLoaded', () => {
      // 1. ID dispute asli dari ?id=<angka> saja.
      const urlParams = new URLSearchParams(window.location.search);
      const disputeIdApi = parseInt(urlParams.get('id') || '0', 10) || 0;
      const verifikasiUrlApi = 'verifikasi-dispute.php?id=' + disputeIdApi;
      const stepperLink = document.getElementById('stepperVerifikasiLink');
      const btnForward = document.getElementById('btnForwardDispute');
      if (stepperLink) stepperLink.href = verifikasiUrlApi;
      if (btnForward) btnForward.href = verifikasiUrlApi;

      // 2. Muat detail asli dari API (nama pembeli/produk dari detail order).
      if (disputeIdApi <= 0) {
        document.getElementById('rightCardTitle').textContent = 'Dispute tidak ditemukan — buka dari daftar dispute.';
        renderAttachments([]);
      } else {
        fetch('../../api/dispute/detail.php?id=' + disputeIdApi)
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (!res.ok || !res.data) {
              showToast('Dispute tidak ditemukan.', 'info');
              return;
            }
            const d = res.data;
            document.getElementById('caseIdRetur').textContent = 'Dispute #' + d.id;
            document.getElementById('caseIdPesanan').textContent = 'Order #' + d.order_id;
            document.getElementById('casePembeli').textContent = 'Pembeli #' + d.buyer_id;
            document.getElementById('caseProduk').textContent = 'Produk #' + d.produk_id;
            document.getElementById('caseTglBeli').textContent = '—';
            document.getElementById('caseTglKomplain').textContent = formatTanggalApi(d.created_at);
            document.getElementById('caseAlasan').textContent = d.judul || '—';
            document.getElementById('caseBukti').textContent = d.bukti_foto ? '1 Foto' : 'Tidak ada bukti foto';
            document.getElementById('caseStatusBadge').innerHTML = badgeForApi(d);
            document.getElementById('rightCardTitle').textContent = 'Detail Dispute #' + d.id;
            document.getElementById('rightCardBadge').innerHTML = badgeForApi(d);
            document.getElementById('fieldKomplain').textContent = d.deskripsi || '—';
            document.getElementById('fieldUsulan').textContent = '—';
            const att = d.bukti_foto
              ? [{ type: String(d.bukti_foto).split('.').pop().toUpperCase(), title: d.bukti_foto, desc: 'Lampiran foto bukti dari pembeli' }]
              : [];
            renderAttachments(att);
            return fetch('../../api/order/detail.php?id=' + d.order_id)
              .then(function (r) { return r.json(); })
              .then(function (resOrder) {
                if (!resOrder.ok || !resOrder.data || !resOrder.data.order) return;
                const o = resOrder.data.order;
                document.getElementById('casePembeli').textContent = o.nama_buyer || ('Pembeli #' + d.buyer_id);
                document.getElementById('caseProduk').textContent = (o.nama_produk || ('Produk #' + d.produk_id)) + ' (' + Number(o.jumlah_kg).toLocaleString('id-ID') + ' Kg)';
                document.getElementById('caseTglBeli').textContent = formatTanggalApi(o.created_at);
              })
              .catch(function () { /* biarkan 'Pembeli #id' / 'Produk #id' */ });
          })
          .catch(function () { showToast('Gagal memuat dispute.', 'info'); });
      }

      // 4. Render Attachment items (dipakai juga saat data asli dari API tiba)
      function renderAttachments(attachments) {
        const attachmentList = document.getElementById('attachmentList');
        attachmentList.innerHTML = '';

        if (!attachments || !attachments.length) {
          attachmentList.innerHTML = '<div style="padding: 13px 16px; font-size: 14px; color: #64748b;">Tidak ada bukti yang dilampirkan.</div>';
          return;
        }

        attachments.forEach((att, idx) => {
        const item = document.createElement('div');
        item.className = 'attachment-item';
        item.setAttribute('data-index', idx);

        const isVideo = att.type === 'MP4';
        const iconSvg = isVideo
          ? `<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <rect x="2" y="6" width="13" height="12" rx="2"/>
               <polygon points="22 8 15 12 22 16 22 8"/>
             </svg>`
          : `<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
               <circle cx="8.5" cy="8.5" r="1.5"/>
               <polyline points="21 15 16 10 5 21"/>
             </svg>`;

        item.innerHTML = `
          <div class="attachment-left">
            <div class="attachment-icon">
              ${iconSvg}
            </div>
            <div class="attachment-info">
              <span class="attachment-title">${att.title}</span>
            </div>
          </div>
          <span class="attachment-tag">${att.type}</span>
        `;

        item.addEventListener('click', () => {
          openAttachmentModal(att);
        });

        attachmentList.appendChild(item);
        });
      }

      if (disputeIdApi <= 0) renderAttachments([]);

      // 5. Modal Attachment Preview Controls
      const modal = document.getElementById('attachmentModal');
      const btnCloseModal = document.getElementById('btnCloseModal');
      const btnModalCloseAction = document.getElementById('btnModalCloseAction');
      const btnModalDownload = document.getElementById('btnModalDownload');
      let currentActiveAttachment = null;

      function openAttachmentModal(att) {
        currentActiveAttachment = att;
        document.getElementById('modalAttachmentTitle').textContent = `Pratinjau ${att.title}`;
        document.getElementById('modalPreviewName').textContent = att.title;
        document.getElementById('modalPreviewDesc').textContent = `${att.desc} (${att.type})`;
        modal.classList.add('open');
      }

      function closeModal() {
        modal.classList.remove('open');
      }

      btnCloseModal.addEventListener('click', closeModal);
      btnModalCloseAction.addEventListener('click', closeModal);

      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
          closeModal();
        }
      });

      btnModalDownload.addEventListener('click', () => {
        if (currentActiveAttachment) {
          showToast(`Mengunduh ${currentActiveAttachment.title}...`, 'success');
        }
      });

      // 6. Tolak Dispute Button Event
      const btnReject = document.getElementById('btnRejectDispute');
      btnReject.addEventListener('click', () => {
        // Tolak = klaim refund ditolak, order lanjut ke perusahaan.
        // Putusan asli lewat API (admin only): POST ../../api/dispute/putuskan.php
        // dengan { dispute_id, putusan: 'lanjut_perusahaan' }.
        if (disputeIdApi > 0) {
          btnReject.disabled = true;
          fetch('../../api/dispute/putuskan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dispute_id: disputeIdApi, putusan: 'lanjut_perusahaan' })
          })
            .then(function (r) { return r.json(); })
            .then(function (res) {
              if (!res.ok) throw new Error(res.error || 'Putusan gagal');
              showToast(`Dispute #${disputeIdApi} berhasil ditolak (lanjut ke perusahaan)! Mengalihkan ke daftar...`, 'danger');
              setTimeout(() => {
                window.location.href = 'dispute.php';
              }, 900);
            })
            .catch(function (err) {
              btnReject.disabled = false;
              showToast(err.message || 'Putusan gagal. Pastikan sudah login sebagai admin.', 'danger');
            });
          return;
        }

        // Tanpa ID dispute asli, tidak ada yang bisa diputuskan.
        showToast('Buka dispute dari daftar dulu (butuh ID dispute asli).', 'info');
      });

      // 7. Topbar Admin Profile Button Event
      const adminProfileBtn = document.getElementById('adminProfileBtn');
      if (adminProfileBtn) {
        adminProfileBtn.addEventListener('click', () => {
          toggleAdminDropdown(event);
        });
      }
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

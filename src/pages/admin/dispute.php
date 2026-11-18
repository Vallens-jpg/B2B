<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Dispute & Komplain - Staplr Admin</title>
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
       STAPLR ADMIN - DAFTAR DISPUTE (PIXEL-PERFECT REPLICA)
       Reference: assets/tampilan admin/4. Daftar dispute.png
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

    .content-title {
      font-size: 20px;
      font-weight: 800;
      color: #0f2a4a;
      letter-spacing: -0.2px;
      margin-bottom: 22px;
    }

    /* ============ FILTER PILLS ============ */
    .filter-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .filter-pill {
      height: 36px;
      padding: 0 18px;
      border-radius: 999px;
      font-size: 13.5px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      color: #0f2a4a;
      border: 1px solid #e8ecf2;
      transition: all 0.15s ease;
      cursor: pointer;
      white-space: nowrap;
      font-family: inherit;
      outline: none;
    }

    .filter-pill:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
    }

    .filter-pill.active {
      background: #0f2a4a;
      color: #ffffff;
      border-color: #0f2a4a;
    }

    .filter-pill.active:hover {
      background: #17385f;
    }

    /* ============ CARD TABLE ============ */
    .table-card {
      background: #ffffff;
      border: 1px solid #e8ecf2;
      border-radius: 16px;
      box-shadow: 0 2px 12px -2px rgba(18, 47, 81, 0.04);
      overflow: hidden;
    }

    .table-container {
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

    /* Column Specific Styles */
    .td-id {
      font-weight: 700;
      color: #0f2a4a;
      font-size: 13.5px;
    }

    .td-date {
      color: #475569;
      font-weight: 500;
    }

    .td-buyer {
      color: #334155;
      font-weight: 500;
    }

    .td-category {
      color: #334155;
      font-weight: 500;
    }

    .td-media {
      color: #334155;
      font-weight: 500;
    }

    /* Badges for Status / Decision */
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
    }

    .badge-dot {
      font-size: 9px;
      line-height: 1;
    }

    /* Pending Badge */
    .badge-pending {
      background: #fff4cc;
      color: #8a6400;
      border: 1px solid #fde68a;
    }

    /* Waiting Item Badge */
    .badge-waiting {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }

    /* Approved Badge */
    .badge-approved {
      background: #e3f6ec;
      color: #14803f;
      border: 1px solid #a7f3d0;
    }

    /* Rejected Badge */
    .badge-rejected {
      background: #fde8e8;
      color: #b42318;
      border: 1px solid #fecaca;
    }

    /* Action Detail Button */
    .btn-detail {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      height: 35px;
      padding: 0 18px;
      background: #ffffff;
      border: 1px solid #d0d5dd;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      color: #0f2a4a;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.15s ease;
      white-space: nowrap;
      font-family: inherit;
    }

    .btn-detail:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      transform: translateY(-1px);
    }

    /* Table Footer & Pagination */
    .card-footer {
      padding: 14px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid #edf1f6;
      background: #ffffff;
    }

    .footer-info {
      font-size: 13px;
      color: #64748b;
      font-weight: 500;
    }

    .btn-pagination {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      height: 36px;
      padding: 0 20px;
      background: #ffffff;
      border: 1px solid #d0d5dd;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      color: #0f2a4a;
      cursor: pointer;
      transition: all 0.15s ease;
      white-space: nowrap;
      font-family: inherit;
    }

    .btn-pagination:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    .btn-pagination:active {
      transform: scale(0.98);
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

    .staplr-toast.info {
      background: #0f2a4a;
      border: 1px solid #1e3a8a;
    }

    /* Empty state */
    .empty-row td {
      text-align: center;
      padding: 36px 20px;
      color: #64748b;
      font-size: 13.5px;
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
      .filter-row {
        gap: 8px;
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
      <h2 class="content-title">Daftar Dispute & Komplain</h2>

      <!-- Filter Pills -->
      <div class="filter-row" id="filterPillsRow">
        <button type="button" class="filter-pill active" data-filter="all">Semua</button>
        <button type="button" class="filter-pill" data-filter="pending">
          Perlu Ditinjau (<span id="countPendingBadge">0</span>)
        </button>
        <button type="button" class="filter-pill" data-filter="waiting">Menunggu Barang</button>
        <button type="button" class="filter-pill" data-filter="approved">Disetujui</button>
        <button type="button" class="filter-pill" data-filter="rejected">Ditolak</button>
      </div>

      <!-- Table Card -->
      <div class="table-card">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID RETUR / ORDER</th>
                <th>TGL KOMPLAIN</th>
                <th>PEMBELI</th>
                <th>KATEGORI KOMPLAIN</th>
                <th>BUKTI (MEDIA)</th>
                <th>KEPUTUSAN</th>
                <th>AKSI</th>
              </tr>
            </thead>
            <tbody id="disputeTableBody">
              <!-- Dynamically rendered rows -->
            </tbody>
          </table>
        </div>

        <!-- Footer Pagination Info -->
        <div class="card-footer">
          <span class="footer-info" id="footerInfoText">Memuat dispute…</span>
          <button class="btn-pagination" id="btnNextPage" type="button">Selanjutnya</button>
        </div>
      </div>
    </section>
  </main>

  <!-- Toast Notification -->
  <div id="toastNotification" class="staplr-toast"></div>

  <!-- Client-side Logic Script -->
  <script>
    // ==========================================
    // STAPLR ADMIN - DISPUTE & COMPLAINTS LOGIC
    // ==========================================
    let DISPUTE_LIST = [];

    function muatDisputeAdmin() {
      return fetch('../../api/dispute/list.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok || !res.data || !res.data.dispute) {
            renderDisputes();
            return;
          }
          DISPUTE_LIST = res.data.dispute.map(function (d) {
            let status = 'pending';
            let label = 'Pending';
            if (d.status === 'diproses') { status = 'waiting'; label = 'Menunggu Barang'; }
            else if (d.status === 'diputuskan' && d.putusan === 'uang_kembali') { status = 'approved'; label = 'Disetujui'; }
            else if (d.status === 'diputuskan') { status = 'rejected'; label = 'Ditolak'; }
            const tgl = (d.created_at || '').slice(0, 10);
            return {
              id: '#DSP-' + String(d.id).padStart(4, '0'),
              disputeId: d.id,
              orderId: '#ORD-2026-' + String(d.order_id).padStart(3, '0'),
              date: tgl ? new Date(tgl + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-',
              buyer: d.nama_buyer || 'Pembeli',
              category: d.judul || '-',
              media: d.bukti_foto ? '1 Foto' : '—',
              status: status,
              statusLabel: label
            };
          });
          renderDisputes();
        })
        .catch(function () { renderDisputes(); });
    }

    function showToast(message) {
      const toast = document.getElementById('toastNotification');
      toast.className = 'staplr-toast info show';
      toast.textContent = message;
      setTimeout(() => {
        toast.className = 'staplr-toast';
      }, 2500);
    }

    let activeFilter = 'all';

    function renderDisputes() {
      const disputes = DISPUTE_LIST;

      // Update badge count for Perlu Ditinjau (pending items)
      const pendingCount = disputes.filter(d => d.status === 'pending').length;
      document.getElementById('countPendingBadge').textContent = pendingCount;

      // Filter disputes based on active tab
      const filtered = disputes.filter(d => {
        if (activeFilter === 'all') return true;
        return d.status === activeFilter;
      });

      // Update footer pagination text
      const total = disputes.length;
      const footerInfo = document.getElementById('footerInfoText');
      if (filtered.length === total) {
        footerInfo.textContent = `Menampilkan 1–${filtered.length} dari ${total} dispute`;
      } else if (filtered.length > 0) {
        footerInfo.textContent = `Menampilkan ${filtered.length} dari ${filtered.length} dispute`;
      } else {
        footerInfo.textContent = `Menampilkan 0 dispute`;
      }

      // Render Table Rows
      const tbody = document.getElementById('disputeTableBody');
      tbody.innerHTML = '';

      if (filtered.length === 0) {
        tbody.innerHTML = `
          <tr class="empty-row">
            <td colspan="7">Tidak ada dispute dalam kategori ini.</td>
          </tr>
        `;
        return;
      }

      filtered.forEach(d => {
        const tr = document.createElement('tr');

        // Determine badge styling
        let badgeHtml = '';
        if (d.status === 'pending') {
          badgeHtml = `
            <span class="badge badge-pending">
              <span class="badge-dot">●</span> Pending
            </span>
          `;
        } else if (d.status === 'approved') {
          badgeHtml = `
            <span class="badge badge-approved">
              <span class="badge-dot">●</span> Disetujui
            </span>
          `;
        } else if (d.status === 'rejected') {
          badgeHtml = `
            <span class="badge badge-rejected">
              <span class="badge-dot">●</span> Ditolak
            </span>
          `;
        } else if (d.status === 'waiting') {
          badgeHtml = `
            <span class="badge badge-waiting">
              <span class="badge-dot">●</span> Menunggu Barang
            </span>
          `;
        }

        tr.innerHTML = `
          <td class="td-id">${d.id}</td>
          <td class="td-date">${d.date}</td>
          <td class="td-buyer">${d.buyer}</td>
          <td class="td-category">${d.category}</td>
          <td class="td-media">${d.media}</td>
          <td>${badgeHtml}</td>
          <td>
            <a href="detail-dispute.php?id=${encodeURIComponent(d.disputeId)}" class="btn-detail">Detail</a>
          </td>
        `;

        tbody.appendChild(tr);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      // 1. Filter pill click events
      const pills = document.querySelectorAll('.filter-pill');
      pills.forEach(pill => {
        pill.addEventListener('click', () => {
          pills.forEach(p => p.classList.remove('active'));
          pill.classList.add('active');

          activeFilter = pill.getAttribute('data-filter');
          renderDisputes();
        });
      });

      // 2. Next Page button event
      const btnNext = document.getElementById('btnNextPage');
      btnNext.addEventListener('click', () => {
        showToast('Menampilkan ' + DISPUTE_LIST.length + ' dispute (halaman 1 dari 1)');
      });

      // 3. Topbar Profile button event
      const adminProfileBtn = document.getElementById('adminProfileBtn');
      if (adminProfileBtn) {
        adminProfileBtn.addEventListener('click', () => {
          toggleAdminDropdown(event);
        });
      }

      // Initial render: muat dari API
      muatDisputeAdmin();
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

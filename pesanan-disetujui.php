<?php
session_start();

$activeApproved = null;
if (!empty($_SESSION['pesanan_disetujui'])) {
    $activeApproved = $_SESSION['pesanan_disetujui'];
} elseif (!empty($_SESSION['active_order']) && ($_SESSION['active_order']['status'] ?? '') === 'approved') {
    $activeApproved = $_SESSION['active_order'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Pesanan Disetujui</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body.pd-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    .pd-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 2.25rem 2rem 0 2rem;
    }

    .pd-top-bar {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
      margin-bottom: 1.75rem;
    }

    .pd-title {
      font-size: 1.65rem;
      font-weight: 800;
      color: #111827;
      margin: 0 0 0.3rem 0;
    }

    .pd-subtitle {
      font-size: 0.9rem;
      color: #6B7280;
      margin: 0;
    }

    .pd-badge {
      background: #DCFCE7;
      color: #15803D;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 3px 12px;
      border-radius: 9999px;
      margin-left: 0.5rem;
    }

    /* Card pesanan disetujui */
    .pd-card {
      background: #fff;
      border: 1.5px solid #E5E7EB;
      border-radius: 16px;
      padding: 1.5rem 1.75rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      transition: box-shadow 0.15s;
    }

    .pd-card:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    }

    .pd-card-left {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
    }

    .pd-icon-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #DCFCE7;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .pd-icon-circle svg {
      width: 22px;
      height: 22px;
      stroke: #15803D;
      stroke-width: 2;
      fill: none;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .pd-card-info {
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .pd-card-seller {
      font-size: 1rem;
      font-weight: 800;
      color: #111827;
    }

    .pd-card-product {
      font-size: 0.875rem;
      color: #4B5563;
    }

    .pd-card-meta {
      font-size: 0.8rem;
      color: #9CA3AF;
      margin-top: 0.15rem;
    }

    .pd-card-status {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: #DCFCE7;
      color: #15803D;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 9999px;
      margin-top: 0.3rem;
      width: fit-content;
    }

    .pd-card-right {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.6rem;
      flex-shrink: 0;
    }

    .pd-card-total {
      font-size: 1.05rem;
      font-weight: 800;
      color: #152C4E;
    }

    .pd-card-qty {
      font-size: 0.83rem;
      color: #6B7280;
    }

    .pd-btn-bayar {
      background: #152C4E;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 0.6rem 1.4rem;
      font-size: 0.875rem;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      text-decoration: none;
      display: inline-block;
      transition: background 0.15s;
      margin-top: 0.25rem;
    }

    .pd-btn-bayar:hover {
      background: #1e3d6e;
    }

    .pd-empty {
      text-align: center;
      padding: 4rem 2rem;
      color: #9CA3AF;
    }

    .pd-empty-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
    }

    .pd-empty-text {
      font-size: 1rem;
      font-weight: 600;
      color: #6B7280;
    }

    .pd-empty-sub {
      font-size: 0.875rem;
      color: #9CA3AF;
      margin-top: 0.4rem;
    }
  </style>
</head>
<body class="pd-body">

  <!-- Navbar Header -->
  <header class="dashboard-navbar">
    <div class="navbar-inner">
      <a href="dashboard.php" class="brand-header" style="text-decoration: none;">
        <img src="Logo/logo staplr.png" alt="Staplr Logo" class="brand-logo-img">
        <div class="brand-title-group">
          <span class="brand-dot"></span>
          <span class="brand-name">Staplr</span>
        </div>
      </a>

      <nav class="nav-pills-container">
        <a href="dashboard.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
          </svg>
          Dashboard
        </a>
        <a href="pesanan.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor"/>
            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-linecap="round"/>
          </svg>
          Pesanan
        </a>
        <a href="daftar-dispute.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor"/>
            <path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-linecap="round" stroke-width="3"/>
          </svg>
          Dispute
        </a>
        <a href="pesanan-disetujui.php" class="nav-pill-item active">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Disetujui
        </a>
      </nav>

      <div class="navbar-actions notif-dropdown-wrapper">
        <button type="button" class="notification-bell-btn" id="notifBellBtn" onclick="toggleNotificationDropdown(event)" aria-label="Notifikasi">
          <svg class="bell-icon" viewBox="0 0 24 24">
            <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
          </svg>
          <span class="notification-badge-red" id="notifBadgeRed">3</span>
        </button>
        <a href="profil.php" class="user-profile-capsule" style="text-decoration: none;">
          <div class="user-avatar-circle">AD</div>
          <span class="user-name-text">Buyer</span>
        </a>

        <!-- Notification Dropdown -->
        <div class="notif-dropdown" id="notifDropdown">
          <div class="notif-header">
            <span class="notif-title">Notifikasi</span>
            <button type="button" class="notif-mark-read" onclick="markAllAsRead(event)">Tandai semua dibaca</button>
          </div>
          <div class="notif-list">
            <a href="status-pesanan.php?state=approved" class="notif-item unread">
              <div class="notif-icon-circle green">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </div>
              <div class="notif-content">
                <div class="notif-item-top">
                  <span class="notif-item-title">Pesanan Disetujui Penjual</span>
                  <span class="notif-time">&bull; Baru</span>
                </div>
                <p class="notif-item-body">Pesanan Anda telah disetujui. Lanjutkan pembayaran.</p>
              </div>
            </a>
            <a href="diskusi-dispute.php" class="notif-item unread">
              <div class="notif-icon-circle green">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
              </div>
              <div class="notif-content">
                <div class="notif-item-top">
                  <span class="notif-item-title">Pesan Baru di Dispute #DSP-9021</span>
                  <span class="notif-item-badge-red">1</span>
                </div>
                <p class="notif-item-body">Seller: "Kami sudah mengirim bukti foto ulang..."</p>
              </div>
            </a>
            <a href="daftar-dispute.php" class="notif-item">
              <div class="notif-icon-circle yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <circle cx="12" cy="12" r="10" stroke="#111827"/>
                  <line x1="12" y1="8" x2="12" y2="12" stroke="#111827"/>
                  <line x1="12" y1="16" x2="12.01" y2="16" stroke="#111827"/>
                </svg>
              </div>
              <div class="notif-content">
                <div class="notif-item-top">
                  <span class="notif-item-title dark">Dispute Disetujui &bull; 3j</span>
                </div>
                <p class="notif-item-body">Dispute #DSP-8812 telah disetujui. Dana pengembalian sedang diproses.</p>
              </div>
            </a>
          </div>
          <div class="notif-footer">
            <a href="notifikasi.php" class="notif-footer-link">Lihat Semua Notifikasi</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="pd-container">
    <div class="pd-top-bar">
      <div>
        <h1 class="pd-title">
          Pesanan Disetujui
          <span class="pd-badge">Siap Bayar</span>
        </h1>
        <p class="pd-subtitle">Pesanan berikut telah disetujui penjual. Silakan pilih dan selesaikan pembayaran.</p>
      </div>
    </div>

    <!-- Daftar Pesanan Disetujui -->
    <div id="listDisetujui">
      <?php if ($activeApproved): ?>
      <!-- Card Dinamis dari Pesanan Aktif -->
      <div class="pd-card" style="border: 1.5px solid #10B981; background: #F0FDF4;">
        <div class="pd-card-left">
          <div class="pd-icon-circle">
            <svg viewBox="0 0 24 24">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="pd-card-info">
            <div class="pd-card-seller"><?= htmlspecialchars($activeApproved['seller']) ?></div>
            <div class="pd-card-product"><?= htmlspecialchars($activeApproved['product']) ?> &bull; <?= htmlspecialchars($activeApproved['qty']) ?></div>
            <div class="pd-card-meta">Pesanan <?= htmlspecialchars($activeApproved['order_id']) ?> &bull; Disetujui baru saja</div>
            <div class="pd-card-status">Disetujui Penjual</div>
          </div>
        </div>
        <div class="pd-card-right">
          <div class="pd-card-total"><?= htmlspecialchars($activeApproved['subtotal']) ?></div>
          <div class="pd-card-qty"><?= htmlspecialchars($activeApproved['qty']) ?> &times; <?= htmlspecialchars($activeApproved['price']) ?></div>
          <a href="pembayaran.php" class="pd-btn-bayar">Bayar Sekarang</a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Card 1 — PT Telur Makmur -->
      <div class="pd-card">
        <div class="pd-card-left">
          <div class="pd-icon-circle">
            <svg viewBox="0 0 24 24">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="pd-card-info">
            <div class="pd-card-seller">PT Telur Makmur</div>
            <div class="pd-card-product">Telur Omega &bull; 2 kg</div>
            <div class="pd-card-meta">Pesanan #ORD-2026-001 &bull; Disetujui baru saja</div>
            <div class="pd-card-status">Disetujui Penjual</div>
          </div>
        </div>
        <div class="pd-card-right">
          <div class="pd-card-total">Rp 80.000</div>
          <div class="pd-card-qty">2 kg &times; Rp 40.000</div>
          <a href="status-pesanan.php?state=approved&seller=PT%20Telur%20Makmur&product=Telur%20Omega&qty=2%20kg&price=Rp%2040.000&subtotal=Rp%2080.000"
             class="pd-btn-bayar">Bayar Sekarang</a>
        </div>
      </div>

      <!-- Card 2 — Mitra Unggas Sejahtera -->
      <div class="pd-card">
        <div class="pd-card-left">
          <div class="pd-icon-circle">
            <svg viewBox="0 0 24 24">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="pd-card-info">
            <div class="pd-card-seller">Mitra Unggas Sejahtera</div>
            <div class="pd-card-product">Telur Ayam Ras Grade A &bull; 5 kg</div>
            <div class="pd-card-meta">Pesanan #ORD-2026-002 &bull; Disetujui 1 jam lalu</div>
            <div class="pd-card-status">Disetujui Penjual</div>
          </div>
        </div>
        <div class="pd-card-right">
          <div class="pd-card-total">Rp 140.000</div>
          <div class="pd-card-qty">5 kg &times; Rp 28.000</div>
          <a href="status-pesanan.php?state=approved&seller=Mitra%20Unggas%20Sejahtera&product=Telur%20Ayam%20Ras%20Grade%20A&qty=5%20kg&price=Rp%2028.000&subtotal=Rp%20140.000"
             class="pd-btn-bayar">Bayar Sekarang</a>
        </div>
      </div>

    </div>
  </main>

  <script>
    function toggleNotificationDropdown(e) {
      if (e) e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      if (dd) dd.classList.toggle('show');
    }

    function markAllAsRead(e) {
      if (e) { e.preventDefault(); e.stopPropagation(); }
    }

    document.addEventListener('click', function(e) {
      const dd = document.getElementById('notifDropdown');
      const btn = document.getElementById('notifBellBtn');
      if (dd && dd.classList.contains('show')) {
        if (!dd.contains(e.target) && !btn.contains(e.target)) {
          dd.classList.remove('show');
        }
      }
    });
  </script>
</body>
</html>

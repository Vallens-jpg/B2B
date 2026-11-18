<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Pesanan Disetujui</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
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
      <a href="dashboard_buyer.php" class="brand-header" style="text-decoration: none;">
        <img src="../../assets/img/logo/logo staplr.png" alt="Staplr Logo" class="brand-logo-img">
        <div class="brand-title-group">
          <span class="brand-dot"></span>
          <span class="brand-name">Staplr</span>
        </div>
      </a>

      <nav class="nav-pills-container">
        <a href="dashboard_buyer.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
          </svg>
          Dashboard
        </a>
        <a href="order_list.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor"/>
            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-linecap="round"/>
          </svg>
          Pesanan
        </a>
        <a href="../dispute_list.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor"/>
            <path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-linecap="round" stroke-width="3"/>
          </svg>
          Dispute
        </a>
        <a href="pesanan_disetujui.php" class="nav-pill-item active">
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
          <span class="notification-badge-red" id="notifBadgeRed" style="display:none">0</span>
        </button>
        <a href="../profil.php" class="user-profile-capsule" style="text-decoration: none;">
          <div class="user-avatar-circle">AD</div>
          <span class="user-name-text">Buyer</span>
        </a>

        <!-- Notification Dropdown -->
        <div class="notif-dropdown" id="notifDropdown">
          <div class="notif-header">
            <span class="notif-title">Notifikasi</span>
            <button type="button" class="notif-mark-read" onclick="markAllAsRead(event)">Tandai semua dibaca</button>
          </div>
          <div class="notif-list" id="ddNotifList">
            <div class="notif-item"><div class="notif-content"><p class="notif-item-body">Memuat notifikasi…</p></div></div>
          </div>
          <div class="notif-footer">
            <a href="../notifikasi.php" class="notif-footer-link">Lihat Semua Notifikasi</a>
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
          <span class="pd-badge">Disetujui</span>
        </h1>
        <p class="pd-subtitle">Pesanan berikut telah disetujui penjual dan sedang diproses.</p>
      </div>
    </div>

    <!-- Daftar Pesanan Disetujui -->
    <div id="listDisetujui"></div>

  </main>

  <script>
    function toggleNotificationDropdown(e) {
      if (e) e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      if (dd) dd.classList.toggle('show');
    }

    function markAllAsRead(e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      // Simpan ke database supaya tidak kembali setelah muat ulang.
      fetch('../../api/notifikasi/tandai-dibaca.php', { method: 'POST' }).catch(function () {});
      const badgeRed = document.getElementById('notifBadgeRed');
      if (badgeRed) badgeRed.style.display = 'none';

      const badgeItem = document.getElementById('notifBadgeItem');
      if (badgeItem) badgeItem.style.display = 'none';

      document.querySelectorAll('.notif-item.unread').forEach(item => {
        item.classList.remove('unread');
      });
    }

    // ---- Dropdown lonceng dari API notifikasi (riwayat_status) ----
    (function () {
      var ddList = document.getElementById('ddNotifList');
      var badge = document.getElementById('notifBadgeRed');
      function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      fetch('../../api/notifikasi.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!ddList) return;
          if (!res || !res.ok || !res.data || !res.data.notifikasi || !res.data.notifikasi.length) {
            ddList.innerHTML = '<div class="notif-item"><div class="notif-content"><p class="notif-item-body">Belum ada notifikasi.</p></div></div>';
            if (badge) badge.style.display = 'none';
            return;
          }
          var items = res.data.notifikasi;
          ddList.innerHTML = items.slice(0, 3).map(function (n) {
            return '<a href="../order_detail.php?id=' + n.order_id + '" class="notif-item' + (n.dibaca ? '' : ' unread') + '">'
              + '<div class="notif-icon-circle green"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg></div>'
              + '<div class="notif-content"><div class="notif-item-top"><span class="notif-item-title">' + escapeHtml(n.judul) + '</span></div>'
              + '<p class="notif-item-body">' + escapeHtml(n.isi) + '</p></div></a>';
          }).join('');
          var belum = 0;
          items.forEach(function (n) { if (!n.dibaca) belum++; });
          if (badge) {
            if (belum > 0) {
              badge.innerText = belum;
              badge.style.display = '';
            } else {
              badge.style.display = 'none';
            }
          }
        })
        .catch(function () { /* biarkan placeholder bila API gagal */ });
    })();


    // Daftar pesanan disetujui dari API (bukan contoh): hanya order
    // berstatus "diproses" (sudah dibayar lalu disetujui penjual).
    // Setelah dibayar + disetujui, pesanan muncul di sini; setelah
    // dikirim penjual, pesanan pindah dari daftar ini.
    function pdEscape(s) {
      return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function pdRupiah(n) {
      return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }
    function pdMuatDisetujui() {
      var wadah = document.getElementById('listDisetujui');
      if (!wadah) return;
      fetch('../../api/order/list.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok || !res.data || !res.data.orders) {
            wadah.innerHTML = '<div class="pd-card"><div class="pd-card-info"><div class="pd-card-seller">Gagal memuat pesanan</div><div class="pd-card-product">' + pdEscape(res.error || 'tidak diketahui') + '</div></div></div>';
            return;
          }
          var disetujui = res.data.orders.filter(function (o) { return o.status === 'diproses'; });
          if (!disetujui.length) {
            wadah.innerHTML = '<div class="pd-card"><div class="pd-card-info"><div class="pd-card-seller">Belum ada pesanan yang disetujui</div><div class="pd-card-product">Pesanan yang sudah dibayar dan disetujui penjual akan muncul di sini.</div></div></div>';
            return;
          }
          wadah.innerHTML = disetujui.map(function (o) {
            var nomor = '#ORD-2026-' + String(o.id).padStart(3, '0');
            var tgl = '';
            try { tgl = new Date(String(o.created_at).replace(' ', 'T')).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); } catch (e) {}
            return '<div class="pd-card" style="border: 1.5px solid #10B981; background: #F0FDF4;">' +
              '<div class="pd-card-left"><div class="pd-icon-circle"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>' +
              '<div class="pd-card-info">' +
              '<div class="pd-card-seller">' + pdEscape(o.nama_perusahaan || 'Perusahaan Demo') + '</div>' +
              '<div class="pd-card-product">' + pdEscape(o.nama_produk) + ' &bull; ' + pdEscape(o.jumlah_kg) + ' kg</div>' +
              '<div class="pd-card-meta">Pesanan ' + pdEscape(nomor) + (tgl ? ' &bull; ' + pdEscape(tgl) : '') + '</div>' +
              '<div class="pd-card-status">Disetujui Penjual</div>' +
              '</div></div>' +
              '<div class="pd-card-right">' +
              '<div class="pd-card-total">' + pdEscape(pdRupiah(o.total_bayar)) + '</div>' +
              '<div class="pd-card-qty">' + pdEscape(o.jumlah_kg) + ' kg &times; ' + pdEscape(pdRupiah(o.harga_satuan)) + '</div>' +
              '<a href="../order_detail.php?id=' + o.id + '" class="pd-btn-bayar">Lihat Detail</a>' +
              '</div></div>';
          }).join('');
        })
        .catch(function () {
          wadah.innerHTML = '<div class="pd-card"><div class="pd-card-info"><div class="pd-card-seller">Gagal memuat pesanan</div><div class="pd-card-product">Periksa koneksi atau login kembali.</div></div></div>';
        });
    }
    pdMuatDisetujui();

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

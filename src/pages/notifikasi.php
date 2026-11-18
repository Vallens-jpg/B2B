<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Semua Notifikasi</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Halaman Semua Notifikasi Styles
       ========================================================================== */
    body.notifikasi-page-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    .notifikasi-container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 2.25rem 2.5rem 0 2.5rem;
    }

    /* Top Title Block */
    .notif-top-bar {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 1.75rem;
    }

    .notif-title-area {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .notif-heading-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .notif-main-title {
      font-size: 1.65rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
      margin: 0;
    }

    .notif-unread-badge {
      background-color: #FEF3C7;
      color: #B45309;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 9999px;
    }

    .notif-main-subtitle {
      font-size: 0.925rem;
      color: #6B7280;
      margin: 0;
    }

    .btn-mark-all-read {
      background-color: #FFC200;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.6rem;
      font-size: 0.875rem;
      font-weight: 800;
      color: #111827;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-family: inherit;
      transition: background-color 0.15s ease, transform 0.1s ease;
      white-space: nowrap;
    }

    .btn-mark-all-read:hover {
      background-color: #EBB300;
      transform: translateY(-1px);
    }

    .btn-mark-all-read svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      stroke-width: 2.8;
      fill: none;
    }

    /* Main Notification Card Container */
    .notif-feed-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    /* Filter & Sort Bar inside card */
    .notif-filter-bar {
      padding: 1.15rem 1.75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #F1F5F9;
      background-color: #ffffff;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .notif-filter-pills {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .notif-pill-btn {
      border: 1.5px solid #E2E8F0;
      background-color: #ffffff;
      border-radius: 9999px;
      padding: 0.45rem 1.15rem;
      font-size: 0.825rem;
      font-weight: 700;
      color: #374151;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.15s ease;
    }

    .notif-pill-btn.active {
      background-color: #FFC200;
      border-color: #FFC200;
      color: #111827;
    }

    .notif-sort-text {
      font-size: 0.825rem;
      color: #6B7280;
      font-weight: 500;
    }

    /* Section Subheadings (Hari ini, Kemarin) */
    .notif-section-label {
      background-color: #F8FAFC;
      border-bottom: 1px solid #F1F5F9;
      padding: 0.65rem 1.75rem;
      font-size: 0.825rem;
      font-weight: 800;
      color: #111827;
    }

    .notif-section-count {
      color: #6B7280;
      font-weight: 500;
      margin-left: 0.2rem;
    }

    /* Notification Feed Items */
    .notif-row-item {
      padding: 1.25rem 1.75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      border-bottom: 1px solid #F1F5F9;
      transition: background-color 0.15s ease;
    }

    .notif-row-item:last-child {
      border-bottom: none;
    }

    .notif-row-item.unread {
      background-color: #F0FDF4; /* soft mint */
    }

    .notif-row-item:hover {
      background-color: #F8FAFC;
    }

    .notif-row-left {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
    }

    /* Icon Circles */
    .notif-circle-icon {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .notif-circle-icon.green {
      background-color: #DCFCE7;
      color: #15803D;
    }

    .notif-circle-icon.yellow {
      background-color: #FEF3C7;
      color: #111827;
    }

    .notif-circle-icon.gray {
      background-color: #F8FAFC;
      border: 1px solid #E2E8F0;
      color: #4B5563;
    }

    .notif-circle-icon svg {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      stroke-width: 2;
      fill: none;
    }

    /* Info Texts */
    .notif-row-text-group {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .notif-row-title {
      font-size: 0.925rem;
      font-weight: 800;
      color: #111827;
      line-height: 1.3;
    }

    .notif-row-title.green {
      color: #15803D;
    }

    .notif-row-title.amber {
      color: #B45309;
    }

    .notif-row-desc {
      font-size: 0.85rem;
      color: #4B5563;
      margin: 0;
      line-height: 1.4;
    }

    .notif-row-meta {
      font-size: 0.78rem;
      color: #9CA3AF;
      margin-top: 0.15rem;
    }

    /* Right Action & Status */
    .notif-row-right {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.5rem;
      flex-shrink: 0;
    }

    .notif-status-indicator {
      font-size: 0.78rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
    }

    .notif-status-indicator.unread {
      color: #1E3A8A;
    }

    .notif-status-indicator.read {
      color: #9CA3AF;
      font-weight: 500;
    }

    .status-dot-blue {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background-color: #2563EB;
      display: inline-block;
    }

    .notif-action-link {
      font-size: 0.85rem;
      font-weight: 800;
      color: #111827;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      transition: color 0.15s ease;
    }

    .notif-action-link:hover {
      color: #2563EB;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .notifikasi-container {
        padding: 1.25rem 1rem 0 1rem;
      }
      .notif-row-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.85rem;
      }
      .notif-row-right {
        align-items: flex-start;
        width: 100%;
        border-top: 1px solid #F1F5F9;
        padding-top: 0.65rem;
        flex-direction: row;
        justify-content: space-between;
      }
    }
  </style>
</head>
<body class="notifikasi-page-body">

  <!-- Navbar Header -->
  <header class="dashboard-navbar">
    <div class="navbar-inner">
      <a href="pembeli/dashboard_buyer.php" class="brand-header" style="text-decoration: none;">
        <img src="../assets/img/logo/logo staplr.png" alt="Staplr Logo" class="brand-logo-img">
        <div class="brand-title-group">
          <span class="brand-dot"></span>
          <span class="brand-name">Staplr</span>
        </div>
      </a>

      <nav class="nav-pills-container">
        <a href="pembeli/dashboard_buyer.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor"/>
          </svg>
          Dashboard
        </a>
        <a href="pembeli/order_list.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor"/>
            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-linecap="round"/>
          </svg>
          Pesanan
        </a>
        <a href="dispute_list.php" class="nav-pill-item">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor"/>
            <path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-linecap="round" stroke-width="3"/>
          </svg>
          Dispute
        </a>
        <a href="pembeli/pesanan_disetujui.php" class="nav-pill-item">
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
        <a href="profil.php" class="user-profile-capsule" style="text-decoration: none;">
          <div class="user-avatar-circle">AD</div>
          <span class="user-name-text">Buyer</span>
        </a>

        <!-- Notification Popover Card -->
        <div class="notif-dropdown" id="notifDropdown">
          <div class="notif-header">
            <span class="notif-title">Notifikasi</span>
            <button type="button" class="notif-mark-read" onclick="markAllAsRead(event)">Tandai semua dibaca</button>
          </div>

          <div class="notif-list" id="ddNotifList">
            <div class="notif-item"><div class="notif-content"><p class="notif-item-body">Memuat notifikasi…</p></div></div>
          </div>

          <div class="notif-footer">
            <a href="notifikasi.php" class="notif-footer-link">Lihat Semua Notifikasi</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="notifikasi-container">

    <!-- Top Title & Action Bar -->
    <div class="notif-top-bar">
      <div class="notif-title-area">
        <div class="notif-heading-row">
          <h1 class="notif-main-title">Semua Notifikasi</h1>
          <span class="notif-unread-badge" id="unreadCountBadge">Memuat…</span>
        </div>
        <p class="notif-main-subtitle">Pantau kabar terbaru tentang permintaan, pesanan, dan transaksi Anda.</p>
      </div>

      <button type="button" class="btn-mark-all-read" onclick="markAllNotificationsRead()">
        <svg viewBox="0 0 24 24">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        Tandai semua dibaca
      </button>
    </div>

    <!-- Feed Card Container -->
    <div class="notif-feed-card">

      <!-- Filter Pills & Sorting -->
      <div class="notif-filter-bar">
        <div class="notif-filter-pills">
          <button type="button" class="notif-pill-btn active" id="pillSemua" onclick="setNotifFilter('semua')">
            Semua <span id="countSemua">0</span>
          </button>
          <button type="button" class="notif-pill-btn" id="pillUnread" onclick="setNotifFilter('unread')">
            Belum dibaca <span id="countUnread">0</span>
          </button>
        </div>

        <div class="notif-sort-text">
          Terbaru lebih dulu
        </div>
      </div>

      <!-- SECTION: Hari ini (3) -->
      <div id="daftarNotifikasi">
        <p style="padding:1rem;">Memuat notifikasi…</p>
      </div>
    </div>
  </main>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    let filterMode = 'semua';

    function setNotifFilter(mode) {
      filterMode = mode;
      document.getElementById('pillSemua').classList.toggle('active', mode === 'semua');
      document.getElementById('pillUnread').classList.toggle('active', mode === 'unread');

      const items = document.querySelectorAll('.notif-row-item');
      items.forEach(item => {
        const isRead = item.getAttribute('data-read') === 'true';
        if (mode === 'unread') {
          item.style.display = isRead ? 'none' : 'flex';
        } else {
          item.style.display = 'flex';
        }
      });

    }

    function markAllNotificationsRead() {
      // Mark all rows as read
      document.querySelectorAll('.notif-row-item').forEach(item => {
        item.classList.remove('unread');
        item.setAttribute('data-read', 'true');
        const statusEl = item.querySelector('.notif-status-indicator');
        if (statusEl) {
          statusEl.className = 'notif-status-indicator read';
          statusEl.innerHTML = 'Sudah dibaca';
        }
      });

      // Update badge counts
      const unreadBadge = document.getElementById('unreadCountBadge');
      if (unreadBadge) {
        unreadBadge.innerText = '0 belum dibaca';
      }
      const countUnread = document.getElementById('countUnread');
      if (countUnread) {
        countUnread.innerText = '0';
      }

      // Hide bell badge
      const badgeRed = document.getElementById('notifBadgeRed');
      if (badgeRed) {
        badgeRed.style.display = 'none';
      }

      if (filterMode === 'unread') {
        setNotifFilter('unread');
      }
    }

    // Top navbar dropdown
    function toggleNotificationDropdown(e) {
      if (e) e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      if (dd) {
        dd.classList.toggle('show');
      }
    }

    function markAllAsRead(e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      markAllNotificationsRead();
    }


    // ---- Notifikasi asli dari API (riwayat_status) ----
    (function () {
      var wadah = document.getElementById('daftarNotifikasi');
      function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      function isiDropdown(items) {
        var ddList = document.getElementById('ddNotifList');
        var badge = document.getElementById('notifBadgeRed');
        if (!ddList) return;
        if (!items || !items.length) {
          ddList.innerHTML = '<div class="notif-item"><div class="notif-content"><p class="notif-item-body">Belum ada notifikasi.</p></div></div>';
          if (badge) badge.style.display = 'none';
          return;
        }
        ddList.innerHTML = items.slice(0, 3).map(function (n) {
          return '<a href="order_detail.php?id=' + n.order_id + '" class="notif-item unread">'
            + '<div class="notif-icon-circle green"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg></div>'
            + '<div class="notif-content"><div class="notif-item-top"><span class="notif-item-title">' + escapeHtml(n.judul) + '</span></div>'
            + '<p class="notif-item-body">' + escapeHtml(n.isi) + '</p></div></a>';
        }).join('');
        if (badge) {
          badge.innerText = items.length;
          badge.style.display = '';
        }
      }
      function perbaruiHitungan(jumlah) {
        var badge = document.getElementById('unreadCountBadge');
        if (badge) badge.innerText = jumlah + ' belum dibaca';
        var countSemua = document.getElementById('countSemua');
        if (countSemua) countSemua.innerText = jumlah;
        var countUnread = document.getElementById('countUnread');
        if (countUnread) countUnread.innerText = jumlah;
      }
      fetch('../api/notifikasi.php')
        .then(function (r) {
          if (r.status === 401) { window.location.href = '../login.php'; return null; }
          return r.json();
        })
        .then(function (res) {
          if (!res) return;
          if (!res.ok || !res.data || !res.data.notifikasi || !res.data.notifikasi.length) {
            if (wadah) wadah.innerHTML = '<div class="notif-section-label">Terbaru</div><p style="padding:1rem;">Belum ada notifikasi. Perubahan status pesanan akan muncul di sini.</p>';
            isiDropdown([]);
            perbaruiHitungan(0);
            return;
          }
          const items = res.data.notifikasi;
          if (!wadah) return;
          const group = document.createElement('div');
          group.className = 'notif-section-group';
          group.id = 'groupTerbaru';
          group.innerHTML = '<div class="notif-section-label">Terbaru <span class="notif-section-count">(' + items.length + ')</span></div>';
          items.forEach(function (n) {
            const art = document.createElement('article');
            art.className = 'notif-row-item unread';
            art.setAttribute('data-read', 'false');
            const waktu = n.created_at ? new Date(n.created_at.replace(' ', 'T')).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
            art.innerHTML =
              '<div class="notif-row-left"><div class="notif-circle-icon green">' +
              '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>' +
              '<div class="notif-row-text-group"><h2 class="notif-row-title green">' + escapeHtml(n.judul) + '</h2>' +
              '<p class="notif-row-desc">' + escapeHtml(n.isi) + '</p>' +
              '<div class="notif-row-meta">Pesanan #' + n.order_id + ' • ' + waktu + '</div></div></div>' +
              '<div class="notif-row-right"><span class="notif-status-indicator unread"><span class="status-dot-blue"></span> Belum dibaca</span>' +
              '<a href="order_detail.php?id=' + n.order_id + '" class="notif-action-link">Lihat pesanan ›</a></div>';
            group.appendChild(art);
          });
          wadah.innerHTML = '';
          wadah.appendChild(group);
          isiDropdown(items);
          perbaruiHitungan(items.length);
        })
        .catch(function () {
          if (wadah) wadah.innerHTML = '<div class="notif-section-label">Terbaru</div><p style="padding:1rem;">Gagal memuat notifikasi. Periksa koneksi atau login kembali.</p>';
          isiDropdown([]);
          perbaruiHitungan(0);
        });
    })();

    document.addEventListener('click', function (e) {
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

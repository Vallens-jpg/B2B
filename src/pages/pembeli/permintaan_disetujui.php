<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Permintaan Disetujui</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Permintaan Disetujui Page Styles
       ========================================================================== */
    body.pd-disetujui-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    .pd-page-container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 2.25rem 2.5rem 0 2.5rem;
    }

    /* Top Title & Search Header */
    .pd-header-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 1.5rem;
    }

    .pd-title-group {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .pd-main-heading {
      font-size: 1.65rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
      margin: 0;
    }

    .pd-sub-heading {
      font-size: 0.925rem;
      color: #6B7280;
      margin: 0;
    }

    /* Search Input */
    .pd-search-wrap {
      position: relative;
      width: 320px;
      display: flex;
      align-items: center;
    }

    .pd-search-icon {
      position: absolute;
      left: 1.15rem;
      width: 17px;
      height: 17px;
      stroke: #9CA3AF;
      stroke-width: 2.2;
      fill: none;
      pointer-events: none;
    }

    .pd-search-input {
      width: 100%;
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 9999px;
      padding: 0.75rem 1.1rem 0.75rem 2.85rem;
      font-size: 0.885rem;
      color: #111827;
      font-family: inherit;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .pd-search-input::placeholder {
      color: #9CA3AF;
    }

    .pd-search-input:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Metric Summary Cards */
    .pd-metrics-row {
      display: flex;
      gap: 1.25rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .pd-metric-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 14px;
      padding: 1.25rem 1.5rem;
      width: 240px;
      box-sizing: border-box;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .pd-metric-label {
      font-size: 0.825rem;
      color: #6B7280;
      font-weight: 600;
    }

    .pd-metric-val {
      font-size: 2.15rem;
      font-weight: 800;
      color: #111827;
      line-height: 1.1;
      margin: 0.35rem 0 0.45rem 0;
    }

    .pd-metric-sub {
      font-size: 0.75rem;
      color: #6B7280;
      font-weight: 500;
    }

    /* Main Table Card */
    .pd-table-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 2rem 2.25rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .pd-table-title {
      font-size: 1.25rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1.75rem;
    }

    .pd-table-wrapper {
      width: 100%;
      overflow-x: auto;
    }

    .pd-data-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    .pd-data-table th {
      font-size: 0.85rem;
      font-weight: 700;
      color: #6B7280;
      padding-bottom: 1rem;
      border-bottom: 1.5px solid #F1F5F9;
      white-space: nowrap;
    }

    .pd-data-table td {
      padding: 1.15rem 0;
      border-bottom: 1px solid #F1F5F9;
      vertical-align: middle;
      font-size: 0.9rem;
      color: #111827;
    }

    .pd-data-table tr:last-child td {
      border-bottom: none;
    }

    /* Table Columns Specifics */
    .col-no {
      font-weight: 700;
      color: #111827;
      width: 130px;
    }

    .col-peternak {
      width: 220px;
    }

    .peternak-cell {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .peternak-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: #152C4E;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 800;
      flex-shrink: 0;
    }

    .peternak-name {
      font-weight: 600;
      color: #111827;
      line-height: 1.3;
    }

    .col-produk {
      font-weight: 600;
      width: 180px;
    }

    .col-jumlah {
      font-weight: 600;
      width: 110px;
    }

    .col-total {
      font-weight: 600;
      width: 150px;
    }

    .col-status {
      width: 120px;
    }

    .badge-disetujui {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: #ffffff;
      border: 1.5px solid #F59E0B;
      color: #B45309;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 4px 14px;
      border-radius: 9999px;
    }

    .col-aksi {
      width: 140px;
      text-align: right;
    }

    .btn-ajukan-pesanan {
      background-color: #152C4E;
      color: #ffffff;
      border: none;
      border-radius: 9999px;
      padding: 0.55rem 1.25rem;
      font-size: 0.825rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-block;
      white-space: nowrap;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease, transform 0.1s ease;
    }

    .btn-ajukan-pesanan:hover {
      background-color: #0F203A;
      transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 900px) {
      .pd-metrics-row {
        flex-direction: column;
      }
      .pd-metric-card {
        width: 100%;
      }
      .pd-header-row {
        flex-direction: column;
        align-items: stretch;
      }
      .pd-search-wrap {
        width: 100%;
      }
    }

    @media (max-width: 640px) {
      .pd-page-container {
        padding: 1.25rem 1rem 0 1rem;
      }
      .pd-table-card {
        padding: 1.25rem 1rem;
      }
    }
  </style>
</head>
<body class="pd-disetujui-body">

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
        <a href="permintaan_disetujui.php" class="nav-pill-item active">
          <svg class="nav-pill-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor"/>
            <path d="M8 12l3 3 5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Permintaan Disetujui
        </a>
      </nav>

      <div class="navbar-actions notif-dropdown-wrapper">
        <button type="button" class="notification-bell-btn" id="notifBellBtn" onclick="toggleNotificationDropdown(event)" aria-label="Notifikasi">
          <svg class="bell-icon" viewBox="0 0 24 24">
            <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
          </svg>
          <span class="notification-badge-red" id="notifBadgeRed" style="display:none">0</span>
        </button>
        <a href="../profil.php" class="user-profile-capsule">
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
            <a href="../notifikasi.php" class="notif-footer-link">Lihat Semua Notifikasi</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content Container -->
  <main class="pd-page-container">

    <!-- Top Header: Title & Search -->
    <div class="pd-header-row">
      <div class="pd-title-group">
        <h1 class="pd-main-heading">Permintaan Disetujui</h1>
        <p class="pd-sub-heading">Kelola permintaan yang sudah disetujui dan siap diproses lebih lanjut.</p>
      </div>

      <div class="pd-search-wrap">
        <svg class="pd-search-icon" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input
          type="text"
          id="permintaanSearch"
          class="pd-search-input"
          placeholder="Cari permintaan"
          oninput="searchPermintaan()"
        >
      </div>
    </div>

    <!-- Summary Metrics -->
    <div class="pd-metrics-row">
      <div class="pd-metric-card">
        <span class="pd-metric-label">Total permintaan</span>
        <div class="pd-metric-val" id="metricTotal">0</div>
        <span class="pd-metric-sub" id="metricBaruHariIni">—</span>
      </div>

      <div class="pd-metric-card">
        <span class="pd-metric-label">Disetujui</span>
        <div class="pd-metric-val" id="metricDisetujui">0</div>
        <span class="pd-metric-sub">Siap diproses pembayaran</span>
      </div>
    </div>

    <!-- Main Table Card -->
    <div class="pd-table-card">
      <h2 class="pd-table-title">Daftar permintaan disetujui</h2>

      <div class="pd-table-wrapper">
        <table class="pd-data-table">
          <thead>
            <tr>
              <th class="col-no">No. Permintaan</th>
              <th class="col-peternak">Peternak</th>
              <th class="col-produk">Produk</th>
              <th class="col-jumlah">Jumlah</th>
              <th class="col-total">Total</th>
              <th class="col-status">Status</th>
              <th class="col-aksi">Aksi</th>
            </tr>
          </thead>
          <tbody id="permintaanTableBody">
            <tr><td colspan="7" style="text-align:center;padding:1rem;">Memuat permintaan…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    function searchPermintaan() {
      const q = document.getElementById('permintaanSearch').value.toLowerCase().trim();
      const rows = document.querySelectorAll('.permintaan-row');

      rows.forEach(row => {
        const id = row.getAttribute('data-id').toLowerCase();
        const peternak = row.getAttribute('data-peternak').toLowerCase();
        const produk = row.getAttribute('data-produk').toLowerCase();

        if (!q || id.includes(q) || peternak.includes(q) || produk.includes(q)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // ---- Permintaan disetujui asli dari API (pesanan berstatus diproses) ----
    (function () {
      var tbody = document.getElementById('permintaanTableBody');
      function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
      function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      function inisial(nama) {
        return String(nama || '?').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('') || '?';
      }
      fetch('../../api/order/list.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!tbody) return;
          if (!res || !res.ok || !res.data || !res.data.orders) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:1rem;">Gagal memuat permintaan. Periksa koneksi atau login kembali.</td></tr>';
            return;
          }
          var semua = res.data.orders;
          var disetujui = semua.filter(function (o) { return o.status === 'diproses'; });
          var totalEl = document.getElementById('metricTotal');
          if (totalEl) totalEl.innerText = semua.length;
          var setujuEl = document.getElementById('metricDisetujui');
          if (setujuEl) setujuEl.innerText = disetujui.length;
          var now = new Date();
          var hariIni = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
          var baru = 0;
          semua.forEach(function (o) {
            if (o.created_at && String(o.created_at).slice(0, 10) === hariIni) baru++;
          });
          var baruEl = document.getElementById('metricBaruHariIni');
          if (baruEl) baruEl.innerText = baru + ' permintaan baru hari ini';
          if (!disetujui.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:1rem;">Belum ada permintaan yang disetujui.</td></tr>';
            return;
          }
          tbody.innerHTML = disetujui.map(function (o) {
            var nomor = '#ORD-2026-' + String(o.id).padStart(3, '0');
            var perusahaan = o.nama_perusahaan || '-';
            return '<tr class="permintaan-row" data-id="' + nomor + '" data-peternak="' + escapeHtml(perusahaan) + '" data-produk="' + escapeHtml(o.nama_produk) + '">'
              + '<td class="col-no">' + nomor + '</td>'
              + '<td class="col-peternak"><div class="peternak-cell"><div class="peternak-avatar">' + escapeHtml(inisial(perusahaan)) + '</div><span class="peternak-name">' + escapeHtml(perusahaan) + '</span></div></td>'
              + '<td class="col-produk">' + escapeHtml(o.nama_produk) + '</td>'
              + '<td class="col-jumlah">' + o.jumlah_kg + ' kg</td>'
              + '<td class="col-total">' + rupiah(o.total_bayar) + '</td>'
              + '<td class="col-status"><span class="badge-disetujui">Disetujui</span></td>'
              + '<td class="col-aksi"><a href="../order_detail.php?id=' + o.id + '" class="btn-ajukan-pesanan">Lihat Detail</a></td>'
              + '</tr>';
          }).join('');
        })
        .catch(function () {
          if (tbody) tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:1rem;">Gagal memuat permintaan. Periksa koneksi atau login kembali.</td></tr>';
        });
    })();

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
  <script>
    // Avatar & nama dari user yang sedang login (bukan "AD" hardcode).
    (function () {
      fetch('../../api/auth/me.php').then(function (r) { return r.json(); }).then(function (res) {
        if (!res || !res.ok || !res.data || !res.data.nama) return;
        var nama = res.data.nama;
        var inisial = nama.trim().split(/\s+/).map(function (w) { return w.charAt(0); }).slice(0, 2).join('').toUpperCase();
        document.querySelectorAll('.user-avatar-circle, .ulasan-user-avatar, .pd-user-avatar').forEach(function (el) { el.textContent = inisial; });
        document.querySelectorAll('.user-name-text').forEach(function (el) { el.textContent = nama; });
      }).catch(function () {});
    })();
  </script>
</body>
</html>

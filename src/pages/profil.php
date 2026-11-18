<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Profil Pengguna</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Halaman Profil Pengguna Styles
       ========================================================================== */
    body.profil-page-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    .profil-main-container {
      max-width: 1120px;
      margin: 0 auto;
      padding: 2.75rem 2rem 0 2rem;
    }

    /* 2-Column Grid Layout */
    .profil-grid-layout {
      display: grid;
      grid-template-columns: 290px 1fr;
      gap: 1.75rem;
      align-items: start;
    }

    /* Left Card: Avatar & Summary */
    .profil-avatar-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 3rem 2rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .profil-gradient-circle {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: radial-gradient(circle at 35% 35%, #22c55e 0%, #15803d 55%, #064e3b 100%);
      margin-bottom: 1.5rem;
      box-shadow: inset 0 -4px 8px rgba(0, 0, 0, 0.2);
    }

    .profil-business-name {
      font-size: 1.15rem;
      font-weight: 800;
      color: #111827;
      margin: 0 0 0.35rem 0;
    }

    .profil-business-email {
      font-size: 0.885rem;
      color: #6B7280;
      font-weight: 500;
      margin: 0;
    }

    /* Right Column: Forms & Actions */
    .profil-forms-column {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .profil-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 2rem 2.25rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .profil-section-heading {
      font-size: 1.15rem;
      font-weight: 800;
      color: #111827;
      margin: 0 0 1.5rem 0;
    }

    /* Form Grid Rows */
    .form-row-two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
      margin-bottom: 1.25rem;
    }

    .form-group-item {
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
    }

    .form-group-item.full-width {
      margin-bottom: 0;
    }

    .profil-input-label {
      font-size: 0.8rem;
      font-weight: 700;
      color: #111827;
    }

    .profil-input-field {
      width: 100%;
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.885rem;
      color: #111827;
      font-family: inherit;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .profil-input-field:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Select Dropdown */
    .profil-select-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }

    .profil-select-field {
      width: 100%;
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 10px;
      padding: 0.75rem 2.5rem 0.75rem 1rem;
      font-size: 0.885rem;
      color: #111827;
      font-family: inherit;
      outline: none;
      box-sizing: border-box;
      appearance: none;
      cursor: pointer;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .profil-select-field:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    .profil-select-arrow {
      position: absolute;
      right: 1rem;
      pointer-events: none;
      width: 18px;
      height: 18px;
      stroke: #6B7280;
      stroke-width: 2.2;
      fill: none;
    }

    /* Bottom Action Buttons */
    .profil-action-buttons {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 1rem;
      margin-top: 0.25rem;
    }

    .btn-profil-batal {
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 10px;
      padding: 0.75rem 2rem;
      font-size: 0.875rem;
      font-weight: 700;
      color: #111827;
      text-decoration: none;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .btn-profil-batal:hover {
      background-color: #F8FAFC;
      border-color: #CBD5E1;
    }

    .btn-profil-simpan {
      background-color: #152C4E;
      color: #ffffff;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.85rem;
      font-size: 0.875rem;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease;
    }

    .btn-profil-simpan:hover {
      background-color: #0F203A;
    }

    /* Responsive */
    @media (max-width: 860px) {
      .profil-grid-layout {
        grid-template-columns: 1fr;
      }
      .profil-avatar-card {
        padding: 2rem;
      }
      .form-row-two-col {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="profil-page-body">

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

  <!-- Main Content Container -->
  <main class="profil-main-container">
    <div class="profil-grid-layout">

      <!-- Left Column: Avatar & Business Summary Card -->
      <aside class="profil-avatar-card">
        <div class="profil-gradient-circle"></div>
        <h2 class="profil-business-name" id="displayBusinessName">Memuat…</h2>
        <p class="profil-business-email" id="displayEmail">—</p>
        <p class="profil-business-email" id="displayRole">—</p>
      </aside>

      <!-- Right Column: Form Sections & Actions -->
      <section class="profil-forms-column">

        <!-- 1. Informasi Usaha Card -->
        <div class="profil-card">
          <h2 class="profil-section-heading">Informasi Usaha</h2>

          <div class="form-row-two-col">
            <div class="form-group-item">
              <label for="namaUsaha" class="profil-input-label">Nama Usaha</label>
              <input type="text" id="namaUsaha" class="profil-input-field" value="">
            </div>
            <div class="form-group-item">
              <label for="emailUsaha" class="profil-input-label">Email</label>
              <input type="email" id="emailUsaha" class="profil-input-field" value="">
            </div>
          </div>

          </div>

        </div>

        <!-- Bottom Action Buttons -->
        <div class="profil-action-buttons">
          <a href="pembeli/dashboard_buyer.php" class="btn-profil-batal">Batal</a>
          <button type="button" class="btn-profil-simpan" onclick="simpanProfil()">
            Simpan Perubahan
          </button>
        </div>

      </section>

    </div>
  </main>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    // ---- Profil asli dari API me.php ----
    (function () {
      const labelRole = { pembeli: 'Pembeli', perusahaan: 'Perusahaan', admin: 'Admin' };
      fetch('../api/auth/me.php')
        .then(function (r) {
          if (r.status === 401) { window.location.href = '../login.php'; return null; }
          return r.json();
        })
        .then(function (res) {
          if (!res || !res.ok || !res.data) return;
          const u = res.data;
          if (u.nama) {
            document.getElementById('displayBusinessName').innerText = u.nama;
            const namaUsaha = document.getElementById('namaUsaha');
            if (namaUsaha) namaUsaha.value = u.nama;
            document.querySelectorAll('.user-name-text').forEach(function (el) { el.innerText = u.nama; });
            const avatar = document.querySelector('.user-avatar-circle');
            if (avatar) {
              const inisial = u.nama.trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('');
              avatar.innerText = inisial || 'U';
            }
          }
          if (u.email) {
            document.getElementById('displayEmail').innerText = u.email;
            const emailUsaha = document.getElementById('emailUsaha');
            if (emailUsaha) emailUsaha.value = u.email;
          }
          if (u.role) {
            const roleEl = document.getElementById('displayRole');
            if (roleEl) roleEl.innerText = labelRole[u.role] || u.role;
          }
        })
        .catch(function () { /* biarkan nilai netral bila API gagal */ });
    })();

    // Simpan profil ke database lewat API (bukan hanya ubah tampilan).
    function simpanProfil() {
      const nama = document.getElementById('namaUsaha').value.trim();
      const email = document.getElementById('emailUsaha').value.trim();
      if (!nama || !email) {
        alert('Nama dan email wajib diisi.');
        return;
      }
      fetch('../api/auth/update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nama: nama, email: email })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok) {
            alert(res.error || 'Gagal menyimpan profil.');
            return;
          }
          document.getElementById('displayBusinessName').innerText = res.data.nama;
          document.getElementById('displayEmail').innerText = res.data.email;
          document.querySelectorAll('.user-name-text').forEach(function (el) { el.innerText = res.data.nama; });
          alert('Perubahan profil berhasil disimpan!');
        })
        .catch(function () { alert('Gagal menghubungi server.'); });
    }

    // Dropdown notification helpers
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
      fetch('../api/notifikasi/tandai-dibaca.php', { method: 'POST' }).catch(function () {});
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
      fetch('../api/notifikasi.php')
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
            return '<a href="order_detail.php?id=' + n.order_id + '" class="notif-item' + (n.dibaca ? '' : ' unread') + '">'
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
</body>
</html>

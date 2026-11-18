<?php session_start(); ?>
﻿<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Dashboard Buyer</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <style>
    /* ── Filter Lokasi ── */
    .filter-lokasi-bar {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      flex-wrap: wrap;
      padding: 1.25rem 2rem 0 2rem;
      max-width: 1240px;
      margin: 0 auto;
    }
    .filter-lokasi-label {
      font-size: 0.82rem;
      font-weight: 700;
      color: #6B7280;
      display: flex;
      align-items: center;
      gap: 0.35rem;
      white-space: nowrap;
    }

    .filter-lokasi-label svg {
      width: 14px;
      height: 14px;
      fill: #6B7280;
    }
    .filter-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.42rem 1rem;
      border-radius: 9999px;
      border: 1.5px solid #E2E8F0;
      background: #ffffff;
      color: #374151;
      font-size: 0.8rem;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.15s ease;
      white-space: nowrap;
    }
    .filter-pill:hover {
      border-color: #152C4E;
      color: #152C4E;
      background: #F0F4FF;
    }
    .filter-pill.active {
      background: #152C4E;
      border-color: #152C4E;
      color: #ffffff;
    }
    .filter-pill .pill-count {
      background: rgba(255,255,255,0.25);
      color: inherit;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 9999px;
    }
    .filter-pill:not(.active) .pill-count {
      background: #F1F5F9;
      color: #6B7280;
    }
  </style></head>
<body class="dashboard-body">

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
        <a href="dashboard_buyer.php" class="nav-pill-item active">
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
        <a href="pesanan_disetujui.php" class="nav-pill-item">
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
        <a href="../profil.php" class="user-profile-capsule">
          <div class="user-avatar-circle">AD</div>
          <span class="user-name-text">Buyer</span>
        </a>
        <button type="button" class="notification-bell-btn" onclick="fetch('../../api/auth/logout.php').then(function(){ window.location.href='../login.php'; });" aria-label="Keluar" title="Keluar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
        </button>

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

  <!-- Main Content Grid -->
  <main class="dashboard-main-content">
    <div class="cards-grid-layout" id="sellerCardsContainer">
      <p id="sellerCardsEmpty" style="text-align:center;padding:2rem;color:#6B7280;">Memuat penjual…</p>

  </main>


  <script>
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

    // ---- Daftar penjual asli dari API ----
    (function () {
      var grid = document.getElementById('sellerCardsContainer');
      function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
      function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      fetch('../../api/penjual.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!grid) return;
          if (!res || !res.ok || !res.data || !res.data.penjual) {
            grid.innerHTML = '<p style="text-align:center;padding:2rem;color:#6B7280;">Gagal memuat penjual. Periksa koneksi atau login kembali.</p>';
            return;
          }
          var items = res.data.penjual;
          if (!items.length) {
            grid.innerHTML = '<p style="text-align:center;padding:2rem;color:#6B7280;">Belum ada penjual.</p>';
            return;
          }
          grid.innerHTML = items.map(function (s) {
            var jenis = s.produk_nama ? s.produk_nama : '—';
            var harga = s.harga_termurah !== null ? ('Mulai dari <strong>' + rupiah(s.harga_termurah) + ' / kg</strong>') : '';
            return '<article class="seller-card">'
              + '<div class="card-image-wrapper"><img src="../../assets/img/card1.png" alt="' + escapeHtml(s.nama) + '" class="card-cover-img"></div>'
              + '<div class="card-body-content">'
              + '<h2 class="card-title-name">' + escapeHtml(s.nama) + '</h2>'
              + '<p class="card-jenis-text"><strong>Jenis:</strong> ' + escapeHtml(jenis) + '</p>'
              + '<div class="card-price-text">' + harga + '</div>'
              + '<a href="detail_penjual.php?id=' + s.id + '" class="btn-detail-penjual">Lihat Detail Penjual</a>'
              + '</div></article>';
          }).join('');
        })
        .catch(function () {
          if (grid) grid.innerHTML = '<p style="text-align:center;padding:2rem;color:#6B7280;">Gagal memuat penjual. Periksa koneksi atau login kembali.</p>';
        });
    })();  </script>
</body>
</html>

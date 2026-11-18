<?php session_start(); ?>
﻿<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Daftar Dispute</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Daftar Dispute Styles
       ========================================================================== */
    body.dispute-list-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    .dispute-container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 2.25rem 2.5rem 0 2.5rem;
    }

    /* Top Title & Search Header */
    .dispute-header-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 1.5rem;
    }

    .dispute-title-wrap {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .dispute-page-title {
      font-size: 1.65rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
      margin: 0;
    }

    .dispute-page-subtitle {
      font-size: 0.925rem;
      color: #6B7280;
      margin: 0;
    }

    /* Search Input */
    .dispute-search-wrap {
      position: relative;
      width: 360px;
      display: flex;
      align-items: center;
    }

    .dispute-search-icon {
      position: absolute;
      left: 1.1rem;
      width: 18px;
      height: 18px;
      stroke: #9CA3AF;
      stroke-width: 2.2;
      fill: none;
      pointer-events: none;
    }

    .dispute-search-input {
      width: 100%;
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 12px;
      padding: 0.85rem 1.1rem 0.85rem 2.75rem;
      font-size: 0.885rem;
      color: #111827;
      font-family: inherit;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dispute-search-input::placeholder {
      color: #9CA3AF;
    }

    .dispute-search-input:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Filter Pills */
    .dispute-filter-row {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      flex-wrap: wrap;
      margin-bottom: 1.75rem;
    }

    .dispute-filter-pill {
      background-color: #ffffff;
      border: 1.5px solid #E2E8F0;
      border-radius: 9999px;
      padding: 0.5rem 1.25rem;
      font-size: 0.825rem;
      font-weight: 700;
      color: #374151;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.15s ease;
    }

    .dispute-filter-pill:hover {
      background-color: #F8FAFC;
      border-color: #CBD5E1;
    }

    .dispute-filter-pill.active {
      background-color: #152C4E;
      border-color: #152C4E;
      color: #ffffff;
    }

    /* Dispute Cards List */
    .dispute-cards-list {
      display: flex;
      flex-direction: column;
      gap: 1.15rem;
    }

    .dispute-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 1.35rem 1.75rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      transition: box-shadow 0.15s ease, transform 0.1s ease;
    }

    .dispute-card:hover {
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
      transform: translateY(-1px);
    }

    /* Card Left Info */
    .dispute-card-left {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .dispute-order-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .dispute-order-id {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
    }

    /* Status Badges */
    .dispute-badge {
      display: inline-flex;
      align-items: center;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      padding: 3px 9px;
      border-radius: 6px;
    }

    .dispute-badge-amber {
      background-color: #FEF3C7;
      color: #B45309;
    }

    .dispute-badge-blue {
      background-color: #EFF6FF;
      color: #2563EB;
    }

    .dispute-badge-green {
      background-color: #DEF7EC;
      color: #03543F;
    }

    .dispute-card-seller {
      font-size: 0.885rem;
      font-weight: 700;
      color: #111827;
    }

    .dispute-card-date {
      font-size: 0.78rem;
      color: #6B7280;
    }

    /* Card Right Info */
    .dispute-card-right {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    .dispute-amount-group {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.2rem;
    }

    .dispute-amount-val {
      font-size: 1.15rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
    }

    .dispute-amount-label {
      font-size: 0.75rem;
      color: #6B7280;
      font-weight: 500;
    }

    .btn-dispute-diskusi {
      background-color: #152C4E;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      padding: 0.65rem 1.4rem;
      font-size: 0.875rem;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease;
      display: inline-block;
      text-align: center;
    }

    .btn-dispute-diskusi:hover {
      background-color: #0F203A;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .dispute-header-row {
        flex-direction: column;
        align-items: stretch;
      }
      .dispute-search-wrap {
        width: 100%;
      }
      .dispute-card {
        flex-direction: column;
        align-items: flex-start;
      }
      .dispute-card-right {
        width: 100%;
        justify-content: space-between;
        border-top: 1px solid #F1F5F9;
        padding-top: 0.85rem;
        margin-top: 0.25rem;
      }
      .dispute-amount-group {
        align-items: flex-start;
      }
    }
  </style>
</head>
<body class="dispute-list-body">

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
        <a href="dispute_list.php" class="nav-pill-item active">
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
        <a href="profil.php" class="user-profile-capsule">
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
  <main class="dispute-container">

    <!-- Top Header & Search -->
    <div class="dispute-header-row">
      <div class="dispute-title-wrap">
        <h1 class="dispute-page-title">Daftar Dispute</h1>
        <p class="dispute-page-subtitle">Kelola dan tindak lanjuti sengketa pesanan Anda.</p>
      </div>

      <div class="dispute-search-wrap">
        <svg class="dispute-search-icon" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input
          type="text"
          id="disputeSearchInput"
          class="dispute-search-input"
          placeholder="Cari nomor pesanan atau nama penjual..."
          oninput="filterDisputes()"
        >
      </div>
    </div>

    <!-- Filter Pills -->
    <div class="dispute-filter-row">
      <button type="button" class="dispute-filter-pill active" onclick="setFilter('semua', this)">Semua</button>
      <button type="button" class="dispute-filter-pill" onclick="setFilter('dibuka', this)">Dibuka</button>
      <button type="button" class="dispute-filter-pill" onclick="setFilter('diproses', this)">Diproses</button>
      <button type="button" class="dispute-filter-pill" onclick="setFilter('diputuskan', this)">Diputuskan</button>
    </div>

    <!-- Dispute Cards List -->
    <div class="dispute-cards-list" id="disputeCardsContainer">
      <p style="text-align:center;padding:1rem;color:#6B7280;">Memuat dispute…</p>
    </div>

  </main>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    let activeFilter = 'semua';

    function setFilter(filter, btnEl) {
      activeFilter = filter;
      document.querySelectorAll('.dispute-filter-pill').forEach(btn => btn.classList.remove('active'));
      btnEl.classList.add('active');
      filterDisputes();
    }

    // ---- Daftar dispute asli dari API ----
    (function () {
      var container = document.getElementById('disputeCardsContainer');
      function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
      function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }
      function tanggalId(waktu) {
        var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var p = String(waktu || '').split(' ');
        var d = (p[0] || '').split('-');
        if (d.length < 3) return waktu || '';
        return parseInt(d[2], 10) + ' ' + bulan[parseInt(d[1], 10) - 1] + ' ' + d[0] + ' • ' + String(p[1] || '').slice(0, 5) + ' WIB';
      }
      function badge(status) {
        if (status === 'dibuka') return '<span class="dispute-badge dispute-badge-blue">DIBUKA</span>';
        if (status === 'diproses') return '<span class="dispute-badge dispute-badge-amber">DIPROSES</span>';
        return '<span class="dispute-badge dispute-badge-green">DIPUTUSKAN</span>';
      }
      fetch('../api/dispute/list.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!container) return;
          if (!res || !res.ok || !res.data || !res.data.dispute) {
            container.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Gagal memuat dispute. Periksa koneksi atau login kembali.</p>';
            return;
          }
          var items = res.data.dispute;
          if (!items.length) {
            container.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Belum ada dispute.</p>';
            return;
          }
          container.innerHTML = items.map(function (d) {
            var nomor = '#ORD-2026-' + String(d.order_id).padStart(3, '0');
            return '<article class="dispute-card" data-order="' + nomor + '" data-seller="' + escapeHtml(d.nama_perusahaan || '') + '" data-status="' + d.status + '">'
              + '<div class="dispute-card-left">'
              + '<div class="dispute-order-header"><span class="dispute-order-id">' + nomor + '</span>' + badge(d.status) + '</div>'
              + '<div class="dispute-card-seller">Penjual: ' + escapeHtml(d.nama_perusahaan || '-') + '</div>'
              + '<div class="dispute-card-date">Dibuat: ' + tanggalId(d.created_at) + '</div>'
              + '</div>'
              + '<div class="dispute-card-right">'
              + '<div class="dispute-amount-group"><span class="dispute-amount-val">' + rupiah(d.total_bayar) + '</span><span class="dispute-amount-label">Total Dispute</span></div>'
              + '<a href="dispute_detail.php?id=' + d.id + '" class="btn-dispute-diskusi">Diskusi</a>'
              + '</div></article>';
          }).join('');
          filterDisputes();
        })
        .catch(function () {
          if (container) container.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Gagal memuat dispute. Periksa koneksi atau login kembali.</p>';
        });
    })();

    function filterDisputes() {
      const q = document.getElementById('disputeSearchInput').value.toLowerCase().trim();
      const cards = document.querySelectorAll('.dispute-card');

      cards.forEach(card => {
        const orderId = card.getAttribute('data-order').toLowerCase();
        const seller = card.getAttribute('data-seller').toLowerCase();
        const status = card.getAttribute('data-status');

        let matchesSearch = !q || orderId.includes(q) || seller.includes(q);
        // By default show matching search
        let matchesFilter = true;
        if (activeFilter !== 'semua') {
          matchesFilter = (status === activeFilter);
        }

        if (matchesSearch && matchesFilter) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }

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

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Diskusi Dispute (3 Pihak)</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Diskusi Dispute (3 Pihak)
       ========================================================================== */
    body.pd-page-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    /* Top Navigation Header */
    .pd-navbar {
      background-color: #ffffff;
      border-bottom: 1px solid #EBE7DD;
      padding: 0.9rem 2.5rem;
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .pd-nav-back-btn {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      border: 1.5px solid #d1d5db;
      background-color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.15s ease, border-color 0.15s ease;
      flex-shrink: 0;
    }

    .pd-nav-back-btn:hover {
      background-color: #f8fafc;
      border-color: #9ca3af;
    }

    .pd-nav-back-btn svg {
      width: 20px;
      height: 20px;
      stroke: #111827;
      stroke-width: 2.2;
    }

    .pd-nav-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin: 0;
      text-align: center;
      justify-self: center;
    }

    .pd-nav-actions {
      display: flex;
      align-items: center;
      gap: 1.25rem;
      justify-self: end;
    }

    .pd-bell-btn {
      position: relative;
      background: none;
      border: none;
      cursor: pointer;
      padding: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pd-bell-btn svg {
      width: 24px;
      height: 24px;
      fill: #111827;
    }

    .pd-bell-badge {
      position: absolute;
      top: 2px;
      right: 2px;
      width: 17px;
      height: 17px;
      background-color: #ef4444;
      color: #ffffff;
      font-size: 0.7rem;
      font-weight: 700;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #ffffff;
    }

    .pd-user-pill {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      border: 1.5px solid #e2e8f0;
      border-radius: 9999px;
      padding: 4px 14px 4px 5px;
      background-color: #ffffff;
    }

    .pd-user-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background-color: #152c4e;
      color: #ffffff;
      font-size: 0.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      letter-spacing: 0.02em;
    }

    .pd-user-name {
      font-size: 0.875rem;
      font-weight: 600;
      color: #111827;
    }

    /* Main Container */
    .pd-container {
      max-width: 1120px;
      margin: 0 auto;
      padding: 2.25rem 2rem 0 2rem;
    }

    /* Header Block: Dispute Title & Breadcrumb */
    .pd-header-block {
      margin-bottom: 1.5rem;
    }

    .pd-title-row {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      flex-wrap: wrap;
      margin-bottom: 0.4rem;
    }

    .pd-dispute-id {
      font-size: 1.45rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
    }

    .pd-status-pill-admin {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      background-color: #FEF9C3;
      color: #854D0E;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 9999px;
    }

    .pd-pill-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background-color: #CA8A04;
      display: inline-block;
    }

    .pd-breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.85rem;
      color: #6B7280;
    }

    .pd-breadcrumb span {
      color: #6B7280;
    }

    .pd-breadcrumb-sep {
      color: #9CA3AF;
      font-size: 0.9rem;
    }

    /* Summary Card (Top Card) */
    .pd-summary-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 14px;
      padding: 1.35rem 2rem;
      margin-bottom: 1.75rem;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .pd-summary-col {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }

    .pd-summary-label {
      font-size: 0.8rem;
      color: #6B7280;
      font-weight: 500;
    }

    .pd-summary-value {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: -0.01em;
    }

    /* Main Two-Column Layout */
    .pd-main-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 1.75rem;
      align-items: start;
    }

    /* Cards Common */
    .pd-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 14px;
      padding: 1.65rem 1.75rem;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .pd-card-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1.5rem;
    }

    /* Discussion Timeline */
    .pd-timeline-item {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
    }

    .pd-avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-weight: 800;
      color: #ffffff;
      flex-shrink: 0;
    }

    .pd-avatar-buyer {
      background-color: #152C4E;
    }

    .pd-avatar-seller {
      background-color: #10B981;
    }

    .pd-avatar-admin {
      background-color: #854D0E;
    }

    .pd-msg-content {
      flex: 1;
    }

    .pd-msg-header {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      flex-wrap: wrap;
      margin-bottom: 0.45rem;
    }

    .pd-msg-author {
      font-size: 0.95rem;
      font-weight: 800;
      color: #111827;
    }

    /* Role Badges */
    .pd-role-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 9999px;
    }

    .pd-role-buyer {
      background-color: #EEF2F6;
      color: #1E293B;
    }

    .pd-role-buyer .pd-badge-dot {
      background-color: #1E293B;
    }

    .pd-role-seller {
      background-color: #DEF7EC;
      color: #03543F;
    }

    .pd-role-seller .pd-badge-dot {
      background-color: #059669;
    }

    .pd-role-admin {
      background-color: #FEF3C7;
      color: #92400E;
    }

    .pd-role-admin .pd-badge-dot {
      background-color: #CA8A04;
    }

    .pd-badge-dot {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      display: inline-block;
    }

    .pd-msg-time {
      font-size: 0.8rem;
      color: #9CA3AF;
      font-weight: 500;
    }

    .pd-msg-body {
      font-size: 0.885rem;
      color: #374151;
      line-height: 1.55;
      margin: 0;
      margin-bottom: 0.65rem;
    }

    .pd-attachment-row {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex-wrap: wrap;
      margin-top: 0.5rem;
    }

    .pd-attachment-label {
      font-size: 0.8rem;
      color: #6B7280;
      font-weight: 500;
    }

    .pd-attachment-chip {
      background-color: #F3F4F6;
      border: 1px solid #E5E7EB;
      color: #374151;
      font-size: 0.76rem;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 9999px;
      display: inline-flex;
      align-items: center;
    }

    .pd-divider {
      height: 1px;
      background-color: #F1F5F9;
      margin: 1.25rem 0;
    }

    /* Comment Form Area */
    .pd-comment-section {
      margin-top: 1.75rem;
    }

    .pd-comment-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.6rem;
    }

    .pd-comment-textarea {
      width: 100%;
      min-height: 85px;
      border: 1.5px solid #E2E8F0;
      border-radius: 10px;
      padding: 0.85rem 1rem;
      font-size: 0.885rem;
      color: #1F2937;
      font-family: inherit;
      resize: vertical;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      background-color: #ffffff;
      box-sizing: border-box;
      margin-bottom: 1rem;
    }

    .pd-comment-textarea::placeholder {
      color: #9CA3AF;
    }

    .pd-comment-textarea:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    .pd-comment-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .pd-btn-lampiran {
      background-color: #ffffff;
      border: 1.5px solid #D1D5DB;
      border-radius: 8px;
      padding: 0.65rem 1.25rem;
      font-size: 0.85rem;
      font-weight: 700;
      color: #111827;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .pd-btn-lampiran:hover {
      background-color: #f8fafc;
      border-color: #9ca3af;
    }

    .pd-btn-kirim {
      background-color: #152C4E;
      border: none;
      border-radius: 8px;
      padding: 0.65rem 1.4rem;
      font-size: 0.85rem;
      font-weight: 700;
      color: #ffffff;
      cursor: pointer;
      font-family: inherit;
      transition: background-color 0.15s ease;
    }

    .pd-btn-kirim:hover {
      background-color: #0F203A;
    }

    /* Pending Attachments List */
    .pd-pending-attachments {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 0.85rem;
    }

    .pd-pending-chip {
      background-color: #EEF2F6;
      border: 1px solid #CBD5E1;
      color: #1E293B;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 3px 8px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }

    .pd-chip-remove {
      cursor: pointer;
      font-weight: 800;
      color: #ef4444;
      font-size: 0.9rem;
      line-height: 1;
    }

    /* Right Sidebar */
    .pd-sidebar {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    /* Status Dispute Stepper */
    .pd-status-card {
      padding: 1.5rem 1.65rem;
    }

    .pd-stepper-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .pd-stepper-item {
      display: flex;
      align-items: center;
      gap: 0.85rem;
    }

    /* Bullets */
    .pd-bullet-solid-green {
      width: 13px;
      height: 13px;
      border-radius: 50%;
      background-color: #10B981;
      flex-shrink: 0;
    }

    .pd-bullet-active-admin {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      border: 2px solid #111827;
      background-color: #FACC15;
      box-sizing: border-box;
      flex-shrink: 0;
    }

    .pd-bullet-hollow-gray {
      width: 13px;
      height: 13px;
      border-radius: 50%;
      border: 2px solid #CBD5E1;
      background-color: transparent;
      box-sizing: border-box;
      flex-shrink: 0;
    }

    .pd-stepper-text {
      font-size: 0.885rem;
      font-weight: 600;
      color: #374151;
    }

    .pd-stepper-text.active {
      font-weight: 800;
      color: #111827;
    }

    .pd-stepper-text.muted {
      color: #94A3B8;
      font-weight: 500;
    }

    /* Detail Dispute Card */
    .pd-detail-card {
      padding: 1.5rem 1.65rem;
    }

    .pd-detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.95rem;
      gap: 0.75rem;
    }

    .pd-detail-row:last-child {
      margin-bottom: 0;
    }

    .pd-detail-label {
      font-size: 0.85rem;
      color: #6B7280;
      font-weight: 500;
    }

    .pd-detail-value {
      font-size: 0.85rem;
      font-weight: 800;
      color: #111827;
      text-align: right;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .pd-main-grid {
        grid-template-columns: 1fr;
      }
      .pd-summary-card {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      .pd-navbar {
        padding: 0.85rem 1.25rem;
      }
      .pd-container {
        padding: 1.5rem 1.25rem 0 1.25rem;
      }
    }

    @media (max-width: 600px) {
      .pd-nav-title {
        font-size: 1.05rem;
      }
      .pd-user-name {
        display: none;
      }
      .pd-comment-actions {
        flex-direction: column;
        align-items: stretch;
      }
      .pd-btn-lampiran,
      .pd-btn-kirim {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body class="pd-page-body">

  <!-- Top Navigation Header -->
  <header class="pd-navbar">
    <a id="backLink" href="dispute_list.php" class="pd-nav-back-btn" aria-label="Kembali">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="pd-nav-title">PENGAJUAN DISPUTE</h1>
    <div class="pd-nav-actions">
      <a href="notifikasi.php" class="pd-bell-btn" aria-label="Notifikasi">
        <svg viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="pd-bell-badge" id="pdBellBadge" style="display:none">0</span>
      </a>
      <div class="pd-user-pill">
        <div class="pd-user-avatar">AD</div>
        <span class="pd-user-name">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <div class="pd-container">

    <!-- Header Block -->
    <div class="pd-header-block">
      <div class="pd-title-row">
        <span class="pd-dispute-id" id="disputeIdText">Dispute #DSP-…</span>
        <div class="pd-status-pill-admin">
          <span class="pd-pill-dot"></span>
          <span id="statusPillText">Memuat…</span>
        </div>
      </div>
      <div class="pd-breadcrumb">
        <span>Pesanan</span>
        <span class="pd-breadcrumb-sep">›</span>
        <span id="breadcrumbOrder">#ORD-…</span>
        <span class="pd-breadcrumb-sep">›</span>
        <span>Dispute</span>
      </div>
    </div>

    <!-- Summary Card -->
    <div class="pd-summary-card">
      <div class="pd-summary-col">
        <span class="pd-summary-label">Buyer</span>
        <span class="pd-summary-value" id="summaryBuyer">—</span>
      </div>
      <div class="pd-summary-col">
        <span class="pd-summary-label">Perusahaan</span>
        <span class="pd-summary-value" id="summaryPerusahaan">—</span>
      </div>
      <div class="pd-summary-col">
        <span class="pd-summary-label">Nilai Pesanan</span>
        <span class="pd-summary-value" id="summaryNilai">—</span>
      </div>
    </div>

    <!-- Main Two-Column Grid -->
    <div class="pd-main-grid">

      <!-- LEFT COLUMN: Diskusi & Bukti -->
      <div class="pd-card">
        <div class="pd-card-title">Diskusi &amp; Bukti</div>

        <div id="timelineContainer">
          <p style="text-align:center;padding:1rem;color:#6B7280;">Memuat diskusi…</p>

        <!-- Tulis Komentar Section -->
        <div class="pd-comment-section">
          <label for="commentText" class="pd-comment-label">Tulis Komentar</label>
          <textarea
            id="commentText"
            class="pd-comment-textarea"
            placeholder="Tulis komentar Anda..."
          ></textarea>

          <div class="pd-comment-actions">
            <button
              type="button"
              class="pd-btn-kirim"
              onclick="submitComment()"
            >
              Kirim Komentar
            </button>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN: Status & Detail Cards -->
      <div class="pd-sidebar">

        <!-- Status Dispute -->
        <div class="pd-card pd-status-card">
          <div class="pd-card-title">Status Dispute</div>
          <div class="pd-stepper-list">
            <span style="color:#6B7280;">Memuat status…</span>
            </div>

          </div>
        </div>

        <!-- Detail Dispute -->
        <div class="pd-card pd-detail-card">
          <div class="pd-card-title">Detail Dispute</div>

          <div class="pd-detail-row">
            <span class="pd-detail-label">Jenis</span>
            <span class="pd-detail-value" id="detailJenis">—</span>
          </div>

          <div class="pd-detail-row">
            <span class="pd-detail-label">Diajukan</span>
            <span class="pd-detail-value" id="detailDiajukan">—</span>
          </div>


        </div>

      </div>

    </div>

  </div>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    // Preserve back navigation query params
    const urlParams = new URLSearchParams(window.location.search);
    const backBtn = document.getElementById('backLink');
    const fallbackDd = 'pengajuan_dispute.php' + (window.location.search || '');
    backBtn.href = fallbackDd;
    backBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (window.history.length > 1) {
        window.history.back();
      } else if (document.referrer && document.referrer !== window.location.href) {
        window.location.href = document.referrer;
      } else {
        window.location.href = fallbackDd;
      }
    });

    var disputeId = parseInt(urlParams.get('id') || '0', 10);
    var disputeData = null;

    function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
    function escapeHtml(s) {
      return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function waktuId(waktu) {
      var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      var p = String(waktu || '').split(' ');
      var d = (p[0] || '').split('-');
      if (d.length < 3) return waktu || '';
      return parseInt(d[2], 10) + ' ' + bulan[parseInt(d[1], 10) - 1] + ' ' + d[0] + ', ' + String(p[1] || '').slice(0, 5);
    }
    function peranLabel(role) {
      if (role === 'perusahaan') return 'Perusahaan';
      if (role === 'admin') return 'Admin';
      return 'Buyer';
    }
    function itemTimeline(nama, role, waktu, isi, lampiran) {
      var avatarClass = role === 'perusahaan' ? 'pd-avatar-seller' : (role === 'admin' ? 'pd-avatar-admin' : 'pd-avatar-buyer');
      var roleClass = role === 'perusahaan' ? 'pd-role-seller' : (role === 'admin' ? 'pd-role-admin' : 'pd-role-buyer');
      var huruf = String(nama || '?').trim().charAt(0).toUpperCase() || '?';
      var lampiranHtml = lampiran ? '<div class="pd-attachment-row"><span class="pd-attachment-label">Lampiran:</span><span class="pd-attachment-chip">' + escapeHtml(lampiran) + '</span></div>' : '';
      return '<div class="pd-timeline-item">'
        + '<div class="pd-avatar ' + avatarClass + '">' + escapeHtml(huruf) + '</div>'
        + '<div class="pd-msg-content"><div class="pd-msg-header">'
        + '<span class="pd-msg-author">' + escapeHtml(nama) + '</span>'
        + '<span class="pd-role-badge ' + roleClass + '"><span class="pd-badge-dot"></span><span>' + peranLabel(role) + '</span></span>'
        + '<span class="pd-msg-time">' + escapeHtml(waktuId(waktu)) + '</span>'
        + '</div><p class="pd-msg-body">' + escapeHtml(isi).replace(/\n/g, '<br>') + '</p>'
        + lampiranHtml
        + '</div></div>';
    }
    function muatDiskusi() {
      var timeline = document.getElementById('timelineContainer');
      fetch('../api/dispute/pesan.php?dispute_id=' + disputeId)
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!timeline || !disputeData) return;
          var items = [];
          items.push(itemTimeline(disputeData.nama_buyer || '-', 'pembeli', disputeData.created_at, disputeData.deskripsi || '', disputeData.bukti_foto));
          if (res && res.ok && res.data && res.data.pesan) {
            res.data.pesan.forEach(function (m) {
              items.push(itemTimeline(m.nama, m.role, m.created_at, m.pesan, null));
            });
          }
          timeline.innerHTML = items.join('<div class="pd-divider"></div>');
        })
        .catch(function () {
          if (timeline) timeline.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Gagal memuat diskusi.</p>';
        });
    }
    function muatDispute() {
      var timeline = document.getElementById('timelineContainer');
      if (!disputeId) {
        if (timeline) timeline.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Dispute tidak ditemukan. Buka halaman ini dari Daftar Dispute.</p>';
        return;
      }
      fetch('../api/dispute/detail.php?id=' + disputeId)
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok || !res.data) {
            if (timeline) timeline.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">' + escapeHtml((res && res.message) || 'Dispute tidak ditemukan.') + '</p>';
            return;
          }
          var d = res.data;
          disputeData = d;
          var setText = function (id, v) { var el = document.getElementById(id); if (el) el.innerText = v; };
          setText('disputeIdText', 'Dispute #DSP-' + String(d.id).padStart(4, '0'));
          setText('breadcrumbOrder', '#ORD-2026-' + String(d.order_id).padStart(3, '0'));
          setText('summaryBuyer', d.nama_buyer || '-');
          setText('summaryPerusahaan', d.nama_perusahaan || '-');
          setText('summaryNilai', rupiah(d.total_bayar));
          setText('detailJenis', d.judul || '-');
          setText('detailDiajukan', waktuId(d.created_at));
          var labelStatus = d.status === 'dibuka' ? 'Dibuka' : (d.status === 'diproses' ? 'Diproses' : 'Diputuskan');
          setText('statusPillText', labelStatus);
          var langkah = ['Dibuka', 'Diproses', 'Diputuskan'];
          var idx = d.status === 'dibuka' ? 0 : (d.status === 'diproses' ? 1 : 2);
          var stepper = document.querySelector('.pd-stepper-list');
          if (stepper) {
            stepper.innerHTML = langkah.map(function (nama, i) {
              if (i < idx) return '<div class="pd-stepper-item"><span class="pd-bullet-solid-green"></span><span class="pd-stepper-text">' + nama + '</span></div>';
              if (i === idx) return '<div class="pd-stepper-item"><span class="pd-bullet-active-admin"></span><span class="pd-stepper-text active">' + nama + '</span></div>';
              return '<div class="pd-stepper-item"><span class="pd-bullet-hollow-gray"></span><span class="pd-stepper-text muted">' + nama + '</span></div>';
            }).join('');
          }
          muatDiskusi();
        })
        .catch(function () {
          if (timeline) timeline.innerHTML = '<p style="text-align:center;padding:1rem;color:#6B7280;">Gagal memuat dispute. Periksa koneksi atau login kembali.</p>';
        });
    }
    function submitComment() {
      const textarea = document.getElementById('commentText');
      const text = textarea.value.trim();
      if (!text) {
        textarea.focus();
        textarea.style.borderColor = '#EF4444';
        setTimeout(() => { textarea.style.borderColor = '#E2E8F0'; }, 2000);
        return;
      }
      if (!disputeId) return;
      fetch('../api/dispute/pesan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ dispute_id: disputeId, pesan: text })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok) {
            alert((res && res.message) || 'Gagal mengirim komentar.');
            return;
          }
          textarea.value = '';
          muatDiskusi();
        })
        .catch(function () { alert('Gagal mengirim komentar. Periksa koneksi.'); });
    }
    // Badge lonceng: jumlah notifikasi belum dibaca.
    (function () {
      var badge = document.getElementById('pdBellBadge');
      fetch('../api/notifikasi.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!badge || !res || !res.ok || !res.data || !res.data.notifikasi) return;
          var belum = 0;
          res.data.notifikasi.forEach(function (n) { if (!n.dibaca) belum++; });
          if (belum > 0) { badge.innerText = belum; badge.style.display = ''; }
        })
        .catch(function () {});
    })();
    muatDispute();
  </script>
</body>
</html>

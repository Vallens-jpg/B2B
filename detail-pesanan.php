<?php session_start(); ?>
﻿<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Detail Pesanan</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* ==========================================================================
       Detail Pesanan Specific Styles
       ========================================================================== */
    .dp-body {
      background-color: var(--bg-dashboard);
      min-height: 100vh;
      padding-bottom: 7rem;
    }

    .dp-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 2rem 2.5rem 0 2.5rem;
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    /* ===================== PROGRESS CARD ===================== */
    .dp-progress-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 1.4rem 1.6rem;
    }

    .dp-progress-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.1rem;
    }

    .dp-progress-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.2rem;
    }

    .dp-progress-sub {
      font-size: 0.82rem;
      color: #94a3b8;
    }

    .dp-saat-ini {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: #64748b;
    }

    .dp-saat-ini svg {
      width: 15px;
      height: 15px;
      stroke: #94a3b8;
      stroke-width: 2;
      fill: none;
    }

    /* Step cards row */
    .dp-steps-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.75rem;
    }

    .dp-step-card {
      border-radius: 10px;
      padding: 0.8rem 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    /* Done (green) */
    .dp-step-card.done {
      background-color: #ecfdf5;
      border: 1.5px solid #a7f3d0;
    }

    /* Active (yellow) */
    .dp-step-card.active {
      background-color: #fffbeb;
      border: 1.5px solid #fde68a;
    }

    /* Pending (gray) */
    .dp-step-card.pending {
      background-color: #f8fafc;
      border: 1.5px solid #e5e7eb;
    }

    .dp-step-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Checkmark circle */
    .dp-step-check {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background-color: #059669;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .dp-step-check svg {
      width: 13px;
      height: 13px;
      stroke: #ffffff;
      stroke-width: 2.8;
      fill: none;
    }

    /* Number circle */
    .dp-step-num {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.82rem;
      font-weight: 800;
      flex-shrink: 0;
    }

    .dp-step-num.active-num {
      background-color: #f59e0b;
      color: #ffffff;
    }

    .dp-step-num.pending-num {
      background-color: #e5e7eb;
      color: #94a3b8;
    }

    /* Status label */
    .dp-step-status {
      font-size: 0.78rem;
      font-weight: 700;
    }

    .dp-step-status.selesai  { color: #059669; }
    .dp-step-status.aktif   { color: #d97706; }
    .dp-step-status.menunggu { color: #94a3b8; }

    .dp-step-status.aktif::before {
      content: "● ";
      font-size: 0.6rem;
    }

    /* Step label */
    .dp-step-label {
      font-size: 0.95rem;
      font-weight: 800;
    }

    .dp-step-label.selesai-label  { color: #065f46; }
    .dp-step-label.aktif-label   { color: #92400e; }
    .dp-step-label.pending-label  { color: #94a3b8; }

    /* ===================== MIDDLE GRID ===================== */
    .dp-middle-grid {
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 1.25rem;
      align-items: start;
    }

    /* ---- Info Supplier Card ---- */
    .dp-info-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 1.5rem 1.6rem;
    }

    .dp-info-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1.25rem;
    }

    .dp-info-row {
      margin-bottom: 1.1rem;
    }

    .dp-info-row:last-child {
      margin-bottom: 0;
    }

    .dp-info-label {
      font-size: 0.8rem;
      color: #94a3b8;
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .dp-info-value {
      font-size: 1rem;
      font-weight: 700;
      color: #111827;
    }

    /* ---- Status Saat Ini Card ---- */
    .dp-status-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 1.5rem 1.6rem;
    }

    .dp-status-title {
      font-size: 1rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.75rem;
    }

    .dp-status-state {
      font-size: 0.975rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.5rem;
    }

    .dp-status-desc {
      font-size: 0.875rem;
      color: #64748b;
      line-height: 1.6;
    }

    /* ===================== RINGKASAN CARD ===================== */
    .dp-ringkasan-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 1.4rem 1.6rem;
    }

    .dp-ringkasan-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.6rem;
    }

    .dp-ringkasan-value {
      font-size: 0.975rem;
      color: #374151;
      font-weight: 500;
    }

    /* ===================== BOTTOM ACTION BAR ===================== */
    .dp-action-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: #ffffff;
      border-top: 1px solid #e5e7eb;
      padding: 1rem 0;
      z-index: 50;
    }

    /* Top Nav Row */
    .dp-top-nav-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.25rem;
      gap: 1rem;
    }

    .dp-link-daftar-pesanan {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      font-size: 0.88rem;
      font-weight: 700;
      color: #152C4E;
      text-decoration: none;
      background-color: #ffffff;
      border: 1px solid #cbd5e1;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .dp-link-daftar-pesanan:hover {
      background-color: #F0F4FF;
      border-color: #152C4E;
      color: #0F203A;
      transform: translateY(-1px);
    }

    .dp-link-daftar-pesanan svg {
      width: 16px;
      height: 16px;
      stroke: #152C4E;
      flex-shrink: 0;
    }

    .dp-order-id-badge {
      font-size: 0.82rem;
      font-weight: 700;
      color: #64748b;
      background-color: #f1f5f9;
      padding: 0.4rem 0.75rem;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
    }

    .dp-action-bar-inner {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 2.5rem;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .btn-daftar-pesanan {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      background-color: #ffffff;
      border: 1.5px solid #152C4E;
      color: #152C4E;
      padding: 1rem;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(21, 44, 78, 0.06);
    }

    .btn-daftar-pesanan:hover {
      background-color: #F0F4FF;
      border-color: #0F203A;
      color: #0F203A;
      transform: translateY(-1px);
    }

    .btn-daftar-pesanan svg {
      width: 18px;
      height: 18px;
      stroke: #152C4E;
      stroke-width: 2;
      fill: none;
      flex-shrink: 0;
    }

    .btn-dispute {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background-color: #ffffff;
      border: 1.5px solid #d1d5db;
      color: #374151;
      padding: 1rem;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .btn-dispute:hover {
      background-color: #f8fafc;
    }

    .btn-dispute svg {
      width: 18px;
      height: 18px;
      stroke: #64748b;
      stroke-width: 1.8;
      fill: none;
    }

    .btn-konfirmasi-diterima {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      background-color: var(--primary-navy);
      border: none;
      color: #ffffff;
      padding: 1rem;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.2s ease;
      box-shadow: 0 6px 18px rgba(21, 44, 78, 0.2);
    }

    .btn-konfirmasi-diterima:hover {
      background-color: var(--primary-navy-hover);
    }

    .btn-konfirmasi-diterima svg {
      width: 18px;
      height: 18px;
      stroke: #ffffff;
      stroke-width: 2.2;
      fill: none;
    }

    @media (max-width: 900px) {
      .dp-container {
        padding: 1.5rem 1.25rem 0 1.25rem;
      }
      .dp-steps-row {
        grid-template-columns: repeat(2, 1fr);
      }
      .dp-middle-grid {
        grid-template-columns: 1fr;
      }
      .dp-action-bar-inner {
        padding: 0 1.25rem;
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 480px) {
      .dp-steps-row {
        grid-template-columns: 1fr 1fr;
      }
      .dp-action-bar-inner {
        grid-template-columns: 1fr;
      }
    }
  
    /* ---- Bukti Pengiriman ---- */
    .dp-bukti-card {
      background: #fff;
      border: 1.5px solid #E5E7EB;
      border-radius: 14px;
      padding: 1.25rem 1.5rem;
      margin-top: 1.25rem;
    }
    .dp-bukti-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    .dp-bukti-title {
      font-size: 0.95rem;
      font-weight: 800;
      color: #152C4E;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .dp-bukti-badge {
      background: #DCFCE7;
      color: #15803D;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 2px 9px;
      border-radius: 9999px;
    }
    .dp-bukti-meta {
      font-size: 0.8rem;
      color: #9CA3AF;
    }
    .dp-bukti-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
      gap: 0.75rem;
    }
    .dp-bukti-thumb {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      aspect-ratio: 4/3;
      cursor: pointer;
      border: 1.5px solid #E5E7EB;
      background: #F8FAFC;
      transition: transform 0.15s, box-shadow 0.15s;
    }
    .dp-bukti-thumb:hover {
      transform: scale(1.03);
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .dp-bukti-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .dp-bukti-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.15s;
    }
    .dp-bukti-thumb:hover .dp-bukti-overlay {
      background: rgba(0,0,0,0.28);
    }
    .dp-bukti-overlay svg {
      opacity: 0;
      transition: opacity 0.15s;
      color: #fff;
    }
    .dp-bukti-thumb:hover .dp-bukti-overlay svg {
      opacity: 1;
    }
    .dp-bukti-caption {
      font-size: 0.75rem;
      color: #6B7280;
      text-align: center;
      margin-top: 0.35rem;
    }
    /* Lightbox */
    .dp-lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.82);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 1rem;
    }
    .dp-lightbox.open {
      display: flex;
    }
    .dp-lightbox img {
      max-width: 90vw;
      max-height: 80vh;
      border-radius: 12px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.5);
      object-fit: contain;
    }
    .dp-lightbox-caption {
      color: #fff;
      font-size: 0.9rem;
      opacity: 0.85;
    }
    .dp-lightbox-close {
      position: absolute;
      top: 1.25rem;
      right: 1.5rem;
      background: rgba(255,255,255,0.15);
      border: none;
      color: #fff;
      font-size: 1.75rem;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
      transition: background 0.15s;
    }
    .dp-lightbox-close:hover { background: rgba(255,255,255,0.28); }
</style>
</head>
<body class="dp-body">

  <!-- Top Navigation Header -->
  <header class="detail-navbar">
    <a id="backLink" href="invoice-pembayaran.php" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Detail Pesanan</h1>
    <div class="navbar-actions">
      <button type="button" class="notification-bell-btn" aria-label="Notifikasi">
        <svg class="bell-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="notification-badge-red">3</span>
      </button>
      <div class="user-profile-capsule">
        <div class="user-avatar-circle">AD</div>
        <span class="user-name-text">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="dp-container">

    <!-- Top Navigation Row ke Daftar Pesanan -->
    <div class="dp-top-nav-row">
      <a href="pesanan.php" class="dp-link-daftar-pesanan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span>Ke Halaman Daftar Pesanan</span>
      </a>
      <span class="dp-order-id-badge" id="orderIdBadge">#ORD-2026-103</span>
    </div>

    <!-- Progres Pesanan -->
    <div class="dp-progress-card">
      <div class="dp-progress-header">
        <div>
          <div class="dp-progress-title">Progres Pesanan</div>
          <div class="dp-progress-sub">Tahapan pengiriman pesanan ini.</div>
        </div>
        <div class="dp-saat-ini">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Saat ini: Dikirim
        </div>
      </div>

      <div class="dp-steps-row">

        <!-- Step 1: Selesai -->
        <div class="dp-step-card done">
          <div class="dp-step-top">
            <div class="dp-step-check">
              <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <span class="dp-step-status selesai">Selesai</span>
          </div>
          <div class="dp-step-label selesai-label">Menunggu Pembayaran</div>
        </div>

        <!-- Step 2: Selesai -->
        <div class="dp-step-card done">
          <div class="dp-step-top">
            <div class="dp-step-check">
              <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <span class="dp-step-status selesai">Selesai</span>
          </div>
          <div class="dp-step-label selesai-label">Diproses</div>
        </div>

        <!-- Step 3: Aktif -->
        <div class="dp-step-card active">
          <div class="dp-step-top">
            <div class="dp-step-num active-num">3</div>
            <span class="dp-step-status aktif">Aktif</span>
          </div>
          <div class="dp-step-label aktif-label">Dikirim</div>
        </div>

        <!-- Step 4: Menunggu -->
        <div class="dp-step-card pending">
          <div class="dp-step-top">
            <div class="dp-step-num pending-num">4</div>
            <span class="dp-step-status menunggu">Menunggu</span>
          </div>
          <div class="dp-step-label pending-label">Diterima</div>
        </div>

      </div>
    </div>

    <!-- Middle Grid -->
    <div class="dp-middle-grid">

      <!-- Informasi Supplier & Produk -->
      <div class="dp-info-card">
        <div class="dp-info-title">Informasi Supplier &amp; Produk</div>

        <div class="dp-info-row">
          <div class="dp-info-label">Supplier</div>
          <div class="dp-info-value" id="supplierName">PT Telur Makmur</div>
        </div>

        <div class="dp-info-row">
          <div class="dp-info-label">Produk</div>
          <div class="dp-info-value" id="productName">Telur Ayam Ras Grade A</div>
        </div>

        <div class="dp-info-row">
          <div class="dp-info-label">Jumlah</div>
          <div class="dp-info-value" id="jumlahInfo">200 kg @ Rp 25.000 / kg</div>
        </div>

        <div class="dp-info-row">
          <div class="dp-info-label">Total Harga</div>
          <div class="dp-info-value" id="totalHarga">Rp 5.000.000</div>
        </div>
      </div>

      <!-- Status Saat Ini -->
      <div class="dp-status-card">
        <div class="dp-status-title">Status Saat Ini</div>
        <div class="dp-status-state">Barang dalam pengiriman</div>
        <div class="dp-status-desc">Pesanan sedang dalam proses pengiriman. Periksa bukti kirim untuk memastikan pengiriman sudah benar.</div>
      </div>

      <!-- Bukti Pengiriman dari Penjual -->
      <div class="dp-bukti-card">
        <div class="dp-bukti-header">
          <div class="dp-bukti-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
            Bukti Pengiriman dari Penjual
            <span class="dp-bukti-badge">Diterima</span>
          </div>
          <div class="dp-bukti-meta">Dikirim: 03 Nov 2026, 09.30</div>
        </div>
        <div class="dp-bukti-grid" style="grid-template-columns: 220px;">
          <!-- Foto barang -->
          <div>
            <div class="dp-bukti-thumb" onclick="bukaLightbox('img/bukti-kirim-telur.jpg', 'Foto barang — Telur siap kirim')">
              <img src="img/bukti-kirim-telur.jpg" alt="Bukti pengiriman telur">
              <div class="dp-bukti-overlay">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                  <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
              </div>
            </div>
            <div class="dp-bukti-caption">Foto barang siap kirim</div>
          </div>
        </div>
      </div>

    </div>

    <!-- Ringkasan Pembayaran -->
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem;">
      <div class="dp-ringkasan-card">
        <div class="dp-ringkasan-title">Ringkasan Pembayaran</div>
        <div class="dp-ringkasan-value" id="ringkasanValue">Rp 5.000.000 [LUNAS via BCA]</div>
      </div>
      <!-- Spacer to align with right column above -->
      <div></div>
    </div>

  </div>

  <!-- Bottom Action Bar -->
  <div class="dp-action-bar">
    <div class="dp-action-bar-inner">
      <a href="pesanan.php" class="btn-daftar-pesanan" id="btnDaftarPesanan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
          <rect x="9" y="3" width="6" height="4" rx="1"/>
          <path d="M9 12h6"/>
          <path d="M9 16h6"/>
        </svg>
        Daftar Pesanan
      </a>
      <button class="btn-dispute" onclick="ajukanDispute()">
        <svg viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="9"/>
          <line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round"/>
          <circle cx="12" cy="16" r="0.5" fill="#64748b" stroke="none"/>
        </svg>
        Ajukan Dispute
      </button>
      <button class="btn-konfirmasi-diterima" onclick="konfirmasiDiterima()">
        <svg viewBox="0 0 24 24">
          <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
          <path d="M8 12l3 3 5-5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Konfirmasi Diterima
      </button>
    </div>
  </div>

  <!-- Floating Navigation -->

  <script>
    const urlParams   = new URLSearchParams(window.location.search);
    const sellerParam = urlParams.get('seller')   || '1';
    const product     = urlParams.get('product')  || 'Telur Ayam Ras Grade A';
    const qty         = urlParams.get('qty')       || '200 kg';
    const price       = urlParams.get('price')     || 'Rp 25.000';
    const subtotal    = urlParams.get('subtotal')  || 'Rp 5.000.000';

    const sellersMap = {
      '1': 'PT Telur Makmur',
      '2': 'Mitra Unggas Sejahtera',
      '3': 'Tunas Farm',
      '4': 'Berkah Telur Utama',
      '5': 'Ternak Mandiri',
      '6': 'Sinar Jaya Poultry'
    };

    const company = sellersMap[sellerParam] || 'PT Telur Makmur';
    const hasParams = urlParams.get('seller') || urlParams.get('product');

    // Fill dynamic values
    document.getElementById('supplierName').innerText = hasParams ? company : 'PT Telur Makmur';
    document.getElementById('productName').innerText  = hasParams ? product : 'Telur Ayam Ras Grade A';

    // Jumlah: "200 kg @ Rp 25.000 / kg"
    const qtyClean = qty.replace(/\s*kg/i, '').trim() || '200';
    document.getElementById('jumlahInfo').innerText  = hasParams
      ? qtyClean + ' kg @ ' + price + ' / kg'
      : '200 kg @ Rp 25.000 / kg';
    document.getElementById('totalHarga').innerText  = hasParams ? subtotal : 'Rp 5.000.000';
    const orderParam = urlParams.get('order');
    if (orderParam) {
      const badge = document.getElementById('orderIdBadge');
      if (badge) badge.innerText = orderParam;
    }
    document.getElementById('ringkasanValue').innerText = (hasParams ? subtotal : 'Rp 5.000.000') + ' [LUNAS via BCA]';

    // Back link — dinamis sesuai asal halaman
    const fromPage = urlParams.get('from');
    let fallbackDpes = 'pesanan.php';
    if (fromPage === 'pesanan') {
      document.getElementById('backLink').href = 'pesanan.php';
    } else {
      const backQ =
        '?seller='   + encodeURIComponent(sellerParam) +
        '&product='  + encodeURIComponent(product) +
        '&qty='      + encodeURIComponent(qty) +
        '&price='    + encodeURIComponent(price) +
        '&subtotal=' + encodeURIComponent(subtotal);
      fallbackDpes = 'invoice-pembayaran.php' + backQ;
      document.getElementById('backLink').href = fallbackDpes;
    }

    const dpesBackBtn = document.getElementById('backLink');
    if (dpesBackBtn) {
      dpesBackBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
          window.history.back();
        } else if (document.referrer && document.referrer !== window.location.href) {
          window.location.href = document.referrer;
        } else {
          window.location.href = fallbackDpes;
        }
      });
    }

    // Actions
    function ajukanDispute() {
      window.location.href = 'pengajuan-dispute.php' +
        '?seller='   + encodeURIComponent(sellerParam) +
        '&product='  + encodeURIComponent(product) +
        '&qty='      + encodeURIComponent(qty) +
        '&price='    + encodeURIComponent(price) +
        '&subtotal=' + encodeURIComponent(subtotal);
    }

    function konfirmasiDiterima() {
      window.location.href = 'dashboard.php';
    }
  
    // ---- Lightbox Bukti Pengiriman ----
    function bukaLightbox(src, caption) {
      const lb = document.getElementById("dpLightbox");
      const img = document.getElementById("dpLightboxImg");
      const cap = document.getElementById("dpLightboxCaption");
      img.src = src;
      // fallback jika src lokal tidak ada
      img.onerror = function() {
        this.src = "https://placehold.co/800x600/e2e8f0/6b7a99?text=Bukti+Pengiriman";
      };
      cap.textContent = caption;
      lb.classList.add("open");
      document.body.style.overflow = "hidden";
    }
    function tutupLightbox() {
      document.getElementById("dpLightbox").classList.remove("open");
      document.body.style.overflow = "";
    }
    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape") tutupLightbox();
    });
</script>

  <!-- Lightbox Bukti Pengiriman -->
  <div class="dp-lightbox" id="dpLightbox" onclick="tutupLightbox()">
    <button class="dp-lightbox-close" onclick="tutupLightbox()" aria-label="Tutup">&times;</button>
    <img id="dpLightboxImg" src="" alt="Bukti Pengiriman">
    <div class="dp-lightbox-caption" id="dpLightboxCaption"></div>
  </div>
</body>
</html>

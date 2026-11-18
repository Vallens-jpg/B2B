<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Pengajuan Dispute</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Pengajuan Dispute - Matching Reference Design Form
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

    /* Form Container */
    .pd-form-container {
      max-width: 860px;
      margin: 0 auto;
      padding: 2.25rem 1.5rem 0 1.5rem;
    }

    /* Main Form Card */
    .pd-form-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 2.25rem 2.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    /* Section Titles */
    .pd-section-heading {
      font-size: 1.15rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      margin-bottom: 1.15rem;
    }

    /* Ringkasan Pesanan Box */
    .pd-product-box {
      border: 1px solid #E5E7EB;
      border-radius: 12px;
      padding: 1.1rem 1.35rem;
      display: flex;
      align-items: center;
      gap: 1.35rem;
      background-color: #ffffff;
    }

    .pd-product-thumb {
      width: 84px;
      height: 84px;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;
    }

    .pd-product-details {
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .pd-product-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin: 0;
    }

    .pd-product-amount-row {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      font-size: 0.875rem;
    }

    .pd-product-amount-label {
      color: #6B7280;
      font-weight: 500;
    }

    .pd-product-amount-value {
      font-weight: 800;
      color: #111827;
    }

    /* Section: Alasan Dispute */
    .pd-section-spacing {
      margin-top: 2rem;
    }

    .pd-sub-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #374151;
      margin-bottom: 0.55rem;
    }

    .pd-textarea-field {
      width: 100%;
      min-height: 125px;
      border: 1.5px solid #E2E8F0;
      border-radius: 10px;
      padding: 0.95rem 1.1rem;
      font-size: 0.885rem;
      color: #1F2937;
      font-family: inherit;
      resize: vertical;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      background-color: #ffffff;
    }

    .pd-textarea-field::placeholder {
      color: #9CA3AF;
    }

    .pd-textarea-field:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Buttons Row */
    .pd-action-buttons {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-top: 2.25rem;
    }

    .pd-btn-cancel {
      padding: 0.95rem 2.5rem;
      background-color: #ffffff;
      border: 1.5px solid #D1D5DB;
      border-radius: 10px;
      font-size: 0.875rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: inherit;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.15s ease, border-color 0.15s ease;
      white-space: nowrap;
    }

    .pd-btn-cancel:hover {
      background-color: #f8fafc;
      border-color: #9ca3af;
    }

    .pd-btn-submit {
      flex: 1;
      padding: 0.95rem 1.5rem;
      background-color: #152C4E;
      border: none;
      border-radius: 10px;
      font-size: 0.875rem;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: inherit;
      text-align: center;
      transition: background-color 0.15s ease, box-shadow 0.15s ease;
      box-shadow: 0 4px 14px rgba(21, 44, 78, 0.15);
    }

    .pd-btn-submit:hover {
      background-color: #0F203A;
      box-shadow: 0 6px 18px rgba(21, 44, 78, 0.25);
    }

    /* Responsive */
    @media (max-width: 640px) {
      .pd-navbar {
        padding: 0.85rem 1.25rem;
      }
      .pd-form-container {
        padding: 1.25rem 1rem 0 1rem;
      }
      .pd-form-card {
        padding: 1.5rem 1.25rem;
      }
      .pd-nav-title {
        font-size: 1.05rem;
      }
      .pd-user-name {
        display: none;
      }
      .pd-action-buttons {
        flex-direction: column-reverse;
      }
      .pd-btn-cancel,
      .pd-btn-submit {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</head>
<body class="pd-page-body">

  <!-- Top Navigation Header -->
  <header class="pd-navbar">
    <a id="backLink" href="order_detail.php" class="pd-nav-back-btn" aria-label="Kembali">
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
  <div class="pd-form-container">
    <div class="pd-form-card">

      <!-- 1. RINGKASAN PESANAN -->
      <h2 class="pd-section-heading">RINGKASAN PESANAN</h2>
      <div class="pd-product-box">
        <img src="../assets/img/prod_grade_a.png" alt="Produk" class="pd-product-thumb" id="summaryImg">
        <div class="pd-product-details">
          <h3 class="pd-product-title" id="summaryTitle">Memuat…</h3>
          <div class="pd-product-amount-row">
            <span class="pd-product-amount-label">Jumlah:</span>
            <span class="pd-product-amount-value" id="summaryAmount">—</span>
          </div>
        </div>
      </div>

      <!-- 2. ALASAN DISPUTE -->
      <div class="pd-section-spacing">
        <h2 class="pd-section-heading">ALASAN DISPUTE</h2>
        <div class="pd-sub-label">Judul Dispute</div>
        <input
          type="text"
          id="disputeJudul"
          class="pd-textarea-field"
          placeholder="Contoh: Telur pecah saat diterima"
          style="margin-bottom:1rem;"
        >
        <div class="pd-sub-label">Deskripsi Detail</div>
        <textarea
          id="disputeReason"
          class="pd-textarea-field"
          placeholder="Jelaskan kondisi kendala pada produk secara detail..."
          rows="4"
        ></textarea>
      </div>

      <!-- 4. ACTION BUTTONS -->
      <div class="pd-action-buttons">
        <a id="cancelBtn" href="order_detail.php" class="pd-btn-cancel">BATAL</a>
        <button type="button" class="pd-btn-submit" onclick="submitPengajuanDispute()">
          KIRIM PENGAJUAN DISPUTE
        </button>
      </div>

    </div>
  </div>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    // Ringkasan asli dari API (?order_id=)
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = parseInt(urlParams.get('order_id') || urlParams.get('id') || '0', 10);
    function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
    if (!orderId) {
      document.getElementById('summaryTitle').innerText = 'Pesanan tidak ditemukan. Buka halaman ini dari Detail Pesanan.';
    } else {
      fetch('../api/order/detail.php?id=' + orderId)
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok || !res.data || !res.data.order) {
            document.getElementById('summaryTitle').innerText = (res && res.message) || 'Pesanan tidak ditemukan.';
            return;
          }
          var o = res.data.order;
          document.getElementById('summaryTitle').innerText = o.nama_produk;
          document.getElementById('summaryAmount').innerText = o.jumlah_kg + ' kg — ' + rupiah(o.total_bayar);
          document.getElementById('summaryImg').alt = o.nama_produk;
        })
        .catch(function () {
          document.getElementById('summaryTitle').innerText = 'Gagal memuat pesanan.';
        });
    }

    // Preserve back / cancel button destination
    const cancelBtn = document.getElementById('cancelBtn');
    const backLink = document.getElementById('backLink');
    const fallbackPd = orderId ? ('order_detail.php?id=' + orderId) : 'order_list.php';
    cancelBtn.href = fallbackPd;
    backLink.href = fallbackPd;

    function handleSmartBackPd() {
      if (window.history.length > 1) {
        window.history.back();
      } else if (document.referrer && document.referrer !== window.location.href) {
        window.location.href = document.referrer;
      } else {
        window.location.href = fallbackPd;
      }
    }
    backLink.addEventListener('click', function(e) {
      e.preventDefault();
      handleSmartBackPd();
    });
    cancelBtn.addEventListener('click', function(e) {
      e.preventDefault();
      handleSmartBackPd();
    });

    // Submit pengajuan dispute (buat asli ke API)
    function submitPengajuanDispute() {
      const judul = document.getElementById('disputeJudul').value.trim();
      const reason = document.getElementById('disputeReason').value.trim();
      if (!orderId) {
        alert('Pesanan tidak ditemukan.');
        return;
      }
      if (!judul || !reason) {
        alert('Judul dan deskripsi dispute wajib diisi.');
        return;
      }
      fetch('../api/dispute/buat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, judul: judul, deskripsi: reason })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok) {
            alert((res && res.message) || 'Gagal mengirim pengajuan dispute.');
            return;
          }
          window.location.href = 'dispute_detail.php?id=' + res.data.dispute_id;
        })
        .catch(function () { alert('Gagal mengirim pengajuan dispute. Periksa koneksi.'); });
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
  </script>
  <script>
    // Avatar & nama dari user yang sedang login (bukan "AD" hardcode).
    (function () {
      fetch('../api/auth/me.php').then(function (r) { return r.json(); }).then(function (res) {
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

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Konfirmasi Pesanan</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Konfirmasi Pesanan Specific Styles
       ========================================================================== */
    .kp-body {
      background-color: var(--bg-dashboard);
      min-height: 100vh;
      padding-bottom: 6rem;
    }

    .kp-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem 2.5rem 0 2.5rem;
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 2.5rem;
      align-items: start;
    }

    /* ---------- Left: Ringkasan pesanan ---------- */
    .kp-summary-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 1.35rem 1.4rem;
    }

    .kp-summary-head {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-bottom: 1.1rem;
    }

    .kp-summary-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
    }

    .kp-status-badge {
      background-color: #fef3c7;
      color: #b45309;
      font-size: 0.725rem;
      font-weight: 700;
      padding: 0.28rem 0.6rem;
      border-radius: 14px;
    }

    .kp-summary-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 0.9rem;
      font-size: 0.9rem;
    }

    .kp-summary-label {
      color: #64748b;
    }

    .kp-summary-value {
      font-weight: 800;
      color: #111827;
      text-align: right;
    }

    .kp-summary-notes-label {
      font-size: 0.9rem;
      color: #64748b;
      margin-bottom: 0.55rem;
    }

    .kp-summary-notes-box {
      background-color: #f8fafc;
      border: 1px solid #eef2f6;
      border-radius: 10px;
      padding: 0.8rem 0.9rem;
      font-size: 0.85rem;
      line-height: 1.55;
      color: #334155;
    }

    /* ---------- Right: Tinjau Pesanan ---------- */
    .kp-review-card {
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 2rem 2.1rem 1.75rem 2.1rem;
    }

    .kp-review-title {
      font-size: 1.6rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.5rem;
      letter-spacing: -0.01em;
    }

    .kp-review-subtitle {
      font-size: 0.925rem;
      color: #64748b;
      margin-bottom: 1.6rem;
    }

    .kp-section-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 0.9rem;
    }

    /* Perusahaan box */
    .kp-company-box {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      background-color: #f8fafc;
      border: 1px solid #eef2f6;
      border-radius: 12px;
      padding: 1rem 1.1rem;
      margin-bottom: 1.5rem;
    }

    .kp-company-avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background-color: #ede9fe;
      color: #6d28d9;
      font-size: 0.9rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .kp-company-name {
      font-size: 0.975rem;
      font-weight: 600;
      color: #111827;
    }

    .kp-divider {
      height: 1px;
      background-color: #eef2f6;
      margin: 0 0 1.5rem 0;
    }

    /* Item pesanan table */
    .kp-table-head {
      display: grid;
      grid-template-columns: 2.4fr 0.8fr 1fr 1fr;
      gap: 1rem;
      align-items: center;
      background-color: #f8fafc;
      border-radius: 10px;
      padding: 0.8rem 1.1rem;
      font-size: 0.85rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 0.4rem;
    }

    .kp-table-head .col-qty,
    .kp-table-head .col-price {
      text-align: left;
    }

    .kp-table-head .col-sub {
      text-align: right;
    }

    .kp-table-row {
      display: grid;
      grid-template-columns: 2.4fr 0.8fr 1fr 1fr;
      gap: 1rem;
      align-items: center;
      padding: 0.85rem 1.1rem;
      font-size: 0.925rem;
      color: #1f2937;
    }

    .kp-product-cell {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      font-weight: 600;
      color: #111827;
    }

    .kp-product-icon {
      width: 34px;
      height: 34px;
      border-radius: 9px;
      background-color: #fef9c3;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .kp-product-icon svg {
      width: 18px;
      height: 18px;
    }

    .kp-sub-cell {
      text-align: right;
      font-weight: 800;
      color: #111827;
    }

    /* Pengiriman & pembayaran */
    .kp-notes-box {
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 1rem 1.1rem;
      margin-bottom: 1.25rem;
    }

    .kp-notes-label {
      font-size: 0.82rem;
      color: #64748b;
      margin-bottom: 0.45rem;
    }

    .kp-notes-text {
      font-size: 0.925rem;
      color: #1f2937;
      line-height: 1.5;
    }

    .kp-notes-input {
      width: 100%;
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 0.8rem 1rem;
      font-size: 0.9rem;
      color: #1f2937;
      font-family: var(--font-family);
    }

    .kp-total-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      background-color: #f8fafc;
      border: 1px solid #eef2f6;
      border-radius: 12px;
      padding: 1.1rem 1.2rem;
      margin-bottom: 1.1rem;
    }

    .kp-total-label {
      font-size: 1rem;
      font-weight: 700;
      color: #111827;
    }

    .kp-total-value {
      font-size: 1.5rem;
      font-weight: 800;
      color: #111827;
    }

    .kp-info-line {
      display: flex;
      align-items: center;
      gap: 0.45rem;
      font-size: 0.825rem;
      color: #64748b;
      margin-bottom: 1.5rem;
    }

    .kp-info-line svg {
      width: 15px;
      height: 15px;
      stroke: #94a3b8;
      flex-shrink: 0;
    }

    /* Action buttons */
    .kp-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
    }

    .btn-kembali-edit {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color: #ffffff;
      border: 1px solid #d1d5db;
      color: #111827;
      padding: 0.85rem 1.35rem;
      border-radius: 10px;
      font-size: 0.925rem;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.2s ease;
    }

    .btn-kembali-edit:hover {
      background-color: #f8fafc;
    }

    .btn-kembali-edit svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
    }

    .btn-konfirmasi {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background-color: var(--primary-navy);
      border: none;
      color: #ffffff;
      padding: 0.9rem 1.5rem;
      border-radius: 10px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.2s ease;
      box-shadow: 0 6px 16px rgba(21, 44, 78, 0.18);
    }

    .btn-konfirmasi:hover {
      background-color: var(--primary-navy-hover);
    }

    .btn-konfirmasi svg {
      width: 17px;
      height: 17px;
      stroke: currentColor;
    }

    @media (max-width: 1024px) {
      .kp-container {
        grid-template-columns: 1fr;
        padding: 2rem 1.5rem 0 1.5rem;
      }
    }

    @media (max-width: 640px) {
      .kp-table-head {
        display: none;
      }
      .kp-table-row {
        grid-template-columns: 1fr;
        gap: 0.35rem;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 0.75rem;
      }
      .kp-sub-cell,
      .kp-table-head .col-sub {
        text-align: left;
      }
      .kp-actions {
        flex-direction: column;
      }
      .btn-kembali-edit,
      .btn-konfirmasi {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body class="kp-body">

  <!-- Top Navigation Header -->
  <header class="detail-navbar">
    <a id="backLink" href="checkout.php" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Konfirmasi Pesanan</h1>
    <div class="navbar-actions">
      <a href="../notifikasi.php" class="notification-bell-btn" aria-label="Notifikasi">
        <svg class="bell-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="notification-badge-red" id="kpBellBadge" style="display:none">0</span>
      </a>
      <div class="user-profile-capsule">
        <div class="user-avatar-circle">AD</div>
        <span class="user-name-text">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="kp-container">

    <!-- Left: Ringkasan pesanan -->
    <aside class="kp-summary-card">
      <div class="kp-summary-head">
        <span class="kp-summary-title">Ringkasan pesanan</span>
        <span class="kp-status-badge">Belum dikirim</span>
      </div>

      <div class="kp-summary-row">
        <span class="kp-summary-label">Total pesanan</span>
        <span id="summaryTotal" class="kp-summary-value">—</span>
      </div>
      <div class="kp-summary-row">
        <span class="kp-summary-label">Tanggal Pesanan</span>
        <span id="summaryTanggal" class="kp-summary-value">—</span>
      </div>

      <div class="kp-summary-notes-label">Catatan pesanan</div>
      <div id="summaryNotes" class="kp-summary-notes-box">—</div>
    </aside>

    <!-- Right: Tinjau Pesanan -->
    <section class="kp-review-card">
      <h2 class="kp-review-title">Tinjau Pesanan</h2>
      <p class="kp-review-subtitle">Periksa kembali rincian berikut sebelum mengonfirmasi pesanan.</p>

      <!-- Perusahaan -->
      <h3 class="kp-section-title">Perusahaan</h3>
      <div class="kp-company-box">
        <div id="companyAvatar" class="kp-company-avatar">–</div>
        <span id="companyName" class="kp-company-name">Memuat…</span>
      </div>

      <div class="kp-divider"></div>

      <!-- Item pesanan -->
      <h3 class="kp-section-title">Item pesanan</h3>
      <div class="kp-table-head">
        <div>Produk</div>
        <div class="col-qty">Qty</div>
        <div class="col-price">Harga</div>
        <div class="col-sub">Subtotal</div>
      </div>
      <div class="kp-table-row">
        <div class="kp-product-cell">
          <span class="kp-product-icon">
            <svg viewBox="0 0 24 24" fill="none">
              <ellipse cx="12" cy="13" rx="7" ry="9" fill="#f59e0b" stroke="#b45309" stroke-width="1.6"/>
            </svg>
          </span>
          <span id="productName">Memuat…</span>
        </div>
        <div id="productQty">—</div>
        <div id="productPrice">—</div>
        <div id="productSubtotal" class="kp-sub-cell">—</div>
      </div>

      <div class="kp-divider" style="margin-top: 1.25rem;"></div>

      <!-- Pengiriman & pembayaran -->
      <h3 class="kp-section-title">Pengiriman &amp; pembayaran</h3>
      <div class="kp-notes-box">
        <div class="kp-notes-label">Catatan pesanan (opsional)</div>
        <div id="notesDisplay" class="kp-notes-text">—</div>
        <input id="notesInput" type="text" class="kp-notes-input" value="" style="display:none;">
      </div>

      <div class="kp-total-box">
        <span class="kp-total-label">Total pesanan</span>
        <span id="totalValue" class="kp-total-value">—</span>
      </div>

      <div class="kp-info-line">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
          <path d="M12 8h.01M11 12h1v4h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Klik Konfirmasi Pesanan untuk membuat pesanan.
      </div>

      <!-- Action Buttons -->
      <div class="kp-actions">
        <a id="editLink" href="checkout.php" class="btn-kembali-edit">
          <svg viewBox="0 0 24 24" fill="none"><path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
          Kembali &amp; Edit
        </a>
        <button type="button" id="confirmBtn" class="btn-konfirmasi">
          Konfirmasi Pesanan
          <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
        </button>
      </div>
    </section>
  </main>

  <!-- Floating Navigation -->

  <script>
    // Data asli: produk dari api/produk/detail.php (?id=), pesanan dibuat lewat API.
    const urlParams = new URLSearchParams(window.location.search);
    const produkIdParam = urlParams.get('id');
    const qtyParam = parseInt((urlParams.get('qty') || '2').replace(/[^0-9]/g, ''), 10) || 2;
    const notesParam = urlParams.get('notes') || '';

    function formatRupiah(num) {
      return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    const confirmBtn = document.getElementById('confirmBtn');
    const editLink = document.getElementById('editLink');
    const backLink = document.getElementById('backLink');

    // Lonceng: jumlah notifikasi belum dibaca yang asli
    fetch('../../api/notifikasi.php')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok || !res.data) return;
        const unread = res.data.filter(function (n) { return !n.dibaca; }).length;
        const badge = document.getElementById('kpBellBadge');
        if (badge) {
          badge.textContent = unread;
          badge.style.display = unread > 0 ? '' : 'none';
        }
      })
      .catch(function () {});

    // Tautan kembali & edit -> halaman Ajukan Pesanan untuk produk yang sama
    const fallbackKp = produkIdParam
      ? 'checkout.php?id=' + encodeURIComponent(produkIdParam)
      : 'checkout.php';
    backLink.href = fallbackKp;
    editLink.href = fallbackKp;

    const kpBackBtn = backLink;
    if (kpBackBtn) {
      kpBackBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
          window.history.back();
        } else if (document.referrer && document.referrer !== window.location.href) {
          window.location.href = document.referrer;
        } else {
          window.location.href = fallbackKp;
        }
      });
    }

    // Catatan yang diedit pengguna ikut tampil di ringkasan kiri
    const notesInput = document.getElementById('notesInput');
    notesInput.addEventListener('input', function () {
      const val = this.value.trim() ? this.value : '—';
      document.getElementById('summaryNotes').innerText = val;
      document.getElementById('notesDisplay').innerText = val;
    });

    if (notesParam) {
      notesInput.value = notesParam;
      document.getElementById('notesDisplay').innerText = notesParam;
      document.getElementById('summaryNotes').innerText = notesParam;
    }

    // Tanggal pesanan = hari ini
    document.getElementById('summaryTanggal').innerText =
      new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

    if (!produkIdParam) {
      document.getElementById('productName').innerText =
        'Produk tidak ditemukan. Buka halaman ini dari Detail Produk atau Checkout.';
      document.getElementById('companyName').innerText = '—';
      if (confirmBtn) confirmBtn.disabled = true;
    } else {
      // Isi data pesanan dari produk asli
      fetch('../../api/produk/detail.php?id=' + encodeURIComponent(produkIdParam))
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok || !res.data || !res.data.produk) {
            document.getElementById('productName').innerText = 'Produk tidak ditemukan.';
            if (confirmBtn) confirmBtn.disabled = true;
            return;
          }
          const pr = res.data.produk;
          const harga = Number(pr.harga_per_kg) || 0;
          const subtotal = qtyParam * harga;
          const company = pr.nama_perusahaan || '—';
          document.getElementById('companyName').innerText = company;
          document.getElementById('companyAvatar').innerText = company.charAt(0);
          document.getElementById('productName').innerText = pr.nama;
          document.getElementById('productQty').innerText = String(qtyParam);
          document.getElementById('productPrice').innerText = formatRupiah(harga);
          document.getElementById('productSubtotal').innerText = formatRupiah(subtotal);
          document.getElementById('totalValue').innerText = formatRupiah(subtotal);
          document.getElementById('summaryTotal').innerText = formatRupiah(subtotal);
        })
        .catch(function () {
          document.getElementById('productName').innerText = 'Gagal memuat produk.';
          if (confirmBtn) confirmBtn.disabled = true;
        });
    }

    // Konfirmasi Pesanan -> buat pesanan asli lewat API, lalu ke detail pesanan
    confirmBtn.addEventListener('click', async function () {
      if (!produkIdParam) return;
      confirmBtn.disabled = true;
      try {
        const res = await fetch('../../api/order/create.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ produk_id: Number(produkIdParam), jumlah_kg: qtyParam })
        });
        const data = await res.json();
        if (!data.ok) {
          alert(data.error || 'Gagal membuat pesanan.');
          confirmBtn.disabled = false;
          return;
        }
        window.location.href = '../order_detail.php?id=' + data.data.order_id;
      } catch (e) {
        alert('Gagal menghubungi server.');
        confirmBtn.disabled = false;
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

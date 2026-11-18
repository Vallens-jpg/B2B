<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Ajukan Permintaan</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Ajukan Permintaan Specific Styles
       ========================================================================== */
    .ajukan-body {
      background-color: var(--bg-dashboard);
      min-height: 100vh;
      padding-bottom: 6rem;
    }

    .form-container-center {
      max-width: 780px;
      margin: 2.5rem auto 0 auto;
      padding: 0 1.5rem;
    }

    .ajukan-card {
      background-color: #ffffff;
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .ajukan-card-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1.75rem;
      letter-spacing: -0.01em;
    }

    .field-label-sm {
      display: block;
      font-size: 0.85rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 0.5rem;
    }

    .input-readonly {
      width: 100%;
      padding: 0.85rem 1rem;
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      font-size: 0.925rem;
      color: #334155;
      font-weight: 500;
    }

    .section-divider {
      height: 1px;
      background-color: #f1f5f9;
      margin: 2rem 0;
    }

    .item-pesanan-header {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1rem;
    }

    .table-headers-grid {
      display: grid;
      grid-template-columns: 2fr 110px 140px 110px 40px;
      gap: 1rem;
      align-items: center;
      margin-bottom: 0.5rem;
      font-size: 0.8rem;
      font-weight: 700;
      color: #64748b;
    }

    .item-row-grid {
      display: grid;
      grid-template-columns: 2fr 110px 140px 110px 40px;
      gap: 1rem;
      align-items: center;
      margin-bottom: 1.25rem;
    }

    .select-wrapper {
      position: relative;
    }

    .form-select {
      width: 100%;
      padding: 0.8rem 1rem;
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      font-size: 0.9rem;
      color: #1e293b;
      appearance: none;
      cursor: pointer;
      font-weight: 500;
    }

    .select-arrow {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      pointer-events: none;
      stroke: #64748b;
    }

    .form-input-qty, .form-input-price {
      width: 100%;
      padding: 0.8rem 1rem;
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      font-size: 0.9rem;
      color: #1e293b;
      font-weight: 500;
    }

    .subtotal-text {
      font-size: 0.95rem;
      font-weight: 800;
      color: #059669;
    }

    .btn-delete-row {
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
      transition: color 0.2s ease;
    }

    .btn-delete-row:hover {
      color: #dc2626;
    }

    .btn-delete-row svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
    }

    .btn-tambah-item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color: #ffffff;
      border: 1px solid #cbd5e1;
      color: #1e293b;
      padding: 0.65rem 1.15rem;
      border-radius: 10px;
      font-size: 0.875rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.2s ease;
      margin-top: 0.5rem;
    }

    .btn-tambah-item:hover {
      background-color: #f8fafc;
    }

    .btn-tambah-item svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      stroke-width: 2.5;
    }

    .textarea-notes {
      width: 100%;
      padding: 0.85rem 1rem;
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      font-size: 0.925rem;
      color: #1e293b;
      resize: vertical;
      min-height: 90px;
      font-family: var(--font-family);
    }

    .action-footer-buttons {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 780px;
      margin: 2rem auto 0 auto;
      padding: 0 1.5rem;
      gap: 1.5rem;
    }

    .btn-batal-outline {
      flex: 1;
      padding: 0.9rem;
      background-color: #ffffff;
      border: 1.5px solid #dc2626;
      color: #dc2626;
      border-radius: 10px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      transition: background-color 0.2s ease;
    }

    .btn-batal-outline:hover {
      background-color: #fef2f2;
    }

    .btn-kirim-primary {
      flex: 1;
      padding: 0.9rem;
      background-color: var(--primary-navy);
      border: none;
      color: #ffffff;
      border-radius: 10px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      transition: background-color 0.2s ease;
      box-shadow: 0 4px 12px rgba(21, 44, 78, 0.15);
    }

    .btn-kirim-primary:hover {
      background-color: var(--primary-navy-hover);
    }

    @media (max-width: 768px) {
      .table-headers-grid {
        display: none;
      }
      .item-row-grid {
        grid-template-columns: 1fr;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
      }
    }
  </style>
</head>
<body class="ajukan-body">

  <!-- Top Navbar -->
  <header class="detail-navbar">
    <a href="produk_detail.php" id="backLink" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Ajukan Permintaan</h1>
    <div class="navbar-actions">
      <a href="../notifikasi.php" class="notification-bell-btn" aria-label="Notifikasi">
        <svg class="bell-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="notification-badge-red" id="ajukanBellBadge" style="display:none">0</span>
      </a>
      <div class="user-profile-capsule">
        <div class="user-avatar-circle">AD</div>
        <span class="user-name-text">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Form Container -->
  <div class="form-container-center">
    <div class="ajukan-card">
      <h2 class="ajukan-card-title">Ajukan Permintaan</h2>

      <form action="#" method="POST" id="ajukanForm">
        <!-- Perusahaan -->
        <div class="form-group" style="margin-bottom: 1.75rem;">
          <label class="field-label-sm">Perusahaan :</label>
          <input type="text" id="companyInput" class="input-readonly" value="" readonly>
        </div>

        <div class="section-divider"></div>

        <!-- Item Pesanan -->
        <div class="item-pesanan-header">Item pesanan</div>

        <div class="table-headers-grid">
          <div>Produk *</div>
          <div>Qty/kg *</div>
          <div>Harga /kg *</div>
          <div>Subtotal</div>
          <div></div>
        </div>

        <div class="item-row-grid">
          <div class="select-wrapper">
            <select class="form-select" id="productSelect">
              <option value="Telur Ayam Grade A">Telur Ayam Grade A</option>
              <option value="Telur Ayam Grade B">Telur Ayam Grade B</option>
              <option value="Telur Ayam Grade C">Telur Ayam Grade C</option>
              <option value="Telur Ayam Kampung">Telur Ayam Kampung</option>
              <option value="Telur Omega">Telur Omega</option>
            </select>
            <svg class="select-arrow" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
          </div>
          <div>
            <input type="text" id="qtyInput" class="form-input-qty" value="2 kg">
          </div>
          <div>
            <input type="text" id="priceInput" class="form-input-price" value="">
          </div>
          <div>
            <span id="subtotalText" class="subtotal-text">Rp 0</span>
          </div>
          <div>
            <button type="button" class="btn-delete-row" aria-label="Hapus item">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="button" class="btn-tambah-item">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Tambah Item
        </button>

        <div class="section-divider"></div>

        <!-- Catatan Pesanan -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="field-label-sm">Catatan pesanan <span style="color: #94a3b8; font-weight: 400;">(opsional)</span></label>
          <textarea class="textarea-notes" placeholder="Tulis catatan pesanan di sini..."></textarea>
        </div>
      </form>
    </div>
  </div>

  <!-- Action Footer Buttons -->
  <div class="action-footer-buttons">
    <a href="produk_detail.php" id="cancelLink" class="btn-batal-outline">Batal</a>
    <button type="button" id="kirimBtn" class="btn-kirim-primary">Kirim Permintaan</button>
  </div>

  <!-- Floating Navigation -->

  <script>
    // Data asli: produk & perusahaan dari API, pesanan dibuat lewat API.
    const urlParams = new URLSearchParams(window.location.search);
    const produkIdParam = urlParams.get('id');

    let produkByName = {};
    let penjualNamaById = {};

    function parseNumber(str) {
      return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    function formatRupiah(num) {
      return 'Rp ' + num.toLocaleString('id-ID');
    }

    function isiPerusahaan(namaProduk) {
      const p = produkByName[namaProduk];
      const el = document.getElementById('companyInput');
      if (!el) return;
      if (p && p.perusahaan_id && penjualNamaById[p.perusahaan_id]) {
        el.value = penjualNamaById[p.perusahaan_id];
      } else if (p && p.perusahaan_id) {
        el.value = 'Perusahaan #' + p.perusahaan_id;
      } else {
        el.value = '';
      }
    }

    // Tombol kembali & batal -> halaman Detail Produk (atau Dashboard)
    const backToProduct = produkIdParam
      ? 'produk_detail.php?id=' + encodeURIComponent(produkIdParam)
      : 'dashboard_buyer.php';
    document.getElementById('backLink').href = backToProduct;
    document.getElementById('cancelLink').href = backToProduct;

    function handleSmartBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else if (document.referrer && document.referrer !== window.location.href) {
        window.location.href = document.referrer;
      } else {
        window.location.href = backToProduct;
      }
    }

    const backBtn = document.getElementById('backLink');
    const cancelBtn = document.getElementById('cancelLink');
    if (backBtn) {
      backBtn.addEventListener('click', function(e) {
        e.preventDefault();
        handleSmartBack();
      });
    }
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        handleSmartBack();
      });
    }

    // Lonceng: jumlah notifikasi belum dibaca yang asli
    fetch('../../api/notifikasi.php')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok || !res.data) return;
        const unread = res.data.filter(function (n) { return !n.dibaca; }).length;
        const badge = document.getElementById('ajukanBellBadge');
        if (badge) {
          badge.textContent = unread;
          badge.style.display = unread > 0 ? '' : 'none';
        }
      })
      .catch(function () {});

    // Format rupiah otomatis & hitung subtotal
    const qtyInput = document.getElementById('qtyInput');
    const priceInput = document.getElementById('priceInput');
    const subtotalText = document.getElementById('subtotalText');

    function recalcSubtotal() {
      const qty = parseNumber(qtyInput.value);
      const price = parseNumber(priceInput.value);
      subtotalText.innerText = formatRupiah(qty * price);
    }

    priceInput.addEventListener('input', function () {
      const num = parseNumber(this.value);
      this.value = num ? formatRupiah(num) : '';
      recalcSubtotal();
    });

    qtyInput.addEventListener('input', function () {
      const num = parseNumber(this.value);
      this.value = num ? num + ' kg' : '';
      recalcSubtotal();
    });

    // Isi daftar produk dari database (API katalog), supaya harga & id produk asli.
    const productSelect = document.getElementById('productSelect');
    fetch('../../api/penjual.php')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.ok && res.data && res.data.penjual) {
          res.data.penjual.forEach(function (pj) { penjualNamaById[pj.id] = pj.nama; });
        }
      })
      .catch(function () {});
    fetch('../../api/katalog.php')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok || !res.data || !res.data.produk) return;
        productSelect.innerHTML = '';
        res.data.produk.forEach(function (pr) {
          produkByName[pr.nama] = pr;
          const opt = document.createElement('option');
          opt.value = pr.nama;
          opt.text = pr.nama + ' — Rp ' + Number(pr.harga_per_kg).toLocaleString('id-ID') + '/kg';
          opt.dataset.harga = pr.harga_per_kg;
          productSelect.appendChild(opt);
        });
        let target = null;
        if (produkIdParam) {
          target = res.data.produk.find(function (pr) { return String(pr.id) === String(produkIdParam); });
        }
        if (!target && res.data.produk.length) target = res.data.produk[0];
        if (target) {
          productSelect.value = target.nama;
          priceInput.value = 'Rp ' + Number(target.harga_per_kg).toLocaleString('id-ID');
          isiPerusahaan(target.nama);
          recalcSubtotal();
        }
      })
      .catch(function () {});

    productSelect.addEventListener('change', function () {
      const pr = produkByName[this.value];
      if (pr) {
        priceInput.value = 'Rp ' + Number(pr.harga_per_kg).toLocaleString('id-ID');
        isiPerusahaan(pr.nama);
        recalcSubtotal();
      }
    });

    // Tambah Item baru
    let itemCounter = 1;
    document.querySelector('.btn-tambah-item').addEventListener('click', function () {
      itemCounter++;
      const row = document.createElement('div');
      row.className = 'item-row-grid';
      row.innerHTML = `
        <div class="select-wrapper">
          <select class="form-select">
            <option>Telur Ayam Grade A</option>
            <option>Telur Ayam Grade B</option>
            <option>Telur Ayam Grade C</option>
            <option>Telur Ayam Kampung</option>
            <option>Telur Omega</option>
          </select>
          <svg class="select-arrow" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <div><input type="text" class="form-input-qty" value="1 kg"></div>
        <div><input type="text" class="form-input-price" value="Rp 0"></div>
        <div><span class="subtotal-text">Rp 0</span></div>
        <div>
          <button type="button" class="btn-delete-row" aria-label="Hapus item">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
          </button>
        </div>`;
      this.parentNode.insertBefore(row, this);
      bindRowEvents(row);
    });

    // Ikon hapus pada baris pertama
    bindRowEvents(document.querySelector('.item-row-grid'));

    function bindRowEvents(row) {
      if (!row) return;
      const qty = row.querySelector('.form-input-qty');
      const price = row.querySelector('.form-input-price');
      const sub = row.querySelector('.subtotal-text');

      price.addEventListener('input', function () {
        const num = parseNumber(this.value);
        this.value = num ? formatRupiah(num) : '';
        sub.innerText = formatRupiah(parseNumber(qty.value) * num);
      });

      qty.addEventListener('input', function () {
        const num = parseNumber(this.value);
        this.value = num ? num + ' kg' : '';
        sub.innerText = formatRupiah(num * parseNumber(price.value));
      });

      row.querySelector('.btn-delete-row').addEventListener('click', function () {
        row.remove();
      });
    }

    // Kirim Permintaan -> buat pesanan asli lewat API, lalu ke detail pesanan
    document.getElementById('kirimBtn').addEventListener('click', async function () {
      const rows = document.querySelectorAll('.item-row-grid');
      const firstRow = rows[0];
      const product = firstRow.querySelector('.form-select').value;
      const qty = parseNumber(firstRow.querySelector('.form-input-qty').value);
      const p = produkByName[product];
      if (!p) {
        alert('Produk tidak ditemukan di katalog.');
        return;
      }
      if (!qty || qty <= 0) {
        alert('Jumlah harus lebih dari 0.');
        return;
      }
      try {
        const res = await fetch('../../api/order/create.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ produk_id: p.id, jumlah_kg: qty })
        });
        const data = await res.json();
        if (!data.ok) {
          alert(data.error || 'Gagal membuat pesanan.');
          return;
        }
        window.location.href = '../order_detail.php?id=' + data.data.order_id;
      } catch (e) {
        alert('Gagal menghubungi server.');
      }
    });
  </script>
</body>
</html>

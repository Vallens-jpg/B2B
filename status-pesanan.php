<?php
session_start();

$sellersMap = [
    '1' => 'PT Telur Makmur',
    '2' => 'Mitra Unggas Sejahtera',
    '3' => 'Tunas Farm',
    '4' => 'Berkah Telur Utama',
    '5' => 'Ternak Mandiri',
    '6' => 'Sinar Jaya Poultry'
];

// Handle new order submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sellerId = $_POST['seller'] ?? '1';
    $company = $_POST['seller_name'] ?? ($sellersMap[$sellerId] ?? 'PT Telur Makmur');
    $product = $_POST['product'] ?? 'Telur Ayam Ras Grade A';
    $price = $_POST['price'] ?? 'Rp 28.000';
    $qty = $_POST['qty'] ?? '2 kg';
    $subtotal = $_POST['subtotal'] ?? 'Rp 56.000';
    $notes = $_POST['notes'] ?? '';

    $_SESSION['active_order'] = [
        'order_id' => '#ORD-2026-' . rand(100, 999),
        'seller_id' => $sellerId,
        'seller' => $company,
        'product' => $product,
        'price' => $price,
        'qty' => $qty,
        'subtotal' => $subtotal,
        'notes' => $notes,
        'status' => 'pending',
        'created_at' => date('d M Y, H:i')
    ];
}

// Fallback init jika dibuka langsung dengan GET atau session
if (!isset($_SESSION['active_order'])) {
    $sellerId = $_GET['seller'] ?? '1';
    $_SESSION['active_order'] = [
        'order_id' => '#ORD-2026-' . rand(100, 999),
        'seller_id' => $sellerId,
        'seller' => $sellersMap[$sellerId] ?? 'PT Telur Makmur',
        'product' => $_GET['product'] ?? 'Telur Ayam Ras Grade A',
        'price' => $_GET['price'] ?? 'Rp 28.000',
        'qty' => $_GET['qty'] ?? '2 kg',
        'subtotal' => $_GET['subtotal'] ?? 'Rp 56.000',
        'notes' => $_GET['notes'] ?? '',
        'status' => 'pending',
        'created_at' => date('d M Y, H:i')
    ];
}

// Handle simulation ACC penjual via PHP GET action
if (isset($_GET['action']) && $_GET['action'] === 'approve') {
    $_SESSION['active_order']['status'] = 'approved';
    $_SESSION['pesanan_disetujui'] = $_SESSION['active_order'];
    header('Location: status-pesanan.php?state=approved');
    exit;
}

$state = $_GET['state'] ?? ($_SESSION['active_order']['status'] ?? 'pending');
if ($state === 'approved') {
    $_SESSION['active_order']['status'] = 'approved';
    $_SESSION['pesanan_disetujui'] = $_SESSION['active_order'];
}

$order = $_SESSION['active_order'];
$isApproved = ($order['status'] === 'approved' || $state === 'approved');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Status Pesanan</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .btn-acc-simulasi {
      margin-top: 0.75rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 0.95rem;
      font-size: 0.8rem;
      font-weight: 700;
      color: #065f46;
      background: #d1fae5;
      border: 1.5px solid #6ee7b7;
      border-radius: 6px;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.15s ease;
    }
    .btn-acc-simulasi:hover {
      background: #a7f3d0;
      color: #064e3b;
      transform: translateY(-1px);
    }
  </style>
</head>
<body class="sp-body">

  <!-- Top Navigation Header -->
  <header class="detail-navbar">
    <a id="backLink" href="dashboard.php" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Status Pesanan</h1>
    <div class="navbar-actions">
      <a href="notifikasi.php" class="notification-bell-btn" aria-label="Notifikasi" style="text-decoration:none">
        <svg class="bell-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="notification-badge-red">3</span>
      </a>
      <div class="user-profile-capsule">
        <div class="user-avatar-circle">AD</div>
        <span class="user-name-text">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="sp-container">
    <!-- Alert Banner -->
    <div class="sp-alert-banner" id="alertBanner">
      <div class="sp-alert-title" id="alertTitle">Menunggu Persetujuan Penjual</div>
      <p class="sp-alert-desc" id="alertDesc">
        Pesanan Anda telah dikirim ke <strong id="bannerCompany">PT Telur Makmur</strong>.
        Penjual sedang meninjau pesanan dan ketersediaan stok Anda. Tombol pembayaran akan aktif setelah pesanan disetujui penjual.
      </p>
      <button type="button" id="btnSimulateApprove" class="btn-acc-simulasi" onclick="approveOrder()">
        Simulasikan ACC Penjual (Setujui Sekarang)
      </button>
    </div>

    <div class="sp-inner">
      <!-- Detail Pesanan Card -->
      <div class="sp-detail-card">
        <div class="sp-card-head">
          <span class="sp-card-head-title">Detail Pesanan</span>
          <span class="sp-ringkasan-badge">Ringkasan</span>
        </div>

        <div class="sp-row">
          <span class="sp-row-label">Nilai Pesanan</span>
          <span id="orderValue" class="sp-row-value strong">Rp 500.000</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Metode Pembayaran</span>
          <span class="sp-row-value">Menunggu konfirmasi</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Alamat Pengiriman</span>
          <span class="sp-row-value">Belum dikonfirmasi</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Nomor Pesanan</span>
          <span id="orderNumber" class="sp-row-value">#ORD-2026-103</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Perusahaan</span>
          <span id="orderCompany" class="sp-row-value">PT Jaya Makmur</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Item Pesanan</span>
          <span id="orderItem" class="sp-row-value">Telur Ayam (2 Peti) – Rp 500.000</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Jadwal Kirim</span>
          <span class="sp-row-value">12 Okt 2027</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="sp-actions">
        <a href="pesanan.php" id="cancelReqLink" class="btn-batalkan">Batalkan Pesanan</a>
        <div class="sp-review-group">
          <a href="#" id="paymentBtn" class="btn-tinjau-disabled">Lanjut ke Pembayaran</a>
          <span id="reviewCaption" class="sp-review-caption">Menunggu persetujuan penjual...</span>
        </div>
      </div>
    </div>
  </main>

  <!-- Floating Navigation -->

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const sellerId = urlParams.get('seller') || '1';
    const productName = urlParams.get('product') || 'Telur Ayam Ras Grade A';
    const productPrice = urlParams.get('price') || 'Rp 28.000';
    const qty = urlParams.get('qty') || '2 kg';
    const subtotal = urlParams.get('subtotal') || 'Rp 56.000';
    const notes = urlParams.get('notes') || '';

    const sellersMap = {
      '1': 'PT Telur Makmur',
      '2': 'Mitra Unggas Sejahtera',
      '3': 'Tunas Farm',
      '4': 'Berkah Telur Utama',
      '5': 'Ternak Mandiri',
      '6': 'Sinar Jaya Poultry'
    };

    const hasParams = urlParams.get('seller') || urlParams.get('product');
    const company = sellersMap[sellerId] || 'PT Telur Makmur';

    document.getElementById('bannerCompany').innerText = company;
    document.getElementById('orderCompany').innerText = company;
    document.getElementById('orderValue').innerText = subtotal;
    document.getElementById('orderItem').innerText = `${productName} (${qty}) – ${subtotal}`;
    
    // Simpan atau buat order ID konsisten
    const orderId = sessionStorage.getItem('currentOrderId') || ('#ORD-2026-' + (Math.floor(Math.random() * 899) + 100));
    sessionStorage.setItem('currentOrderId', orderId);
    document.getElementById('orderNumber').innerText = orderId;

    // Tanggal kirim besok
    const tglKirim = new Date();
    tglKirim.setDate(tglKirim.getDate() + 1);
    const optionsTgl = { day: 'numeric', month: 'short', year: 'numeric' };
    const tglKirimStr = tglKirim.toLocaleDateString('id-ID', optionsTgl);
    const tglEl = document.querySelectorAll('.sp-row-value');
    if (tglEl.length >= 7) {
      tglEl[6].innerText = tglKirimStr;
    }

    // Tombol kembali ke halaman Ajukan Pesanan
    const backToAjukan = 'ajukan-pesanan.php' +
      '?seller=' + encodeURIComponent(sellerId) +
      '&product=' + encodeURIComponent(productName) +
      '&price=' + encodeURIComponent(productPrice) +
      '&qty=' + encodeURIComponent(qty) +
      '&subtotal=' + encodeURIComponent(subtotal) +
      '&notes=' + encodeURIComponent(notes);

    const spBackBtn = document.getElementById('backLink');
    if (spBackBtn) {
      spBackBtn.href = backToAjukan;
      spBackBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
          window.history.back();
        } else if (document.referrer && document.referrer !== window.location.href) {
          window.location.href = document.referrer;
        } else {
          window.location.href = backToAjukan;
        }
      });
    }

    // Query untuk ke halaman Pembayaran setelah disetujui
    const paymentQuery =
      '?seller=' + encodeURIComponent(sellerId) +
      '&product=' + encodeURIComponent(productName) +
      '&qty=' + encodeURIComponent(qty) +
      '&price=' + encodeURIComponent(productPrice) +
      '&subtotal=' + encodeURIComponent(subtotal) +
      '&notes=' + encodeURIComponent(notes);

    // Fungsi saat penjual menyetujui pesanan
    function approveOrder() {
      const banner = document.getElementById('alertBanner');
      const title = document.getElementById('alertTitle');
      const desc = document.getElementById('alertDesc');
      const simBtn = document.getElementById('btnSimulateApprove');
      const paymentBtn = document.getElementById('paymentBtn');
      const caption = document.getElementById('reviewCaption');

      banner.classList.add('sp-banner-approved');
      // Simpan notifikasi ke localStorage agar muncul di halaman notifikasi
      try {
        const q = '&seller=' + encodeURIComponent(sellerParam)
          + '&product=' + encodeURIComponent(productParam)
          + '&qty=' + encodeURIComponent(qtyParam)
          + '&price=' + encodeURIComponent(priceParam)
          + '&subtotal=' + encodeURIComponent(subtotalParam);
        localStorage.setItem('pesananDisetujui', JSON.stringify({ seller: company, query: q }));
      } catch(e) {}
      title.innerHTML = 'Pesanan Disetujui Penjual';
      desc.innerHTML = `Pesanan Anda telah disetujui oleh <strong>${company}</strong>. Stok tersedia dan siap dikirim. Silakan selesaikan pembayaran untuk memproses pesanan.`;

      if (simBtn) simBtn.style.display = 'none';

      paymentBtn.classList.remove('btn-tinjau-disabled');
      paymentBtn.classList.add('btn-tinjau-active');
      paymentBtn.style.pointerEvents = 'auto';
      paymentBtn.style.cursor = 'pointer';
      paymentBtn.innerText = 'Lanjut ke Pembayaran';
      paymentBtn.href = 'pembayaran.php' + paymentQuery;

      caption.innerHTML = 'Pesanan disetujui, silakan lakukan pembayaran';
      caption.style.color = '#059669';
      caption.style.fontWeight = '600';
    }

    // Jika parameter state=approved, langsung tampilkan disetujui
    const state = urlParams.get('state');
    if (state === 'approved') {
      approveOrder();
    } else {
      // Simulasikan persetujuan otomatis setelah 4 detik untuk kenyamanan pengujian
      const autoTimer = setTimeout(() => {
        approveOrder();
      }, 4000);

      // Jika tombol simulasi diklik duluan, hentikan timer
      const simBtn = document.getElementById('btnSimulateApprove');
      if (simBtn) {
        simBtn.addEventListener('click', () => {
          clearTimeout(autoTimer);
        });
      }
    }
  </script>
</body>
</html>

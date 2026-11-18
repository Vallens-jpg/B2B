<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Status Pesanan</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body class="sp-body">

  <!-- Top Navigation Header -->
  <header class="detail-navbar">
    <a id="backLink" href="dashboard_buyer.php" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Status Pesanan</h1>
    <div class="navbar-actions">
      <a href="../notifikasi.php" class="notification-bell-btn" aria-label="Notifikasi" style="text-decoration:none">
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
      <div class="sp-alert-title" id="alertTitle">Memuat status pesanan…</div>
      <p class="sp-alert-desc" id="alertDesc">
      Mengambil data pesanan Anda…
      </p>
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
          <span id="orderValue" class="sp-row-value strong">Memuat…</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Nomor Pesanan</span>
          <span id="orderNumber" class="sp-row-value">—</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Perusahaan</span>
          <span id="orderCompany" class="sp-row-value">—</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Item Pesanan</span>
          <span id="orderItem" class="sp-row-value">Memuat…</span>
        </div>
        <div class="sp-row">
          <span class="sp-row-label">Tanggal Pesanan</span>
          <span class="sp-row-value" id="orderTanggal">—</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="sp-actions">
        <a href="order_list.php" id="cancelReqLink" class="btn-batalkan">Batalkan Pesanan</a>
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


    // Tombol kembali ke halaman Ajukan Pesanan
    const backToAjukan = 'checkout.php' +
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

    // ---- Data asli dari API: daftar pesanan pembeli yang login ----
    // Kartu utama diisi dari API; bila belum ada pesanan, tampilkan keadaan kosong.
    const labelStatusPesanan = {
      menunggu_bayar: 'Menunggu Pembayaran',
      dibayar: 'Dibayar',
      diproses: 'Diproses',
      dikirim: 'Dikirim',
      diterima: 'Diterima',
      selesai: 'Selesai',
      dibatalkan: 'Dibatalkan'
    };
    let spModeSelesai = false; // true setelah data asli tampil / fallback diputuskan

    function rupiahSp(n) {
      return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function tampilkanPesananAsli(orders) {
      if (!orders || !orders.length) return false;
      spModeSelesai = true;
      const pertama = orders[0];
      // Kartu utama = pesanan terbaru pembeli.
      document.getElementById('orderNumber').innerText = '#' + pertama.id;
      document.getElementById('orderValue').innerText = rupiahSp(pertama.total_bayar);
      document.getElementById('orderItem').innerText = pertama.nama_produk + ' (' + pertama.jumlah_kg + ' kg) – ' + rupiahSp(pertama.total_bayar);
      document.getElementById('orderCompany').innerText = pertama.nama_perusahaan || '-';
      const tgl = pertama.created_at ? new Date(pertama.created_at.replace(' ', 'T')) : null;
      const tglEl = document.getElementById('orderTanggal');
      if (tgl && !isNaN(tgl) && tglEl) {
        tglEl.innerText = tgl.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
      }
      // Banner + tombol sesuai status asli (tanpa simulasi ACC).
      const banner = document.getElementById('alertBanner');
      const title = document.getElementById('alertTitle');
      const desc = document.getElementById('alertDesc');
      const paymentBtn = document.getElementById('paymentBtn');
      const caption = document.getElementById('reviewCaption');
      const statusLabel = labelStatusPesanan[pertama.status] || pertama.status;
      const judulBanner = {
        menunggu_bayar: 'Menunggu Pembayaran',
        dibayar: 'Pembayaran Diterima',
        diproses: 'Pesanan Diproses',
        dikirim: 'Pesanan Dikirim',
        diterima: 'Pesanan Diterima',
        selesai: 'Pesanan Selesai',
        dibatalkan: 'Pesanan Dibatalkan'
      };
      title.innerText = judulBanner[pertama.status] || ('Status: ' + statusLabel);
      desc.innerHTML = 'Pesanan #' + pertama.id + ' — ' + pertama.nama_produk + ' (' + pertama.jumlah_kg + ' kg). Status saat ini: <strong>' + statusLabel + '</strong>.';
      if (pertama.status === 'menunggu_bayar') {
        banner.classList.remove('sp-banner-approved');
        paymentBtn.classList.remove('btn-tinjau-disabled');
        paymentBtn.classList.add('btn-tinjau-active');
        paymentBtn.style.pointerEvents = 'auto';
        paymentBtn.style.cursor = 'pointer';
        paymentBtn.innerText = 'Lanjut ke Pembayaran';
        paymentBtn.href = '../order_detail.php?id=' + pertama.id;
        const cancelLink = document.getElementById('cancelReqLink');
        if (cancelLink) cancelLink.href = '../order_detail.php?id=' + pertama.id;
        caption.innerText = 'Pesanan dibuat, silakan selesaikan pembayaran';
        caption.style.color = '#d97706';
        caption.style.fontWeight = '600';
      } else if (pertama.status === 'dibatalkan') {
        caption.innerText = 'Pesanan ini dibatalkan';
      } else {
        banner.classList.add('sp-banner-approved');
        caption.innerText = 'Status pesanan: ' + statusLabel;
        caption.style.color = '#059669';
        caption.style.fontWeight = '600';
      }
      // Pesanan lain pembeli, memakai kartu .sp-detail-card yang sama.
      const inner = document.querySelector('.sp-inner');
      const actions = document.querySelector('.sp-actions');
      orders.slice(1).forEach(function (o) {
        const card = document.createElement('div');
        card.className = 'sp-detail-card';
        card.style.marginTop = '1rem';
        const label = labelStatusPesanan[o.status] || o.status;
        const tglO = o.created_at ? new Date(o.created_at.replace(' ', 'T')).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
        card.innerHTML =
          '<div class="sp-card-head"><span class="sp-card-head-title">Pesanan #' + o.id + '</span><span class="sp-ringkasan-badge">' + label + '</span></div>' +
          '<div class="sp-row"><span class="sp-row-label">Item Pesanan</span><span class="sp-row-value">' + o.nama_produk + ' (' + o.jumlah_kg + ' kg)</span></div>' +
          '<div class="sp-row"><span class="sp-row-label">Nilai Pesanan</span><span class="sp-row-value strong">' + rupiahSp(o.total_bayar) + '</span></div>' +
          '<div class="sp-row"><span class="sp-row-label">Tanggal</span><span class="sp-row-value">' + tglO + '</span></div>' +
          '<div class="sp-row"><span class="sp-row-label">Detail</span><span class="sp-row-value"><a href="../order_detail.php?id=' + o.id + '">Lihat Detail ›</a></span></div>';
        if (inner && actions) inner.insertBefore(card, actions);
      });
      return true;
    }

    // Ambil pesanan asli; bila belum ada pesanan / API gagal, tampilkan
    // keadaan kosong yang jujur — tanpa simulasi persetujuan / data contoh.
    fetch('../../api/order/list.php')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.ok && res.data && res.data.orders && res.data.orders.length) {
          // Bila dibuka dengan ?id=N (misalnya setelah bayar dari detail
          // pesanan), kartu utama = pesanan itu, bukan sekadar yang terbaru.
          var idParam = urlParams.get('id');
          if (idParam) {
            var dipilih = res.data.orders.filter(function (o) { return String(o.id) === String(idParam); });
            if (dipilih.length) {
              var lainnya = res.data.orders.filter(function (o) { return String(o.id) !== String(idParam); });
              tampilkanPesananAsli(dipilih.concat(lainnya));
            } else {
              tampilkanKosong('Pesanan #' + idParam + ' tidak ditemukan.');
            }
          } else {
            tampilkanPesananAsli(res.data.orders);
          }
        } else {
          tampilkanKosong();
        }
      })
      .catch(function () { tampilkanKosong('Gagal memuat pesanan. Periksa koneksi atau login kembali.'); });

    function tampilkanKosong(pesan) {
      spModeSelesai = true;
      const title = document.getElementById('alertTitle');
      const desc = document.getElementById('alertDesc');
      const caption = document.getElementById('reviewCaption');
      const cancelLink = document.getElementById('cancelReqLink');
      if (title) title.innerText = 'Belum ada pesanan';
      if (desc) desc.innerText = pesan || 'Kamu belum punya pesanan. Buat pesanan dari katalog, lalu statusnya akan tampil di sini.';
      document.getElementById('orderNumber').innerText = '—';
      document.getElementById('orderValue').innerText = '—';
      document.getElementById('orderCompany').innerText = '—';
      document.getElementById('orderItem').innerText = 'Belum ada pesanan';
      const tglEl = document.getElementById('orderTanggal');
      if (tglEl) tglEl.innerText = '—';
      if (caption) caption.innerText = 'Belum ada pesanan';
      if (cancelLink) cancelLink.style.display = 'none';
    }
  </script>
</body>
</html>

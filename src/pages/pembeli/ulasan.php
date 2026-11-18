<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Ulasan Produk</title>
  <link rel="stylesheet" href="../../assets/css/styles.css">
  <style>
    /* ==========================================================================
       Ulasan Produk Page Styles
       ========================================================================== */
    body.ulasan-page-body {
      background-color: #FAF6ED;
      min-height: 100vh;
      font-family: var(--font-family, 'Plus Jakarta Sans', sans-serif);
      color: #111827;
      margin: 0;
      padding: 0;
      padding-bottom: 6rem;
    }

    /* Top Navigation Header */
    .ulasan-navbar {
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

    .ulasan-nav-back-btn {
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

    .ulasan-nav-back-btn:hover {
      background-color: #f8fafc;
      border-color: #9ca3af;
    }

    .ulasan-nav-back-btn svg {
      width: 20px;
      height: 20px;
      stroke: #111827;
      stroke-width: 2.2;
    }

    .ulasan-nav-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin: 0;
      text-align: center;
      justify-self: center;
    }

    .ulasan-nav-actions {
      display: flex;
      align-items: center;
      gap: 1.25rem;
      justify-self: end;
    }

    .ulasan-bell-btn {
      position: relative;
      background: none;
      border: none;
      cursor: pointer;
      padding: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .ulasan-bell-btn svg {
      width: 24px;
      height: 24px;
      fill: #111827;
    }

    .ulasan-bell-badge {
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

    .ulasan-user-pill {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      border: 1.5px solid #e2e8f0;
      border-radius: 9999px;
      padding: 4px 14px 4px 5px;
      background-color: #ffffff;
    }

    .ulasan-user-avatar {
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

    .ulasan-user-name {
      font-size: 0.875rem;
      font-weight: 600;
      color: #111827;
    }

    /* Container */
    .ulasan-container {
      max-width: 860px;
      margin: 0 auto;
      padding: 2.25rem 1.5rem 0 1.5rem;
    }

    /* Main Card */
    .ulasan-card {
      background-color: #ffffff;
      border: 1px solid #E5E7EB;
      border-radius: 16px;
      padding: 2rem 2.25rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    /* Section Sub-cards with border */
    .ulasan-subcard {
      border: 1px solid #E5E7EB;
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      margin-bottom: 1.25rem;
      background-color: #ffffff;
    }

    .ulasan-subcard-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      margin-bottom: 0.95rem;
    }

    /* Ringkasan Pesanan Inner Box */
    .ulasan-product-box {
      border: 1px solid #E2E8F0;
      background-color: #F8FAFC;
      border-radius: 10px;
      padding: 0.95rem 1.25rem;
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .ulasan-product-img {
      width: 74px;
      height: 74px;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;
    }

    .ulasan-product-info {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .ulasan-product-name {
      font-size: 1.05rem;
      font-weight: 800;
      color: #111827;
      margin: 0;
    }

    .ulasan-product-meta {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      font-size: 0.875rem;
    }

    .ulasan-product-meta span {
      color: #6B7280;
      font-weight: 500;
    }

    .ulasan-product-meta strong {
      font-weight: 800;
      color: #111827;
    }

    /* Rating & Detail Form */
    .ulasan-label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: #374151;
      margin-bottom: 0.55rem;
    }

    .ulasan-rating-boxes {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      margin-bottom: 1.25rem;
    }

    .rating-star-box {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      background-color: #EEF2F6;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.15s ease, transform 0.1s ease;
    }

    .rating-star-box:hover {
      background-color: #E2E8F0;
      transform: scale(1.05);
    }

    .rating-star-box.selected {
      background-color: #FEF3C7;
    }

    .rating-star-box svg {
      width: 19px;
      height: 19px;
      stroke: #64748B;
      stroke-width: 2;
      fill: none;
      transition: all 0.15s ease;
    }

    .rating-star-box.selected svg {
      stroke: #D97706;
      fill: #FBBF24;
    }

    .ulasan-textarea {
      width: 100%;
      min-height: 110px;
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

    .ulasan-textarea:focus {
      border-color: #94A3B8;
      box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
    }

    /* Action Buttons */
    .ulasan-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-top: 1.75rem;
    }

    .btn-ulasan-batal {
      padding: 0.95rem 2.75rem;
      background-color: #ffffff;
      border: 1.5px solid #EF4444;
      border-radius: 10px;
      font-size: 0.875rem;
      font-weight: 800;
      color: #EF4444;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      cursor: pointer;
      font-family: inherit;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.15s ease, border-color 0.15s ease;
      white-space: nowrap;
    }

    .btn-ulasan-batal:hover {
      background-color: #FEF2F2;
    }

    .btn-ulasan-kirim {
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

    .btn-ulasan-kirim:hover {
      background-color: #0F203A;
      box-shadow: 0 6px 18px rgba(21, 44, 78, 0.25);
    }

    /* Responsive */
    @media (max-width: 640px) {
      .ulasan-navbar {
        padding: 0.85rem 1.25rem;
      }
      .ulasan-container {
        padding: 1.25rem 1rem 0 1rem;
      }
      .ulasan-card {
        padding: 1.5rem 1.25rem;
      }
      .ulasan-nav-title {
        font-size: 1.05rem;
      }
      .ulasan-user-name {
        display: none;
      }
      .ulasan-actions {
        flex-direction: column-reverse;
      }
      .btn-ulasan-batal,
      .btn-ulasan-kirim {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</head>
<body class="ulasan-page-body">

  <!-- Top Navigation Header -->
  <header class="ulasan-navbar">
    <a id="backLink" href="order_list.php" class="ulasan-nav-back-btn" aria-label="Kembali">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="ulasan-nav-title">ULASAN PRODUK</h1>
    <div class="ulasan-nav-actions">
      <a href="../notifikasi.php" class="ulasan-bell-btn" aria-label="Notifikasi">
        <svg viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 5 14h14a1 1 0 0 0 .707-1.707L18 11.586V8a6 6 0 0 0-6-6zM10 18a2 2 0 0 0 4 0h-4z"/>
        </svg>
        <span class="ulasan-bell-badge" id="ulasanBellBadge" style="display:none">0</span>
      </a>
      <div class="ulasan-user-pill">
        <div class="ulasan-user-avatar">AD</div>
        <span class="ulasan-user-name">Buyer</span>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <main class="ulasan-container">
    <div class="ulasan-card">

      <!-- 1. RINGKASAN PESANAN -->
      <div class="ulasan-subcard">
        <h2 class="ulasan-subcard-title">RINGKASAN PESANAN</h2>
        <div class="ulasan-product-box">
          <img src="../../assets/img/prod_grade_a.png" alt="Produk" class="ulasan-product-img" id="summaryImg">
          <div class="ulasan-product-info">
            <h3 class="ulasan-product-name" id="summaryTitle">Memuat…</h3>
            <div class="ulasan-product-meta">
              <span>Jumlah:</span>
              <strong id="summaryQty">—</strong>
            </div>
            <div class="ulasan-product-meta">
              <span>Total:</span>
              <strong id="summaryAmount">—</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. ULASAN PRODUK -->
      <div class="ulasan-subcard">
        <h2 class="ulasan-subcard-title">ULASAN PRODUK</h2>
        
        <label class="ulasan-label">Beri Rating</label>
        <div class="ulasan-rating-boxes" id="ratingContainer">
          <div class="rating-star-box" data-rating="1" onclick="selectRating(1)">
            <svg viewBox="0 0 24 24">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="rating-star-box" data-rating="2" onclick="selectRating(2)">
            <svg viewBox="0 0 24 24">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="rating-star-box" data-rating="3" onclick="selectRating(3)">
            <svg viewBox="0 0 24 24">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="rating-star-box" data-rating="4" onclick="selectRating(4)">
            <svg viewBox="0 0 24 24">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="rating-star-box" data-rating="5" onclick="selectRating(5)">
            <svg viewBox="0 0 24 24">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
        </div>

        <label for="reviewDetail" class="ulasan-label">Deskripsi Detail</label>
        <textarea
          id="reviewDetail"
          class="ulasan-textarea"
          rows="4"
        ></textarea>
      </div>

      <!-- 4. ACTION BUTTONS -->
      <div class="ulasan-actions">
        <a id="cancelBtn" href="order_list.php" class="btn-ulasan-batal">BATAL</a>
        <button type="button" class="btn-ulasan-kirim" onclick="kirimUlasan()">
          KIRIM ULASAN
        </button>
      </div>

    </div>
  </main>

  <!-- Floating Navigation for Prototype Walkthrough -->

  <script>
    // Ringkasan asli dari API (?order_id=)
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = parseInt(urlParams.get('order_id') || urlParams.get('id') || '0', 10);
    function rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
    if (!orderId) {
      document.getElementById('summaryTitle').innerText = 'Pesanan tidak ditemukan. Buka halaman ini dari Detail Pesanan.';
    } else {
      fetch('../../api/order/detail.php?id=' + orderId)
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok || !res.data || !res.data.order) {
            document.getElementById('summaryTitle').innerText = (res && res.message) || 'Pesanan tidak ditemukan.';
            return;
          }
          var o = res.data.order;
          document.getElementById('summaryTitle').innerText = o.nama_produk;
          document.getElementById('summaryImg').alt = o.nama_produk;
          document.getElementById('summaryAmount').innerText = rupiah(o.total_bayar);
          var q = document.getElementById('summaryQty');
          if (q) q.innerText = o.jumlah_kg + ' kg';
        })
        .catch(function () {
          document.getElementById('summaryTitle').innerText = 'Gagal memuat pesanan.';
        });
    }

    // Preserve back destination
    const cancelBtn = document.getElementById('cancelBtn');
    const backLink = document.getElementById('backLink');
    const fallbackUl = 'order_list.php' + (window.location.search || '');
    cancelBtn.href = fallbackUl;
    backLink.href = fallbackUl;

    function handleSmartBackUl() {
      if (window.history.length > 1) {
        window.history.back();
      } else if (document.referrer && document.referrer !== window.location.href) {
        window.location.href = document.referrer;
      } else {
        window.location.href = fallbackUl;
      }
    }
    backLink.addEventListener('click', function(e) {
      e.preventDefault();
      handleSmartBackUl();
    });
    cancelBtn.addEventListener('click', function(e) {
      e.preventDefault();
      handleSmartBackUl();
    });

    // Interactive Star Rating
    let currentRating = 0;
    function selectRating(rating) {
      currentRating = rating;
      const boxes = document.querySelectorAll('.rating-star-box');
      boxes.forEach(box => {
        const boxRating = parseInt(box.getAttribute('data-rating'), 10);
        if (boxRating <= rating) {
          box.classList.add('selected');
        } else {
          box.classList.remove('selected');
        }
      });
    }

    // Submit Review (kirim asli ke API)
    function kirimUlasan() {
      if (currentRating === 0) {
        alert('Silakan pilih rating bintang terlebih dahulu.');
        return;
      }
      if (!orderId) {
        alert('Pesanan tidak ditemukan.');
        return;
      }
      fetch('../../api/ulasan/simpan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, rating: currentRating, komentar: document.getElementById('reviewDetail').value.trim() })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.ok) {
            alert((res && res.message) || 'Gagal mengirim ulasan.');
            return;
          }
          alert('Terima kasih! Ulasan Anda berhasil dikirim.');
          window.location.href = 'order_list.php';
        })
        .catch(function () { alert('Gagal mengirim ulasan. Periksa koneksi.'); });
    }
    // Badge lonceng: jumlah notifikasi belum dibaca.
    (function () {
      var badge = document.getElementById('ulasanBellBadge');
      fetch('../../api/notifikasi.php')
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
</body>
</html>

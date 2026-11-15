<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Detail Produk</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="dp-body">

  <!-- Top Navigation Header -->
  <header class="detail-navbar">
    <a id="backLink" href="detail-penjual.php?id=1" class="back-btn-circle" aria-label="Kembali">
      <svg class="back-arrow-icon" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
    <h1 class="detail-page-heading">Detail Produk</h1>
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
  <main class="dp-container">
    <!-- Left: Product Image -->
    <div class="dp-image-box">
      <img id="productImg" src="images/prod_grade_a.png" alt="Produk" class="dp-cover-img">
    </div>

    <!-- Right: Product Info -->
    <div class="dp-info-column">
      <span class="dp-badge-pill">Detail Produk</span>
      <h2 id="productTitle" class="dp-product-title">Telur Ayam Grade A</h2>
      <div id="productPrice" class="dp-product-price">Rp 50.000 <span>/ kg</span></div>

      <!-- Tentang Produk Card -->
      <div class="dp-card">
        <h3 class="dp-card-title">Tentang produk</h3>
        <p class="dp-about-desc">
          Telur ayam ras segar dari peternak terverifikasi, dikemas rapi dan siap kirim dalam partai besar.
        </p>
        <div class="dp-info-row">
          <span class="dp-info-label">Ketersediaan &amp; kondisi</span>
          <span class="dp-info-value">Konfirmasi ke peternak</span>
        </div>
        <div class="dp-info-row">
          <span class="dp-info-label">Pengiriman / pengambilan</span>
          <span class="dp-info-value">Diskusikan dengan peternak</span>
        </div>
        <div class="dp-info-row">
          <span class="dp-info-label">Metode pembayaran</span>
          <span class="dp-info-value">Pastikan metode yang diterima</span>
        </div>
      </div>

      <!-- Lokasi Produk Card -->
      <div class="dp-card">
        <h3 class="dp-card-title">Lokasi produk</h3>
        <div class="dp-map-box" id="dpMapBox" title="Klik untuk buka di Google Maps">
          <!-- Realistic Maps Vector (SVG) -->
          <svg class="dp-map-svg" viewBox="0 0 520 180" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="pinShadow" x="-30%" y="-30%" width="160%" height="160%">
                <feDropShadow dx="0" dy="2.5" stdDeviation="2.5" flood-color="#0f172a" flood-opacity="0.3"/>
              </filter>
              <filter id="badgeShadow" x="-20%" y="-20%" width="140%" height="160%">
                <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#0f172a" flood-opacity="0.16"/>
              </filter>
              <pattern id="urbanGrid" width="60" height="60" patternUnits="userSpaceOnUse">
                <rect width="58" height="58" fill="#f5f4ef" stroke="#ece9e1" stroke-width="1.2"/>
              </pattern>
            </defs>

            <!-- Base Land / Urban Background -->
            <rect width="520" height="180" fill="#f4f3ee"/>
            <rect width="520" height="180" fill="url(#urbanGrid)" opacity="0.6"/>

            <!-- Map Elements Group (can be scaled with zoom controls) -->
            <g id="mapElementsGroup" style="transform-origin: 260px 90px; transition: transform 0.25s ease;">
              <!-- Green Park 1 (North West) -->
              <path d="M -10,-10 L 175,-10 L 150,65 L 45,85 L -10,60 Z" fill="#d2ecd0" stroke="#bfe0bc" stroke-width="1"/>
              <circle cx="50" cy="30" r="4" fill="#a4d19f"/>
              <circle cx="70" cy="40" r="5" fill="#99c994"/>
              <circle cx="115" cy="25" r="4.5" fill="#a4d19f"/>
              <text x="50" y="58" fill="#4d7c50" font-size="9" font-weight="700" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">Taman Agro Mandiri</text>

              <!-- Green Park 2 (South East) -->
              <path d="M 370,120 C 400,105 450,115 530,100 L 530,190 L 340,190 C 350,155 355,135 370,120 Z" fill="#d8edd6" stroke="#c4e2c2" stroke-width="1"/>
              <circle cx="430" cy="155" r="5" fill="#a4d19f"/>
              <circle cx="450" cy="145" r="4" fill="#99c994"/>
              <text x="395" y="172" fill="#4d7c50" font-size="8.5" font-weight="700" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">Sentra Peternakan</text>

              <!-- Water / River -->
              <path d="M -10,145 Q 120,130 200,165 T 390,150 T 530,125" fill="none" stroke="#aadaf0" stroke-width="20" stroke-linecap="round"/>
              <path d="M -10,145 Q 120,130 200,165 T 390,150 T 530,125" fill="none" stroke="#c5e7f7" stroke-width="14" stroke-linecap="round"/>
              <text x="90" y="150" fill="#3e84a6" font-size="8" font-style="italic" font-weight="600" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" transform="rotate(4, 90, 150)">Sungai Ciliwung</text>

              <!-- Secondary Streets (White with grey borders) -->
              <line x1="85" y1="-10" x2="85" y2="190" stroke="#dadcd8" stroke-width="8" stroke-linecap="square"/>
              <line x1="85" y1="-10" x2="85" y2="190" stroke="#ffffff" stroke-width="6" stroke-linecap="square"/>

              <line x1="0" y1="50" x2="220" y2="190" stroke="#dadcd8" stroke-width="7" stroke-linecap="round"/>
              <line x1="0" y1="50" x2="220" y2="190" stroke="#ffffff" stroke-width="5" stroke-linecap="round"/>

              <line x1="260" y1="-10" x2="260" y2="190" stroke="#dadcd8" stroke-width="9" stroke-linecap="square"/>
              <line x1="260" y1="-10" x2="260" y2="190" stroke="#ffffff" stroke-width="7" stroke-linecap="square"/>
              <!-- Bridge effect over river -->
              <line x1="254" y1="155" x2="266" y2="155" stroke="#94a3b8" stroke-width="10"/>

              <line x1="430" y1="-10" x2="430" y2="190" stroke="#dadcd8" stroke-width="8" stroke-linecap="square"/>
              <line x1="430" y1="-10" x2="430" y2="190" stroke="#ffffff" stroke-width="6" stroke-linecap="square"/>

              <line x1="260" y1="35" x2="530" y2="25" stroke="#dadcd8" stroke-width="6" stroke-linecap="round"/>
              <line x1="260" y1="35" x2="530" y2="25" stroke="#ffffff" stroke-width="4" stroke-linecap="round"/>

              <!-- Primary Arterial Road (Main Highway in Gold/Yellow) -->
              <path d="M -10,95 Q 160,85 260,95 T 530,90" fill="none" stroke="#f6c256" stroke-width="15" stroke-linejoin="round" stroke-linecap="round"/>
              <path d="M -10,95 Q 160,85 260,95 T 530,90" fill="none" stroke="#fed876" stroke-width="11" stroke-linejoin="round" stroke-linecap="round"/>

              <!-- Road Names -->
              <text id="svgMainRoadName" x="175" y="109" fill="#5c4e2e" font-size="8.5" font-weight="700" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">Jl. Peternakan Raya</text>
              <text x="268" y="45" fill="#71717a" font-size="7.5" font-weight="600" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">Jl. Surya Unggas</text>
              <text x="438" y="70" fill="#71717a" font-size="7.5" font-weight="600" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">Jl. Niaga IV</text>

              <!-- Pin Accuracy Pulse -->
              <circle cx="260" cy="95" r="16" fill="#ea4335" opacity="0.18">
                <animate attributeName="r" values="12;22;12" dur="2.4s" repeatCount="indefinite"/>
                <animate attributeName="opacity" values="0.25;0.05;0.25" dur="2.4s" repeatCount="indefinite"/>
              </circle>

              <!-- Pin Ground Shadow -->
              <ellipse cx="260" cy="96" rx="7" ry="3" fill="#0f172a" opacity="0.3"/>

              <!-- Google Maps Classic Red Pin -->
              <g filter="url(#pinShadow)">
                <path d="M260 95 C255 88 245 74 245 63 C245 54.7 251.7 48 260 48 C268.3 48 275 54.7 275 63 C275 74 265 88 260 95 Z" fill="#ea4335"/>
                <circle cx="260" cy="63" r="4.2" fill="#ffffff"/>
              </g>

              <!-- Floating Tooltip / Seller Badge -->
              <g filter="url(#badgeShadow)" id="svgBadgeGroup">
                <rect id="svgBadgeRect" x="180" y="15" width="160" height="26" rx="13" fill="#ffffff" stroke="#e2e8f0" stroke-width="1"/>
                <circle id="svgBadgeCircle" cx="196" cy="28" r="6" fill="#15803d"/>
                <path id="svgBadgeCheck" d="M193.5 28 L195.5 30 L198.5 26.5" fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                <text id="svgSellerBadgeText" x="207" y="31.5" fill="#0f172a" font-size="9" font-weight="700" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">PT Telur Makmur</text>
              </g>
            </g>
          </svg>

          <!-- Google Maps UI Overlay Controls -->
          <div class="dp-map-chip">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="#ea4335">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            <span>Peta Lokasi</span>
          </div>

          <!-- Top Right: "Buka di Maps" Button -->
          <a id="btnOpenGoogleMaps" href="#" target="_blank" rel="noopener noreferrer" class="dp-map-open-btn" title="Buka di Google Maps">
            <span>Buka di Maps</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
              <polyline points="15 3 21 3 21 9"></polyline>
              <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
          </a>

          <!-- Bottom Left: Google Watermark -->
          <div class="dp-map-watermark">
            <span style="color:#4285F4">G</span><span style="color:#EA4335">o</span><span style="color:#FBBC05">o</span><span style="color:#4285F4">g</span><span style="color:#34A853">l</span><span style="color:#EA4335">e</span>
          </div>

          <!-- Bottom Right: Zoom Controls -->
          <div class="dp-map-zoom-controls">
            <button type="button" class="dp-zoom-btn" id="btnZoomIn" aria-label="Perbesar peta" title="Perbesar">+</button>
            <div class="dp-zoom-divider"></div>
            <button type="button" class="dp-zoom-btn" id="btnZoomOut" aria-label="Perkecil peta" title="Perkecil">−</button>
          </div>
        </div>
        <div class="dp-seller-row">
          <div id="sellerInitial" class="dp-seller-avatar">U</div>
          <div>
            <div class="dp-seller-label">Lokasi penjual</div>
            <div id="sellerAddress" class="dp-seller-address">Jl. Peternakan Raya No. 12</div>
          </div>
        </div>
      </div>

      <!-- Order Button -->
      <a id="rfqLink" href="ajukan-pesanan.php?seller=1" class="btn-rfq-primary">Ajukan Pesanan</a>
    </div>
  </main>

  <!-- Floating Navigation -->

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const sellerId = urlParams.get('seller') || urlParams.get('id') || '1';
    const productName = urlParams.get('product') || 'Telur Ayam Ras Grade A';
    const productPrice = urlParams.get('price') || 'Rp 28.000';

    const sellersInfo = {
      '1': { name: 'PT Telur Makmur', address: 'Jl. Peternakan Raya No. 12', initial: 'U' },
      '2': { name: 'Mitra Unggas Sejahtera', address: 'Jl. Raya Blitar No. 45', initial: 'U' },
      '3': { name: 'Tunas Farm', address: 'Jl. Peternakan Dramaga No. 7', initial: 'U' },
      '4': { name: 'Berkah Telur Utama', address: 'Jl. Kenanga No. 21', initial: 'U' },
      '5': { name: 'Ternak Mandiri', address: 'Jl. Puncak Raya No. 3', initial: 'U' },
      '6': { name: 'Sinar Jaya Poultry', address: 'Jl. Cihampelas No. 88', initial: 'U' }
    };

    // Nama tampil di halaman detail (Title Case rapi)
    const displayTitle = productName
      .split(' ')
      .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
      .join(' ');

    const productImages = {
      'Telur Omega': 'images/prod_omega.png',
      'Telur Ayam Kampung': 'images/prod_kampung.png',
      'Telur Ayam Ras Grade A': 'images/prod_grade_a.png',
      'Telur Ayam Ras Grade B': 'images/prod_grade_b.png',
      'Telur Ayam Ras Grade C': 'images/prod_grade_c.png',
      'Telur ayam': 'images/prod_grade_a.png'
    };

    const seller = sellersInfo[sellerId] || sellersInfo['1'];

    document.getElementById('productTitle').innerText = displayTitle;
    document.getElementById('productPrice').innerHTML = `${productPrice} <span>/ kg</span>`;
    document.getElementById('productImg').src = productImages[productName] || 'images/prod_grade_a.png';
    document.getElementById('productImg').alt = displayTitle;
    document.getElementById('sellerAddress').innerText = seller.address;
    document.getElementById('sellerInitial').innerText = seller.initial;
    const fallbackBackUrl = `detail-penjual.php?id=${sellerId}`;
    const backBtn = document.getElementById('backLink');
    if (backBtn) {
      backBtn.href = fallbackBackUrl;
      backBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
          window.history.back();
        } else if (document.referrer && document.referrer !== window.location.href) {
          window.location.href = document.referrer;
        } else {
          window.location.href = fallbackBackUrl;
        }
      });
    }

    document.getElementById('rfqLink').href =
      `ajukan-pesanan.php?seller=${sellerId}` +
      `&product=${encodeURIComponent(productName)}` +
      `&price=${encodeURIComponent(productPrice)}`;

    // Update peta sesuai lokasi penjual
    const mapRoad = document.getElementById('svgMainRoadName');
    const mapBadge = document.getElementById('svgSellerBadgeText');
    const badgeRect = document.getElementById('svgBadgeRect');
    const openMapsBtn = document.getElementById('btnOpenGoogleMaps');
    const mapBox = document.getElementById('dpMapBox');

    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(seller.name + ', ' + seller.address)}`;

    if (mapRoad) {
      const streetOnly = seller.address.split(' No.')[0];
      mapRoad.textContent = streetOnly;
    }
    if (mapBadge) {
      mapBadge.textContent = seller.name;
      const badgeWidth = Math.max(140, Math.min(240, seller.name.length * 8.5 + 44));
      if (badgeRect) {
        badgeRect.setAttribute('width', badgeWidth);
        badgeRect.setAttribute('x', 260 - badgeWidth / 2);
        const circle = document.getElementById('svgBadgeCircle');
        const check = document.getElementById('svgBadgeCheck');
        if (circle) circle.setAttribute('cx', 260 - badgeWidth / 2 + 16);
        if (check) check.setAttribute('transform', `translate(${260 - badgeWidth / 2 - 177.5}, 0)`);
        mapBadge.setAttribute('x', 260 - badgeWidth / 2 + 27);
      }
    }
    if (openMapsBtn) {
      openMapsBtn.href = googleMapsUrl;
    }
    if (mapBox) {
      mapBox.addEventListener('click', (e) => {
        if (!e.target.closest('.dp-map-zoom-controls') && !e.target.closest('.dp-map-open-btn')) {
          window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
        }
      });
    }

    // Kontrol Zoom Peta
    let currentZoom = 1;
    const mapElementsGroup = document.getElementById('mapElementsGroup');
    const btnZoomIn = document.getElementById('btnZoomIn');
    const btnZoomOut = document.getElementById('btnZoomOut');

    if (btnZoomIn && mapElementsGroup) {
      btnZoomIn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (currentZoom < 1.6) {
          currentZoom = Math.round((currentZoom + 0.2) * 10) / 10;
          mapElementsGroup.style.transform = `scale(${currentZoom})`;
        }
      });
    }
    if (btnZoomOut && mapElementsGroup) {
      btnZoomOut.addEventListener('click', (e) => {
        e.stopPropagation();
        if (currentZoom > 0.8) {
          currentZoom = Math.round((currentZoom - 0.2) * 10) / 10;
          mapElementsGroup.style.transform = `scale(${currentZoom})`;
        }
      });
    }
  </script>
</body>
</html>

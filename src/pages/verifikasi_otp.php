<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi OTP - Staplr</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Tampilan mengikuti desain Figma "V OTP" (node 81:1106),
       "Wait" (node 81:1116) dan "Berhasil disimpan" (node 81:1153):
       kartu OTP putih di atas latar gelap, 6 kotak kode, tombol navy. */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    html,
    body {
      height: 100%;
    }

    body {
      background: linear-gradient(144.58deg, rgb(15, 42, 74) 0%, rgb(32, 177, 90) 100%);
      color: #0f2a4a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
      position: relative;
      overflow: hidden;
    }

    .overlay-gelap {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
      z-index: 0;
    }

    .kartu-otp {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 582px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 16px 0 rgba(6, 24, 46, 0.12), 0 28px 60px -10px rgba(6, 24, 46, 0.28);
      padding: 48px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
      text-align: center;
    }

    .ikon-email {
      width: 72px;
      height: 72px;
    }

    .otp-header {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: center;
    }

    .otp-title {
      font-size: 32px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .otp-subtitle {
      font-size: 15px;
      font-weight: 400;
      color: #64748b;
      line-height: 22px;
    }

    .otp-email {
      font-size: 17px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .otp-inputs {
      display: flex;
      gap: 12px;
      justify-content: center;
      width: 100%;
    }

    .otp-box {
      width: 64px;
      height: 72px;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      text-align: center;
      font-size: 28px;
      font-weight: 700;
      font-family: inherit;
      color: #0f2a4a;
      caret-color: #ffc700;
    }

    .otp-box:focus {
      outline: none;
      background: #ffffff;
      border: 2px solid #0f2a4a;
      box-shadow: 0 4px 14px 0 rgba(15, 42, 74, 0.18);
    }

    .kirim-ulang {
      display: flex;
      gap: 6px;
      justify-content: center;
      font-size: 14px;
      color: #64748b;
      align-items: baseline;
    }

    .kirim-ulang button {
      background: none;
      border: none;
      padding: 0;
      font-size: 14px;
      font-weight: 700;
      font-family: inherit;
      color: #ffc700;
      cursor: pointer;
    }

    .kirim-ulang button:disabled {
      color: #94a3b8;
      cursor: default;
    }

    .hitung-mundur {
      font-size: 13px;
      color: #94a3b8;
    }

    .btn-verifikasi {
      width: 100%;
      height: 56px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(180deg, #17385f 0%, #0f2a4a 100%);
      color: #ffffff;
      font-size: 16px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      box-shadow: 0 2px 6px 0 rgba(15, 42, 74, 0.18), 0 10px 24px -4px rgba(15, 42, 74, 0.38);
    }

    .btn-verifikasi:disabled {
      opacity: 0.6;
      cursor: wait;
    }

    .banner-tes {
      display: none;
      width: 100%;
      background: #fffbeb;
      border: 1px solid #fde68a;
      color: #92400e;
      font-size: 14px;
      border-radius: 10px;
      padding: 10px 14px;
      text-align: left;
    }

    .banner-tes.tampil {
      display: block;
    }

    .banner-tes strong {
      font-size: 18px;
      letter-spacing: 2px;
    }

    .pesan-error {
      display: none;
      width: 100%;
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
      font-size: 14px;
      border-radius: 10px;
      padding: 10px 14px;
      text-align: left;
    }

    .pesan-error.tampil {
      display: block;
    }

    .form-footer {
      font-size: 14px;
      color: #64748b;
    }

    .form-footer a {
      color: #0f2a4a;
      font-weight: 700;
      text-decoration: none;
    }

    /* Layar tunggu (desain "Wait") dan berhasil (desain "Berhasil disimpan"). */
    .layar-penuh {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
      z-index: 10;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }

    .layar-penuh.tampil {
      display: flex;
    }

    .spinner {
      width: 72px;
      height: 72px;
      border: 6px solid rgba(255, 255, 255, 0.3);
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: putar 0.9s linear infinite;
    }

    @keyframes putar {
      to { transform: rotate(360deg); }
    }

    .kartu-berhasil {
      width: 100%;
      max-width: 536px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 16px 0 rgba(6, 24, 46, 0.12), 0 28px 60px -10px rgba(6, 24, 46, 0.28);
      padding: 48px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
      text-align: center;
    }

    .centang {
      width: 120px;
      height: 120px;
    }

    .berhasil-title {
      font-size: 32px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .berhasil-subtitle {
      font-size: 15px;
      color: #64748b;
    }

    @media (max-width: 640px) {
      .kartu-otp,
      .kartu-berhasil {
        padding: 32px 22px;
      }

      .otp-title,
      .berhasil-title {
        font-size: 26px;
      }

      .otp-box {
        width: 48px;
        height: 60px;
        font-size: 22px;
      }

      .otp-inputs {
        gap: 8px;
      }
    }
  </style>
</head>

<body>
  <div class="overlay-gelap"></div>

  <div class="kartu-otp">
    <img class="ikon-email" src="../assets/img/figma_icon_mail.svg" alt="Email">

    <div class="otp-header">
      <h1 class="otp-title">Verifikasi OTP</h1>
      <p class="otp-subtitle">Masukkan 6 digit kode yang telah dikirim ke</p>
      <p class="otp-email" id="emailTersamar">email Anda</p>
    </div>

    <div class="banner-tes" id="bannerTes"></div>
    <div class="pesan-error" id="pesanError"></div>

    <div class="otp-inputs" id="otpInputs">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
      <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
    </div>

    <div class="kirim-ulang">
      <span>Belum menerima kode?</span>
      <button type="button" id="btnKirimUlang">Kirim Ulang</button>
    </div>
    <div class="hitung-mundur" id="hitungMundur"></div>

    <button class="btn-verifikasi" id="btnVerifikasi" type="button">Verifikasi</button>

    <div class="form-footer">
      <a href="lupa_sandi.php">Kembali ke Lupa Kata Sandi</a>
    </div>
  </div>

  <div class="layar-penuh" id="layarTunggu">
    <div class="spinner"></div>
  </div>

  <div class="layar-penuh" id="layarBerhasil">
    <div class="kartu-berhasil">
      <svg class="centang" viewBox="0 0 120 120" fill="none">
        <circle cx="60" cy="60" r="56" fill="#dcfce7"></circle>
        <circle cx="60" cy="60" r="56" stroke="#20b15a" stroke-width="4"></circle>
        <path d="M38 61 L55 78 L84 44" stroke="#20b15a" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
      <h2 class="berhasil-title">Berhasil disimpan</h2>
      <p class="berhasil-subtitle">Kata sandi Anda sudah diganti. Silakan masuk dengan kata sandi baru.</p>
      <button class="btn-verifikasi" type="button" onclick="window.location.href='login.php'">Kembali ke Halaman Masuk</button>
    </div>
  </div>

  <script>
    (function () {
      var params = new URLSearchParams(window.location.search);
      var email = params.get('email') || sessionStorage.getItem('otp_reset_email') || '';
      var pesan = document.getElementById('pesanError');
      var banner = document.getElementById('bannerTes');
      var boxes = Array.prototype.slice.call(document.querySelectorAll('.otp-box'));
      var btnVerifikasi = document.getElementById('btnVerifikasi');
      var btnKirimUlang = document.getElementById('btnKirimUlang');
      var hitung = document.getElementById('hitungMundur');

      if (!email) {
        window.location.href = 'lupa_sandi.php';
        return;
      }

      function maskEmail(e) {
        var p = e.split('@');
        if (p.length !== 2) return e;
        return p[0].slice(0, 2) + '***@' + p[1];
      }

      document.getElementById('emailTersamar').textContent = maskEmail(email);

      // MODE TES: kode OTP ditampilkan di layar supaya bisa diuji tanpa email.
      var kodeUji = sessionStorage.getItem('otp_kode_uji');
      if (kodeUji) {
        banner.innerHTML = 'Mode tes &mdash; kode OTP Anda: <strong>' + kodeUji + '</strong>';
        banner.classList.add('tampil');
      }

      function tampilError(teks) {
        pesan.textContent = teks;
        pesan.classList.add('tampil');
      }

      function sembunyiError() {
        pesan.classList.remove('tampil');
      }

      // Hitung mundur masa berlaku kode (10 menit sejak diminta).
      var timerBerlaku = null;
      function mulaiHitungBerlaku() {
        var ts = Number(sessionStorage.getItem('otp_kedaluwarsa_ts') || 0);
        if (!ts) {
          hitung.textContent = '';
          return;
        }
        clearInterval(timerBerlaku);
        timerBerlaku = setInterval(function () {
          var sisa = Math.max(0, Math.floor((ts - Date.now()) / 1000));
          var m = Math.floor(sisa / 60);
          var d = sisa % 60;
          hitung.textContent = sisa > 0
            ? 'Kode berlaku ' + m + ':' + String(d).padStart(2, '0')
            : 'Kode kedaluwarsa. Minta kode baru.';
          if (sisa <= 0) clearInterval(timerBerlaku);
        }, 500);
      }
      mulaiHitungBerlaku();

      // Jeda kirim ulang 60 detik.
      var timerUlang = null;
      function mulaiJedaUlang() {
        var sisa = 60;
        btnKirimUlang.disabled = true;
        btnKirimUlang.textContent = 'Kirim Ulang (' + sisa + ' dtk)';
        clearInterval(timerUlang);
        timerUlang = setInterval(function () {
          sisa -= 1;
          if (sisa <= 0) {
            clearInterval(timerUlang);
            btnKirimUlang.disabled = false;
            btnKirimUlang.textContent = 'Kirim Ulang';
          } else {
            btnKirimUlang.textContent = 'Kirim Ulang (' + sisa + ' dtk)';
          }
        }, 1000);
      }
      mulaiJedaUlang();

      boxes.forEach(function (box, i) {
        box.addEventListener('input', function () {
          box.value = box.value.replace(/[^0-9]/g, '').slice(0, 1);
          if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
        });
        box.addEventListener('keydown', function (e) {
          if (e.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
          if (e.key === 'Enter') btnVerifikasi.click();
        });
        box.addEventListener('paste', function (e) {
          e.preventDefault();
          var teks = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
          teks.split('').forEach(function (ch, j) {
            if (boxes[j]) boxes[j].value = ch;
          });
          if (teks.length) boxes[Math.min(teks.length, 5)].focus();
        });
      });
      boxes[0].focus();

      function ambilKode() {
        return boxes.map(function (b) { return b.value; }).join('');
      }

      function bersihkanKotak() {
        boxes.forEach(function (b) { b.value = ''; });
        boxes[0].focus();
      }

      async function panggilApi(url, badan) {
        var res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(badan)
        });
        return res.json();
      }

      btnVerifikasi.addEventListener('click', async function () {
        sembunyiError();
        var kode = ambilKode();
        if (kode.length !== 6) {
          tampilError('Masukkan 6 digit kode OTP.');
          return;
        }
        var passwordBaru = sessionStorage.getItem('otp_reset_password') || '';
        if (!passwordBaru) {
          tampilError('Sesi berakhir. Ulangi dari halaman lupa kata sandi.');
          return;
        }
        btnVerifikasi.disabled = true;
        document.getElementById('layarTunggu').classList.add('tampil');
        try {
          var hasil = await panggilApi('../api/auth/otp_verifikasi.php', { email: email, kode: kode });
          if (!hasil.ok) {
            tampilError(hasil.error || 'Verifikasi gagal.');
            bersihkanKotak();
            return;
          }
          var reset = await panggilApi('../api/auth/reset_password.php', {
            token_reset: hasil.data.token_reset,
            password_baru: passwordBaru,
            konfirmasi_password: passwordBaru
          });
          if (!reset.ok) {
            tampilError(reset.error || 'Gagal menyimpan kata sandi baru.');
            return;
          }
          sessionStorage.removeItem('otp_reset_email');
          sessionStorage.removeItem('otp_reset_password');
          sessionStorage.removeItem('otp_kode_uji');
          sessionStorage.removeItem('otp_kedaluwarsa_ts');
          document.getElementById('layarBerhasil').classList.add('tampil');
        } catch (err) {
          tampilError('Tidak dapat terhubung ke server. Coba lagi.');
        } finally {
          document.getElementById('layarTunggu').classList.remove('tampil');
          btnVerifikasi.disabled = false;
        }
      });

      btnKirimUlang.addEventListener('click', async function () {
        sembunyiError();
        btnKirimUlang.disabled = true;
        try {
          var hasil = await panggilApi('../api/auth/otp_minta.php', { email: email });
          if (!hasil.ok) {
            tampilError(hasil.error || 'Gagal meminta kode baru.');
            btnKirimUlang.disabled = false;
            btnKirimUlang.textContent = 'Kirim Ulang';
            return;
          }
          if (hasil.data && hasil.data.kode_uji) {
            sessionStorage.setItem('otp_kode_uji', hasil.data.kode_uji);
            banner.innerHTML = 'Mode tes &mdash; kode OTP Anda: <strong>' + hasil.data.kode_uji + '</strong>';
            banner.classList.add('tampil');
          }
          if (hasil.data && hasil.data.kedaluwarsa_detik) {
            sessionStorage.setItem('otp_kedaluwarsa_ts', String(Date.now() + hasil.data.kedaluwarsa_detik * 1000));
            mulaiHitungBerlaku();
          }
          bersihkanKotak();
          mulaiJedaUlang();
        } catch (err) {
          tampilError('Tidak dapat terhubung ke server. Coba lagi.');
          btnKirimUlang.disabled = false;
          btnKirimUlang.textContent = 'Kirim Ulang';
        }
      });
    })();
  </script>
</body>

</html>

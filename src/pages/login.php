<?php
// Kalau sudah login, langsung ke dashboard sesuai role.
// Login aslinya lewat ../api/auth/login.php (cek tabel users).
session_start();

if (!empty($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'pembeli';
    // Admin ke dashboard admin, perusahaan ke dashboard perusahaan,
    // pembeli (dan role lain) ke dashboard pembeli.
    $dashboard = ($role === 'admin') ? 'admin/dashboard.php'
        : (($role === 'perusahaan') ? 'perusahaan/dashboard_perusahaan.php' : 'pembeli/dashboard_buyer.php');
    header('Location: ' . $dashboard);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staplr - Masuk</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    /* Langkah verifikasi OTP (6 kotak, mengikuti pola verifikasi_otp.php). */
    .otp-step { display: none; margin-top: 0.5rem; }
    .otp-step.tampil { display: block; }
    .otp-step .otp-email { font-weight: 700; margin: 0 0 0.9rem; font-size: 0.95rem; }
    .otp-inputs { display: flex; gap: 8px; justify-content: center; margin-bottom: 0.9rem; }
    .otp-box { width: 46px; height: 54px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 10px; text-align: center; font-size: 22px; font-weight: 700; color: #0F2A4A; }
    .otp-box:focus { outline: none; border: 2px solid #0F2A4A; background: #FFFFFF; }
    .otp-error { display: none; color: #DC2626; font-size: 0.85rem; margin: 0 0 0.75rem; }
    .otp-error.tampil { display: block; }
    .kirim-ulang { font-size: 0.85rem; color: #64748B; text-align: center; margin: 0.75rem 0 0; }
    .kirim-ulang button { background: none; border: none; padding: 0; font-size: 0.85rem; font-weight: 700; color: #F59E0B; cursor: pointer; }
    .kirim-ulang button:disabled { color: #94A3B8; cursor: default; }
    .otp-kembali { display: block; text-align: center; margin-top: 0.75rem; font-size: 0.85rem; color: #64748B; cursor: pointer; background: none; border: none; width: 100%; }
  </style>
</head>
<body>

  <div class="split-container">
    <!-- Left Hero Section -->
    <div class="hero-section">
      <!-- Decorative Background Circles -->
      <div class="hero-circles">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
      </div>

      <div class="hero-content-wrapper">
        <!-- Top Logo Header -->
        <header class="brand-header">
          <img src="../assets/img/logo/logo staplr.png" alt="Staplr Logo" class="brand-logo-img">
          <div class="brand-title-group">
            <span class="brand-dot"></span>
            <span class="brand-name">Staplr</span>
          </div>
        </header>

        <!-- Main Hero Body -->
        <main class="hero-body">
          <span class="hero-badge">PLATFORM TERPERCAYA</span>
          <h1 class="hero-title">Selamat Datang<br>kembali</h1>
          <p class="hero-description">
            Daftarkan akun Anda dan mulai bertransaksi dengan cepat, aman, dan transparan.
          </p>

          <ul class="hero-features">
            <li class="feature-item">
              <span class="check-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span>Transaksi aman dan terverifikasi</span>
            </li>
            <li class="feature-item">
              <span class="check-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span>Pendaftaran selesai dalam 2 menit</span>
            </li>
            <li class="feature-item">
              <span class="check-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span>Dukungan pelanggan setiap hari</span>
            </li>
          </ul>
        </main>
      </div>
    </div>

    <!-- Right Form Section -->
    <div class="form-section">
      <div class="auth-card">
        <h2 class="auth-card-title">Masuk</h2>
        <p class="auth-card-subtitle">Silakan masuk untuk melanjutkan ke akun Anda.</p>

        <!-- Role Selector Tabs -->
        <div class="role-tabs" id="roleTabs">
          <button type="button" class="role-tab-btn active" data-role="pembeli">
            <span class="tab-dot"></span> Pembeli
          </button>
          <button type="button" class="role-tab-btn" data-role="perusahaan">
            <span class="tab-dot" style="display: none;"></span> Perusahaan
          </button>
        </div>

        <!-- Login Form -->
        <form action="../api/auth/login.php" method="POST" id="loginForm">
          <p id="loginError" style="display:none; color:#DC2626; font-size:0.85rem; margin:0 0 0.75rem;"></p>
          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-input" placeholder="nama@email.com" name="email" required>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" id="password" class="form-input" placeholder="••••••••" name="password" required>
            <div class="forgot-wrapper">
              <a href="lupa_sandi.php" class="forgot-link">Lupa Kata Sandi?</a>
            </div>
          </div>

          <button type="submit" class="btn-submit-primary" id="loginBtn">Masuk</button>
        <input type="hidden" name="role" id="selectedRole" value="pembeli">
        </form>

        <!-- Langkah 2 login: verifikasi OTP yang dikirim ke email. -->
        <div class="otp-step" id="otpStep">
          <p style="margin:0 0 0.25rem; font-weight:700;">Verifikasi OTP</p>
          <p class="otp-email" id="otpEmailTersamar"></p>
          <p class="otp-error" id="otpError"></p>
          <div class="otp-inputs" id="otpInputs">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
            <input class="otp-box" type="text" inputmode="numeric" maxlength="1">
          </div>
          <button type="button" class="btn-submit-primary" id="otpVerifyBtn" style="width:100%;">Verifikasi</button>
          <p class="kirim-ulang">Belum menerima kode? <button type="button" id="otpResendBtn">Kirim Ulang</button></p>
          <button type="button" class="otp-kembali" id="otpBackBtn">Kembali ke form masuk</button>
        </div>

        <!-- Akun demo guest: pembeli dan perusahaan, password sama -->
        <div style="margin-top:1rem; padding:0.9rem; border:1px dashed #CBD5E1; border-radius:0.75rem; background:#F8FAFC;">
          <strong>Akun demo</strong><br>
          Pembeli: <code>guest@staplr.com</code> / <code>guest123</code><br>
          Perusahaan: <code>perusahaan@staplr.com</code> / <code>guest123</code><br>
          Admin: <code>admin@staplr.com</code> / <code>guest123</code> (ketik manual di form di atas, migrasi <code>06_seed_demo_admin.sql</code>)<br>
          <button type="button" class="btn-submit-primary" id="guestLoginPembeliBtn" style="margin-top:0.6rem; width:100%;">Masuk sebagai Guest Demo Pembeli</button>
          <button type="button" class="btn-submit-primary" id="guestLoginPerusahaanBtn" style="margin-top:0.6rem; width:100%;">Masuk sebagai Guest Demo Perusahaan</button>
          <p style="font-size:0.8rem; color:#64748B; margin:0.5rem 0 0;">Pilih tombol sesuai peran yang mau dicoba, tidak perlu pindah tab role di atas. Hanya untuk demo di laptop sendiri. Akun pembeli dari <code>database/migrations/02_seed_demo_users.sql</code>, akun perusahaan dari <code>05_seed_demo_perusahaan.sql</code>.</p>
        </div>

        <div class="auth-footer">
          Belum punya akun? <a href="daftar.php" class="link-highlight">Daftar</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Navigation Toolbar to switch between the 3 visual design states -->

  <script>
    // Tab switching interactivity
    const tabBtns = document.querySelectorAll('.role-tab-btn');
    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        tabBtns.forEach(b => {
          b.classList.remove('active');
          const dot = b.querySelector('.tab-dot');
          if (dot) dot.style.display = 'none';
        });
        btn.classList.add('active');
        const activeDot = btn.querySelector('.tab-dot');
        if (activeDot) activeDot.style.display = 'inline-block';
        const roleInput = document.getElementById('selectedRole');
        if (roleInput) roleInput.value = btn.dataset.role || 'pembeli';
      });
    });

    // Login lewat API asli (cek tabel users), lalu ke dashboard sesuai role.
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');

    function showLoginError(msg) {
      if (!loginError) return;
      loginError.textContent = msg;
      loginError.style.display = 'block';
    }

    // Kredensial login tersimpan untuk kirim ulang OTP (panggil login.php lagi).
    let loginEmail = '';
    let loginPassword = '';

    function redirectDashboard(role) {
      window.location.href = (role === 'admin') ? 'admin/dashboard.php'
        : ((role === 'perusahaan') ? 'perusahaan/dashboard_perusahaan.php' : 'pembeli/dashboard_buyer.php');
    }

    async function doLogin(email, password) {
      const res = await fetch('../api/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      const result = await res.json().catch(() => null);
      if (!res.ok || !result || !result.ok) {
        showLoginError((result && result.error) || 'Login gagal. Coba lagi.');
        return false;
      }
      // Langkah 2: password benar, tapi session baru dibuat setelah OTP terverifikasi.
      if (result.data && result.data.butuh_otp) {
        loginEmail = email;
        loginPassword = password;
        tampilkanOtpStep(result.data.email_tersamar || email);
        return true;
      }
      const role = result.data && result.data.role ? result.data.role : 'pembeli';
      // Admin ke dashboard admin, perusahaan ke dashboard perusahaan,
      // pembeli ke dashboard pembeli (sebelumnya admin jatuh ke dashboard pembeli).
      redirectDashboard(role);
      return true;
    }

    async function handleLogin(e) {
      if (e) e.preventDefault();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      if (!email || !password) {
        showLoginError('Email dan password wajib diisi.');
        return;
      }
      try {
        await doLogin(email, password);
      } catch (err) {
        showLoginError('Tidak bisa terhubung ke server. Pastikan Docker jalan.');
      }
    }

    // ---- Langkah OTP (6 kotak, mengikuti pola verifikasi_otp.php) ----
    const otpStep = document.getElementById('otpStep');
    const otpError = document.getElementById('otpError');
    const otpBoxes = Array.prototype.slice.call(document.querySelectorAll('#otpInputs .otp-box'));
    const otpVerifyBtn = document.getElementById('otpVerifyBtn');
    const otpResendBtn = document.getElementById('otpResendBtn');
    let timerUlang = null;

    function tampilOtpError(msg) {
      if (!otpError) return;
      otpError.textContent = msg;
      otpError.classList.add('tampil');
    }

    function sembunyiOtpError() {
      if (!otpError) return;
      otpError.classList.remove('tampil');
    }

    function tampilkanOtpStep(emailTersamar) {
      document.getElementById('otpEmailTersamar').textContent =
        'Masukkan 6 digit kode yang dikirim ke ' + emailTersamar;
      document.getElementById('loginForm').style.display = 'none';
      otpStep.classList.add('tampil');
      sembunyiOtpError();
      bersihkanKotakOtp();
      mulaiJedaUlang();
    }

    function sembunyikanOtpStep() {
      otpStep.classList.remove('tampil');
      document.getElementById('loginForm').style.display = '';
    }

    function bersihkanKotakOtp() {
      otpBoxes.forEach(function (b) { b.value = ''; });
      if (otpBoxes[0]) otpBoxes[0].focus();
    }

    function ambilKodeOtp() {
      return otpBoxes.map(function (b) { return b.value; }).join('');
    }

    // Jeda kirim ulang 60 detik (sama dengan jeda di server).
    function mulaiJedaUlang() {
      let sisa = 60;
      otpResendBtn.disabled = true;
      otpResendBtn.textContent = 'Kirim Ulang (' + sisa + ' dtk)';
      clearInterval(timerUlang);
      timerUlang = setInterval(function () {
        sisa -= 1;
        if (sisa <= 0) {
          clearInterval(timerUlang);
          otpResendBtn.disabled = false;
          otpResendBtn.textContent = 'Kirim Ulang';
        } else {
          otpResendBtn.textContent = 'Kirim Ulang (' + sisa + ' dtk)';
        }
      }, 1000);
    }

    otpBoxes.forEach(function (box, i) {
      box.addEventListener('input', function () {
        box.value = box.value.replace(/[^0-9]/g, '').slice(0, 1);
        if (box.value && i < otpBoxes.length - 1) otpBoxes[i + 1].focus();
      });
      box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !box.value && i > 0) otpBoxes[i - 1].focus();
        if (e.key === 'Enter') otpVerifyBtn.click();
      });
      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const teks = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
        teks.split('').forEach(function (ch, j) {
          if (otpBoxes[j]) otpBoxes[j].value = ch;
        });
        if (teks.length) otpBoxes[Math.min(teks.length, 5)].focus();
      });
    });

    otpVerifyBtn.addEventListener('click', async function () {
      sembunyiOtpError();
      const kode = ambilKodeOtp();
      if (kode.length !== 6) {
        tampilOtpError('Masukkan 6 digit kode OTP.');
        return;
      }
      otpVerifyBtn.disabled = true;
      try {
        const res = await fetch('../api/auth/otp_verifikasi.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: loginEmail, kode: kode, tujuan: 'login' })
        });
        const hasil = await res.json().catch(() => null);
        if (!res.ok || !hasil || !hasil.ok) {
          tampilOtpError((hasil && hasil.error) || 'Verifikasi gagal.');
          bersihkanKotakOtp();
          return;
        }
        redirectDashboard(hasil.data && hasil.data.role ? hasil.data.role : 'pembeli');
      } catch (err) {
        tampilOtpError('Tidak bisa terhubung ke server. Coba lagi.');
      } finally {
        otpVerifyBtn.disabled = false;
      }
    });

    // Kirim ulang = panggil login.php lagi dengan kredensial yang sama.
    otpResendBtn.addEventListener('click', async function () {
      sembunyiOtpError();
      otpResendBtn.disabled = true;
      try {
        const res = await fetch('../api/auth/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: loginEmail, password: loginPassword })
        });
        const hasil = await res.json().catch(() => null);
        if (!res.ok || !hasil || !hasil.ok) {
          tampilOtpError((hasil && hasil.error) || 'Gagal meminta kode baru.');
          otpResendBtn.disabled = false;
          otpResendBtn.textContent = 'Kirim Ulang';
          return;
        }
        bersihkanKotakOtp();
        mulaiJedaUlang();
      } catch (err) {
        tampilOtpError('Tidak bisa terhubung ke server. Coba lagi.');
        otpResendBtn.disabled = false;
        otpResendBtn.textContent = 'Kirim Ulang';
      }
    });

    document.getElementById('otpBackBtn').addEventListener('click', sembunyikanOtpStep);

    if (loginForm) {
      loginForm.addEventListener('submit', handleLogin);
    }

    // Tombol Guest Demo: dua tombol terpisah, masing-masing langsung masuk
    // dengan akun demonya sendiri (tidak tergantung tab role di atas).
    async function guestLogin(email) {
      document.getElementById('email').value = email;
      document.getElementById('password').value = 'guest123';
      try {
        await doLogin(email, 'guest123');
      } catch (err) {
        showLoginError('Tidak bisa terhubung ke server. Pastikan Docker jalan dan migrasi 01, 02, dan 05 sudah dijalankan.');
      }
    }
    const guestPembeliBtn = document.getElementById('guestLoginPembeliBtn');
    if (guestPembeliBtn) {
      guestPembeliBtn.addEventListener('click', function () { guestLogin('guest@staplr.com'); });
    }
    const guestPerusahaanBtn = document.getElementById('guestLoginPerusahaanBtn');
    if (guestPerusahaanBtn) {
      guestPerusahaanBtn.addEventListener('click', function () { guestLogin('perusahaan@staplr.com'); });
    }
  </script>
</body>
</html>

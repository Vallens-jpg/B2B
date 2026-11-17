<?php
// Kalau sudah login, langsung ke dashboard sesuai role.
// Login aslinya lewat ../api/auth/login.php (cek tabel users).
session_start();

if (!empty($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'pembeli';
    $dashboard = ($role === 'perusahaan') ? 'perusahaan/dashboard_perusahaan.php' : 'pembeli/dashboard_buyer.php';
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

        <!-- Akun demo guest: pembeli dan perusahaan, password sama -->
        <div style="margin-top:1rem; padding:0.9rem; border:1px dashed #CBD5E1; border-radius:0.75rem; background:#F8FAFC;">
          <strong>Akun demo</strong><br>
          Pembeli: <code>guest@staplr.com</code> / <code>guest123</code><br>
          Perusahaan: <code>perusahaan@staplr.com</code> / <code>guest123</code><br>
          <button type="button" class="btn-submit-primary" id="guestLoginPembeliBtn" style="margin-top:0.6rem; width:100%;">Masuk sebagai Guest Demo Pembeli</button>
          <button type="button" class="btn-submit-primary" id="guestLoginPerusahaanBtn" style="margin-top:0.6rem; width:100%;">Masuk sebagai Guest Demo Perusahaan</button>
          <p style="font-size:0.8rem; color:#64748B; margin:0.5rem 0 0;">Pilih tombol sesuai peran yang mau dicoba, tidak perlu pindah tab role di atas. Hanya untuk demo di laptop sendiri. Akun pembeli dari <code>database/migrations/02_seed_demo_users.sql</code>, akun perusahaan dari <code>05_seed_demo_perusahaan.sql</code>.</p>
        </div>

        <div class="auth-footer">
          Belum punya akun? <a href="#" class="link-highlight">Daftar</a>
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
      const role = result.data && result.data.role ? result.data.role : 'pembeli';
      window.location.href = (role === 'perusahaan') ? 'perusahaan/dashboard_perusahaan.php' : 'pembeli/dashboard_buyer.php';
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

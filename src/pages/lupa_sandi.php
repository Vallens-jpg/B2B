<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi - Staplr</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Tampilan mengikuti desain Figma "Lupa Sandi" (node 5:3704):
       latar gradien navy ke hijau, modal putih 560px, tombol navy. */
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

    .decor {
      position: absolute;
      pointer-events: none;
    }

    .decor-1 { width: 700px; height: 700px; left: -220px; top: -240px; }
    .decor-2 { width: 600px; height: 600px; left: 1000px; top: 620px; }
    .decor-3 { width: 360px; height: 360px; left: 1020px; top: 60px; }
    .decor-4 { width: 300px; height: 300px; left: 80px; top: 700px; }

    .modal {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 560px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 16px 0 rgba(6, 24, 46, 0.12), 0 28px 60px -10px rgba(6, 24, 46, 0.28);
      padding: 40px;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    .modal-header {
      display: flex;
      flex-direction: column;
      gap: 6px;
      padding-bottom: 6px;
    }

    .modal-title {
      font-size: 30px;
      font-weight: 700;
      color: #0f2a4a;
    }

    .modal-subtitle {
      font-size: 15px;
      font-weight: 400;
      color: #64748b;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .field-label {
      font-size: 14px;
      font-weight: 600;
      color: #0f2a4a;
    }

    .field-input {
      width: 100%;
      height: 52px;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      padding: 0 16px;
      font-size: 15px;
      font-family: inherit;
      color: #0f2a4a;
    }

    .field-input::placeholder {
      color: #94a3b8;
    }

    .field-input:focus {
      outline: 2px solid rgba(15, 42, 74, 0.35);
      border-color: #0f2a4a;
    }

    .actions {
      display: flex;
      gap: 12px;
      padding-top: 6px;
    }

    .btn {
      flex: 1 0 0;
      height: 56px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .btn-secondary {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      color: #0f2a4a;
    }

    .btn-primary {
      background: linear-gradient(180deg, #17385f 0%, #0f2a4a 100%);
      border: none;
      color: #ffffff;
      box-shadow: 0 2px 6px 0 rgba(15, 42, 74, 0.18), 0 10px 24px -4px rgba(15, 42, 74, 0.38);
    }

    .btn-primary:disabled {
      opacity: 0.6;
      cursor: wait;
    }

    .pesan-error {
      display: none;
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
      font-size: 14px;
      border-radius: 10px;
      padding: 10px 14px;
    }

    .pesan-error.tampil {
      display: block;
    }

    .form-footer {
      text-align: center;
      font-size: 14px;
      color: #64748b;
    }

    .form-footer a {
      color: #0f2a4a;
      font-weight: 700;
      text-decoration: none;
    }

    @media (max-width: 640px) {
      .modal {
        padding: 28px 22px;
      }

      .modal-title {
        font-size: 26px;
      }

      .decor {
        display: none;
      }
    }
  </style>
</head>

<body>
  <img class="decor decor-1" src="../assets/img/figma_decor_1.svg" alt="">
  <img class="decor decor-2" src="../assets/img/figma_decor_2.svg" alt="">
  <img class="decor decor-3" src="../assets/img/figma_decor_3.svg" alt="">
  <img class="decor decor-4" src="../assets/img/figma_decor_4.svg" alt="">

  <div class="modal">
    <div class="modal-header">
      <h1 class="modal-title">Lupa Kata Sandi</h1>
      <p class="modal-subtitle">Masukkan email dan kata sandi baru Anda.</p>
    </div>

    <div class="pesan-error" id="pesanError"></div>

    <form id="formLupa" novalidate>
      <div style="display:flex;flex-direction:column;gap:18px;">
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="field-input" id="email" type="email" placeholder="nama@email.com" autocomplete="email" required>
        </div>

        <div class="field">
          <label class="field-label" for="passwordBaru">Kata Sandi Baru</label>
          <input class="field-input" id="passwordBaru" type="password" placeholder="••••••••" autocomplete="new-password" required>
        </div>

        <div class="field">
          <label class="field-label" for="konfirmasiPassword">Konfirmasi Kata Sandi</label>
          <input class="field-input" id="konfirmasiPassword" type="password" placeholder="••••••••" autocomplete="new-password" required>
        </div>

        <div class="actions">
          <a class="btn btn-secondary" href="login.php">Batal</a>
          <button class="btn btn-primary" id="btnSimpan" type="submit">Simpan</button>
        </div>
      </div>
    </form>

    <div class="form-footer">
      <a href="login.php">Kembali ke Halaman Masuk</a>
    </div>
  </div>

  <script>
    (function () {
      var form = document.getElementById('formLupa');
      var pesan = document.getElementById('pesanError');
      var btn = document.getElementById('btnSimpan');

      function tampilError(teks) {
        pesan.textContent = teks;
        pesan.classList.add('tampil');
      }

      form.addEventListener('submit', async function (e) {
        e.preventDefault();
        pesan.classList.remove('tampil');

        var email = document.getElementById('email').value.trim();
        var baru = document.getElementById('passwordBaru').value;
        var konfirmasi = document.getElementById('konfirmasiPassword').value;

        if (!email || email.indexOf('@') < 0) {
          tampilError('Masukkan alamat email yang valid.');
          return;
        }
        if (baru.length < 6) {
          tampilError('Kata sandi baru minimal 6 karakter.');
          return;
        }
        if (baru !== konfirmasi) {
          tampilError('Konfirmasi kata sandi tidak cocok.');
          return;
        }

        btn.disabled = true;
        btn.textContent = 'Mengirim...';
        try {
          var res = await fetch('../api/auth/otp_minta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
          });
          var hasil = await res.json();
          if (!hasil.ok) {
            tampilError(hasil.error || 'Gagal meminta kode OTP.');
            return;
          }
          // Kata sandi baru disimpan sementara di browser sampai OTP terverifikasi,
          // lalu dikirim ke reset_password.php dan langsung dihapus.
          sessionStorage.setItem('otp_reset_email', email);
          sessionStorage.setItem('otp_reset_password', baru);
          if (hasil.data && hasil.data.kode_uji) {
            sessionStorage.setItem('otp_kode_uji', hasil.data.kode_uji);
          }
          if (hasil.data && hasil.data.kedaluwarsa_detik) {
            sessionStorage.setItem('otp_kedaluwarsa_ts', String(Date.now() + hasil.data.kedaluwarsa_detik * 1000));
          }
          window.location.href = 'verifikasi_otp.php?email=' + encodeURIComponent(email);
        } catch (err) {
          tampilError('Tidak dapat terhubung ke server. Coba lagi.');
        } finally {
          btn.disabled = false;
          btn.textContent = 'Simpan';
        }
      });
    })();
  </script>
</body>

</html>

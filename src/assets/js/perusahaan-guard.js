// Pengaman halaman perusahaan: hanya akun perusahaan yang boleh membuka halaman ini.
// Kalau belum login atau perannya bukan perusahaan, lempar ke halaman login.
// Data tetap aman di sisi API, tapi halaman tidak lagi menampilkan kerangka kosong.
(function () {
  fetch('../../api/auth/me.php')
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (res) {
      if (!res || !res.ok || !res.data || res.data.role !== 'perusahaan') {
        window.location.href = '../login.php';
      }
    })
    .catch(function () { window.location.href = '../login.php'; });
})();

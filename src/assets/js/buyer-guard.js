// Pengaman halaman pembeli: hanya akun pembeli yang boleh membuka halaman ini.
// Kalau belum login atau perannya bukan pembeli, lempar ke halaman login.
// Dipakai di dua kedalaman folder (pages/ dan pages/pembeli/), jadi alamat
// API dan login dihitung dari URL halaman.
(function () {
  var dalamPembeli = window.location.pathname.indexOf('/pembeli/') !== -1;
  var awalan = dalamPembeli ? '../../' : '../';
  fetch(awalan + 'api/auth/me.php')
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (res) {
      if (!res || !res.ok || !res.data || res.data.role !== 'pembeli') {
        window.location.href = (dalamPembeli ? '../' : '') + 'login.php';
      }
    })
    .catch(function () { window.location.href = (dalamPembeli ? '../' : '') + 'login.php'; });
})();

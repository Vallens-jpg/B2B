function toggleBuyerProfileDropdown(e) {
  if (e) {
    e.stopPropagation();
  }
  const notifDd = document.getElementById('notifDropdown');
  if (notifDd) notifDd.classList.remove('show');

  const dd = document.getElementById('buyerProfileDropdown');
  if (dd) {
    const isShowing = dd.classList.toggle('show');
    const cap = document.querySelector('.buyer-profile-wrapper .user-profile-capsule');
    if (cap) cap.setAttribute('aria-expanded', isShowing ? 'true' : 'false');
    // Isi nama di header dropdown dari nama pengguna yang sudah dimuat
    // halaman (bukan nama contoh di HTML).
    if (isShowing) {
      const sumber = document.querySelector('.user-name-text')
        || document.querySelector('.pd-user-name')
        || document.querySelector('.ulasan-user-name');
      const target = dd.querySelector('.buyer-dropdown-name');
      if (sumber && target && sumber.textContent.trim()) {
        target.textContent = sumber.textContent.trim();
      }
    }
  }
}

document.addEventListener('click', function (e) {
  const dd = document.getElementById('buyerProfileDropdown');
  if (dd && dd.classList.contains('show')) {
    if (!e.target.closest('.buyer-profile-wrapper')) {
      dd.classList.remove('show');
      const cap = document.querySelector('.buyer-profile-wrapper .user-profile-capsule');
      if (cap) cap.setAttribute('aria-expanded', 'false');
    }
  }
});

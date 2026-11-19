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

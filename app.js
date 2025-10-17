// ===== TICKER (boucle sans trou)
(function () {
  const ticker = document.querySelector('.logo-ticker');
  const track  = document.querySelector('.logo-track');
  if (!ticker || !track) return;

  const groups = track.querySelectorAll('.logo-group');
  if (groups.length < 2) return;

  const g1 = groups[0];
  const g2 = groups[1];

  // Remplir g1 jusqu'à couvrir la largeur du viewport
  const target = ticker.offsetWidth + 200;
  while (g1.scrollWidth < target) {
    g1.append(...Array.from(g1.children).map(n => n.cloneNode(true)));
  }

  // g2 = clone exact de g1 pour jonction parfaite
  const clone = g1.cloneNode(true);
  clone.setAttribute('aria-hidden', 'true');
  track.replaceChild(clone, g2);
})();

// ===== MENU MOBILE
const navToggle = document.querySelector('.nav-toggle');
const mobileMenu = document.getElementById('mobile-menu');

function closeMobile(){
  if (!mobileMenu) return;
  mobileMenu.classList.remove('open');
  document.body.classList.remove('no-scroll');
  if (navToggle) {
    navToggle.classList.remove('is-open');
    navToggle.setAttribute('aria-expanded','false');
  }
}

if (navToggle && mobileMenu) {
  navToggle.addEventListener('click', () => {
    const willOpen = !mobileMenu.classList.contains('open');
    mobileMenu.classList.toggle('open', willOpen);
    document.body.classList.toggle('no-scroll', willOpen);
    navToggle.classList.toggle('is-open', willOpen);
    navToggle.setAttribute('aria-expanded', String(willOpen));
  });

  // Fermer au clic d’un lien
  mobileMenu.addEventListener('click', (e) => {
    if (e.target.tagName === 'A') closeMobile();
  });

  // Fermer si on repasse en desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 900) closeMobile();
  });
}

// ===== ONGLETS EXPERTISES
const tabs = Array.from(document.querySelectorAll('.tabs .tab'));
const panels = {
  eng: document.getElementById('tab-eng'),
  analyst: document.getElementById('tab-analyst'),
  scientist: document.getElementById('tab-scientist'),
  ml: document.getElementById('tab-ml'),
};

function activateTab(key) {
  tabs.forEach(btn => {
    const isActive = btn.dataset.tab === key;
    btn.classList.toggle('active', isActive);
    btn.setAttribute('aria-selected', String(isActive));
  });
  Object.entries(panels).forEach(([k, el]) => {
    if (!el) return;
    el.hidden = k !== key;
    if (!el.hidden) el.setAttribute('tabindex','-1'); else el.removeAttribute('tabindex');
  });
}

tabs.forEach(btn => {
  btn.addEventListener('click', () => activateTab(btn.dataset.tab));
  btn.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
      e.preventDefault();
      const i = tabs.indexOf(btn);
      const dir = e.key === 'ArrowRight' ? 1 : -1;
      const next = (i + dir + tabs.length) % tabs.length;
      tabs[next].focus();
    }
  });
});

if (tabs.length) activateTab('eng');
// ===== SCROLL VERS LE BAS
document.querySelectorAll('[data-scroll="bottom"]').forEach((el) => {
  el.addEventListener('click', (e) => {
    // si c'est un lien, on évite de changer d'URL
    e.preventDefault();
    window.scrollTo({
      top: document.documentElement.scrollHeight,
      behavior: 'smooth',
    });
  });
});
// ... (le reste de votre JS existant)

// ===== HEADER: passer en fond blanc après défilement
(function () {
  const header = document.querySelector('.site-header');
  const logo   = document.getElementById('brandLogo');
  if (!header || !logo) return;

  const LIGHT_LOGO = 'assets/CHOURAK_blanc.png'; // affiché en haut de page (fond visuel)
  const DARK_LOGO  = 'assets/CHOURAK.png';       // affiché après scroll (header blanc)

  const onScroll = () => {
    const solid = window.scrollY > 10;
    header.classList.toggle('is-solid', solid);
    logo.src = solid ? DARK_LOGO : LIGHT_LOGO;
  };

  // init + écoute du scroll
  onScroll();
  window.addEventListener('scroll', onScroll);
})();


/* ==== Betöltési képernyő kezelése ==== */
window.addEventListener('load', () => {
  const loadingScreen = document.getElementById('loading-screen');
  setTimeout(() => {
    loadingScreen.classList.add('hidden');
    // Animációk indítása a betöltő eltűnése után
    startPageAnimations();
  }, 1000); // 1 másodperc után eltűnik
});

/* ==== Oldal betöltési és inicializálási logika ==== */

// Várjuk meg, amíg a teljes DOM betöltődik
document.addEventListener('DOMContentLoaded', () => {
  // Animációk későbbi indításra készen állnak
});

/* ==== Animációk indítása függvény ==== */
function startPageAnimations() {
  const explore = document.querySelector('.explore');
  const theWorld = document.querySelector('.the-world');

  // Header-elemek listába gyűjtése
  const headerItems = [
    document.querySelector('header .logo'),
    document.querySelector('header .hamburger'),
    ...document.querySelectorAll('header nav a')
  ];

  // Animációk időzítése
  setTimeout(() => {
    explore.classList.add('visible');              // 1. Fedezd fel (0,2 mp)
  }, 200);

  setTimeout(() => {
    theWorld.classList.add('visible');             // 2. a természetet (0,8 mp)
  }, 800);

  // 3. Header elemek egymás után
  headerItems.forEach((el, index) => {
    setTimeout(() => {
      el.classList.add('visible-header');
    }, 1400 + index * 200);                       // 1,4 mp kezdés, 0,2 mp lépések
  });
}


/* ==== "Rólunk" szekció görgetése ==== */

document.addEventListener('DOMContentLoaded', () => {
  const aboutLink = document.querySelector('a[href="#about-section"]');
  if (aboutLink) {
    aboutLink.addEventListener('click', (e) => {
      e.preventDefault();
      const targetSection = document.querySelector('#about-section');
      if (targetSection) {
        targetSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }
});

/* ==== Hamburger menü és mobil navigáció ==== */
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('nav');

hamburger.addEventListener('click', () => {
  navMenu.classList.toggle('open');
  hamburger.classList.toggle('active'); // opcionális animációhoz
});
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



/* ==== Hamburger menü és mobil navigáció ==== */
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('nav');

console.log('Hamburger:', hamburger);
console.log('NavMenu:', navMenu);

if (hamburger && navMenu) {
  hamburger.addEventListener('click', () => {
    console.log('Hamburger clicked');
    navMenu.classList.toggle('open');
    hamburger.classList.toggle('active'); // opcionális animációhoz
    console.log('Nav open:', navMenu.classList.contains('open'));
  });
} else {
  console.log('Hamburger or nav not found');
}

        // Close menu when clicking outside (mobile)
        document.addEventListener('click', (event) => {
            const isClickInsideNav = navMenu.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);
            if (!isClickInsideNav && !isClickOnHamburger && navMenu.classList.contains('open')) {
                navMenu.classList.remove('open');
                hamburger.classList.remove('active');
            }
        });



/* ==== Végtelen szöveg görgetés (Marquee) implementáció ==== */
const marquee = document.getElementById('marquee');
const container = marquee.parentElement;
const originalContent = marquee.innerHTML;

/**
 * Végtelen szöveggörgetés (Marquee) rendszer
 * 
 * A rendszer működése:
 * 1. Tartalom duplázása az egyenletes görgetéshez
 * 2. Szélesség számítás a megfelelő mennyiségű tartalomhoz
 * 3. Hardware-gyorsított animáció transform3d-vel
 * 4. Egér hatására megálló animáció
 */
// Marquee tartalmának felduplázása a folyamatos görgetéshez
function fillMarquee() {
  marquee.innerHTML = originalContent;
  let contentWidth = 0;
  let blockCount = 0; // Biztonsági számláló a végtelen ciklus elkerüléséhez

  while (contentWidth < container.offsetWidth * 3) {
    const span = document.createElement('span');
    span.className = 'marquee-block';
    span.innerHTML = originalContent;
    marquee.appendChild(span);
    contentWidth = marquee.scrollWidth;
    blockCount++;
    if (blockCount > 20) break;
  }
}

let pos = 0;
let speed = 1.5;
let paused = false;
marquee.addEventListener('mouseenter', () => (paused = true));
marquee.addEventListener('mouseleave', () => (paused = false));

// Folyamatos vízszintes görgetés animáció
function animate() {
  if (!paused) {
    pos -= speed; // Pozíció frissítése a sebesség alapján
    if (Math.abs(pos) >= marquee.scrollWidth / 3) pos = 0; // Vissza az elejre ha tulment
    marquee.style.transform = `translate3d(${pos}px, 0, 0)`; // Hardware-gyorsított transform
  }
  requestAnimationFrame(animate);
}

fillMarquee();
animate();

/* ==== Körkörös galéria slider ==== */
// A slider elemek és vezérlők inicializálása
const slides = document.querySelectorAll('.slide-circle');
const prev = document.querySelector('.prev-circle');
const next = document.querySelector('.next-circle');
const sliderWrapper = document.querySelector('.slider-wrapper');

let current = 0;

/*
 * Frissíti a slider megjelenését és pozícionálja a képeket
 * - Középen az aktív kép nagyobb méretben
 * - Két oldalt kisebb méretben a szomszédos képek
 * - A többi kép elhalványul és még kisebb
 * - Transform használata a hardver-gyorsítás miatt
 */
function updateSlides() {
  const total = slides.length;
  const isMobile = window.innerWidth <= 600;

slides.forEach((slide, index) => {
  let offset = (index - current + total) % total;

    let scale, translateX, translateY, opacity;

    translateY = 0;

    if(offset === 0) {
    scale = isMobile ? 1.5 : 1.4;
    translateX = 0;
    opacity = 1;
  } else if(offset === 1 || offset === total - 1) {
    scale = isMobile ? 0.9 : 1.1;
    translateX = offset === 1 ? (isMobile ? 120 : 150) : (isMobile ? -120 : -150);
    opacity = 0.8;
  } else if(offset === 2 || offset === total - 2) {
    scale = isMobile ? 0.6 : 0.9;
    translateX = offset === 2 ? (isMobile ? 80 : 270) : (isMobile ? -80 : -270);
    opacity = isMobile ? 0.3 : 0.7;
  } else {
    scale = 0.7;
    translateX = 0;
    opacity = 0;
  }

    // Soha ne rejtsük el a képeket, mindig display block
    slide.style.display = 'block';
    slide.style.transform = `translateX(${translateX}px) translateY(${translateY}px) scale(${scale})`;
    slide.style.opacity = opacity;

    slide.classList.toggle('active', offset === 0);
  });
}

prev.addEventListener('click', () => {
  current = (current - 1 + slides.length) % slides.length;
  updateSlides();
});

next.addEventListener('click', () => {
  current = (current + 1) % slides.length;
  updateSlides();
});

// Initialize slides on page load
updateSlides();

// Auto-slide every 3 seconds
let autoSlideInterval = setInterval(() => {
  current = (current + 1) % slides.length;
  updateSlides();
}, 3000);

// Touch events for swipe support on mobile
sliderWrapper.addEventListener('touchstart', e => {
  sliderWrapper.startX = e.touches[0].clientX;
});

sliderWrapper.addEventListener('touchend', e => {
  const diffX = sliderWrapper.startX - e.changedTouches[0].clientX;
  if(diffX > 40) {
    next.click();
  } else if(diffX < -40) {
    prev.click();
  }
});

// Pause auto-slide on mouse enter
const sliderContainer = document.querySelector('.circle-slider-container');
sliderContainer.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
sliderContainer.addEventListener('mouseleave', () => {
  autoSlideInterval = setInterval(() => {
    current = (current + 1) % slides.length;
    updateSlides();
  }, 3000);
});







sliderWrapper.addEventListener('touchstart', (e) => {
  startX = e.touches[0].clientX;
});

sliderWrapper.addEventListener('touchend', (e) => {
  const endX = e.changedTouches[0].clientX;
  const diff = startX - endX;
  if(diff > 40) { // balra húzás
    next.click();
  } else if(diff < -40) { // jobbra húzás
    prev.click();
  }
});

// Animated scroll-progress bar
(function() {
  const progress = document.getElementById('scroll-progress');
  window.addEventListener('scroll', () => {
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const percent = (scrollTop / docHeight) * 100;
    progress.style.height = percent + 'vh';
  });
})();

// Animációk hozzáadása
function initGlobalFadeIn() {
  const items = document.querySelectorAll('.fade-in');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  items.forEach(item => observer.observe(item));
}

document.addEventListener('DOMContentLoaded', () => {
  // ha kell, előbb felvesszük a fade-in osztályt:
  document.querySelectorAll('body > *').forEach(el => el.classList.add('fade-in'));
  initGlobalFadeIn();
});

/* ==== Smooth scroll for anchor links ==== */
document.querySelectorAll('a[href^="#about"]').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector('#about');
    const topOffset = 60; // Például header magassága (px), ha kell
    const y = target.getBoundingClientRect().top + window.pageYOffset - topOffset;
    window.scrollTo({ top: y, behavior: 'smooth' });
  });
});
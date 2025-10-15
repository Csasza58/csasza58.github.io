document.addEventListener("DOMContentLoaded", () => {
  // Make entire post card clickable except category button
  document.querySelectorAll(".post").forEach(function (post) {
    post.addEventListener("click", function (e) {
      if (e.target.classList.contains("category__button")) return;
      var link = post.querySelector('a[href*="post.php?id="]');
      if (link) {
        window.location = link.getAttribute("href");
      }
    });
  });
  // Admin sidebar functionality
  const sidebar = document.querySelector("aside");
  const showSidebarBtn = document.querySelector("#show__sidebar-btn");
  const hideSidebarBtn = document.querySelector("#hide__sidebar-btn");

  if (sidebar && showSidebarBtn) {
    // Kezdetben a sidebar rejtve van (menu-hidden osztály)
    let isMenuOpen = false;
    sidebar.classList.add('menu-hidden');
    showSidebarBtn.style.display = "flex";
    showSidebarBtn.style.right = "calc(100vw - 80vw - 3rem)";
    
    const toggleSidebar = () => {
      if (isMenuOpen) {
        // Bezárás - menü jobbra eltűnik, gomb is vele megy
        sidebar.classList.add('menu-hidden');
        showSidebarBtn.style.right = "calc(100vw - 80vw - 3rem)";
        showSidebarBtn.innerHTML = '<i class="uil uil-angle-right-b"></i>';
        isMenuOpen = false;
      } else {
        // Megnyitás - menü balra kijön, gomb a menü jobb szélén marad
        sidebar.classList.remove('menu-hidden');
        showSidebarBtn.style.right = "-3rem";
        showSidebarBtn.innerHTML = '<i class="uil uil-angle-left-b"></i>';
        isMenuOpen = true;
      }
    };

    showSidebarBtn.addEventListener("click", toggleSidebar);
    
    // Hide button eseménykezelő ha létezik
    if (hideSidebarBtn) {
      hideSidebarBtn.addEventListener("click", toggleSidebar);
    }
  }

  // Hamburger menu is now handled by inline JavaScript in header.php

  // Featured posts carousel functionality
  const carousel = document.querySelector('.featured-carousel');
  const slides = document.querySelectorAll('.featured-slide');
  const dots = document.querySelectorAll('.carousel-dot');
  
  if (carousel && slides.length > 1) {
    let currentSlide = 0;
    const totalSlides = slides.length;
    
    // Function to show specific slide
    function showSlide(index) {
      // Remove active class from all slides and dots
      slides.forEach(slide => slide.classList.remove('active'));
      dots.forEach(dot => dot.classList.remove('active'));
      
      // Add active class to current slide and dot
      slides[index].classList.add('active');
      if (dots[index]) {
        dots[index].classList.add('active');
      }
      
      currentSlide = index;
    }
    
    // Function to go to next slide
    function nextSlide() {
      const next = (currentSlide + 1) % totalSlides;
      showSlide(next);
    }
    
    // Auto-advance carousel every 15 seconds
    const autoAdvance = setInterval(nextSlide, 15000);
    
    // Manual control with dots
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        clearInterval(autoAdvance);
        showSlide(index);
        // Restart auto-advance after manual interaction
        setTimeout(() => {
          setInterval(nextSlide, 7000);
        }, 7000);
      });
    });
    
    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
      clearInterval(autoAdvance);
    });
  }
});

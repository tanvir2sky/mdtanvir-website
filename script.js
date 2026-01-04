// Theme Toggle
const themeToggle = document.getElementById("theme-toggle");
const html = document.documentElement;

// Check for saved theme preference or default to light mode
const currentTheme = localStorage.getItem("theme") || "light";
html.classList.toggle("dark", currentTheme === "dark");

themeToggle.addEventListener("click", () => {
  html.classList.toggle("dark");
  const theme = html.classList.contains("dark") ? "dark" : "light";
  localStorage.setItem("theme", theme);
});

// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById("mobile-menu-toggle");
const mobileMenu = document.getElementById("mobile-menu");

mobileMenuToggle.addEventListener("click", () => {
  mobileMenu.classList.toggle("hidden");
  const icon = mobileMenuToggle.querySelector("i");
  if (mobileMenu.classList.contains("hidden")) {
    icon.classList.remove("fa-times");
    icon.classList.add("fa-bars");
  } else {
    icon.classList.remove("fa-bars");
    icon.classList.add("fa-times");
  }
});

// Close mobile menu when clicking on a link
const mobileMenuLinks = mobileMenu.querySelectorAll("a");
mobileMenuLinks.forEach((link) => {
  link.addEventListener("click", () => {
    mobileMenu.classList.add("hidden");
    const icon = mobileMenuToggle.querySelector("i");
    icon.classList.remove("fa-times");
    icon.classList.add("fa-bars");
  });
});

// Smooth Scrolling for Navigation Links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      const offsetTop = target.offsetTop - 64; // Account for fixed navbar
      window.scrollTo({
        top: offsetTop,
        behavior: "smooth",
      });
    }
  });
});

// Active Navigation Link on Scroll
const sections = document.querySelectorAll("section[id]");
const navLinks = document.querySelectorAll(".nav-link");

function updateActiveNavLink() {
  const scrollY = window.pageYOffset;

  sections.forEach((section) => {
    const sectionHeight = section.offsetHeight;
    const sectionTop = section.offsetTop - 100;
    const sectionId = section.getAttribute("id");

    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      navLinks.forEach((link) => {
        link.classList.remove("active");
        if (link.getAttribute("href") === `#${sectionId}`) {
          link.classList.add("active");
        }
      });
    }
  });
}

window.addEventListener("scroll", updateActiveNavLink);

// Intersection Observer for Section Animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("visible");
    }
  });
}, observerOptions);

sections.forEach((section) => {
  observer.observe(section);
});

// Scroll to Top Button
const scrollTopButton = document.getElementById("scroll-top");

window.addEventListener("scroll", () => {
  if (window.pageYOffset > 300) {
    scrollTopButton.classList.remove("opacity-0", "pointer-events-none");
    scrollTopButton.classList.add("opacity-100");
  } else {
    scrollTopButton.classList.add("opacity-0", "pointer-events-none");
    scrollTopButton.classList.remove("opacity-100");
  }
});

scrollTopButton.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});

// Add animation delays to skill cards
const skillCards = document.querySelectorAll(".skill-card");
skillCards.forEach((card, index) => {
  card.style.animationDelay = `${index * 0.1}s`;
});

// Add animation delays to project cards
const projectCards = document.querySelectorAll(".project-card");
projectCards.forEach((card, index) => {
  card.style.animationDelay = `${index * 0.15}s`;
});

// Add animation delays to contact cards
const contactCards = document.querySelectorAll(".contact-card");
contactCards.forEach((card, index) => {
  card.style.animationDelay = `${index * 0.1}s`;
});

// Initialize: Make hero section visible immediately
document.addEventListener("DOMContentLoaded", () => {
  const heroSection = document.getElementById("home");
  if (heroSection) {
    heroSection.classList.add("visible");
  }
});

// Performance: Lazy load images if any are added later
if ("loading" in HTMLImageElement.prototype) {
  const images = document.querySelectorAll('img[loading="lazy"]');
  images.forEach((img) => {
    img.src = img.dataset.src;
  });
}

// Parallax effect removed - hero section stays fixed

// Particles.js Configuration
function initParticles() {
  const isDark = html.classList.contains("dark");

  particlesInstance = particlesJS("particles-js", {
    particles: {
      number: {
        value: 80,
        density: {
          enable: true,
          value_area: 800,
        },
      },
      color: {
        value: isDark ? "#38bdf8" : "#0284c7", // Primary color based on theme
      },
      shape: {
        type: "circle",
        stroke: {
          width: 0,
          color: "#000000",
        },
      },
      opacity: {
        value: isDark ? 0.3 : 0.2,
        random: true,
        anim: {
          enable: true,
          speed: 1,
          opacity_min: 0.1,
          sync: false,
        },
      },
      size: {
        value: 3,
        random: true,
        anim: {
          enable: true,
          speed: 2,
          size_min: 0.5,
          sync: false,
        },
      },
      line_linked: {
        enable: true,
        distance: 150,
        color: isDark ? "#38bdf8" : "#0284c7",
        opacity: isDark ? 0.2 : 0.15,
        width: 1,
      },
      move: {
        enable: true,
        speed: 2,
        direction: "none",
        random: true,
        straight: false,
        out_mode: "out",
        bounce: false,
        attract: {
          enable: false,
          rotateX: 600,
          rotateY: 1200,
        },
      },
    },
    interactivity: {
      detect_on: "canvas",
      events: {
        onhover: {
          enable: true,
          mode: "repulse",
        },
        onclick: {
          enable: true,
          mode: "push",
        },
        resize: true,
      },
      modes: {
        grab: {
          distance: 400,
          line_linked: {
            opacity: 1,
          },
        },
        bubble: {
          distance: 400,
          size: 40,
          duration: 2,
          opacity: 8,
          speed: 3,
        },
        repulse: {
          distance: 200,
          duration: 0.4,
        },
        push: {
          particles_nb: 4,
        },
        remove: {
          particles_nb: 2,
        },
      },
    },
    retina_detect: true,
  });
}

// Initialize particles on page load
document.addEventListener("DOMContentLoaded", () => {
  initParticles();
});

// Reinitialize particles when theme changes
let particlesInstance = null;

themeToggle.addEventListener("click", () => {
  setTimeout(() => {
    if (typeof particlesJS !== "undefined" && particlesInstance) {
      // Destroy existing particles instance
      if (particlesInstance.pJSDom && particlesInstance.pJSDom[0]) {
        particlesInstance.pJSDom[0].pJS.fn.vendors.destroypJS();
        particlesInstance.pJSDom = [];
      }
      // Reinitialize with new theme
      initParticles();
    }
  }, 100);
});

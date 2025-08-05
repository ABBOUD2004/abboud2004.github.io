document.addEventListener("DOMContentLoaded", () => {
  // Show elements on load
  const animated = document.querySelectorAll(".animate");
  animated.forEach((el, index) => {
    setTimeout(() => {
      el.classList.add("show");
    }, index * 200);
  });

  // Typed.js
  new Typed("#typed-text", {
    strings: ["Full Stack Developer", "Frontend Designer", "Backend Engineer"],
    typeSpeed: 60,
    backSpeed: 40,
    loop: true,
    smartBackspace: true,
    showCursor: true,
    cursorChar: '|'
  });
});




  // ScrollSpy-like behavior
  // Show navbar when scrolling down
  let prevScrollPos = window.scrollY;
  window.addEventListener("scroll", function () {
    const currentScrollPos = window.scrollY;
    const navbar = document.querySelector(".main-navbar");

    if (currentScrollPos > 100) {
      if (currentScrollPos > prevScrollPos) {
        // scrolling down
        navbar.style.top = "0";
      } else {
        // scrolling up
        navbar.style.top = "0";
      }
    } else {
      navbar.style.top = "0";
    }
    prevScrollPos = currentScrollPos;

    // Highlight active section
    const sections = document.querySelectorAll("section[id]");
    const links = document.querySelectorAll(".main-navbar .nav-link");

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 150;
      const sectionHeight = section.offsetHeight;

      if (
        window.scrollY >= sectionTop &&
        window.scrollY < sectionTop + sectionHeight
      ) {
        links.forEach((link) => {
          link.classList.remove("active");
          if (link.getAttribute("href").includes(section.id)) {
            link.classList.add("active");
          }
        });
      }
    });
  });


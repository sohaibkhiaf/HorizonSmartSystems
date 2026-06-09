

// Smart header scroll
const header = document.getElementById("mainHeader");

let lastScrollY = window.scrollY;

window.addEventListener("scroll", () => {
    const currentScrollY = window.scrollY;

    // Add background when scrolled
    if (currentScrollY > 50) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }

    // // Hide on scroll down, show on scroll up
    // if (currentScrollY > lastScrollY && currentScrollY > 80) {
    //     // scrolling down
    //     header.classList.add("hidden");
    // } else {
    //     // scrolling up
    //     header.classList.remove("hidden");
    // }

    // lastScrollY = currentScrollY;



const sections = document.querySelectorAll("[data-section]");
const tabs = document.querySelectorAll(".nav-tab");

window.addEventListener("scroll", () => {
  let current = "";

  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.clientHeight;

    if (window.scrollY >= sectionTop - sectionHeight / 3) {
      current = section.getAttribute("data-section");
    }
  });

  tabs.forEach(tab => {
    tab.classList.remove("active");

    if (tab.getAttribute("data-target") === current) {
      tab.classList.add("active");
    }
  });
});


});

const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");

menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("active");
});




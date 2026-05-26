
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

    // Hide on scroll down, show on scroll up
    if (currentScrollY > lastScrollY && currentScrollY > 80) {
        // scrolling down
        header.classList.add("hidden");
    } else {
        // scrolling up
        header.classList.remove("hidden");
    }

    lastScrollY = currentScrollY;
});

const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");

menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("active");
});




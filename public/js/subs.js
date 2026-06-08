new Swiper(".trending-swiper", {
    slidesPerView: "auto",
    spaceBetween: 20,
    grabCursor: true
});


const tabs = document.querySelectorAll(".tab");
const products = document.querySelectorAll(".product-card");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {

        // Remove active class from all tabs
        tabs.forEach(t => t.classList.remove("active"));

        // Add active class to clicked tab
        tab.classList.add("active");

        const category = tab.dataset.category;

        products.forEach(product => {
            const productCategory = product.dataset.category;

            if (category === "all" || category === productCategory) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }
        });
    });
});

document.getElementById('shopNowBtn').addEventListener('click', function(e) {
    e.preventDefault();

    window.scrollBy({
        top: 600,
        behavior: 'smooth'
    });
});








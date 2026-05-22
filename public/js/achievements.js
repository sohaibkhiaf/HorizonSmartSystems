const tabs = document.querySelectorAll(".tab");
const projects = document.querySelectorAll(".acheivement-card");


tabs.forEach(tab => {
    tab.addEventListener("click", () => {

        // remove active
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");

        const type = tab.dataset.type;

        projects.forEach(project => {
            const projectType = project.dataset.type;

            if (type === "all" || type === projectType) {
                project.style.display = "block";
            } else {
                project.style.display = "none";
            }
        });
    });
});

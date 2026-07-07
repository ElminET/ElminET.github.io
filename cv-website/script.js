const mainTitle = document.getElementById("mainTitle");

mainTitle.addEventListener("click", function () {
    mainTitle.classList.toggle("clicked");
});

const navLinks = document.querySelectorAll(".nav-link");
const navbarMenu = document.getElementById("navbarMenu");

navLinks.forEach(function (link) {
    link.addEventListener("click", function () {
        if (navbarMenu.classList.contains("show")) {
            const menu = new bootstrap.Collapse(navbarMenu);
            menu.hide();
        }
    });
});
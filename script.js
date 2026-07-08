const mainTitle = document.getElementById("mainTitle");

if (mainTitle) {
    mainTitle.addEventListener("click", function () {
        mainTitle.classList.toggle("clicked");
    });
}

const navLinks = document.querySelectorAll(".nav-link");
const navbarMenu = document.getElementById("navbarMenu");

navLinks.forEach(function (link) {
    link.addEventListener("click", function () {
        if (navbarMenu && navbarMenu.classList.contains("show")) {
            const menu = new bootstrap.Collapse(navbarMenu);
            menu.hide();
        }
    });
});

const darkModeBtn = document.getElementById("darkModeBtn");
const darkModeIcon = document.getElementById("darkModeIcon");

if (localStorage.getItem("darkMode") === "aan") {
    document.body.classList.add("dark-mode");

    if (darkModeIcon) {
        darkModeIcon.classList.remove("bi-moon-stars-fill");
        darkModeIcon.classList.add("bi-brightness-high-fill");
    }
}

if (darkModeBtn) {
    darkModeBtn.addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("darkMode", "aan");

            if (darkModeIcon) {
                darkModeIcon.classList.remove("bi-moon-stars-fill");
                darkModeIcon.classList.add("bi-brightness-high-fill");
            }
        } else {
            localStorage.setItem("darkMode", "uit");

            if (darkModeIcon) {
                darkModeIcon.classList.remove("bi-brightness-high-fill");
                darkModeIcon.classList.add("bi-moon-stars-fill");
            }
        }
    });
}
var openMobileMenu = false;

function openNav() {
    if (openMobileMenu === false) {
        openMobileMenu = true;
        document.getElementById("mobileNav").style.width = "75%";
        document.getElementById("mobileNav").style.visibility = "visible";
    } else {
        openMobileMenu = false;
        closeNav();
    }
}

function closeNav() {
    openMobileMenu = false;
    document.getElementById("mobileNav").style.width = "0";
    document.getElementById("mobileNav").style.visibility = "hidden";
}

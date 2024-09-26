import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/style.css";
import "./styles/app.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

document.addEventListener("DOMContentLoaded", function () {
    const mobileMenuButton = document.getElementById("mobile-menu-button");
    const mobileNav = document.getElementById("mobile-nav");

    mobileMenuButton.addEventListener("click", function () {
        mobileNav.classList.toggle("mobile-nav--active");
    });

    const profileButton = document.getElementById("profile-menu-button");
    const profileDropdown = document.getElementById("profile-dropdown");

    profileButton.addEventListener("click", function () {
        // Toggle the dropdown visibility
        profileDropdown.classList.toggle("header__dropdown--active");
    });

    // Close the dropdown if user clicks outside of it
    document.addEventListener("click", function (event) {
        if (
            !profileButton.contains(event.target) &&
            !profileDropdown.contains(event.target)
        ) {
            profileDropdown.classList.remove("header__dropdown--active");
        }
    });
});

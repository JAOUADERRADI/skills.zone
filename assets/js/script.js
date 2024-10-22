document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileNav = document.getElementById("mobile-nav");
  const menuIcon = document.getElementById("menu-icon");

  mobileMenuButton.addEventListener("click", function () {
    mobileNav.classList.toggle("mobile-nav--active");
    menuIcon.classList.toggle("menu-open");

    document.body.classList.toggle("no-scroll");
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

  function handleScroll() {
    const sections = [
      {
        image: document.getElementById("image-1"),
        content: document.getElementById("content-1"),
      },
      //   {
      //     image: document.getElementById("image-2"),
      //     content: document.getElementById("content-2"),
      //   },
      //   {
      //     image: document.getElementById("image-3"),
      //     content: document.getElementById("content-3"),
      //   },
    ];
    const scrollY = window.scrollY;
    const windowHeight = window.innerHeight;

    sections.forEach((section, index) => {
      const sectionTop = index * windowHeight;
      const sectionBottom = (index + 1) * windowHeight;
      const isVisible = scrollY >= sectionTop && scrollY < sectionBottom;

      if (isVisible) {
        section.content.classList.remove("hidden");
        section.image.style.transform = `scale(${1 - ((scrollY - sectionTop) / windowHeight) * 0.15
          })`;
      } else {
        section.content.classList.add("hidden");
      }
    });
  }

  window.addEventListener("scroll", handleScroll);

  function createCarousel(carouselId, prevButtonId, nextButtonId) {
    const carousel = document.getElementById(carouselId);
    const prevButton = document.getElementById(prevButtonId);
    const nextButton = document.getElementById(nextButtonId);

    let currentIndex = 0; // Current index of the carousel
    const totalItems = carousel.children.length; // Total number of items in the carousel

    // Function to determine how many items should be shown based on screen width
    function getItemsPerView() {
      return window.innerWidth >= 768 ? 3 : 1; // Show 3 items on desktop, 1 item on mobile
    }

    function updateCarousel() {
      const itemWidth = carousel.children[0].getBoundingClientRect().width;
      const offset = -(currentIndex * itemWidth);
      carousel.style.transform = `translateX(${offset}px)`;
    }

    // Event listener for the "previous" button
    prevButton.addEventListener("click", () => {
      currentIndex =
        currentIndex > 0 ? currentIndex - 1 : totalItems - getItemsPerView();
      if (currentIndex < 0) currentIndex = 0;
      updateCarousel();
    });

    // Event listener for the "next" button
    nextButton.addEventListener("click", () => {
      currentIndex = currentIndex + 1 < totalItems ? currentIndex + 1 : 0;
      updateCarousel();
    });

    window.addEventListener("resize", () => {
      updateCarousel();
    });

    updateCarousel();
  }

  // Initialize two carousels with the same code logic
  createCarousel("carousel1", "prevButton1", "nextButton1");
  createCarousel("carousel2", "prevButton2", "nextButton2");
});

/**
 * DOM Helpers
 * Contains utility functions for DOM manipulation
 */

/**
 * Checks if the current page matches a specific page type
 * @param {string} pageType - The page type to check for
 * @param {Function} callback - Function to execute if on matching page
 */
export function onCurrentPage(pageType, callback) {
  const body = document.body;
  if (body && body.classList.contains(`page-${pageType}`)) {
    callback();
  }
}

/**
 * Adds smooth scrolling behavior to anchor links
 * @param {string} selector - Selector for anchor links (default: 'a[href^="#"]')
 */
export function initSmoothScrolling(selector = 'a[href^="#"]') {
  document.querySelectorAll(selector).forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
        });
      }
    });
  });
}

/**
 * Makes card elements clickable (clicking anywhere on the card activates its main link)
 * @param {string} cardSelector - Selector for card elements
 */
export function makeCardsClickable(cardSelector = '.product-card, .team-card, .segment-card') {
  const cards = document.querySelectorAll(cardSelector);
  
  cards.forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', (event) => {
      const link = card.querySelector('a');
      if (link && !event.target.closest('a')) {
        link.click();
      }
    });
    
    // Prevent double click on links inside cards
    const links = card.querySelectorAll('a');
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.stopPropagation();
      });
    });
  });
}

/**
 * Initializes lazy loading for images
 * @param {string} selector - Selector for lazy-loaded images
 */
export function initLazyLoading(selector = 'img.lazy') {
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const image = entry.target;
          image.src = image.dataset.src;
          image.classList.remove("lazy");
          imageObserver.unobserve(image);
        }
      });
    });

    document.querySelectorAll(selector).forEach((img) => {
      imageObserver.observe(img);
    });
  } else {
    // Fallback for browsers that don't support IntersectionObserver
    document.querySelectorAll(selector).forEach((img) => {
      img.src = img.dataset.src;
      img.classList.remove("lazy");
    });
  }
} 
/**
 * Mobile menu implementation
 * Uses inline handler for primary toggle functionality
 * and JavaScript for accessibility and enhanced behaviors
 */
document.addEventListener('DOMContentLoaded', function() {
  var menuButton = document.querySelector('.mobile-menu-toggle');
  var mainNav = document.querySelector('.main-nav');
  
  if (menuButton && mainNav) {
    // Update ARIA attributes when menu state changes
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'class') {
          var isOpen = mainNav.classList.contains('open');
          menuButton.setAttribute('aria-expanded', isOpen.toString());
        }
      });
    });
    
    // Start observing the nav element for class changes
    observer.observe(mainNav, { attributes: true });
    
    // Set initial ARIA attributes
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-controls', 'main-nav-menu');
    mainNav.setAttribute('id', 'main-nav-menu');
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (mainNav.classList.contains('open') && 
          !event.target.closest('.mobile-menu-toggle') && 
          !event.target.closest('.main-nav')) {
        menuButton.click(); // Use the inline handler by triggering a click
      }
    });
    
    // Close menu when ESC key is pressed
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && mainNav.classList.contains('open')) {
        menuButton.click();
      }
    });
    
    // Close menu when window is resized to desktop width
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768 && mainNav.classList.contains('open')) {
        menuButton.click();
      }
    });
  }
}); 
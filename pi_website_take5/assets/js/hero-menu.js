/**
 * Hero Menu Handler
 * 
 * Handles the functionality for the minimal hero menu:
 * - Toggle menu visibility on mobile
 * - Accessibility features
 * - Close menu on outside click
 * - Support for RTL/LTR languages
 */

document.addEventListener('DOMContentLoaded', function() {
  // Get elements
  const heroMenuToggle = document.querySelector('.hero-menu-toggle');
  const heroNav = document.querySelector('.hero-nav');
  
  // Exit if elements don't exist
  if (!heroMenuToggle || !heroNav) {
    return;
  }
  
  // ARIA setup
  heroMenuToggle.setAttribute('aria-expanded', 'false');
  heroMenuToggle.setAttribute('aria-controls', 'hero-nav-menu');
  heroNav.setAttribute('id', 'hero-nav-menu');
  
  // Toggle menu function
  function toggleHeroMenu(e) {
    if (e) {
      e.preventDefault();
    }
    
    const isOpen = heroNav.classList.contains('visible');
    
    // Toggle states
    heroMenuToggle.classList.toggle('active');
    heroNav.classList.toggle('visible');
    
    // Update ARIA
    heroMenuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
  }
  
  // Add click handler to toggle button
  heroMenuToggle.addEventListener('click', toggleHeroMenu);
  
  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    const isOpen = heroNav.classList.contains('visible');
    
    if (isOpen && 
        !event.target.closest('.hero-menu-toggle') && 
        !event.target.closest('.hero-nav')) {
      toggleHeroMenu();
    }
  });
  
  // Close menu when ESC key is pressed
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && 
        heroNav.classList.contains('visible')) {
      toggleHeroMenu();
    }
  });
  
  // Add accessibility navigation to menu items
  const menuLinks = heroNav.querySelectorAll('a');
  menuLinks.forEach((link, index) => {
    // Allow keyboard navigation
    link.addEventListener('keydown', function(e) {
      // Handle last item to close menu on tab
      if (e.key === 'Tab' && index === menuLinks.length - 1 && !e.shiftKey) {
        setTimeout(() => {
          if (heroNav.classList.contains('visible')) {
            toggleHeroMenu();
          }
        }, 0);
      }
    });
  });
  
  // Handle hash changes for smooth scrolling to sections
  const navLinks = heroNav.querySelectorAll('a[href^="#"]');
  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Close menu if it's open
      if (heroNav.classList.contains('visible')) {
        toggleHeroMenu();
      }
    });
  });
}); 
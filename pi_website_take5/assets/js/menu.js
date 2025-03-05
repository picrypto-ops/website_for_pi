/**
 * Consolidated Menu Handler
 * 
 * This file combines all menu-related functionality:
 * - Mobile menu toggle
 * - Accessibility features
 * - Menu closing behaviors
 * - Responsive handling
 */

document.addEventListener('DOMContentLoaded', function() {
  // Get elements
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  
  if (!menuToggle || !mainNav) {
    console.error('Critical elements not found for menu');
    return;
  }
  
  // Initialize menu state based on screen size
  function initMenuState() {
    if (window.innerWidth <= 768) {
      // Mobile view - hide menu
      mainNav.style.maxHeight = '0px';
      mainNav.style.overflow = 'hidden';
      mainNav.classList.remove('open');
      menuToggle.classList.remove('active');
      menuToggle.setAttribute('aria-expanded', 'false');
    } else {
      // Desktop view - show menu
      mainNav.style.maxHeight = '';
      mainNav.style.overflow = '';
      mainNav.classList.remove('open'); // Ensure mobile classes are removed
      document.body.classList.remove('menu-open');
    }
  }
  
  // Initial setup
  initMenuState();
  
  // Set ARIA attributes
  menuToggle.setAttribute('aria-controls', 'main-nav-menu');
  mainNav.setAttribute('id', 'main-nav-menu');
  
  // Toggle menu function
  function toggleMenu(e) {
    if (e) {
      e.preventDefault();
    }
    
    // Only handle toggle for mobile devices
    if (window.innerWidth > 768) {
      return;
    }
    
    const isOpen = mainNav.classList.contains('open');
    
    // Toggle states
    menuToggle.classList.toggle('active');
    mainNav.classList.toggle('open');
    document.body.classList.toggle('menu-open');
    
    // Explicitly set position of toggle button in active state
    if (!isOpen) {
      // When opening menu, fix the toggle button position based on direction
      const isRTL = document.documentElement.dir === 'rtl';
      
      // Set fixed position at the top
      menuToggle.style.position = 'fixed';
      menuToggle.style.top = '25px';
      menuToggle.style.zIndex = '2000';
      
      // Set left/right based on direction
      if (isRTL) {
        // For RTL (Hebrew) - position on the right
        menuToggle.style.right = '15px';
        menuToggle.style.left = 'auto';
      } else {
        // For LTR (English) - position on the left
        menuToggle.style.left = '15px';
        menuToggle.style.right = 'auto';
      }
    } else {
      // When closing, reset inline styles
      menuToggle.style.position = '';
      menuToggle.style.top = '';
      menuToggle.style.left = '';
      menuToggle.style.right = '';
      menuToggle.style.zIndex = '';
    }
    
    // Update ARIA
    menuToggle.setAttribute('aria-expanded', (!isOpen).toString());
    
    // Handle height animation for mobile only
    if (!isOpen) {
      mainNav.style.maxHeight = '500px';
    } else {
      mainNav.style.maxHeight = '0px';
    }
  }
  
  // Add click handler to toggle button
  menuToggle.addEventListener('click', toggleMenu);
  
  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    if (window.innerWidth <= 768 && 
        mainNav.classList.contains('open') && 
        !event.target.closest('.mobile-menu-toggle') && 
        !event.target.closest('.main-nav')) {
      toggleMenu();
    }
  });
  
  // Close menu when ESC key is pressed
  document.addEventListener('keydown', function(event) {
    if (window.innerWidth <= 768 && 
        event.key === 'Escape' && 
        mainNav.classList.contains('open')) {
      toggleMenu();
    }
  });
  
  // Handle window resize
  window.addEventListener('resize', function() {
    initMenuState();
  });
  
  console.log('Menu handlers initialized');
}); 
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
    return;
  }
  
  // Initialize menu state based on screen size
  function initMenuState() {
    // Don't override menu state if a toggle is in progress
    if (window.isMenuToggleInProgress) {
      return;
    }
    
    if (window.innerWidth <= 767) {
      // Mobile view - hide menu
      mainNav.style.maxHeight = '0px';
      mainNav.style.overflow = 'hidden';
      mainNav.style.opacity = '0';
      mainNav.style.visibility = 'hidden';
      mainNav.classList.remove('open');
      menuToggle.classList.remove('active');
      menuToggle.setAttribute('aria-expanded', 'false');
    } else {
      // Desktop view - show menu
      mainNav.style.maxHeight = '';
      mainNav.style.overflow = '';
      mainNav.style.opacity = '';
      mainNav.style.visibility = '';
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
    
    // Only handle toggle for mobile devices - adjust breakpoint to match CSS
    if (window.innerWidth > 767) {
      return;
    }
    
    // Prevent multiple rapid toggles
    if (window.isMenuToggleInProgress) {
      return;
    }
    
    // Set flag to prevent multiple toggles
    window.isMenuToggleInProgress = true;
    
    const isOpen = mainNav.classList.contains('open');
    
    // Toggle states
    menuToggle.classList.toggle('active');
    document.body.classList.toggle('menu-open');
    
    // Explicitly set position of toggle button in active state
    if (!isOpen) {
      // When opening menu, fix the toggle button position based on direction
      const isRTL = document.documentElement.dir === 'rtl';
      
      // Add open class immediately
      mainNav.classList.add('open');
      
      // Set fixed position at the top
      menuToggle.style.position = 'fixed';
      menuToggle.style.top = '25px';
      menuToggle.style.zIndex = '10000'; // Higher z-index to ensure button is visible
      
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
      
      // Handle height animation for mobile only - show immediately
      mainNav.style.visibility = 'visible';
      mainNav.style.opacity = '1';
      mainNav.style.maxHeight = '100vh';
      mainNav.style.zIndex = '9999'; // Ensure menu is on top when visible
      
      // Update ARIA
      menuToggle.setAttribute('aria-expanded', 'true');
      
      // Allow new toggles after animation completes
      setTimeout(function() {
        window.isMenuToggleInProgress = false;
      }, 500);
    } else {
      // For closing, start animation then remove class at the end
      
      // Start closing animation
      mainNav.style.maxHeight = '0px';
      mainNav.style.opacity = '0';
      
      // Wait for animation to complete before removing classes and visibility
      setTimeout(function() {
        // When closing, reset inline styles
        menuToggle.style.position = '';
        menuToggle.style.top = '';
        menuToggle.style.left = '';
        menuToggle.style.right = '';
        menuToggle.style.zIndex = '';
        
        // Remove class after animation completes
        mainNav.classList.remove('open');
        
        // Finally hide the menu
        mainNav.style.visibility = 'hidden';
        mainNav.style.zIndex = '-1'; // Move it behind other elements
        
        // Update ARIA
        menuToggle.setAttribute('aria-expanded', 'false');
        
        // Allow new toggles
        window.isMenuToggleInProgress = false;
      }, 400); // Just before the transition completes
    }
  }
  
  // Add click handler to toggle button
  menuToggle.addEventListener('click', function(e) {
    toggleMenu(e);
  });
  
  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    if (window.innerWidth <= 767 && 
        mainNav.classList.contains('open') && 
        !event.target.closest('.mobile-menu-toggle') && 
        !event.target.closest('.main-nav')) {
      toggleMenu();
    }
  });
  
  // Close menu when ESC key is pressed
  document.addEventListener('keydown', function(event) {
    if (window.innerWidth <= 767 && 
        event.key === 'Escape' && 
        mainNav.classList.contains('open')) {
      toggleMenu();
    }
  });
  
  // Track window width to detect threshold crossing
  let previousWindowWidth = window.innerWidth;
  
  // Handle window resize with debounce to avoid excessive calls
  let resizeTimer;
  window.addEventListener('resize', function() {
    // Clear previous timeout
    clearTimeout(resizeTimer);
    
    // Set a new timeout to execute the function after 250ms
    resizeTimer = setTimeout(function() {
      const currentWindowWidth = window.innerWidth;
      
      // Only reset menu if we cross the mobile/desktop threshold (767px)
      const wasMobile = previousWindowWidth <= 767;
      const isMobile = currentWindowWidth <= 767;
      
      // Only reinitialize if we've crossed the threshold
      if (wasMobile !== isMobile) {
        initMenuState();
      }
      
      // Update previous window width
      previousWindowWidth = currentWindowWidth;
    }, 250);
  });
}); 
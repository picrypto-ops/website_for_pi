// Create a debug version to confirm JS is running
document.addEventListener('DOMContentLoaded', function() {
  console.log('Mobile menu script loaded');
  
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  
  console.log('Menu toggle button:', menuToggle);
  console.log('Main navigation:', mainNav);
  
  if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', function(e) {
      console.log('Menu button clicked');
      e.stopPropagation();
      
      this.classList.toggle('active');
      mainNav.classList.toggle('open');
      document.body.classList.toggle('menu-open');
      
      console.log('Menu open state:', mainNav.classList.contains('open'));
      
      const expanded = mainNav.classList.contains('open');
      this.setAttribute('aria-expanded', expanded.toString());
    });
    
    // Rest of the code remains the same...
  }
}); 
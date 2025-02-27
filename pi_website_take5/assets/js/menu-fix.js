// Direct hamburger menu fix
window.onload = function() {
  console.log('Direct menu fix loaded');
  
  // Get elements
  var menuButton = document.querySelector('.mobile-menu-toggle');
  var mainNav = document.querySelector('.main-nav');
  
  if (!menuButton || !mainNav) {
    console.error('Critical elements not found for menu');
    return;
  }
  
  // Explicitly set starting conditions
  mainNav.style.maxHeight = '0px';
  mainNav.style.overflow = 'hidden';
  
  // Direct toggle function
  function toggleMenuDirect() {
    console.log('Direct toggle called');
    if (mainNav.style.maxHeight === '0px' || !mainNav.classList.contains('open')) {
      mainNav.style.maxHeight = '500px';
      mainNav.classList.add('open');
      menuButton.classList.add('active');
    } else {
      mainNav.style.maxHeight = '0px';
      mainNav.classList.remove('open');
      menuButton.classList.remove('active');
    }
  }
  
  // Add click handler
  menuButton.onclick = toggleMenuDirect;
  
  // Also try direct DOM 0 event
  menuButton.onclick = function(e) {
    e.preventDefault();
    toggleMenuDirect();
    return false;
  };
  
  // Also try adding event listener for good measure
  menuButton.addEventListener('click', function(e) {
    e.preventDefault();
    toggleMenuDirect();
  });
  
  console.log('Click handlers added directly to button');
}; 
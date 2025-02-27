<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 
<script src="assets/js/mobile-menu.js" defer></script>

<script>
// Add enhanced RTL support for mobile menu
document.addEventListener('DOMContentLoaded', function() {
  const isRTL = document.documentElement.dir === 'rtl';
  
  // Apply RTL-specific adjustments
  if (isRTL) {
    // Ensure mobile menu toggle button position is correct
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    if (menuToggle) {
      menuToggle.classList.add('rtl-position');
    }
    
    // Ensure language switcher is positioned correctly
    const langSwitcher = document.querySelector('.language-switcher');
    if (langSwitcher) {
      langSwitcher.classList.add('rtl-position');
    }
  }
  
  // Properly handle menu toggle regardless of direction
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  if (menuToggle) {
    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      this.classList.toggle('active');
      document.querySelector('.main-nav').classList.toggle('open');
      document.body.classList.toggle('menu-open');
    });
  }
});
</script> 
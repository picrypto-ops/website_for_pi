/**
 * Language switcher handler
 * Preserves page position when switching languages
 */
document.addEventListener('DOMContentLoaded', function() {
  const langSwitchers = document.querySelectorAll('.lang-switch');
  
  // Store current URL including hash
  function getCurrentUrlWithHash() {
    return window.location.pathname + window.location.search + window.location.hash;
  }
  
  // Store both scroll position and URL sections
  function storeCurrentPosition() {
    const scrollPos = window.scrollY || document.documentElement.scrollTop;
    const currentUrl = getCurrentUrlWithHash();
    const sections = document.querySelectorAll('section[id], div[id]');
    
    // Find which section is currently in view
    let activeSection = null;
    let minDistance = Infinity;
    
    sections.forEach(section => {
      const rect = section.getBoundingClientRect();
      const distanceFromTop = Math.abs(rect.top);
      
      if (distanceFromTop < minDistance) {
        minDistance = distanceFromTop;
        activeSection = section.id;
      }
    });
    
    // Store all the information
    sessionStorage.setItem('scrollData', JSON.stringify({
      scrollPos,
      currentUrl,
      activeSection,
      timestamp: Date.now()
    }));
  }
  
  langSwitchers.forEach(switcher => {
    switcher.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Store position information before navigation
      storeCurrentPosition();
      
      // Navigate to the new URL
      window.location.href = this.getAttribute('href');
    });
  });
  
  // Restore scroll position on page load
  function restoreScrollPosition() {
    const scrollDataStr = sessionStorage.getItem('scrollData');
    
    if (!scrollDataStr) return;
    
    try {
      const scrollData = JSON.parse(scrollDataStr);
      
      // Only restore if it's a recent navigation (within 10 seconds)
      if (Date.now() - scrollData.timestamp > 10000) {
        sessionStorage.removeItem('scrollData');
        return;
      }
      
      // Try to scroll to section first if available
      if (scrollData.activeSection) {
        const section = document.getElementById(scrollData.activeSection);
        if (section) {
          section.scrollIntoView({ behavior: 'instant' });
          sessionStorage.removeItem('scrollData');
          return;
        }
      }
      
      // Fall back to scroll position
      window.scrollTo(0, scrollData.scrollPos);
      
      // Remove the stored data
      sessionStorage.removeItem('scrollData');
    } catch (e) {
      console.error('Error restoring scroll position:', e);
      sessionStorage.removeItem('scrollData');
    }
  }
  
  // Use both DOMContentLoaded and window.onload for more reliable restoration
  if (document.readyState === 'complete') {
    setTimeout(restoreScrollPosition, 100);
  } else {
    window.addEventListener('load', function() {
      setTimeout(restoreScrollPosition, 100);
    });
  }
}); 
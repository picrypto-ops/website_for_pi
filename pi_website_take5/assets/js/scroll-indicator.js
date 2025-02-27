/**
 * Scroll Indicator Handler
 * Shows only the scroll indicator for the currently visible section
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all scroll indicators
    const scrollIndicators = document.querySelectorAll('.scroll-indicator');
    
    // Function to determine which section is currently most visible
    function getCurrentSection() {
        const sections = document.querySelectorAll('.full-page-section, .hero');
        let maxVisibleSection = null;
        let maxVisibleAmount = 0;
        const viewportHeight = window.innerHeight;
        const scrollPosition = window.scrollY;
        
        sections.forEach(section => {
            if (!section.id) return; // Skip sections without IDs
            
            const rect = section.getBoundingClientRect();
            const sectionTop = rect.top + scrollPosition;
            const sectionHeight = rect.height;
            
            // Calculate visibility percentage
            const visibleTop = Math.max(scrollPosition, sectionTop);
            const visibleBottom = Math.min(scrollPosition + viewportHeight, sectionTop + sectionHeight);
            const visibleHeight = Math.max(0, visibleBottom - visibleTop);
            const visibilityPercentage = visibleHeight / viewportHeight;
            
            // If this section has more visible area than previously found max
            if (visibilityPercentage > maxVisibleAmount) {
                maxVisibleAmount = visibilityPercentage;
                maxVisibleSection = section.id;
            }
        });
        
        return maxVisibleSection;
    }
    
    // Function to update scroll indicator visibility
    function updateScrollIndicators() {
        const currentSection = getCurrentSection();
        
        // Hide all scroll indicators first
        scrollIndicators.forEach(indicator => {
            indicator.classList.remove('is-visible');
            indicator.style.opacity = '0';
            indicator.style.pointerEvents = 'none';
            
            // If we're on contact-us, completely hide all indicators
            if (currentSection === 'contact-us') {
                indicator.style.display = 'none';
            } else {
                indicator.style.display = 'block';
            }
        });
        
        // Show indicator only if current section is not contact-us and is valid
        if (currentSection && currentSection !== 'contact-us') {
            const currentIndicator = document.querySelector(`.scroll-indicator[data-parent-section="${currentSection}"]`);
            if (currentIndicator) {
                currentIndicator.classList.add('is-visible');
                currentIndicator.style.opacity = '1';
                currentIndicator.style.pointerEvents = 'auto';
            }
        }
    }
    
    // Throttle function to limit how often a function runs
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // Initial hide for all indicators, then show as needed
    scrollIndicators.forEach(indicator => {
        indicator.style.opacity = '0';
    });
    
    // Update on scroll with throttling
    window.addEventListener('scroll', throttle(updateScrollIndicators, 100));
    
    // Also update on window resize
    window.addEventListener('resize', throttle(updateScrollIndicators, 100));
    
    // Make sure we run this after the page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(updateScrollIndicators, 100);
    });
    
    // Update when clicking on any link
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function() {
            // Short delay to let the page scroll
            setTimeout(updateScrollIndicators, 500);
        });
    });
}); 
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the PI background wave
    if (typeof initPiBgWave === 'function') {
        initPiBgWave();
    }
    
    // Show header on scroll past the hero section
    const header = document.getElementById('site-header');
    const heroSection = document.querySelector('.hero.full-page');
    
    if (header && heroSection) {
        window.addEventListener('scroll', function() {
            if (window.scrollY >= heroSection.offsetHeight - 100) {
                header.classList.remove('initially-hidden');
            } else {
                header.classList.add('initially-hidden');
            }
        });
    }

    // Get header height for scroll calculations
    const headerHeight = header ? header.offsetHeight : 0;
    
    // Better handling of scroll indicators
    const updateScrollIndicators = function() {
        const sections = document.querySelectorAll('.full-page-section, .hero.full-page');
        const windowHeight = window.innerHeight;
        const scrollY = window.scrollY;
        const scrollIndicator = document.querySelector('.scroll-indicator');
        
        // Find current section
        let currentSectionIndex = -1;
        sections.forEach((section, index) => {
            const rect = section.getBoundingClientRect();
            // If section is more than 50% visible
            if (rect.top <= windowHeight/2 && rect.bottom >= windowHeight/2) {
                currentSectionIndex = index;
            }
        });
        
        // Show/hide scroll indicator based on position
        if (scrollIndicator) {
            // Hide on last section or when scrolled to bottom
            if (currentSectionIndex === sections.length - 1 || 
                (window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
                scrollIndicator.style.opacity = '0';
                scrollIndicator.style.pointerEvents = 'none';
            } else {
                scrollIndicator.style.opacity = '1';
                scrollIndicator.style.pointerEvents = 'auto';
                
                // Update scroll target to next section
                if (currentSectionIndex >= 0 && currentSectionIndex < sections.length - 1) {
                    const nextSection = sections[currentSectionIndex + 1];
                    const nextSectionId = nextSection.id;
                    if (nextSectionId) {
                        const scrollButton = scrollIndicator.querySelector('.scroll-down');
                        if (scrollButton) {
                            scrollButton.setAttribute('href', '#' + nextSectionId);
                        }
                    }
                }
            }
        }
    };
    
    // Initial call
    updateScrollIndicators();
    
    // Call on scroll
    window.addEventListener('scroll', updateScrollIndicators);
    
    // Improve smooth scrolling with header offset
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            if (targetId) {
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Calculate position with header offset
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    
                    // Smooth scroll with header offset
                    window.scrollTo({
                        top: targetPosition - headerHeight,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without scrolling
                    history.pushState(null, null, '#' + targetId);
                }
            }
        });
    });
    
    // Make scroll buttons more noticeable with animation
    document.querySelectorAll('.scroll-down').forEach(button => {
        button.classList.add('animated');
    });
    
    // Add accessible screen reader text
    document.querySelectorAll('.scroll-down').forEach(button => {
        if (!button.querySelector('.sr-only')) {
            const srSpan = document.createElement('span');
            srSpan.className = 'sr-only';
            srSpan.textContent = 'Scroll Down';
            button.prepend(srSpan);
        }
    });
}); 
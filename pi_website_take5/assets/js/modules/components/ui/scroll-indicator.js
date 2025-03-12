/**
 * Scroll Indicator Handler
 * Shows only the scroll indicator for the currently visible section
 */

class ScrollIndicator {
    constructor() {
        this.scrollIndicators = null;
        this.isInitialized = false;
    }

    /**
     * Determine which section is currently most visible in the viewport
     * @private
     * @return {string|null} - ID of the most visible section
     */
    _getCurrentSection() {
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
    
    /**
     * Update scroll indicator visibility based on current section
     * @private
     */
    _updateScrollIndicators = () => {
        const currentSection = this._getCurrentSection();
        
        // Hide all scroll indicators first
        this.scrollIndicators.forEach(indicator => {
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
            // Find the indicator for the current section
            let currentIndicator = document.querySelector(`.scroll-indicator[data-parent-section="${currentSection}"]`);
            
            // If no exact match, check if it's a segment section
            if (!currentIndicator && currentSection.startsWith('segment-')) {
                // Try to find a generic segment indicator
                currentIndicator = document.querySelector(`.scroll-indicator[data-parent-section="${currentSection}"]`);
            }
            
            if (currentIndicator) {
                currentIndicator.classList.add('is-visible');
                currentIndicator.style.opacity = '1';
                currentIndicator.style.pointerEvents = 'auto';
            }
        }
    }
    
    /**
     * Throttle function to limit how often a function runs
     * @private
     * @param {Function} func - The function to throttle
     * @param {number} limit - Time limit in milliseconds
     * @return {Function} - Throttled function
     */
    _throttle(func, limit) {
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
    
    /**
     * Scroll to a target element with additional offset to ensure it's fully visible
     * @private
     * @param {HTMLElement} targetElement - The element to scroll to
     */
    _scrollToElement(targetElement) {
        if (!targetElement) return;
        
        // Get the element's position relative to the document
        const targetRect = targetElement.getBoundingClientRect();
        const targetPosition = window.scrollY + targetRect.top;
        
        // Add a small offset to ensure the element is fully visible
        const offset = 20; // Pixels to offset
        
        // Scroll to the target position
        window.scrollTo({
            top: targetPosition - offset,
            behavior: 'smooth'
        });
    }
    
    /**
     * Initialize the scroll indicator functionality
     * @param {Object} options - Configuration options
     * @return {ScrollIndicator} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        // Get all scroll indicators
        this.scrollIndicators = document.querySelectorAll('.scroll-indicator');
        
        if (!this.scrollIndicators || this.scrollIndicators.length === 0) {
            console.warn('No scroll indicators found on the page');
            return this;
        }
        
        // Add click event listeners to all scroll indicators
        this.scrollIndicators.forEach(indicator => {
            const link = indicator.querySelector('a.scroll-down');
            if (link) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('data-target');
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        // Use improved scroll method instead of scrollIntoView
                        this._scrollToElement(targetElement);
                        
                        // Update indicators after scrolling
                        setTimeout(this._updateScrollIndicators, 1000);
                    } else if (targetId === 'page-bottom') {
                        // Special case: scroll to the bottom of the page
                        const pageHeight = document.body.scrollHeight || document.documentElement.scrollHeight;
                        window.scrollTo({
                            top: pageHeight,
                            behavior: 'smooth'
                        });
                        
                        // Update indicators after scrolling
                        setTimeout(this._updateScrollIndicators, 1000);
                    }
                });
            }
        });
        
        // Initial hide for all indicators, then show as needed
        this.scrollIndicators.forEach(indicator => {
            indicator.style.opacity = '0';
        });
        
        // Create throttled update function
        const throttledUpdate = this._throttle(this._updateScrollIndicators, 100);
        
        // Update on scroll with throttling
        window.addEventListener('scroll', throttledUpdate);
        
        // Also update on window resize
        window.addEventListener('resize', throttledUpdate);
        
        // Make sure we run this after the page is fully loaded
        window.addEventListener('load', () => {
            setTimeout(this._updateScrollIndicators, 100);
        });
        
        // Update when clicking on any link
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', () => {
                // Short delay to let the page scroll
                setTimeout(this._updateScrollIndicators, 500);
            });
        });
        
        // Initial update
        setTimeout(this._updateScrollIndicators, 100);
        
        this.isInitialized = true;
        return this;
    }
}

export default ScrollIndicator; 
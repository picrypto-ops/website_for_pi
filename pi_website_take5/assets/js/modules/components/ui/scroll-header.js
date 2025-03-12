/**
 * Scroll Header
 * Manages header visibility when scrolling on the home page
 */

class ScrollHeader {
    constructor() {
        this.isInitialized = false;
        this.header = null;
        this.heroSection = null;
        this.scrollThreshold = 100; // Pixels from hero bottom to trigger header
    }

    /**
     * Handle scroll events to show/hide header
     * @private
     */
    _handleScroll = () => {
        if (!this.header || !this.heroSection) return;
        
        // Show header when scrolled past hero section (minus threshold)
        if (window.scrollY >= this.heroSection.offsetHeight - this.scrollThreshold) {
            this.header.classList.remove('initially-hidden');
        } else {
            this.header.classList.add('initially-hidden');
        }
    }

    /**
     * Initialize the scroll header functionality
     * @param {Object} options - Configuration options
     * @param {string} options.headerSelector - Selector for the header element
     * @param {string} options.heroSelector - Selector for the hero section
     * @param {number} options.scrollThreshold - Pixels from hero bottom to trigger header
     * @return {ScrollHeader} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        const config = {
            headerSelector: '#site-header',
            heroSelector: '.hero.full-page',
            scrollThreshold: this.scrollThreshold,
            ...options
        };
        
        // Find elements
        this.header = document.querySelector(config.headerSelector);
        this.heroSection = document.querySelector(config.heroSelector);
        this.scrollThreshold = config.scrollThreshold;
        
        // Only initialize if we have both header and hero section
        if (this.header && this.heroSection) {
            // Set initial state based on current scroll position
            this._handleScroll();
            
            // Add scroll event listener
            window.addEventListener('scroll', this._handleScroll);
            
            this.isInitialized = true;
        } else {
            console.warn('ScrollHeader: Could not find header or hero section');
        }
        
        return this;
    }

    /**
     * Clean up event listeners
     */
    destroy() {
        if (!this.isInitialized) return;
        
        window.removeEventListener('scroll', this._handleScroll);
        this.isInitialized = false;
    }
}

export default ScrollHeader; 
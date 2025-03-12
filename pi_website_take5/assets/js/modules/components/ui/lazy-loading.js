/**
 * Lazy Loading
 * Handles lazy loading of images for better performance
 */

class LazyLoading {
    constructor() {
        this.isInitialized = false;
        this.imageObserver = null;
    }

    /**
     * Create and configure the IntersectionObserver
     * @private
     */
    _setupObserver() {
        if ('IntersectionObserver' in window) {
            this.imageObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        
                        // Only set src if data-src exists
                        if (image.dataset.src) {
                            image.src = image.dataset.src;
                        }
                        
                        // Handle srcset as well if present
                        if (image.dataset.srcset) {
                            image.srcset = image.dataset.srcset;
                        }
                        
                        image.classList.remove("lazy");
                        this.imageObserver.unobserve(image);
                    }
                });
            });
        }
    }

    /**
     * Apply lazy loading to images that match the selector
     * @param {string} selector - CSS selector for lazy loaded images
     * @private
     */
    _applyLazyLoading(selector) {
        const lazyImages = document.querySelectorAll(selector);
        
        if (lazyImages.length === 0) {
            console.warn(`No lazy-loaded images found with selector: ${selector}`);
            return;
        }

        if (this.imageObserver) {
            // Modern browsers: use IntersectionObserver
            lazyImages.forEach(img => {
                this.imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            lazyImages.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                if (img.dataset.srcset) {
                    img.srcset = img.dataset.srcset;
                }
                img.classList.remove("lazy");
            });
        }
    }

    /**
     * Initialize lazy loading for images
     * @param {Object} options - Configuration options
     * @param {string} options.selector - CSS selector for lazy-loaded images
     * @return {LazyLoading} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        const config = {
            selector: 'img.lazy',
            ...options
        };
        
        this._setupObserver();
        this._applyLazyLoading(config.selector);
        
        this.isInitialized = true;
        return this;
    }
}

export default LazyLoading; 
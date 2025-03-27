/**
 * Hero Menu
 * Handles the functionality for the minimal hero menu on the hero section
 */

class HeroMenu {
    constructor() {
        this.isInitialized = false;
        this.heroMenuToggle = null;
        this.heroNav = null;
    }

    /**
     * Toggle hero menu visibility
     * @private
     * @param {Event} e - The click event
     */
    _toggleHeroMenu = (e) => {
        if (e) {
            e.preventDefault();
        }
        
        const isOpen = this.heroNav.classList.contains('visible');
        
        // Toggle states
        this.heroMenuToggle.classList.toggle('active');
        this.heroNav.classList.toggle('visible');
        
        // Update ARIA
        this.heroMenuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
    }

    /**
     * Handle clicks outside the menu to close it
     * @private
     * @param {Event} event - The click event
     */
    _handleOutsideClick = (event) => {
        const isOpen = this.heroNav.classList.contains('visible');
        
        if (isOpen && 
            !event.target.closest('.hero-menu-toggle') && 
            !event.target.closest('.hero-nav')) {
            this._toggleHeroMenu();
        }
    }

    /**
     * Handle ESC key to close the menu
     * @private
     * @param {KeyboardEvent} event - The keyboard event
     */
    _handleEscKey = (event) => {
        if (event.key === 'Escape' && 
            this.heroNav.classList.contains('visible')) {
            this._toggleHeroMenu();
        }
    }

    /**
     * Add accessibility navigation to menu items
     * @private
     */
    _setupAccessibility() {
        const menuLinks = this.heroNav.querySelectorAll('a');
        
        menuLinks.forEach((link, index) => {
            // Allow keyboard navigation
            link.addEventListener('keydown', (e) => {
                // Handle last item to close menu on tab
                if (e.key === 'Tab' && index === menuLinks.length - 1 && !e.shiftKey) {
                    setTimeout(() => {
                        if (this.heroNav.classList.contains('visible')) {
                            this._toggleHeroMenu();
                        }
                    }, 0);
                }
            });
        });
    }

    /**
     * Set up smooth scrolling for navigation links
     * @private
     */
    _setupSmoothScrolling() {
        const navLinks = this.heroNav.querySelectorAll('a[href^="#"]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Close menu if it's open
                if (this.heroNav.classList.contains('visible')) {
                    this._toggleHeroMenu();
                }
            });
        });
    }

    /**
     * Initialize hero menu
     * @param {Object} options - Configuration options
     * @param {string} options.menuToggleSelector - Selector for menu toggle button
     * @param {string} options.navSelector - Selector for hero navigation
     * @return {HeroMenu} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        const config = {
            menuToggleSelector: '.hero-menu-toggle',
            navSelector: '.hero-nav',
            ...options
        };
        
        // Get elements
        this.heroMenuToggle = document.querySelector(config.menuToggleSelector);
        this.heroNav = document.querySelector(config.navSelector);
        
        // Exit if elements don't exist
        if (!this.heroMenuToggle || !this.heroNav) {
            console.warn('Hero menu elements not found. Hero menu not initialized.');
            return this;
        }
        
        // ARIA setup
        this.heroMenuToggle.setAttribute('aria-expanded', 'false');
        this.heroMenuToggle.setAttribute('aria-controls', 'hero-nav-menu');
        this.heroNav.setAttribute('id', 'hero-nav-menu');
        
        // Add click handler to toggle button
        this.heroMenuToggle.addEventListener('click', this._toggleHeroMenu);
        
        // Close menu when clicking outside
        document.addEventListener('click', this._handleOutsideClick);
        
        // Close menu when ESC key is pressed
        document.addEventListener('keydown', this._handleEscKey);
        
        // Setup additional functionality
        this._setupAccessibility();
        this._setupSmoothScrolling();
        
        this.isInitialized = true;
        return this;
    }

    /**
     * Clean up event listeners
     */
    destroy() {
        if (!this.isInitialized) return;
        
        // Remove event listeners
        this.heroMenuToggle.removeEventListener('click', this._toggleHeroMenu);
        document.removeEventListener('click', this._handleOutsideClick);
        document.removeEventListener('keydown', this._handleEscKey);
        
        // Remove link event listeners - would need more complex tracking of these
        
        this.isInitialized = false;
    }
}

export default HeroMenu; 
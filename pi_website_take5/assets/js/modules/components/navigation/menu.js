/**
 * Main Navigation Menu
 * Handles mobile menu toggle, accessibility features, and responsive behavior
 */

class NavigationMenu {
    constructor() {
        this.isInitialized = false;
        this.menuToggle = null;
        this.mainNav = null;
        this.isMenuToggleInProgress = false;
        this.previousWindowWidth = window.innerWidth;
        this.resizeTimer = null;
    }

    /**
     * Initialize menu state based on screen size
     * @private
     */
    _initMenuState() {
        // Don't override menu state if a toggle is in progress
        if (this.isMenuToggleInProgress) {
            return;
        }
        
        if (window.innerWidth <= 767) {
            // Mobile view - hide menu
            this.mainNav.style.maxHeight = '0px';
            this.mainNav.style.overflow = 'hidden';
            this.mainNav.style.opacity = '0';
            this.mainNav.style.visibility = 'hidden';
            this.mainNav.classList.remove('open');
            this.menuToggle.classList.remove('active');
            this.menuToggle.setAttribute('aria-expanded', 'false');
        } else {
            // Desktop view - show menu
            this.mainNav.style.maxHeight = '';
            this.mainNav.style.overflow = '';
            this.mainNav.style.opacity = '';
            this.mainNav.style.visibility = '';
            this.mainNav.classList.remove('open'); // Ensure mobile classes are removed
            document.body.classList.remove('menu-open');
        }
    }

    /**
     * Toggle menu function for mobile devices
     * @private
     * @param {Event} e - The click event
     */
    _toggleMenu = (e) => {
        if (e) {
            e.preventDefault();
        }
        
        // Only handle toggle for mobile devices - adjust breakpoint to match CSS
        if (window.innerWidth > 767) {
            return;
        }
        
        // Prevent multiple rapid toggles
        if (this.isMenuToggleInProgress) {
            return;
        }
        
        // Set flag to prevent multiple toggles
        this.isMenuToggleInProgress = true;
        
        const isOpen = this.mainNav.classList.contains('open');
        
        // Toggle states
        this.menuToggle.classList.toggle('active');
        document.body.classList.toggle('menu-open');
        
        // Explicitly set position of toggle button in active state
        if (!isOpen) {
            // When opening menu, fix the toggle button position based on direction
            const isRTL = document.documentElement.dir === 'rtl';
            
            // Add open class immediately
            this.mainNav.classList.add('open');
            
            // Set fixed position at the top
            this.menuToggle.style.position = 'fixed';
            this.menuToggle.style.top = '25px';
            this.menuToggle.style.zIndex = '10000'; // Higher z-index to ensure button is visible
            
            // Set left/right based on direction
            if (isRTL) {
                // For RTL (Hebrew) - position on the right
                this.menuToggle.style.right = '15px';
                this.menuToggle.style.left = 'auto';
            } else {
                // For LTR (English) - position on the left
                this.menuToggle.style.left = '15px';
                this.menuToggle.style.right = 'auto';
            }
            
            // Handle height animation for mobile only - show immediately
            this.mainNav.style.visibility = 'visible';
            this.mainNav.style.opacity = '1';
            this.mainNav.style.maxHeight = '100vh';
            this.mainNav.style.zIndex = '9999'; // Ensure menu is on top when visible
            
            // Update ARIA
            this.menuToggle.setAttribute('aria-expanded', 'true');
            
            // Allow new toggles after animation completes
            setTimeout(() => {
                this.isMenuToggleInProgress = false;
            }, 500);
        } else {
            // For closing, start animation then remove class at the end
            
            // Start closing animation
            this.mainNav.style.maxHeight = '0px';
            this.mainNav.style.opacity = '0';
            
            // Wait for animation to complete before removing classes and visibility
            setTimeout(() => {
                // When closing, reset inline styles
                this.menuToggle.style.position = '';
                this.menuToggle.style.top = '';
                this.menuToggle.style.left = '';
                this.menuToggle.style.right = '';
                this.menuToggle.style.zIndex = '';
                
                // Remove class after animation completes
                this.mainNav.classList.remove('open');
                
                // Finally hide the menu
                this.mainNav.style.visibility = 'hidden';
                this.mainNav.style.zIndex = '-1'; // Move it behind other elements
                
                // Update ARIA
                this.menuToggle.setAttribute('aria-expanded', 'false');
                
                // Allow new toggles
                this.isMenuToggleInProgress = false;
            }, 400); // Just before the transition completes
        }
    }

    /**
     * Handle clicks outside the menu to close it
     * @private
     * @param {Event} event - The click event
     */
    _handleOutsideClick = (event) => {
        if (window.innerWidth <= 767 && 
            this.mainNav.classList.contains('open') && 
            !event.target.closest('.mobile-menu-toggle') && 
            !event.target.closest('.main-nav')) {
            this._toggleMenu();
        }
    }

    /**
     * Handle ESC key to close the menu
     * @private
     * @param {KeyboardEvent} event - The keyboard event
     */
    _handleEscKey = (event) => {
        if (window.innerWidth <= 767 && 
            event.key === 'Escape' && 
            this.mainNav.classList.contains('open')) {
            this._toggleMenu();
        }
    }

    /**
     * Handle window resize and reinitialize menu state when crossing breakpoints
     * @private
     */
    _handleResize = () => {
        // Clear previous timeout
        clearTimeout(this.resizeTimer);
        
        // Set a new timeout to execute the function after 250ms
        this.resizeTimer = setTimeout(() => {
            const currentWindowWidth = window.innerWidth;
            
            // Only reset menu if we cross the mobile/desktop threshold (767px)
            const wasMobile = this.previousWindowWidth <= 767;
            const isMobile = currentWindowWidth <= 767;
            
            // Only reinitialize if we've crossed the threshold
            if (wasMobile !== isMobile) {
                this._initMenuState();
            }
            
            // Update previous window width
            this.previousWindowWidth = currentWindowWidth;
        }, 250);
    }

    /**
     * Initialize the navigation menu
     * @param {Object} options - Configuration options
     * @param {string} options.menuToggleSelector - Selector for menu toggle button
     * @param {string} options.navSelector - Selector for main navigation
     * @return {NavigationMenu} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        const config = {
            menuToggleSelector: '.mobile-menu-toggle',
            navSelector: '.main-nav',
            ...options
        };
        
        // Get elements
        this.menuToggle = document.querySelector(config.menuToggleSelector);
        this.mainNav = document.querySelector(config.navSelector);
        
        if (!this.menuToggle || !this.mainNav) {
            console.warn('Menu elements not found. Navigation menu not initialized.');
            return this;
        }
        
        // Set ARIA attributes
        this.menuToggle.setAttribute('aria-controls', 'main-nav-menu');
        this.mainNav.setAttribute('id', 'main-nav-menu');
        
        // Initial setup
        this._initMenuState();
        
        // Add click handler to toggle button
        this.menuToggle.addEventListener('click', this._toggleMenu);
        
        // Close menu when clicking outside
        document.addEventListener('click', this._handleOutsideClick);
        
        // Close menu when ESC key is pressed
        document.addEventListener('keydown', this._handleEscKey);
        
        // Handle window resize
        window.addEventListener('resize', this._handleResize);
        
        this.isInitialized = true;
        return this;
    }

    /**
     * Clean up event listeners
     */
    destroy() {
        if (!this.isInitialized) return;
        
        // Remove event listeners
        this.menuToggle.removeEventListener('click', this._toggleMenu);
        document.removeEventListener('click', this._handleOutsideClick);
        document.removeEventListener('keydown', this._handleEscKey);
        window.removeEventListener('resize', this._handleResize);
        
        // Clear timeout
        clearTimeout(this.resizeTimer);
        
        this.isInitialized = false;
    }
}

export default NavigationMenu; 
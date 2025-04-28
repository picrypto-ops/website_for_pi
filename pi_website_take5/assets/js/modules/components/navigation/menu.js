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

    // ... (keep _initMenuState, _toggleMenu, _handleOutsideClick, _handleEscKey, _handleResize methods as they were) ...

     /**
     * Initialize menu state based on screen size
     * @private
     */
     _initMenuState() {
        // Don't override menu state if a toggle is in progress
        if (this.isMenuToggleInProgress) {
            return;
        }

        const isMobile = window.innerWidth <= 767; // Breakpoint from variables.$mobile-breakpoint

        if (isMobile) {
            // Mobile view - ensure menu is closed initially unless already open
            if (!this.mainNav.classList.contains('open')) {
                this.mainNav.style.maxHeight = '0px';
                this.mainNav.style.overflow = 'hidden';
                this.mainNav.style.opacity = '0';
                this.mainNav.style.visibility = 'hidden';
                this.menuToggle.classList.remove('is-active'); // Use is-active helper class
                this.menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open'); // Ensure body class is removed
            }
        } else {
            // Desktop view - ensure menu is visible and styles are reset
            this.mainNav.style.maxHeight = '';
            this.mainNav.style.overflow = '';
            this.mainNav.style.opacity = '';
            this.mainNav.style.visibility = '';
            this.mainNav.classList.remove('open'); // Ensure mobile classes are removed
            this.menuToggle.classList.remove('is-active');
            document.body.classList.remove('menu-open');
            // Reset potential fixed positioning of toggle button
            this.menuToggle.style.position = '';
            this.menuToggle.style.top = '';
            this.menuToggle.style.left = '';
            this.menuToggle.style.right = '';
            this.menuToggle.style.zIndex = '';
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
            e.stopPropagation(); // Prevent click from bubbling up (e.g., to outside click handler)
        }

        // Only handle toggle for mobile devices - adjust breakpoint to match CSS
        if (window.innerWidth > 767) { // Breakpoint from variables.$mobile-breakpoint
            return;
        }

        if (this.isMenuToggleInProgress) {
            return;
        }
        this.isMenuToggleInProgress = true;

        const isOpen = this.mainNav.classList.contains('open');

        this.menuToggle.classList.toggle('is-active'); // Use helper class
        document.body.classList.toggle('menu-open');

        if (!isOpen) {
            // Opening menu
            this.mainNav.classList.add('open');
            // Set visibility and opacity first for transition
            this.mainNav.style.visibility = 'visible';
            this.mainNav.style.opacity = '1';
            // Use setTimeout to allow display change before starting height transition
            setTimeout(() => {
                this.mainNav.style.maxHeight = 'calc(100vh - ' + this.mainNav.offsetTop + 'px)'; // Calculate max height dynamically
            }, 10); // Small delay

            this.menuToggle.setAttribute('aria-expanded', 'true');

            // Use transitionend event for more reliable end detection
            this.mainNav.addEventListener('transitionend', () => {
                this.isMenuToggleInProgress = false;
            }, { once: true });

        } else {
            // Closing menu
            this.mainNav.style.maxHeight = '0px';
            this.mainNav.style.opacity = '0';

            // Wait for transition to complete before hiding
             this.mainNav.addEventListener('transitionend', () => {
                if (!this.mainNav.classList.contains('open')) { // Check if still closing
                    this.mainNav.style.visibility = 'hidden';
                    // Reset fixed position only if the header itself is not fixed (e.g., not home page scrolled)
                    if(!document.querySelector('.site-header.home-header.initially-hidden')){
                       this.menuToggle.style.position = '';
                       this.menuToggle.style.top = '';
                       this.menuToggle.style.left = '';
                       this.menuToggle.style.right = '';
                       this.menuToggle.style.zIndex = '';
                    }
                }
                this.isMenuToggleInProgress = false;
            }, { once: true });


            // Remove class slightly before transition ends to ensure styles are applied correctly
            // but only visually hide fully after transition
            setTimeout(() => {
                 this.mainNav.classList.remove('open');
                 this.menuToggle.setAttribute('aria-expanded', 'false');
            }, 350); // Adjust timing based on transition duration (400ms)


        }
    }

     /**
     * Handle clicks outside the menu to close it
     * @private
     * @param {Event} event - The click event
     */
     _handleOutsideClick = (event) => {
        const isMobile = window.innerWidth <= 767; // Breakpoint
        if (isMobile &&
            this.mainNav.classList.contains('open') &&
            !this.menuToggle.contains(event.target) && // Check if click was on toggle itself
            !this.mainNav.contains(event.target)) // Check if click was inside nav menu
        {
             // Check if toggle is in progress to prevent double toggling
             if (!this.isMenuToggleInProgress) {
                this._toggleMenu();
             }
        }
    }

    /**
     * Handle ESC key to close the menu
     * @private
     * @param {KeyboardEvent} event - The keyboard event
     */
    _handleEscKey = (event) => {
         const isMobile = window.innerWidth <= 767; // Breakpoint
        if (isMobile &&
            event.key === 'Escape' &&
            this.mainNav.classList.contains('open')) {
             // Check if toggle is in progress
            if (!this.isMenuToggleInProgress) {
                this._toggleMenu();
            }
        }
    }

    /**
     * Handle window resize and reinitialize menu state when crossing breakpoints
     * @private
     */
    _handleResize = () => {
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => {
            const currentWindowWidth = window.innerWidth;
            const wasMobile = this.previousWindowWidth <= 767; // Breakpoint
            const isMobile = currentWindowWidth <= 767; // Breakpoint

            if (wasMobile !== isMobile) {
                 // Reset menu state fully when crossing breakpoint
                 this.isMenuToggleInProgress = false; // Ensure toggle flag is reset
                this._initMenuState();
            }

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
            // --- *** THE FIX IS HERE *** ---
            menuToggleSelector: '.site-header__toggle', // Use the new BEM class
            navSelector: '.site-header__nav',         // Use the new BEM class
            // --- *********************** ---
            ...options
        };

        // Get elements
        this.menuToggle = document.querySelector(config.menuToggleSelector);
        this.mainNav = document.querySelector(config.navSelector);

        if (!this.menuToggle || !this.mainNav) {
            console.warn('Header menu elements not found with BEM selectors. Navigation menu not initialized.');
            return this;
        }

        // Set ARIA attributes - ensure nav ID matches aria-controls
        this.menuToggle.setAttribute('aria-controls', 'main-nav-menu'); // Keep existing ID for nav
        this.mainNav.setAttribute('id', 'main-nav-menu'); // Ensure nav has this ID

        // Initial setup based on current screen width
        this._initMenuState(); // Call initialization

        // Add event listeners
        this.menuToggle.addEventListener('click', this._toggleMenu);
        document.addEventListener('click', this._handleOutsideClick); // Use capture phase potentially? No, regular should be fine.
        document.addEventListener('keydown', this._handleEscKey);
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
        if (this.menuToggle) {
             this.menuToggle.removeEventListener('click', this._toggleMenu);
        }
        document.removeEventListener('click', this._handleOutsideClick);
        document.removeEventListener('keydown', this._handleEscKey);
        window.removeEventListener('resize', this._handleResize);

        // Clear timeout
        clearTimeout(this.resizeTimer);

        // Reset any inline styles potentially added by JS
        if(this.mainNav) {
            this.mainNav.style.maxHeight = '';
            this.mainNav.style.opacity = '';
            this.mainNav.style.visibility = '';
        }
       if(this.menuToggle) {
            this.menuToggle.style.position = '';
            this.menuToggle.style.top = '';
            this.menuToggle.style.left = '';
            this.menuToggle.style.right = '';
            this.menuToggle.style.zIndex = '';
       }


        this.isInitialized = false;
    }
}

export default NavigationMenu;
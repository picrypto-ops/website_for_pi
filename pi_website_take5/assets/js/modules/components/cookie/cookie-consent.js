/**
 * Cookie Consent
 * Handles the cookie consent floating box functionality
 */

class CookieConsent {
    constructor() {
        this.isInitialized = false;
        this.cookieName = 'pi_cookie_consent';
        this.expiryDays = 365;
        this.initDelay = 1000; // Delay in ms before showing banner
    }

    /**
     * Set a cookie with the given name, value and expiry days
     * @private
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} days - Expiry in days
     */
    _setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }
    
    /**
     * Get a cookie value by name
     * @private
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value or null if not found
     */
    _getCookie(name) {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    /**
     * Check if user has already given consent
     * @private
     * @returns {boolean} True if consent has been given
     */
    _hasConsent() {
        return this._getCookie(this.cookieName) === 'accepted';
    }
    
    /**
     * Show the cookie consent floating box
     * @private
     */
    _showBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.classList.add('active');
        }
    }
    
    /**
     * Hide the cookie consent floating box
     * @private
     */
    _hideBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.classList.remove('active');
            // Add a smooth fade-out effect
            banner.style.opacity = '0';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 500);
        }
    }
    
    /**
     * Accept cookies and hide the banner
     * @private
     */
    _acceptCookies = () => {
        this._setCookie(this.cookieName, 'accepted', this.expiryDays);
        this._hideBanner();
    }
    
    /**
     * Decline cookies and hide the banner
     * @private
     */
    _declineCookies = () => {
        this._setCookie(this.cookieName, 'declined', this.expiryDays);
        this._hideBanner();
    }
    
    /**
     * Handle click events for cookie consent buttons
     * @private
     * @param {Event} e - Click event
     */
    _handleClick = (e) => {
        if (e.target.closest('#cookie-accept-btn')) {
            this._acceptCookies();
        } else if (e.target.closest('#cookie-decline-btn')) {
            this._declineCookies();
        }
    }
    
    /**
     * Initialize the cookie consent module
     * @param {Object} options - Configuration options
     * @param {string} options.cookieName - Name of the cookie to store consent
     * @param {number} options.expiryDays - Number of days until consent expires
     * @param {number} options.initDelay - Delay in ms before showing the banner
     * @return {CookieConsent} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        // Apply custom options
        Object.assign(this, {
            cookieName: options.cookieName || this.cookieName,
            expiryDays: options.expiryDays || this.expiryDays,
            initDelay: options.initDelay || this.initDelay
        });
        
        // Bind events
        document.addEventListener('click', this._handleClick);
        
        // Slight delay to ensure smooth animation
        setTimeout(() => {
            if (!this._hasConsent()) {
                this._showBanner();
            }
        }, this.initDelay);
        
        this.isInitialized = true;
        return this;
    }
    
    /**
     * Manually show the cookie consent banner
     * @return {CookieConsent} - The instance for method chaining
     */
    show() {
        this._showBanner();
        return this;
    }
    
    /**
     * Manually hide the cookie consent banner
     * @return {CookieConsent} - The instance for method chaining
     */
    hide() {
        this._hideBanner();
        return this;
    }
    
    /**
     * Check if consent has been given
     * @return {boolean} - True if consent was given
     */
    hasConsent() {
        return this._hasConsent();
    }
    
    /**
     * Manually set consent status
     * @param {boolean} accepted - Whether cookies are accepted
     * @return {CookieConsent} - The instance for method chaining
     */
    setConsent(accepted) {
        this._setCookie(
            this.cookieName, 
            accepted ? 'accepted' : 'declined', 
            this.expiryDays
        );
        return this;
    }
    
    /**
     * Clean up event listeners
     */
    destroy() {
        if (!this.isInitialized) return;
        
        document.removeEventListener('click', this._handleClick);
        
        this.isInitialized = false;
    }
}

export default CookieConsent; 
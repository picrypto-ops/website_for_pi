/**
 * Cookie Consent Script
 * Handles the cookie consent floating box functionality
 */
document.addEventListener('DOMContentLoaded', () => {
    const cookieConsent = {
        cookieName: 'pi_cookie_consent',
        expiryDays: 365,
        
        /**
         * Initialize the cookie consent functionality
         */
        init() {
            // Slight delay to ensure smooth animation
            setTimeout(() => {
                if (!this.hasConsent()) {
                    this.showBanner();
                }
            }, 1000);
            
            this.bindEvents();
        },
        
        /**
         * Set a cookie with the given name, value and expiry days
         * @param {string} name - Cookie name
         * @param {string} value - Cookie value
         * @param {number} days - Expiry in days
         */
        setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = `expires=${date.toUTCString()}`;
            document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
        },
        
        /**
         * Get a cookie value by name
         * @param {string} name - Cookie name
         * @returns {string|null} Cookie value or null if not found
         */
        getCookie(name) {
            const nameEQ = `${name}=`;
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        
        /**
         * Check if user has already given consent
         * @returns {boolean} True if consent has been given
         */
        hasConsent() {
            return this.getCookie(this.cookieName) === 'accepted';
        },
        
        /**
         * Show the cookie consent floating box
         */
        showBanner() {
            const banner = document.getElementById('cookie-consent-banner');
            if (banner) {
                banner.classList.add('active');
            }
        },
        
        /**
         * Hide the cookie consent floating box
         */
        hideBanner() {
            const banner = document.getElementById('cookie-consent-banner');
            if (banner) {
                banner.classList.remove('active');
                // Add a smooth fade-out effect
                banner.style.opacity = '0';
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 500);
            }
        },
        
        /**
         * Accept cookies and hide the banner
         */
        acceptCookies() {
            this.setCookie(this.cookieName, 'accepted', this.expiryDays);
            this.hideBanner();
        },
        
        /**
         * Decline cookies and hide the banner
         */
        declineCookies() {
            this.setCookie(this.cookieName, 'declined', this.expiryDays);
            this.hideBanner();
        },
        
        /**
         * Bind event listeners
         */
        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('#cookie-accept-btn')) {
                    this.acceptCookies();
                } else if (e.target.closest('#cookie-decline-btn')) {
                    this.declineCookies();
                }
            });
        }
    };
    
    // Initialize cookie consent
    cookieConsent.init();
}); 
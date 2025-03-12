/**
 * Main JavaScript file
 * Initializes all components and modules for the website
 */

// Import core functionality
import { 
    validateForm, 
    isValidEmail, 
    onCurrentPage, 
    initSmoothScrolling, 
    makeCardsClickable, 
    initLazyLoading 
} from './modules/core/helpers/index.js';

// Import components
import { AssetManagementIcons } from './modules/components/asset-management/index.js';
import { 
    ScrollIndicator, 
    LazyLoading, 
    Cards 
} from './modules/components/ui/index.js';
import {
    NavigationMenu,
    HeroMenu
} from './modules/components/navigation/index.js';
import { CookieConsent } from './modules/components/cookie/index.js';

// Import graphics
import { BackgroundWave } from './modules/graphics/index.js';

document.addEventListener("DOMContentLoaded", () => {
    // Initialize smooth scrolling for anchor links
    initSmoothScrolling();
    
    // Initialize form validation for contact form
    const contactForm = document.querySelector("#contact-form");
    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (validateForm(this)) {
                // Submit the form
                this.submit();
            }
        });
    }
    
    // Initialize Navigation Menu if elements exist
    const navigationMenu = new NavigationMenu();
    navigationMenu.init();
    
    // Initialize Hero Menu if elements exist
    const heroMenu = new HeroMenu();
    heroMenu.init();
    
    // Initialize Cookie Consent
    const cookieConsent = new CookieConsent();
    cookieConsent.init();
    
    // Initialize Asset Management Icons if needed
    if (document.querySelector('.asset-management-left-hand, .asset-management-right-hand, .asset-management-combined-hands')) {
        const assetManagementIcons = new AssetManagementIcons();
        assetManagementIcons.init({
            // Override default selectors if needed
            leftHandSelector: '.asset-management-left-hand',
            rightHandSelector: '.asset-management-right-hand',
            combinedSelector: '.asset-management-combined-hands'
        });
    }
    
    // Initialize card components
    const cards = new Cards();
    cards.init();
    
    // Initialize lazy loading
    const lazyLoader = new LazyLoading();
    lazyLoader.init();
    
    // Initialize scroll indicator if present
    if (document.querySelector('.scroll-indicator')) {
        const scrollIndicator = new ScrollIndicator();
        scrollIndicator.init();
    }
    
    // Initialize background wave animation if container exists
    if (document.getElementById('threeJsContainer')) {
        const backgroundWave = new BackgroundWave();
        backgroundWave.init();
    }
    
    // Future: Initialize page-specific modules based on current page
    // Example:
    // onCurrentPage('home', () => {
    //     const homePage = new HomePage();
    //     homePage.init();
    // });
}); 
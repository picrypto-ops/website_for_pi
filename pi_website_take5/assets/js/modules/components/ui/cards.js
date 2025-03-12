/**
 * Cards Component
 * Makes card elements clickable by triggering their primary link
 */

class Cards {
    constructor() {
        this.isInitialized = false;
        this.cards = null;
    }

    /**
     * Handle click events on a card
     * @private
     * @param {Event} event - The click event
     */
    _handleCardClick(event) {
        const card = event.currentTarget;
        const link = card.querySelector('a');
        
        // Only trigger the link if the click wasn't directly on another link
        if (link && !event.target.closest('a')) {
            link.click();
        }
    }

    /**
     * Make cards clickable by adding event listeners
     * @param {string} selector - CSS selector for card elements
     * @private
     */
    _makeCardsClickable(selector) {
        this.cards = document.querySelectorAll(selector);
        
        if (this.cards.length === 0) {
            console.warn(`No cards found with selector: ${selector}`);
            return;
        }
        
        this.cards.forEach(card => {
            // Add pointer cursor to indicate clickability
            card.style.cursor = 'pointer';
            
            // Add click handler
            card.addEventListener('click', this._handleCardClick);
            
            // Prevent double triggering when clicking on links inside cards
            const links = card.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            });
        });
    }

    /**
     * Initialize cards functionality
     * @param {Object} options - Configuration options
     * @param {string} options.selector - CSS selector for card elements
     * @return {Cards} - The instance for method chaining
     */
    init(options = {}) {
        if (this.isInitialized) return this;
        
        const config = {
            selector: '.product-card, .team-card, .segment-card',
            ...options
        };
        
        this._makeCardsClickable(config.selector);
        
        this.isInitialized = true;
        return this;
    }

    /**
     * Clean up event listeners to prevent memory leaks
     */
    destroy() {
        if (!this.isInitialized || !this.cards) return;
        
        this.cards.forEach(card => {
            card.removeEventListener('click', this._handleCardClick);
            
            const links = card.querySelectorAll('a');
            links.forEach(link => {
                link.removeEventListener('click', (e) => {
                    e.stopPropagation();
                });
            });
        });
        
        this.isInitialized = false;
    }
}

export default Cards; 
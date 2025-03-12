/**
 * About Us image slider functionality
 * Handles automatic image rotation, navigation, and dot indicators
 */
class AboutImageSlider {
    constructor(element, options = {}) {
        this.sliderElement = element;
        this.slides = Array.from(this.sliderElement.querySelectorAll('.about-image-slider__slide'));
        this.dotsContainer = this.sliderElement.querySelector('.about-image-slider__dots');
        this.prevButton = this.sliderElement.querySelector('.about-image-slider__button--prev');
        this.nextButton = this.sliderElement.querySelector('.about-image-slider__button--next');
        this.container = this.sliderElement.querySelector('.about-image-slider__container');
        
        // Configuration with defaults
        this.options = {
            autoplay: true,
            interval: 5000, // 5 seconds
            ...options
        };
        
        this.currentIndex = 0;
        this.autoplayInterval = null;
        this.isAnimating = false; // Flag to prevent multiple transitions
        this.isInitialized = false; // Flag to track initialization state
        
        // Initialize
        this.init();
    }
    
    init() {
        // If no slides, exit early
        if (this.slides.length === 0) return;
        
        // Temporarily disable transitions during initialization
        this.container.style.transition = 'none';
        
        // Clone first and last slides for smooth infinite loop
        if (this.slides.length > 1) {
            const firstSlideClone = this.slides[0].cloneNode(true);
            const lastSlideClone = this.slides[this.slides.length - 1].cloneNode(true);
            
            this.container.appendChild(firstSlideClone);
            this.container.insertBefore(lastSlideClone, this.slides[0]);
            
            // Update slides array to include clones
            this.slides = Array.from(this.sliderElement.querySelectorAll('.about-image-slider__slide'));
            
            // Set initial position to the first actual slide (after the cloned last slide)
            this.currentIndex = 1; // Start at index 1 (first actual slide)
            this.container.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        }
        
        // Create dot indicators
        this.createDots();
        
        // Set initial active state
        this.updateActiveDot();
        
        // Force a reflow to ensure the transformation is applied immediately
        void this.container.offsetWidth;
        
        // Make the slider initially invisible and fade it in after it's ready
        this.sliderElement.style.opacity = '0';
        
        // Wait for all images to load before enabling transitions and starting autoplay
        this.preloadSlideImages().then(() => {
            // Re-enable transitions after a small delay
            setTimeout(() => {
                this.container.style.transition = 'transform 0.5s ease-in-out';
                this.sliderElement.style.opacity = '1';
                this.sliderElement.style.transition = 'opacity 0.3s ease-in-out';
                this.isInitialized = true;
                
                // Add event listeners
                this.addEventListeners();
                
                // Start autoplay if enabled
                if (this.options.autoplay) {
                    this.startAutoplay();
                }
            }, 50);
        });
    }
    
    createDots() {
        // Clear existing dots
        if (this.dotsContainer) {
            this.dotsContainer.innerHTML = '';
        }
        
        // Only create dots for the original slides (excluding clones)
        const originalSlidesCount = this.slides.length - (this.slides.length > 1 ? 2 : 0);
        
        for (let i = 0; i < originalSlidesCount; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            dot.dataset.index = i;
            this.dotsContainer.appendChild(dot);
        }
        
        this.dots = Array.from(this.dotsContainer.querySelectorAll('.dot'));
    }
    
    addEventListeners() {
        // Navigation buttons
        if (this.prevButton) {
            this.prevButton.addEventListener('click', () => this.prevSlide());
        }
        
        if (this.nextButton) {
            this.nextButton.addEventListener('click', () => this.nextSlide());
        }
        
        // Dot navigation
        this.dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                // Convert dot index to slide index (accounting for the cloned first slide)
                const slideIndex = this.slides.length > 1 ? i + 1 : i;
                this.goToSlide(slideIndex);
                this.resetAutoplay();
            });
        });
        
        // Pause autoplay on hover or touch
        this.sliderElement.addEventListener('mouseenter', () => this.pauseAutoplay());
        this.sliderElement.addEventListener('touchstart', () => this.pauseAutoplay(), { passive: true });
        
        this.sliderElement.addEventListener('mouseleave', () => {
            if (this.options.autoplay) {
                this.startAutoplay();
            }
        });
        this.sliderElement.addEventListener('touchend', () => {
            if (this.options.autoplay) {
                this.startAutoplay();
            }
        }, { passive: true });
        
        // Handle transition end
        this.container.addEventListener('transitionend', () => this.handleTransitionEnd());
    }
    
    handleTransitionEnd() {
        this.isAnimating = false;
        
        // If we've transitioned to a clone, immediately jump to the real slide without animation
        if (this.slides.length > 1) {
            // If we're at the last clone (index this.slides.length - 1)
            if (this.currentIndex === this.slides.length - 1) {
                this.container.style.transition = 'none';
                this.currentIndex = 1; // Go to the first real slide
                this.container.style.transform = `translateX(-${this.currentIndex * 100}%)`;
                
                // Force a reflow
                void this.container.offsetWidth;
                
                // Re-enable transitions
                this.container.style.transition = 'transform 0.5s ease-in-out';
            }
            // If we're at the first clone (index 0)
            else if (this.currentIndex === 0) {
                this.container.style.transition = 'none';
                this.currentIndex = this.slides.length - 2; // Go to the last real slide
                this.container.style.transform = `translateX(-${this.currentIndex * 100}%)`;
                
                // Force a reflow
                void this.container.offsetWidth;
                
                // Re-enable transitions
                this.container.style.transition = 'transform 0.5s ease-in-out';
            }
        }
    }
    
    goToSlide(index) {
        if (this.isAnimating || !this.isInitialized) return;
        this.isAnimating = true;
        
        this.currentIndex = index;
        
        // Update container position with animation
        this.container.style.transition = 'transform 0.5s ease-in-out';
        this.container.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        
        // Update active dot
        this.updateActiveDot();
    }
    
    updateActiveDot() {
        if (!this.dots || this.dots.length === 0) return;
        
        // Calculate the corresponding dot index based on the current slide
        let dotIndex = this.currentIndex;
        if (this.slides.length > 1) {
            // Adjust for cloned slides
            if (this.currentIndex === 0) {
                dotIndex = this.dots.length - 1; // Last dot for first clone
            } else if (this.currentIndex === this.slides.length - 1) {
                dotIndex = 0; // First dot for last clone
            } else {
                dotIndex = this.currentIndex - 1; // Normal case (subtract 1 for the cloned first slide)
            }
        }
        
        this.dots.forEach((dot, i) => {
            if (i === dotIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    nextSlide() {
        if (this.isAnimating) return;
        
        const newIndex = this.currentIndex + 1;
        this.goToSlide(newIndex);
        this.resetAutoplay();
    }
    
    prevSlide() {
        if (this.isAnimating) return;
        
        const newIndex = this.currentIndex - 1;
        this.goToSlide(newIndex);
        this.resetAutoplay();
    }
    
    startAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
        }
        
        this.autoplayInterval = setInterval(() => {
            this.nextSlide();
        }, this.options.interval);
    }
    
    pauseAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
    
    resetAutoplay() {
        if (this.options.autoplay) {
            this.pauseAutoplay();
            this.startAutoplay();
        }
    }
    
    // Helper method to preload all slide images
    preloadSlideImages() {
        const imagePromises = this.slides.map(slide => {
            return new Promise((resolve) => {
                const bgImage = slide.style.backgroundImage;
                if (!bgImage || bgImage === 'none') {
                    // No background image to load
                    resolve();
                    return;
                }
                
                // Extract URL from the backgroundImage style
                const url = bgImage.match(/url\(['"]?(.*?)['"]?\)/)?.[1];
                if (!url) {
                    resolve();
                    return;
                }
                
                const img = new Image();
                img.onload = () => resolve();
                img.onerror = () => resolve(); // Resolve even on error to continue
                img.src = url;
            });
        });
        
        return Promise.all(imagePromises);
    }
}

// Initialize all sliders when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const sliders = document.querySelectorAll('.about-image-slider');
    
    sliders.forEach(slider => {
        new AboutImageSlider(slider);
    });
});

export default AboutImageSlider; 
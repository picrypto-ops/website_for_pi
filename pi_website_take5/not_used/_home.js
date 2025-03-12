document.addEventListener('DOMContentLoaded', function() {
    // Initialize the PI background wave
    if (typeof initPiBgWave === 'function') {
        initPiBgWave();
    }
    
    // Get header for scroll calculations
    const header = document.getElementById('site-header');
    const headerHeight = header ? header.offsetHeight : 0;
    
    // Show header on scroll past the hero section
    const heroSection = document.querySelector('.hero.full-page');
    
    if (header && heroSection) {
        window.addEventListener('scroll', function() {
            if (window.scrollY >= heroSection.offsetHeight - 100) {
                header.classList.remove('initially-hidden');
            } else {
                header.classList.add('initially-hidden');
            }
        });
    }
    
    // Update breadcrumb navigation based on scroll position
    const updateBreadcrumbNavigation = function() {
        const sections = document.querySelectorAll('.full-page-section, .hero.full-page');
        const breadcrumbLinks = document.querySelectorAll('.breadcrumb-navigation a');
        const teamLinks = document.querySelectorAll('.team-nav a');
        const windowHeight = window.innerHeight;
        const scrollY = window.scrollY;
        
        // Find which section is currently most visible
        let currentSectionId = '';
        let maxVisibleAmount = 0;
        
        sections.forEach(section => {
            if (!section.id) return;
            
            const rect = section.getBoundingClientRect();
            const visibleTop = Math.max(0, rect.top);
            const visibleBottom = Math.min(windowHeight, rect.bottom);
            const visibleHeight = Math.max(0, visibleBottom - visibleTop);
            const visibilityPercentage = visibleHeight / windowHeight;
            
            if (visibilityPercentage > maxVisibleAmount) {
                maxVisibleAmount = visibilityPercentage;
                currentSectionId = section.id;
            }
        });
        
        // Clear all active states
        breadcrumbLinks.forEach(link => {
            link.classList.remove('active');
        });
        
        // Update active state for current section
        if (currentSectionId) {
            const activeLink = document.querySelector(`.breadcrumb-navigation a[href="#${currentSectionId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
            
            // Update team navigation if in team section
            if (currentSectionId === 'team') {
                // Find which team group is in view
                const teamGroups = document.querySelectorAll('.team-group');
                let currentTeamGroupId = '';
                
                teamGroups.forEach(group => {
                    const rect = group.getBoundingClientRect();
                    if (rect.top <= windowHeight/2 && rect.bottom >= windowHeight/2) {
                        currentTeamGroupId = group.id;
                    }
                });
                
                if (currentTeamGroupId) {
                    teamLinks.forEach(link => {
                        const href = link.getAttribute('href').substring(1);
                        if (href === currentTeamGroupId) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    });
                }
            }
        }
    };
    
    // Update scroll indicators visibility when scrolling
    const updateScrollIndicators = function() {
        const sections = document.querySelectorAll('.full-page-section, .hero.full-page');
        const windowHeight = window.innerHeight;
        const scrollY = window.scrollY;
        
        sections.forEach((section, index) => {
            const scrollIndicator = section.querySelector('.scroll-indicator');
            if (!scrollIndicator) return;
            
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            
            // Hide scroll indicator on team section since it's scrollable
            if (section.id === 'team') {
                scrollIndicator.style.opacity = '1';
                // Show indicator only when at the bottom of the team section
                const teamBottom = sectionTop + sectionHeight - windowHeight;
                if (scrollY >= teamBottom - 50) {
                    scrollIndicator.style.opacity = '1';
                } else {
                    scrollIndicator.style.opacity = '0';
                }
            } 
            // Otherwise show when section is in view
            else if (scrollY >= sectionTop - headerHeight && 
                scrollY < sectionTop + sectionHeight - windowHeight) {
                scrollIndicator.style.opacity = '1';
            } else {
                scrollIndicator.style.opacity = '0';
            }
            
            // Always hide on last section
            if (index === sections.length - 1) {
                if (scrollIndicator) scrollIndicator.style.opacity = '0';
            }
        });
    };
    
    // Initial call to both functions
    updateBreadcrumbNavigation();
    updateScrollIndicators();
    
    // Call on scroll
    window.addEventListener('scroll', function() {
        updateBreadcrumbNavigation();
        updateScrollIndicators();
    });
    
    // Improved smooth scrolling with header offset
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            if (targetId) {
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Calculate position with header offset
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    
                    // Smooth scroll with header offset
                    window.scrollTo({
                        top: targetPosition - headerHeight,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without scrolling
                    history.pushState(null, null, '#' + targetId);
                }
            }
        });
    });
    
    // Add animated class to scroll buttons
    document.querySelectorAll('.scroll-down').forEach(button => {
        button.classList.add('animated');
    });
    
    // Add scroll helper for team section
    const teamSection = document.querySelector('#team');
    if (teamSection) {
        const teamContent = teamSection.querySelector('.team-preview-grid');
        if (teamContent) {
            // Wrap team content in scrollable container
            const wrapper = document.createElement('div');
            wrapper.className = 'team-content-wrapper';
            
            const scrollableContainer = document.createElement('div');
            scrollableContainer.className = 'team-scrollable-container';
            
            // Move the team content into the scrollable container
            teamContent.parentNode.insertBefore(wrapper, teamContent);
            wrapper.appendChild(scrollableContainer);
            scrollableContainer.appendChild(teamContent);
            
            // Add scroll helper message
            const scrollHelper = document.createElement('div');
            scrollHelper.className = 'scroll-helper';
            scrollHelper.innerHTML = '<span class="scroll-text">Scroll to see more team members</span>';
            wrapper.appendChild(scrollHelper);
            
            // Hide helper when scrolled to bottom
            scrollableContainer.addEventListener('scroll', function() {
                const isAtBottom = this.scrollHeight - this.scrollTop <= this.clientHeight + 50;
                scrollHelper.style.opacity = isAtBottom ? '0' : '0.7';
            });
        }
    }
    
    // Add to the existing DOMContentLoaded event listener
    window.addEventListener('scroll', updateBreadcrumbNavigation);
    window.addEventListener('load', updateBreadcrumbNavigation);
}); 
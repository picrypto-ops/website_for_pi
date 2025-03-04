/**
 * Asset Management Icons Handler
 * Handles dynamic loading and manipulation of SVG icons for asset management section
 */

class AssetManagementIcons {
    constructor() {
        this.iconBasePath = '/assets/images/asset_management/';
        this.icons = {
            handLeft: 'hand_left.svg',
            handRight: 'hand_right.svg',
            combined: 'icon.svg'
        };
    }

    /**
     * Load an SVG icon and inject it into the specified element
     * @param {string} iconName - Name of the icon to load (key from this.icons)
     * @param {HTMLElement} targetElement - Element to inject the SVG into
     * @return {Promise} - Promise that resolves when the SVG is loaded
     */
    loadIcon(iconName, targetElement) {
        if (!this.icons[iconName]) {
            console.error(`Icon ${iconName} not found`);
            return Promise.reject(new Error(`Icon ${iconName} not found`));
        }

        const iconPath = this.iconBasePath + this.icons[iconName];
        
        return fetch(iconPath)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load icon: ${response.statusText}`);
                }
                return response.text();
            })
            .then(svgContent => {
                targetElement.innerHTML = svgContent;
                return targetElement.querySelector('svg');
            })
            .catch(error => {
                console.error('Error loading SVG:', error);
                throw error;
            });
    }

    /**
     * Initialize icons for asset management section
     * @param {Object} options - Configuration options
     */
    init(options = {}) {
        const defaults = {
            leftHandSelector: '.left-hand-icon',
            rightHandSelector: '.right-hand-icon',
            combinedSelector: '.combined-hands-icon'
        };

        const config = {...defaults, ...options};

        // Initialize left hand icons
        document.querySelectorAll(config.leftHandSelector).forEach(element => {
            this.loadIcon('handLeft', element);
        });

        // Initialize right hand icons
        document.querySelectorAll(config.rightHandSelector).forEach(element => {
            this.loadIcon('handRight', element);
        });

        // Initialize combined icons
        document.querySelectorAll(config.combinedSelector).forEach(element => {
            this.loadIcon('combined', element);
        });
    }
}

// Export the class
export default AssetManagementIcons; 
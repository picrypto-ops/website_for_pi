/**
 * HTML Class Converter - BEM Implementation
 * 
 * A utility script to help convert existing HTML files to use BEM class names.
 * This creates a mapping of old class names to new BEM-compliant class names and
 * applies those changes to HTML files.
 * 
 * Usage:
 * 1. Define class mappings in the classMapping object
 * 2. Set the htmlFiles path
 * 3. Run: node html-converter.js
 */

const fs = require('fs');
const path = require('path');
const glob = require('glob');

// Configuration
const config = {
  // HTML/PHP files to update
  htmlFiles: path.resolve(__dirname, '../../../**/*.{html,php}'),
  // Whether to create backup files before changing
  createBackups: true
};

// Define mapping from old class names to new BEM class names
// Format: 'old-class-name': 'new-bem-class-name'
const classMapping = {
  // Header classes
  'header': 'site-header',
  'initially-hidden': 'site-header--hidden',
  'header-content': 'site-header__content',
  'logo': 'site-header__logo',
  'group-name': 'site-header__logo__text',
  'mobile-menu-toggle': 'site-header__mobile-toggle',
  'menu-icon': 'site-header__mobile-toggle__icon',
  'bar': 'site-header__mobile-toggle__bar',
  'active': 'site-header__mobile-toggle--active', // Note: this is context-dependent
  'main-nav': 'site-header__nav',
  'nav-active': 'site-header__nav--active',
  'nav-item': 'site-header__nav__item',
  'nav-link': 'site-header__nav__link',
  'nav-link-active': 'site-header__nav__link--active',
  'header-actions': 'site-header__actions',
  
  // Home page classes
  'home-page': 'home',
  'hero': 'home__hero',
  'hero-top-logo': 'home__hero__logo',
  'right-aligned': 'home__hero__logo--right',
  'left-aligned': 'home__hero__logo--left',
  'hero-language-switcher': 'home__hero__language-switcher',
  'hero-content': 'home__hero__content',
  'hero-title': 'home__hero__title',
  'hero-subtitle': 'home__hero__subtitle',
  'hero-actions': 'home__hero__actions',
  'scroll-indicator': 'home__hero__scroll-indicator',
  'scroll-icon': 'home__hero__scroll-indicator__icon',
  'scroll-text': 'home__hero__scroll-indicator__text',
  'hero-background': 'home__hero__background',
  'overlay': 'home__hero__background__overlay',
  
  // Features section
  'features-section': 'home__features',
  'section-title': 'home__features__title',
  'features-grid': 'home__features__grid',
  'feature-item': 'home__features__item',
  'feature-icon': 'home__features__item__icon',
  'feature-title': 'home__features__item__title',
  'feature-text': 'home__features__item__text',
  
  // About section
  'about-section': 'home__about',
  'about-container': 'home__about__container',
  'about-content': 'home__about__content',
  'about-title': 'home__about__title',
  'about-text': 'home__about__text',
  'about-image': 'home__about__image'
};

/**
 * Function to update class names in an HTML string
 */
function updateClassNames(htmlContent) {
  // Regular expression to match class attributes
  const classRegex = /class=["']([^"']*)["']/g;
  
  return htmlContent.replace(classRegex, (match, classStr) => {
    // Split into individual class names
    const classes = classStr.split(/\s+/);
    
    // Replace each class with its mapped value if it exists
    const updatedClasses = classes.map(className => {
      return classMapping[className.trim()] || className.trim();
    });
    
    // Join back into a class string
    return `class="${updatedClasses.join(' ')}"`;
  });
}

/**
 * Main function to convert HTML files
 */
async function convertHtmlFiles() {
  console.log('Starting HTML class conversion...');
  
  // Get all HTML/PHP files
  const htmlFiles = glob.sync(config.htmlFiles);
  console.log(`Found ${htmlFiles.length} HTML/PHP files to process`);
  
  let convertedCount = 0;
  
  // Process each file
  htmlFiles.forEach(filePath => {
    try {
      // Read file content
      const content = fs.readFileSync(filePath, 'utf8');
      
      // Update class names
      const updatedContent = updateClassNames(content);
      
      // Check if content was changed
      if (content !== updatedContent) {
        // Create backup if enabled
        if (config.createBackups) {
          fs.writeFileSync(`${filePath}.backup`, content);
        }
        
        // Write updated content
        fs.writeFileSync(filePath, updatedContent);
        
        console.log(`Updated: ${filePath}`);
        convertedCount++;
      }
    } catch (err) {
      console.error(`Error processing file ${filePath}:`, err);
    }
  });
  
  console.log(`Conversion complete! Updated ${convertedCount} files.`);
}

// Check if real execution is desired (uncomment to enable)
// convertHtmlFiles().catch(err => console.error('Error during HTML conversion:', err));

// For safety, leave execution commented and output instructions
console.log(`
IMPORTANT: This script is currently in preview mode.

This script will update class names in your HTML/PHP files according to the defined mappings.
Before running this script:
1. Make sure you have backed up your project or are using version control
2. Review the class mappings to ensure they are correct
3. Test on a small subset of files first

To actually run the conversion, uncomment the line:
// convertHtmlFiles().catch(err => console.error('Error during HTML conversion:', err));
`); 
# Testing the BEM Implementation

This guide will walk you through the process of testing the BEM-based SCSS implementation to ensure it doesn't change the appearance of your website.

## Prerequisites

- Local development environment with PHP and a web server (like Apache or NGINX)
- SASS compiler installed (`npm install -g sass`)
- The website should be accessible via a local URL (e.g., http://localhost/your-site/)

## Setup Process

1. **Run the setup script**

   On Windows, open PowerShell and run:
   ```
   cd path\to\pi_website_take5\assets\scss-bem\tools
   .\setup-test.ps1
   ```

   On macOS/Linux, open Terminal and run:
   ```
   cd path/to/pi_website_take5/assets/scss-bem/tools
   bash setup-test.sh
   ```

   This script will:
   - Create any missing placeholder files
   - Compile the BEM SCSS to CSS
   - Set up the testing environment

2. **Configure the test tools**

   Open `style-tester.php` in a text editor and update the configuration:
   ```php
   $config = [
       // Update these paths to match your environment
       'original_css' => '../css/style.css', 
       'bem_css' => '../css/style-bem.css',
       
       // Add all pages you want to test
       'pages' => [
           'home' => '/index.php',
           'about' => '/about.php',
           'contact' => '/contact.php',
           // Add more pages as needed
       ]
   ];
   ```

## Testing Process

1. **Access the Style Tester**

   Open your browser and navigate to:
   ```
   http://localhost/your-site/pi_website_take5/assets/scss-bem/tools/style-tester.php
   ```

   This tool will allow you to toggle between the original styles and the BEM styles.

2. **Use the Visual Diff Tool**

   Click the "Visual Diff" button to open the side-by-side comparison tool. This will show:
   - Original styles on the left
   - BEM styles on the right

   The synchronized scrolling feature will help you spot any differences as you navigate the page.

3. **Test Different Pages**

   Use the dropdown menu to switch between different pages of your website to ensure all pages display correctly with the BEM styles.

4. **Test Responsive Behavior**

   For each page:
   1. Resize your browser window to test different screen sizes
   2. Check that the layout responds the same way with both style sets
   3. Test on mobile devices if possible

5. **Identify and Fix Issues**

   If you notice any visual differences:
   1. Open browser dev tools and inspect the problematic element
   2. Check which CSS rules are being applied in the original vs. BEM version
   3. Modify the BEM SCSS files accordingly
   4. Recompile the BEM CSS:
      ```
      cd path\to\pi_website_take5\assets\scss-bem\tools
      php compile-bem.php
      ```
   5. Refresh the Visual Diff page to verify your fix

## Common Issues and Solutions

1. **Missing Elements**

   If an element is missing in the BEM version, it might be because:
   - The corresponding BEM class is not included in the current conversion
   - There's a typo in the BEM class name
   - The HTML conversion mapping needs to be updated

2. **Styling Differences**

   If elements look different in the BEM version, check:
   - CSS specificity issues (BEM reduces specificity by design)
   - Missing modifiers or states
   - Different variable values

3. **Layout Issues**

   For layout problems:
   - Check media queries and breakpoints
   - Verify grid system implementation
   - Ensure nested element styles are properly converted

## Next Steps

Once you've confirmed the BEM implementation looks identical to the original:

1. Continue converting more components one by one
2. Run the Visual Diff Test after each component is converted
3. When all components pass the visual test, update your HTML templates to use the BEM classes
4. Use the `html-converter.js` tool to help with the HTML updates

## Need Help?

If you encounter issues that you can't resolve:

1. Take screenshots of the visual differences
2. Document which components are affected
3. Check the browser console for any errors
4. Look at the compiled CSS to see if there are any missing styles

By following this testing process, you'll ensure that the BEM implementation maintains the exact same visual appearance as your original CSS. 